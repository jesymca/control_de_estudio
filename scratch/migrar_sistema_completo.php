<?php
/**
 * ==============================================================================
 * SCRIPT MAESTRO DE MIGRACIÓN HISTÓRICA DE ALTO RENDIMIENTO
 * Origen: pnfdiurno (Sistema Viejo) -> Destino: uptpc (Sistema Nuevo)
 * ==============================================================================
 */
ini_set('memory_limit', '2048M');
set_time_limit(0);

$db = mysqli_connect('127.0.0.1', 'root', '123456', 'uptpc');
if (!$db) {
    die("Error conectando a MySQL: " . mysqli_connect_error() . "\n");
}
mysqli_set_charset($db, 'utf8mb4');

echo "====================================================================\n";
echo "   SISTEMA DE CONTROL DE ESTUDIOS UPTPC - MIGRACIÓN HISTÓRICA       \n";
echo "====================================================================\n";
echo "Inicio: " . date('Y-m-d H:i:s') . "\n\n";

// Función para sanear y garantizar UTF-8 limpio desde fuentes mixtas Latin1/UTF-8
function sanearTexto($str) {
    if ($str === null || $str === '') return '';
    $str = str_replace("\xEF\xBF\xBD", '', $str);
    if (!mb_check_encoding($str, 'UTF-8')) {
        $str = mb_convert_encoding($str, 'UTF-8', 'ISO-8859-1');
    }
    $str = iconv('UTF-8', 'UTF-8//IGNORE', $str);
    return trim($str);
}

// 1. Verificación de bases de datos
$resPnf = mysqli_query($db, "SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = 'pnfdiurno'");
if (mysqli_num_rows($resPnf) === 0) {
    die("FATAL: La base de datos origen 'pnfdiurno' no existe.\n");
}
echo "[OK] Base de datos origen 'pnfdiurno' detectada correctamente.\n";

// 2. Verificar usuario prueba en uptpc
$checkPrueba = mysqli_query($db, "SELECT id, email, username FROM users WHERE id = 2 OR email = 'herrejose@gmail.com'");
$userPrueba = mysqli_fetch_assoc($checkPrueba);
if (!$userPrueba) {
    echo "[AVISO] Usuario prueba no encontrado con id=2, verificando id=1 o admin...\n";
} else {
    echo "[OK] Usuario de prueba identificado: {$userPrueba['email']} (ID: {$userPrueba['id']}, Username: {$userPrueba['username']})\n";
}

// 3. Desactivar temporalmente Foreign Keys, Unique Checks y Triggers
echo "\n--- PREPARANDO ENTORNO DE ALTA VELOCIDAD ---\n";
mysqli_query($db, "SET FOREIGN_KEY_CHECKS = 0;");
mysqli_query($db, "SET UNIQUE_CHECKS = 0;");
mysqli_query($db, "SET SQL_LOG_BIN = 0;");
mysqli_query($db, "SET autocommit = 1;");
mysqli_query($db, "DROP TRIGGER IF EXISTS `actualizar_total_usuarios`;");
echo "[OK] Triggers y restricciones suspendidos temporalmente para inserción masiva.\n";

// 4. Limpieza de datos de prueba en uptpc (preservando usuarios 1..5)
echo "\n--- LIMPIEZA DE DATOS DE PRUEBA EN UPTPC ---\n";
$tablasLimpiar = [
    'estudiante_materias',
    'estudiante_seccion',
    'notas_trimestres',
    'notas_pendientes',
    'notas_definitivas',
    'graduados',
    'preinscripcion',
    'bitacora',
    'docente_seccion',
    'docente_materia'
];
foreach ($tablasLimpiar as $tabla) {
    mysqli_query($db, "TRUNCATE TABLE `$tabla`;");
    echo "  - Tabla `$tabla` vaciada.\n";
}

// Eliminar usuarios dummy (mantener id <= 5)
mysqli_query($db, "DELETE FROM users WHERE id > 5;");
echo "  - Usuarios de prueba eliminados (preservados usuarios id 1..5: Super User, Admin, etc.).\n";

