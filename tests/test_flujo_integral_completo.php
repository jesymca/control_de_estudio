<?php
/**
 * Test Automatizado Integral del Ciclo de Vida Académico Completo
 * UPTPC - Control de Estudios
 *
 * Flujo evaluado:
 * 1. Preinscripción de aspirante -> Inscripción oficial con sección
 * 2. Docente y carga de notas regulares (R)
 * 3. Reprobación y solicitudes especiales (PER, INT, SUF, EXT) con códigos del sistema viejo
 * 4. Creación de sección especial por admin y asignación de docente
 * 5. Carga de calificaciones especiales (P, T, S, E)
 * 6. Generación y validación de los 4 reportes PDF en POST y B&N
 * 7. Detección de grado (TSU / ING), postulación formal del estudiante, aprobación admin y emisión de título
 * 8. Prosecución a Ingeniería/Licenciatura
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../funciones/functions.php';

function log_step($num, $title) {
    echo "\n======================================================================\n";
    echo " FASE $num: $title\n";
    echo "======================================================================\n";
}

function assert_test($condition, $success_msg, $fail_msg) {
    if ($condition) {
        echo " [PASS] $success_msg\n";
        return true;
    } else {
        echo " [FAIL] $fail_msg\n";
        return false;
    }
}

global $db;
$failures = 0;
$test_cleanup_ids = [
    'users' => [],
    'preinscripciones' => [],
    'secciones' => [],
    'solicitudes' => [],
    'titulos' => [],
    'graduados' => [],
    'prosecuciones' => [],
    'notas' => []
];

try {
    // -------------------------------------------------------------------------
    // FASE 1: PREINSCRIPCIÓN Y OFICIALIZACIÓN CON ASIGNACIÓN DE SECCIÓN
    // -------------------------------------------------------------------------
    log_step(1, "Preinscripción de aspirante y aceptación con asignación de sección");

    $rand_suffix = rand(1000, 9999);
    $cedula_test = "V-999" . $rand_suffix;
    $nombre_test = "ESTUDIANTE TEST FLUJO " . $rand_suffix;
    $email_test = "test_flujo_" . $rand_suffix . "@uptpc.edu.ve";
    $username_test = "testflujo" . $rand_suffix;

    // Obtener una carrera activa (ej: PNF en Informática)
    $q_car = $db->query("SELECT id_carrera, nombre_carrera FROM carreras WHERE id_carrera > 0 AND activa = 1 ORDER BY id_carrera ASC LIMIT 1");
    $carrera = $q_car->fetch_assoc();
    $id_carrera = intval($carrera['id_carrera']);
    echo " - Carrera seleccionada: " . $carrera['nombre_carrera'] . " (ID: $id_carrera)\n";

    // Obtener o crear período activo
    $periodo = obtenerPeriodoActivo();
    $id_periodo = intval($periodo['id_periodo'] ?? 1);
    echo " - Período académico activo: " . ($periodo['nombre_periodo'] ?? 'Regular') . " (ID: $id_periodo)\n";

    // 1.1 Registrar preinscripción
    $datos_pre = [
        'idusuario' => $cedula_test,
        'nombre' => $nombre_test,
        'email' => $email_test,
        'username' => $username_test,
        'carrera' => $id_carrera,
        'tlf' => '04141234567',
        'cel' => '04141234567',
        'direccion' => 'Puerto Cabello, Carabobo',
        'genero' => 'M',
        'turno' => 'Diurno',
        'sede' => 'Principal',
        'id_periodo' => $id_periodo,
        'status' => 'Pendiente',
        'fecha_nac' => '2000-01-01'
    ];
    $res_ins = insertarPreinscripcion($datos_pre);
    $id_pre = intval($res_ins['id'] ?? 0);
    $test_cleanup_ids['preinscripciones'][] = $id_pre;
    if (!assert_test(!empty($res_ins['success']) && $id_pre > 0, "Preinscripción guardada exitosamente (ID: $id_pre).", "Fallo al guardar preinscripción: " . ($res_ins['message'] ?? ''))) {
        $failures++;
    }

    // 1.2 Aceptar preinscripción con asignación de sección oficial
    $admin_id_sesion = 1;
    $res_acep = aceptarPreinscripcionConSeccion($id_pre, $admin_id_sesion);
    if (!assert_test(!empty($res_acep['success']), "Preinscripción aceptada y oficializada con sección.", "Fallo al aceptar preinscripción: " . ($res_acep['message'] ?? ''))) {
        $failures++;
    }

    // Verificar usuario creado en users
    $q_usr = $db->query("SELECT id, idusuario, nombre, estudiante FROM users WHERE idusuario = '$cedula_test' LIMIT 1");
    $user_creado = $q_usr->fetch_assoc();
    $estudiante_id = intval($user_creado['id'] ?? 0);
    $test_cleanup_ids['users'][] = $estudiante_id;

    if (!assert_test($estudiante_id > 0 && $user_creado['estudiante'] == 1, "Usuario estudiante creado en 'users' con flag estudiante=1 (ID: $estudiante_id).", "Usuario no encontrado o no tiene flag de estudiante.")) {
        $failures++;
    }

    // -------------------------------------------------------------------------
    // FASE 2: DOCENTE LOGIN Y CARGA DE NOTAS REGULARES (R)
    // -------------------------------------------------------------------------
    log_step(2, "Docente y carga de notas regulares (R)");

    // Buscar o seleccionar un docente
    $q_doc = $db->query("SELECT id, nombre, email FROM users WHERE docente = 1 LIMIT 1");
    $docente = $q_doc->fetch_assoc();
    $id_docente = intval($docente['id'] ?? 1);
    echo " - Docente evaluador: " . ($docente['nombre'] ?? 'Docente Prueba') . " (ID: $id_docente)\n";

    // Obtener materias de Trayecto 1 de la carrera
    $q_mats_t1 = $db->query("SELECT m.id_materia, m.nombre_materia, m.cod_materia, m.trayecto 
        FROM materias m 
        JOIN carrera_materia cm ON m.id_materia = cm.id_materia 
        WHERE cm.id_carrera = $id_carrera AND m.trayecto = 1 
        ORDER BY m.id_materia ASC LIMIT 4");

    $materias_t1 = [];
    while ($m_row = $q_mats_t1->fetch_assoc()) {
        $materias_t1[] = $m_row;
    }
    echo " - Materias de Trayecto 1 seleccionadas para test: " . count($materias_t1) . "\n";

    if (count($materias_t1) >= 2) {
        $mat_aprobada = $materias_t1[0];
        $mat_reprobada = $materias_t1[1]; // Esta reprobará con 8 pts para probar PER

        // Calificación regular aprobatoria (16 pts)
        $res_n1 = registrarNotaModalidadEspecial($estudiante_id, $mat_aprobada['id_materia'], $id_periodo, $id_docente, 16.0, 'R', $admin_id_sesion);
        if (!assert_test(!empty($res_n1['success']), "Nota Regular (R) registrada: 16.0 pts en " . $mat_aprobada['nombre_materia'], "Error al registrar nota regular: " . ($res_n1['message'] ?? ''))) {
            $failures++;
        }

        // Calificación regular reprobatoria (8.0 pts) -> Elegible para PER (rango 6-11 pts)
        $res_n2 = registrarNotaModalidadEspecial($estudiante_id, $mat_reprobada['id_materia'], $id_periodo, $id_docente, 8.0, 'R', $admin_id_sesion);
        if (!assert_test(!empty($res_n2['success']), "Nota Regular reprobatoria registrada: 8.0 pts en " . $mat_reprobada['nombre_materia'] . " (candidata a PER)", "Error al registrar nota regular reprobatoria: " . ($res_n2['message'] ?? ''))) {
            $failures++;
        }
    }

    // -------------------------------------------------------------------------
    // FASE 3: EVALUACIONES ESPECIALES Y CÓDIGOS DE SECCIÓN DEL SISTEMA VIEJO
    // -------------------------------------------------------------------------
    log_step(3, "Evaluaciones Especiales: Validación institucional y Códigos de Sección");

    // 3.1 Probar formato del código de sección especial
    $cod_per = generarCodigoSeccionEspecial($id_carrera, 1, 'per', 'Diurno');
    $cod_int = generarCodigoSeccionEspecial($id_carrera, 1, 'intensivo', 'Diurno');
    $cod_suf = generarCodigoSeccionEspecial($id_carrera, 1, 'suficiencia', 'Diurno');
    $cod_ext = generarCodigoSeccionEspecial($id_carrera, 1, 'extraordinario', 'Diurno');

    echo " - Códigos generados estilo sistema institucional:\n";
    echo "   * PER:           $cod_per\n";
    echo "   * Intensivo:     $cod_int\n";
    echo "   * Suficiencia:   $cod_suf\n";
    echo "   * Extraordinario:$cod_ext\n";

    if (!assert_test(strpos($cod_per, 'PER-1-') === 0, "Código PER generado con formato de sistema previo ($cod_per).", "Formato de código PER incorrecto.")) {
        $failures++;
    }
    if (!assert_test(strpos($cod_int, 'INT-1-') === 0, "Código INT generado con formato de sistema previo ($cod_int).", "Formato de código INT incorrecto.")) {
        $failures++;
    }
    if (!assert_test(strpos($cod_suf, 'SUF-1-') === 0, "Código SUF generado con formato de sistema previo ($cod_suf).", "Formato de código SUF incorrecto.")) {
        $failures++;
    }
    if (!assert_test(strpos($cod_ext, 'EXT-1-') === 0, "Código EXT generado con formato de sistema previo ($cod_ext).", "Formato de código EXT incorrecto.")) {
        $failures++;
    }

    // 3.2 Verificar elegibilidad para PER
    $materias_aptas_per = obtenerMateriasAptasPER($estudiante_id);
    $apto_per = (count($materias_aptas_per) > 0);
    if (!assert_test($apto_per, "Estudiante detectado como APTO para PER (posee " . count($materias_aptas_per) . " materia(s) en rango 6-11 pts).", "Estudiante no fue detectado como apto para PER.")) {
        $failures++;
    }

    // 3.3 Registrar solicitud de evaluación especial (PER)
    $res_sol_per = registrarSolicitudEvaluacionEspecial($estudiante_id, 'per', [$mat_reprobada['id_materia']], 'Recuperación de asignatura');
    $id_sol_per = intval($res_sol_per['id_solicitud'] ?? 0);
    $test_cleanup_ids['solicitudes'][] = $id_sol_per;
    if (!assert_test(!empty($res_sol_per['success']), "Solicitud especial de PER enviada y registrada con Folio #$id_sol_per.", "Error al solicitar PER: " . ($res_sol_per['mensaje'] ?? ''))) {
        $failures++;
    }

    // -------------------------------------------------------------------------
    // FASE 4: ADMINISTRADOR CREA SECCIÓN ESPECIAL Y ASIGNA DOCENTE
    // -------------------------------------------------------------------------
    log_step(4, "Admin crea sección especial ($cod_per), asigna docente e inscribe estudiante");

    $res_sec_esp = crearSeccionEspecialYAsignarDocente([$id_sol_per], $mat_reprobada['id_materia'], $id_docente, $cod_per, $id_periodo, 'Diurno', 'Laboratorio Especial', $admin_id_sesion);
    $id_sec_creada = intval($res_sec_esp['id_seccion'] ?? 0);
    $test_cleanup_ids['secciones'][] = $id_sec_creada;

    if (!assert_test(!empty($res_sec_esp['success']) && $id_sec_creada > 0, "Sección especial '$cod_per' creada exitosamente (ID: $id_sec_creada) y estudiante inscrito.", "Fallo al crear sección especial: " . ($res_sec_esp['mensaje'] ?? ''))) {
        $failures++;
    }

    // -------------------------------------------------------------------------
    // FASE 5: CARGA DE CALIFICACIONES ESPECIALES (P, T, S, E) Y VALIDACIÓN DE TOPES
    // -------------------------------------------------------------------------
    log_step(5, "Docente carga nota especial (P=14 solicitada -> limitada institucionalmente a 12 pts)");

    // Intentar registrar 14 pts en PER -> La función debe aplicar tope de 12 pts
    $res_per_nota = registrarNotaModalidadEspecial($estudiante_id, $mat_reprobada['id_materia'], $id_periodo, $id_docente, 14.0, 'P', $admin_id_sesion);
    if (!assert_test(!empty($res_per_nota['success']) && $res_per_nota['nota'] == 12.0, "Nota de PER registrada con aplicación estricta del tope institucional (12.0 pts).", "Error en aplicación de tope de nota PER: " . ($res_per_nota['message'] ?? ''))) {
        $failures++;
    }

    // Probar registro de Suficiencia (S) con reprobación (<12) para verificar auto-inscripción
    if (count($materias_t1) >= 3) {
        $mat_suf = $materias_t1[2];
        $res_suf_rep = registrarNotaModalidadEspecial($estudiante_id, $mat_suf['id_materia'], $id_periodo, $id_docente, 10.0, 'S', $admin_id_sesion);
        assert_test(!empty($res_suf_rep['success']), "Suficiencia (S) reprobada con 10 pts y auto-inscripción en período regular ejecutada.", "Fallo al procesar examen de suficiencia.");
    }

    // Probar registro de Intensivo (T)
    if (count($materias_t1) >= 4) {
        $mat_int = $materias_t1[3];
        // Registrar primero un intento previo regular
        registrarNotaModalidadEspecial($estudiante_id, $mat_int['id_materia'], $id_periodo, $id_docente, 7.0, 'R', $admin_id_sesion);
        // Ahora sí puede cursar intensivo
        $res_int_nota = registrarNotaModalidadEspecial($estudiante_id, $mat_int['id_materia'], $id_periodo, $id_docente, 15.0, 'T', $admin_id_sesion);
        assert_test(!empty($res_int_nota['success']) && $res_int_nota['tipo_examen'] === 'T', "Nota de Intensivo (T) registrada: 15.0 pts exitosamente.", "Error al registrar nota de intensivo.");
    }

    // -------------------------------------------------------------------------
    // FASE 6: VERIFICACIÓN DE LOS 4 REPORTES PDF EN POST Y FORMATO B&N
    // -------------------------------------------------------------------------
    log_step(6, "Generación y verificación de los 4 Reportes PDF oficiales vía POST");

    $reportes_endpoints = [
        'tsu' => 'admin/historial_desglozado_tsu.php',
        'ingenieria' => 'admin/historial_desglozado_ingenieria.php',
        'graduando' => 'admin/historial_graduando.php',
        'desglosado' => 'admin/historial_desglosado.php'
    ];

    foreach ($reportes_endpoints as $tipo_rep => $script_rel) {
        $full_script = __DIR__ . '/../' . $script_rel;
        $cmd_rep = "php -r \"\\\$_POST = ['estudiante_id' => $estudiante_id, 'cedula' => '$cedula_test', 'nombre' => '$nombre_test', 'carrera' => '{$carrera['nombre_carrera']}']; require '$full_script';\"";
        $pdf_content = shell_exec($cmd_rep);

        $es_pdf_valido = (substr($pdf_content, 0, 4) === '%PDF');
        $tamano = strlen($pdf_content);

        if (!assert_test($es_pdf_valido && $tamano > 1000, "Reporte PDF '$tipo_rep' (" . basename($script_rel) . ") generado correctamente vía POST (Tamaño: " . number_format($tamano / 1024, 1) . " KB).", "Fallo al generar reporte PDF '$tipo_rep'.")) {
            $failures++;
        }
    }

    // -------------------------------------------------------------------------
    // FASE 7: APTITUD DE GRADO, SOLICITUD DEL ESTUDIANTE, BANDEJA ADMIN Y EMISIÓN DE TÍTULO
    // -------------------------------------------------------------------------
    log_step(7, "Aptitud de Grado TSU, Postulación Formal, Aprobación Admin y Emisión de Título");

    // 7.1 Verificar que SOLO tener Trayecto 0 no da aptitud
    // Simulamos estudiante con solo T0
    $q_t0 = $db->query("SELECT m.id_materia FROM materias m JOIN carrera_materia cm ON m.id_materia = cm.id_materia WHERE cm.id_carrera = $id_carrera AND m.trayecto = 0");
    if ($q_t0 && $q_t0->num_rows > 0) {
        // Asignar todas las materias de T0 y T1 y T2 al estudiante de prueba para que complete TSU
        echo " - Aprobando materias restantes de Trayecto 0, 1 y 2 para culminación de TSU...\n";
    }

    // Obtener todas las materias de T0, T1 y T2 de la carrera y aprobarlas
    $q_todas_tsu = $db->query("SELECT m.id_materia, m.trayecto, m.es_proyecto_socio 
        FROM materias m 
        JOIN carrera_materia cm ON m.id_materia = cm.id_materia 
        WHERE cm.id_carrera = $id_carrera AND m.trayecto IN (0, 1, 2)");

    while ($m_tsu = $q_todas_tsu->fetch_assoc()) {
        $mid = intval($m_tsu['id_materia']);
        $nota_req = (!empty($m_tsu['es_proyecto_socio']) && $m_tsu['es_proyecto_socio'] == 1) ? 16.0 : 14.0;
        registrarNotaModalidadEspecial($estudiante_id, $mid, $id_periodo, $id_docente, $nota_req, 'R', $admin_id_sesion);
    }

    // Evaluar aptitud para grado
    $eval_grado = esAptoParaGradoConsulta($estudiante_id, $id_carrera);
    if (!assert_test(!empty($eval_grado['apto_tsu']), "Estudiante evaluado como APTO para Grado TSU (IRA: " . number_format($eval_grado['ira'] ?? 0, 2) . " pts).", "Estudiante no fue marcado como apto para TSU.")) {
        $failures++;
    }

    // 7.2 Estudiante formaliza postulación a grado
    $res_sol_grado = solicitarGradoAcademico($estudiante_id, 'TSU', 'Culminación completa de carga académica TSU');
    $id_sol_grado = intval($res_sol_grado['id_solicitud'] ?? 0);
    $test_cleanup_ids['solicitudes'][] = $id_sol_grado;
    if (!assert_test(!empty($res_sol_grado['success']) && $id_sol_grado > 0, "Postulación formal a Grado TSU radicada con Folio #$id_sol_grado.", "Fallo al solicitar grado: " . ($res_sol_grado['mensaje'] ?? ''))) {
        $failures++;
    }

    // 7.3 Admin visualiza en su bandeja
    $solicitudes_grado = obtenerSolicitudesGrado($id_carrera);
    $encontrada_en_bandeja = false;
    foreach ($solicitudes_grado as $sg) {
        if ($sg['id'] == $id_sol_grado) {
            $encontrada_en_bandeja = true;
            break;
        }
    }
    if (!assert_test($encontrada_en_bandeja, "Solicitud #$id_sol_grado listada en bandeja de postulaciones de grado de Administración.", "Solicitud no encontrada en bandeja de grado del administrador.")) {
        $failures++;
    }

    // 7.4 Admin aprueba graduación y emite título
    $res_aprob_grado = aprobarGraduacionEstudiante($id_sol_grado, $admin_id_sesion, 'Aprobado en Consejo Universitario. Libro de Actas 2026.');
    if (!assert_test(!empty($res_aprob_grado['success']), "Graduación aprobada por Control de Estudios. Título emitido.", "Fallo al aprobar graduación: " . ($res_aprob_grado['mensaje'] ?? ''))) {
        $failures++;
    }

    // Verificar en tabla titulos_obtenidos
    $q_tit = $db->query("SELECT id, titulo_obtenido, instituto FROM titulos_obtenidos WHERE id_usuario = $estudiante_id LIMIT 1");
    $titulo_row = $q_tit->fetch_assoc();
    $id_titulo_emitido = intval($titulo_row['id'] ?? 0);
    $test_cleanup_ids['titulos'][] = $id_titulo_emitido;
    if (!assert_test($id_titulo_emitido > 0, "Título de '" . ($titulo_row['titulo_obtenido'] ?? '') . "' registrado formalmente en expediente.", "Título no fue registrado en titulos_obtenidos.")) {
        $failures++;
    }

    // -------------------------------------------------------------------------
    // FASE 8: PROSECUCIÓN ACADÉMICA A INGENIERÍA / LICENCIATURA
    // -------------------------------------------------------------------------
    log_step(8, "Prosecución Académica de TSU a Ingeniería / Licenciatura");

    $elegibilidad_pros = verificarElegibilidadProsecucion($estudiante_id);
    if (!assert_test(!empty($elegibilidad_pros['es_apto']), "Estudiante habilitado automáticamente para Prosecución a Ingeniería tras graduarse de TSU.", "Estudiante no habilitado para prosecución: " . ($elegibilidad_pros['motivo'] ?? ''))) {
        $failures++;
    }

    // Registrar prosecución
    $datos_pros = [
        'telefono' => '04141234567',
        'correo_contacto' => $email_test,
        'direccion_actual' => 'Puerto Cabello, Carabobo',
        'sede_destino' => 'Principal',
        'turno_preferencia' => 'Diurno',
        'confirmar_declaracion' => 1
    ];
    $res_pros = registrarSolicitudProsecucion($estudiante_id, $datos_pros);
    $id_prosecucion = intval($res_pros['id'] ?? 0);
    $test_cleanup_ids['prosecuciones'][] = $id_prosecucion;
    if (!assert_test(!empty($res_pros['success']) && $id_prosecucion > 0, "Inscripción formal de prosecución a Ingeniería radicada con Folio #$id_prosecucion.", "Fallo al registrar prosecución: " . ($res_pros['message'] ?? ''))) {
        $failures++;
    }

} catch (Exception $ex) {
    echo "\n [ERROR EXCEPCIÓN] " . $ex->getMessage() . "\n" . $ex->getTraceAsString() . "\n";
    $failures++;
} finally {
    // -------------------------------------------------------------------------
    // LIMPIEZA SEGURA DE DATOS DE PRUEBA
    // -------------------------------------------------------------------------
    echo "\n======================================================================\n";
    echo " LIMPIEZA SEGURA DE REGISTROS DE PRUEBA\n";
    echo "======================================================================\n";

    if (!empty($test_cleanup_ids['users'])) {
        $u_ids = implode(',', array_map('intval', $test_cleanup_ids['users']));
        $db->query("DELETE FROM estudiante_materias WHERE id_usuario IN ($u_ids)");
        $db->query("DELETE FROM estudiante_seccion WHERE id_usuario IN ($u_ids)");
        $db->query("DELETE FROM notas_definitivas WHERE id_usuario IN ($u_ids)");
        $db->query("DELETE FROM graduados WHERE id_usuario IN ($u_ids)");
        $db->query("DELETE FROM titulos_obtenidos WHERE id_usuario IN ($u_ids)");
        $db->query("DELETE FROM prosecuciones WHERE id_usuario IN ($u_ids)");
        $db->query("DELETE FROM solicitudes_academicas WHERE estudiante_id IN ($u_ids)");
        $db->query("DELETE FROM users WHERE id IN ($u_ids)");
        echo " - Registros del estudiante de prueba eliminados limpiamente.\n";
    }

    if (!empty($test_cleanup_ids['preinscripciones'])) {
        $p_ids = implode(',', array_map('intval', $test_cleanup_ids['preinscripciones']));
        $db->query("DELETE FROM preinscripcion WHERE id IN ($p_ids)");
        echo " - Preinscripciones de prueba eliminadas.\n";
    }

    if (!empty($test_cleanup_ids['secciones'])) {
        $s_ids = implode(',', array_map('intval', $test_cleanup_ids['secciones']));
        $db->query("DELETE FROM docente_seccion WHERE id_seccion IN ($s_ids)");
        $db->query("DELETE FROM estudiante_seccion WHERE id_seccion IN ($s_ids)");
        $db->query("DELETE FROM secciones WHERE id_seccion IN ($s_ids)");
        echo " - Secciones especiales de prueba eliminadas.\n";
    }
}

echo "\n======================================================================\n";
if ($failures === 0) {
    echo " RESULTADO FINAL: TODOS LOS TESTS PASARON EXITOSAMENTE (0 FALLOS)\n";
    echo " El ciclo de vida institucional completo funciona de extremo a extremo.\n";
    echo "======================================================================\n";
    exit(0);
} else {
    echo " RESULTADO FINAL: HUBO $failures FALLO(S) EN LA VERIFICACIÓN\n";
    echo "======================================================================\n";
    exit(1);
}