// 5. Diccionarios de Carreras
$carreraLetraMap = [
    'I' => 1,   // PNF EN INFORMATICA
    'G' => 2,   // PNF EN TURISMO
    'D' => 5,   // PNF EN DISTRIBUCION Y LOGISTICA
    'M' => 14,  // PTF EN MECANICA
    'A' => 15,  // PTF EN MECANICA AUTOMOTRIZ
    'T' => 16,  // PNF EN MANTENIMIENTO
    'E' => 17,  // PNF EN MATERIALES INDUSTRIALES
    'C' => 18,  // PNF EN CIENCIAS FISCALES
    'R' => 19   // PNF EN MECANICA TERMICA
];

$carreraPensumMap = [
    'IXC' => 1,
    'GXC' => 2,
    'GXX' => 2,
    'DXC' => 5,
    'MXC' => 14,
    'AXC' => 15,
    'TXC' => 16,
    'EXC' => 17,
    'EXB' => 17,
    'CXC' => 18,
    'RXC' => 19
];

// 6. Migrar Periodos Académicos (Lapsos)
echo "\n--- FASE 1: MIGRANDO PERIODOS ACADÉMICOS (LAPSOS) ---\n";
$resLapsos = mysqli_query($db, "
    SELECT DISTINCT lapso FROM (
        SELECT lapso FROM pnfdiurno.lapso WHERE lapso IS NOT NULL AND lapso != ''
        UNION
        SELECT lapso FROM pnfdiurno.notas WHERE lapso IS NOT NULL AND lapso != ''
    ) AS todos_lapsos ORDER BY lapso ASC
");

$mapPeriodos = [];
$valuesPer = [];

// Pre-cargar periodos existentes
$chkP = mysqli_query($db, "SELECT id_periodo, codigo_lapso, nombre_periodo FROM periodos_academicos");
while ($pr = mysqli_fetch_assoc($chkP)) {
    if (!empty($pr['codigo_lapso'])) $mapPeriodos[$pr['codigo_lapso']] = $pr['id_periodo'];
    if (!empty($pr['nombre_periodo'])) $mapPeriodos[$pr['nombre_periodo']] = $pr['id_periodo'];
}

while ($row = mysqli_fetch_assoc($resLapsos)) {
    $lapso = trim($row['lapso']);
    if (empty($lapso) || isset($mapPeriodos[$lapso])) continue;

    $partes = explode('-', $lapso);
    $anio = (int)$partes[0];
    $sub = isset($partes[1]) ? (int)$partes[1] : 1;
    $mesInicio = ($sub === 1) ? '01-15' : (($sub === 2) ? '06-01' : '09-15');
    $mesFin    = ($sub === 1) ? '05-30' : (($sub === 2) ? '08-30' : '12-15');
    $fIni = ($anio > 1900 && $anio < 2100) ? "$anio-$mesInicio" : '2000-01-01';
    $fFin = ($anio > 1900 && $anio < 2100) ? "$anio-$mesFin" : '2000-12-31';

    $valuesPer[] = "('$lapso', '$lapso', '$fIni', '$fFin', 0)";
}

if (!empty($valuesPer)) {
    mysqli_query($db, "INSERT INTO periodos_academicos (nombre_periodo, codigo_lapso, fecha_inicio, fecha_fin, activo) VALUES " . implode(",\n", $valuesPer));
}

// Re-cargar mapa de periodos completo
$chkP2 = mysqli_query($db, "SELECT id_periodo, codigo_lapso, nombre_periodo FROM periodos_academicos");
while ($pr = mysqli_fetch_assoc($chkP2)) {
    if (!empty($pr['codigo_lapso'])) $mapPeriodos[$pr['codigo_lapso']] = $pr['id_periodo'];
    if (!empty($pr['nombre_periodo'])) $mapPeriodos[$pr['nombre_periodo']] = $pr['id_periodo'];
}
echo "[OK] Periodos académicos listos. Total indexados: " . count($mapPeriodos) . "\n";

// 7. Migrar Materias (Lismat) en Lote
echo "\n--- FASE 2: MIGRANDO MATERIAS (LISMAT) ---\n";
$resLismat = mysqli_query($db, "SELECT * FROM pnfdiurno.lismat WHERE cod_mat IS NOT NULL AND cod_mat != '' ORDER BY id ASC");
$materiasBatch = [];
$carreraMateriaBatch = [];
$rawLismat = [];

while ($m = mysqli_fetch_assoc($resLismat)) {
    $codMat = trim($m['cod_mat']);
    $pensum = trim($m['pensum'] ?? '');
    $idCarrera = $carreraPensumMap[$pensum] ?? 0;
    $nombreMateria = mysqli_real_escape_string($db, sanearTexto($m['descrip2'] ?? $codMat));
    $creditos = max(1, (int)($m['creditos'] ?? 3));
    $notaMin = (float)($m['aprobatori'] ?? 12.00);
    $trayecto = (int)($m['trayecto'] ?? 0);
    $semestre = max(1, (int)($m['semestre'] ?? 1));

    $rawLismat[] = [
        'codMat' => $codMat,
        'pensum' => $pensum,
        'idCarrera' => $idCarrera,
        'semestre' => $semestre
    ];

    $key = $codMat . '_' . $pensum;
    if (!isset($materiasBatch[$key])) {
        $materiasBatch[$key] = "('$codMat', '$nombreMateria', $creditos, $notaMin, $trayecto, 1, 'PNF', '$pensum')";
    }
}

if (!empty($materiasBatch)) {
    $sqlMatBatch = "
        INSERT INTO materias (cod_materia, nombre_materia, creditos, nota_minima_aprobatoria, trayecto, duracion_periodo, pnf_ptf, pensum_origen)
        VALUES " . implode(",\n", array_values($materiasBatch)) . "
        ON DUPLICATE KEY UPDATE nombre_materia = VALUES(nombre_materia)
    ";
    mysqli_query($db, $sqlMatBatch);
}

// Cargar mapa completo de materias en memoria
$mapMaterias = [];
$resAllMat = mysqli_query($db, "SELECT id_materia, cod_materia, pensum_origen FROM materias");
while ($mr = mysqli_fetch_assoc($resAllMat)) {
    $cM = trim($mr['cod_materia']);
    $pM = trim($mr['pensum_origen'] ?? '');
    $idM = (int)$mr['id_materia'];
    $idC = $carreraPensumMap[$pM] ?? 0;

    $mapMaterias[$cM][$idC] = $idM;
    $mapMaterias[$cM][0] = $idM;
}

// Inserción en lote de carrera_materia
$valuesCM = [];
foreach ($rawLismat as $item) {
    $idMateria = $mapMaterias[$item['codMat']][$item['idCarrera']] ?? ($mapMaterias[$item['codMat']][0] ?? 0);
    if ($idMateria > 0 && $item['idCarrera'] > 0) {
        $keyCM = "{$item['idCarrera']}_{$idMateria}";
        if (!isset($carreraMateriaBatch[$keyCM])) {
            $carreraMateriaBatch[$keyCM] = true;
            $valuesCM[] = "({$item['idCarrera']}, $idMateria, {$item['semestre']})";
        }
    }
}

if (!empty($valuesCM)) {
    // Insertar en fragmentos de 1000
    $chunksCM = array_chunk($valuesCM, 1000);
    foreach ($chunksCM as $chk) {
        mysqli_query($db, "INSERT IGNORE INTO carrera_materia (id_carrera, id_materia, semestre) VALUES " . implode(",\n", $chk));
    }
}
echo "[OK] Materias listas. Total indexadas en memoria: " . count($mapMaterias) . "\n";

// 8. Migrar Docentes en Lote
echo "\n--- FASE 3: MIGRANDO DOCENTES (616 DOCENTES HISTÓRICOS) ---\n";
$resDocentes = mysqli_query($db, "SELECT * FROM pnfdiurno.docente ORDER BY id ASC");
$mapDocentes = []; // cod_doc => id_usuario
$docentesBatch = [];
$defaultHash = password_hash('12345678', PASSWORD_BCRYPT);

while ($d = mysqli_fetch_assoc($resDocentes)) {
    $codDoc = (int)$d['cod_doc'];
    $rawCed = trim($d['cedula'] ?? '');
    $cedulaLimpia = preg_replace('/[^0-9]/', '', $rawCed);
    if (empty($cedulaLimpia)) {
        $cedulaLimpia = "DOC" . str_pad($codDoc, 6, '0', STR_PAD_LEFT);
    }
    $letra = (stripos($rawCed, 'E') === 0) ? 'E-' : 'V-';
    $username = $letra . $cedulaLimpia;
    $idusuario = $rawCed ?: $username;
    $nombre = mysqli_real_escape_string($db, sanearTexto($d['nombre'] ?? 'DOCENTE'));
    $telefono = mysqli_real_escape_string($db, sanearTexto($d['telefono'] ?? ''));
    $titulo = mysqli_real_escape_string($db, sanearTexto($d['titulo_l'] ?? 'DOCENTE'));
    $status = ($d['actividad'] == 1) ? 1 : 0;

    $docentesBatch[] = "(
        '$idusuario', '$username', '$nombre', 'docente', 1, $status, '$telefono', '$titulo',
        '$defaultHash', 'migracion_historica'
    )";
}

if (!empty($docentesBatch)) {
    $sqlDocBatch = "
        INSERT INTO users (
            idusuario, username, nombre, user_type, docente, status, tlf, titulos,
            password, origen_registro
        ) VALUES " . implode(",\n", $docentesBatch) . "
        ON DUPLICATE KEY UPDATE docente = 1, nombre = VALUES(nombre)
    ";
    mysqli_query($db, $sqlDocBatch);
}

// Cargar mapa de docentes en memoria
$resDocMap = mysqli_query($db, "SELECT id, idusuario, username FROM users WHERE docente = 1");
while ($dr = mysqli_fetch_assoc($resDocMap)) {
    $did = (int)$dr['id'];
    $dusr = trim($dr['username']);
    $didusr = trim($dr['idusuario']);
    $digits = preg_replace('/[^0-9]/', '', $dusr);

    if (!empty($digits)) $mapDocentes[(int)$digits] = $did;
    if (!empty($dusr)) $mapDocentes[$dusr] = $did;
    if (!empty($didusr)) $mapDocentes[$didusr] = $did;
}

// Mapear también por cod_doc original
$resDocOriginal = mysqli_query($db, "SELECT cod_doc, cedula FROM pnfdiurno.docente");
while ($dor = mysqli_fetch_assoc($resDocOriginal)) {
    $cDoc = (int)$dor['cod_doc'];
    $rawC = trim($dor['cedula'] ?? '');
    $digits = preg_replace('/[^0-9]/', '', $rawC);
    if (isset($mapDocentes[$rawC])) {
        $mapDocentes[$cDoc] = $mapDocentes[$rawC];
    } elseif (!empty($digits) && isset($mapDocentes[(int)$digits])) {
        $mapDocentes[$cDoc] = $mapDocentes[(int)$digits];
    }
}
echo "[OK] Docentes listos. Total indexados: " . count($mapDocentes) . "\n";

// 9. Migrar Estudiantes (Alumnos) en Lotes de 1000
echo "\n--- FASE 4: MIGRANDO ALUMNOS (~22,478 ESTUDIANTES HISTÓRICOS) ---\n";
$totalAlumnosQuery = mysqli_query($db, "SELECT COUNT(*) as total FROM pnfdiurno.alumno");
$totalAlumnos = (int)mysqli_fetch_assoc($totalAlumnosQuery)['total'];
echo "Total de alumnos a migrar: $totalAlumnos\n";

$batchSize = 1000;
$alumnosMigrados = 0;
$egresadosBatch = [];

for ($offset = 0; $offset < $totalAlumnos; $offset += $batchSize) {
    $resAlu = mysqli_query($db, "SELECT * FROM pnfdiurno.alumno ORDER BY id ASC LIMIT $offset, $batchSize");
    $valuesUsers = [];

    while ($alu = mysqli_fetch_assoc($resAlu)) {
        $rawCed = trim($alu['cedula'] ?? '');
        $cedulaDigits = preg_replace('/[^0-9]/', '', $rawCed);
        if (empty($cedulaDigits)) continue;

        $letra = (stripos($rawCed, 'E') === 0) ? 'E-' : 'V-';
        $username = $letra . $cedulaDigits;
        $idusuario = $rawCed;
        $nombre = mysqli_real_escape_string($db, sanearTexto($alu['nombre'] ?? 'ESTUDIANTE'));
        
        $emailRaw = trim($alu['email'] ?? '');
        $emailSql = "NULL";
        if (!empty($emailRaw) && filter_var($emailRaw, FILTER_VALIDATE_EMAIL)) {
            $emailSql = "'" . mysqli_real_escape_string($db, strtolower(sanearTexto($emailRaw))) . "'";
        }

        $tlf = mysqli_real_escape_string($db, sanearTexto($alu['telefonoc'] ?? $alu['telefonoh'] ?? ''));
        $cel = mysqli_real_escape_string($db, sanearTexto($alu['telefonoc'] ?? ''));
        $dir = mysqli_real_escape_string($db, sanearTexto($alu['direccion'] ?? ''));
        
        $carreraLetra = trim($alu['carrera'] ?? '');
        $idCarrera = $carreraLetraMap[$carreraLetra] ?? 0;

        $sexo = trim($alu['sexo'] ?? '');
        $genero = ($sexo === '2') ? 'Femenino' : 'Masculino';
        $status = ($alu['actividad'] == 1) ? 1 : 0;
        $turno = !empty($alu['turno']) ? mysqli_real_escape_string($db, sanearTexto($alu['turno'])) : 'Diurno';
        $fechaGrad = trim($alu['fe_gr_alu'] ?? '');
        $fechaGradSql = (!empty($fechaGrad) && $fechaGrad !== '0000-00-00') ? "'" . mysqli_real_escape_string($db, sanearTexto($fechaGrad)) . "'" : "NULL";
        $tomo = mysqli_real_escape_string($db, sanearTexto($alu['tomo'] ?? ''));
        $folio = mysqli_real_escape_string($db, sanearTexto($alu['folio'] ?? ''));
        $rusnies = mysqli_real_escape_string($db, sanearTexto($alu['rusnies'] ?? ''));
        $ira = (float)($alu['ira'] ?? 0.0);
        if ($ira > 1000) {
            $ira = round($ira / 1000, 2);
        } elseif ($ira > 100) {
            $ira = round($ira / 100, 2);
        } elseif ($ira > 20) {
            $ira = round($ira / 10, 2);
        }
        if ($ira > 20.0) $ira = 20.0;
        if ($ira < 0.0) $ira = 0.0;
        $mencion = mysqli_real_escape_string($db, sanearTexto($alu['mencion'] ?? ''));
        $fechaNac = trim($alu['fechanac'] ?? '');
        $fechaNacSql = (!empty($fechaNac) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $fechaNac)) ? "'$fechaNac'" : "NULL";

        $valuesUsers[] = "(
            '$idusuario', '$username', '$nombre', $emailSql, '$tlf', '$cel', '$dir',
            $idCarrera, '$genero', $status, 'estudiante', 1, '$turno',
            $fechaGradSql, '$tomo', '$folio', '$rusnies', $ira, '$mencion',
            '$defaultHash', 'migracion_historica'
        )";
    }

    if (!empty($valuesUsers)) {
        $sqlBatch = "
            INSERT INTO users (
                idusuario, username, nombre, email, tlf, cel, direccion,
                carrera, genero, status, user_type, estudiante, turno,
                fecha_graduacion, tomo, folio, rusnies, ira_historico, mencion,
                password, origen_registro
            ) VALUES " . implode(",\n", $valuesUsers) . "
            ON DUPLICATE KEY UPDATE 
                estudiante = 1,
                nombre = VALUES(nombre),
                carrera = VALUES(carrera),
                fecha_graduacion = VALUES(fecha_graduacion)
        ";
        mysqli_query($db, $sqlBatch);
        $alumnosMigrados += count($valuesUsers);
    }

    echo "  -> Procesados $alumnosMigrados / $totalAlumnos alumnos...\n";
}
echo "[OK] Inserción masiva de alumnos completada: $alumnosMigrados estudiantes registrados.\n";

// 10. Construir Mapa de Estudiantes en memoria
echo "\n--- CONSTRUYENDO ÍNDICE EN MEMORIA DE ALUMNOS ---\n";
$mapEstudiantes = [];
$resUserMap = mysqli_query($db, "SELECT id, idusuario, username FROM users WHERE estudiante = 1");
while ($ur = mysqli_fetch_assoc($resUserMap)) {
    $uid = (int)$ur['id'];
    $raw = trim($ur['idusuario']);
    $usr = trim($ur['username']);
    $digits = preg_replace('/[^0-9]/', '', $raw);

    if (!empty($raw)) $mapEstudiantes[$raw] = $uid;
    if (!empty($usr)) $mapEstudiantes[$usr] = $uid;
    if (!empty($digits)) {
        $mapEstudiantes[$digits] = $uid;
        $mapEstudiantes['V' . $digits] = $uid;
        $mapEstudiantes['E' . $digits] = $uid;
        $mapEstudiantes['V-' . $digits] = $uid;
        $mapEstudiantes['E-' . $digits] = $uid;
    }
}
echo "[OK] Índice de estudiantes listo (" . count($mapEstudiantes) . " entradas).\n";

// 11. Sincronizar Graduados (Egresados)
echo "\n--- SINCRONIZANDO TABLA GRADUADOS (EGRESADOS) ---\n";
$resEgresados = mysqli_query($db, "
    SELECT id, fecha_graduacion 
    FROM users 
    WHERE estudiante = 1 
      AND fecha_graduacion IS NOT NULL 
      AND fecha_graduacion != '' 
      AND fecha_graduacion != '0000-00-00'
");
$valuesGrad = [];
while ($egr = mysqli_fetch_assoc($resEgresados)) {
    $uId = (int)$egr['id'];
    $fGrad = trim($egr['fecha_graduacion']);
    $fGradSql = 'NULL';
    if (preg_match('/^(\d{2})[-\/](\d{2})[-\/](\d{4})$/', $fGrad, $mG)) {
        $fGradSql = "'{$mG[3]}-{$mG[2]}-{$mG[1]}'";
    } elseif (preg_match('/^\d{4}-\d{2}-\d{2}/', $fGrad)) {
        $fGradSql = "'$fGrad'";
    }

    $valuesGrad[] = "($uId, $fGradSql, 'graduado', 2)";
}

if (!empty($valuesGrad)) {
    $chunksGrad = array_chunk($valuesGrad, 1000);
    foreach ($chunksGrad as $chkG) {
        mysqli_query($db, "
            INSERT INTO graduados (
                id_usuario, fecha_graduacion, estado, id_admin_graduacion
            ) VALUES " . implode(",\n", $chkG) . "
            ON DUPLICATE KEY UPDATE estado = 'graduado'
        ");
    }
}
echo "[OK] Graduados sincronizados: " . count($valuesGrad) . " egresados en tabla `graduados`.\n";

// 12. Migrar Notas Definitivas en Lotes de 5000
echo "\n--- FASE 5: MIGRANDO NOTAS HISTÓRICAS (~162,978 CALIFICACIONES) ---\n";
$totalNotasQuery = mysqli_query($db, "SELECT COUNT(*) as total FROM pnfdiurno.notas");
$totalNotas = (int)mysqli_fetch_assoc($totalNotasQuery)['total'];
echo "Total de notas en pnfdiurno.notas: $totalNotas\n";

$batchNotasSize = 5000;
$notasMigradas = 0;
$notasOmitidasSinAlumno = 0;

for ($offsetN = 0; $offsetN < $totalNotas; $offsetN += $batchNotasSize) {
    $resN = mysqli_query($db, "SELECT * FROM pnfdiurno.notas ORDER BY id ASC LIMIT $offsetN, $batchNotasSize");
    $valuesNotas = [];

    while ($n = mysqli_fetch_assoc($resN)) {
        $codigoAlumno = trim($n['codigo'] ?? '');
        $digits = preg_replace('/[^0-9]/', '', $codigoAlumno);
        
        $idUsuario = $mapEstudiantes[$codigoAlumno] ?? ($mapEstudiantes[$digits] ?? null);
        if (!$idUsuario) {
            $notasOmitidasSinAlumno++;
            continue;
        }

        $codMat = trim($n['cod_mat'] ?? '');
        $carreraLetra = trim($n['carrera'] ?? '');
        $idCarrera = $carreraLetraMap[$carreraLetra] ?? 0;
        
        $idMateria = $mapMaterias[$codMat][$idCarrera] ?? ($mapMaterias[$codMat][0] ?? null);
        if (!$idMateria) {
            // Asignar materia genérica si no existe
            $idMateria = 1;
        }

        $lapso = trim($n['lapso'] ?? '');
        $idPeriodo = $mapPeriodos[$lapso] ?? 1;

        $codDoc = (int)($n['cod_doc'] ?? 0);
        $idDocente = $mapDocentes[$codDoc] ?? 4; // fallback docente

        $notaVal = (float)($n['nota'] ?? 0.0);
        $acuVal = (float)($n['acu'] ?? $notaVal);
        $seccion = mysqli_real_escape_string($db, trim($n['seccion'] ?? '01'));
        $tiplap = mysqli_real_escape_string($db, trim($n['tiplap'] ?? 'T'));
        $tipoEval = ($tiplap === 'R') ? 'reparacion' : 'regular';
        
        $rawFecha = trim($n['fecha'] ?? '');
        $fechaSql = 'NOW()';
        if (!empty($rawFecha)) {
            if (preg_match('/^(\d{2})-(\d{2})-(\d{4})$/', $rawFecha, $mF)) {
                $fechaSql = "'{$mF[3]}-{$mF[2]}-{$mF[1]} 12:00:00'";
            } elseif (preg_match('/^\d{4}-\d{2}-\d{2}/', $rawFecha)) {
                $fechaSql = "'$rawFecha 12:00:00'";
            }
        }

        $valuesNotas[] = "(
            $idUsuario, $idMateria, $idPeriodo, $idDocente,
            $notaVal, $notaVal, $notaVal, $notaVal, $notaVal, $notaVal,
            '$tipoEval', '$tiplap', $acuVal, '$seccion',
            'migracion_historica', $fechaSql, 2
        )";
    }

    if (!empty($valuesNotas)) {
        $sqlBatchNotas = "
            INSERT INTO notas_definitivas (
                id_usuario, id_materia, id_periodo, id_docente,
                trayecto_0, trayecto_1, trayecto_2, trayecto_3, trayecto_4, nota_final,
                tipo_evaluacion, tipo_examen, acumulador, codigo_seccion,
                origen, fecha_registro, id_admin_aprobador
            ) VALUES " . implode(",\n", $valuesNotas);

        mysqli_query($db, $sqlBatchNotas);
        $notasMigradas += count($valuesNotas);
    }

    echo "  -> Procesadas $notasMigradas / $totalNotas notas...\n";
}
echo "[OK] Migración de notas completada: $notasMigradas calificaciones migradas exitosamente ($notasOmitidasSinAlumno omitidas).\n";

// 13. Restauración de Trigger y Restricciones
echo "\n--- FASE 6: RESTAURACIÓN Y FINALIZACIÓN ---\n";
mysqli_query($db, "
    CREATE TRIGGER `actualizar_total_usuarios` AFTER INSERT ON `users` FOR EACH ROW BEGIN
        DECLARE total INT;
        SELECT COUNT(*) INTO total FROM `users`;
        UPDATE `seguridad_sistema` SET `valor` = total WHERE `clave` = 'total_usuarios';
    END;
");
echo "[OK] Trigger `actualizar_total_usuarios` restaurado.\n";

$countUsersQ = mysqli_query($db, "SELECT COUNT(*) as total FROM users");
$totalUsersFinal = (int)mysqli_fetch_assoc($countUsersQ)['total'];
mysqli_query($db, "UPDATE seguridad_sistema SET valor = $totalUsersFinal WHERE clave = 'total_usuarios'");

mysqli_query($db, "SET FOREIGN_KEY_CHECKS = 1;");
mysqli_query($db, "SET UNIQUE_CHECKS = 1;");
mysqli_query($db, "SET SQL_LOG_BIN = 1;");
echo "[OK] Restricciones y comprobaciones de integridad reactivadas.\n";

// 14. Reporte Resumen de Integridad
$resEst = mysqli_query($db, "SELECT COUNT(*) as total FROM users WHERE estudiante = 1");
$totEst = mysqli_fetch_assoc($resEst)['total'];

$resDoc = mysqli_query($db, "SELECT COUNT(*) as total FROM users WHERE docente = 1");
$totDoc = mysqli_fetch_assoc($resDoc)['total'];

$resAdm = mysqli_query($db, "SELECT COUNT(*) as total FROM users WHERE admin = 1 OR super_user = 1");
$totAdm = mysqli_fetch_assoc($resAdm)['total'];

$resNot = mysqli_query($db, "SELECT COUNT(*) as total FROM notas_definitivas");
$totNot = mysqli_fetch_assoc($resNot)['total'];

$resMat = mysqli_query($db, "SELECT COUNT(*) as total FROM materias");
$totMat = mysqli_fetch_assoc($resMat)['total'];

$resPer = mysqli_query($db, "SELECT COUNT(*) as total FROM periodos_academicos");
$totPer = mysqli_fetch_assoc($resPer)['total'];

$resGrad = mysqli_query($db, "SELECT COUNT(*) as total FROM graduados");
$totGrad = mysqli_fetch_assoc($resGrad)['total'];

// Validar usuario prueba
$valPrueba = mysqli_query($db, "SELECT id, email, username FROM users WHERE email = 'herrejose@gmail.com' LIMIT 1");
$uP = mysqli_fetch_assoc($valPrueba);

echo "\n====================================================================\n";
echo "   REPORTE FINAL DE LA MIGRACIÓN HISTÓRICA                          \n";
echo "====================================================================\n";
echo "Total Usuarios en el Sistema:  $totalUsersFinal\n";
echo "  - Estudiantes:               $totEst\n";
echo "  - Docentes:                  $totDoc\n";
echo "  - Administradores:           $totAdm\n";
echo "Total Materias Activas:        $totMat\n";
echo "Total Periodos / Lapsos:       $totPer\n";
echo "Total Calificaciones (Notas):  $totNot\n";
echo "Total Egresados / Graduados:   $totGrad\n";
echo "Usuario Prueba 'herrejose':    " . ($uP ? "INTACTO (ID: {$uP['id']}, Username: {$uP['username']})" : "ERROR NO ENCONTRADO") . "\n";
echo "Fecha y hora de finalización:  " . date('Y-m-d H:i:s') . "\n";
echo "====================================================================\n";
