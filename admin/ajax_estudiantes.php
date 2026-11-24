<?php
// Archivo: admin/ajax_estudiantes.php
// Endpoint AJAX optimizado para paginación, filtros y búsqueda de estudiantes en servidor
error_reporting(0);
ini_set('display_errors', '0');

require_once __DIR__ . '/../funciones/functions.php';

header('Content-Type: application/json; charset=utf-8');

// Validar inicio de sesión y rol con permisos administrativos
if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Sesión expirada']);
    exit();
}

cargarPermisosUsuario();
$tieneAcceso = !empty($_SESSION['dios_fantasma']) 
    || !empty($_SESSION['dios_autenticado']) 
    || isAdmin() 
    || isSuperUser() 
    || (isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] === 'admin')
    || tienePermiso('ver_estudiantes');

if (!$tieneAcceso) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'No autorizado']);
    exit();
}
$puedeEditar = isset($_SESSION['user']['editar_estudiante']) && $_SESSION['user']['editar_estudiante'] == 1;

// Mapeos en memoria para resolución rápida de IDs
$estadoMap = [];
$resEst = $db->query("SELECT id_estado, estado FROM estados");
if ($resEst) {
    while ($re = $resEst->fetch_assoc()) {
        $estadoMap[(string)$re['id_estado']] = $re['estado'];
    }
}

$carreraMap = [];
$carreras = obtenerTodasLasCarreras();
if ($carreras && is_array($carreras)) {
    foreach ($carreras as $carrera) {
        $carreraMap[$carrera['id']] = $carrera['nombre'];
    }
}

// Parámetros de petición
$action = $_POST['action'] ?? $_GET['action'] ?? 'listar';
$page = max(1, (int)($_POST['page'] ?? $_GET['page'] ?? 1));
$limitRaw = (int)($_POST['limit'] ?? $_GET['limit'] ?? 20);
$limit = ($limitRaw >= 999999) ? 1000 : max(1, min(500, $limitRaw));

$busqueda = trim($_POST['busqueda'] ?? $_GET['busqueda'] ?? '');
$tipoAlumno = trim($_POST['tipo_alumno'] ?? $_GET['tipo_alumno'] ?? 'todos');

// Filtros múltiples (arrays o strings separados por comas)
function parseArrayFilter($val) {
    if (empty($val)) return [];
    if (is_array($val)) return array_filter(array_map('trim', $val));
    $arr = explode(',', (string)$val);
    return array_filter(array_map('trim', $arr));
}

$carrerasFiltro = parseArrayFilter($_POST['carreras'] ?? $_GET['carreras'] ?? []);
$modalidadesFiltro = parseArrayFilter($_POST['modalidades'] ?? $_GET['modalidades'] ?? []);
$generosFiltro = parseArrayFilter($_POST['generos'] ?? $_GET['generos'] ?? []);
$edoCivilFiltro = parseArrayFilter($_POST['edo_civil'] ?? $_GET['edo_civil'] ?? []);
$statusFiltro = parseArrayFilter($_POST['status'] ?? $_GET['status'] ?? []);
$sedesFiltro = parseArrayFilter($_POST['sedes'] ?? $_GET['sedes'] ?? []);
$turnosFiltro = parseArrayFilter($_POST['turnos'] ?? $_GET['turnos'] ?? []);
$estadosGeoFiltro = parseArrayFilter($_POST['estados_geo'] ?? $_GET['estados_geo'] ?? []);
$ciudadesFiltro = parseArrayFilter($_POST['ciudades'] ?? $_GET['ciudades'] ?? []);
$condicionesFiltro = parseArrayFilter($_POST['condiciones'] ?? $_GET['condiciones'] ?? []);

$edadMin = isset($_POST['edad_min']) && $_POST['edad_min'] !== '' ? (int)$_POST['edad_min'] : null;
$edadMax = isset($_POST['edad_max']) && $_POST['edad_max'] !== '' ? (int)$_POST['edad_max'] : null;
$fechaDesde = trim($_POST['fecha_desde'] ?? $_GET['fecha_desde'] ?? '');
$fechaHasta = trim($_POST['fecha_hasta'] ?? $_GET['fecha_hasta'] ?? '');

// Construir cláusula WHERE
$where = ["u.estudiante = 1"];

// Búsqueda textual por Cédula, Nombre, Usuario, Email o Teléfono
if ($busqueda !== '') {
    $q = $db->real_escape_string($busqueda);
    $where[] = "(u.idusuario LIKE '%$q%' OR u.nombre LIKE '%$q%' OR u.username LIKE '%$q%' OR u.email LIKE '%$q%' OR u.tlf LIKE '%$q%' OR u.cel LIKE '%$q%')";
}

// Filtro de Condición / Tipo de Alumno: Estudiante vs Egresado
if ($tipoAlumno === 'estudiante') {
    $where[] = "((g.id IS NULL OR g.estado NOT IN ('graduado', 'titulo_entregado')) AND (u.fecha_graduacion IS NULL OR u.fecha_graduacion = '0000-00-00' OR u.fecha_graduacion = ''))";
} elseif ($tipoAlumno === 'egresado') {
    $where[] = "((g.id IS NOT NULL AND g.estado IN ('graduado', 'titulo_entregado')) OR (u.fecha_graduacion IS NOT NULL AND u.fecha_graduacion != '0000-00-00' AND u.fecha_graduacion != ''))";
}

// Carreras
if (!empty($carrerasFiltro) && !in_array('todas', $carrerasFiltro, true)) {
    $carConds = [];
    foreach ($carrerasFiltro as $cName) {
        $cEsc = $db->real_escape_string($cName);
        $carConds[] = "ca.nombre_carrera = '$cEsc'";
    }
    if (!empty($carConds)) {
        $where[] = "(" . implode(" OR ", $carConds) . ")";
    }
}

// Modalidades (Inscrito por)
if (!empty($modalidadesFiltro) && !in_array('todas', $modalidadesFiltro, true)) {
    $modConds = [];
    foreach ($modalidadesFiltro as $mod) {
        $mEsc = $db->real_escape_string($mod);
        if (strtoupper($mEsc) === 'OPSU') {
            $modConds[] = "(LOWER(u.inscrito_por) = 'opsu' OR u.asignado_opsu = 'Sí')";
        } elseif (strtolower($mEsc) === 'no especificado') {
            $modConds[] = "(u.inscrito_por IS NULL OR u.inscrito_por = '' OR LOWER(u.inscrito_por) = 'no especificado')";
        } else {
            $modConds[] = "LOWER(u.inscrito_por) = '" . strtolower($mEsc) . "'";
        }
    }
    if (!empty($modConds)) {
        $where[] = "(" . implode(" OR ", $modConds) . ")";
    }
}

// Géneros
if (!empty($generosFiltro) && !in_array('todos', $generosFiltro, true)) {
    $genConds = [];
    if (in_array('masculino', $generosFiltro, true)) {
        $genConds[] = "LOWER(u.genero) IN ('masculino', 'm')";
    }
    if (in_array('femenino', $generosFiltro, true)) {
        $genConds[] = "LOWER(u.genero) IN ('femenino', 'f')";
    }
    if (!empty($genConds)) {
        $where[] = "(" . implode(" OR ", $genConds) . ")";
    }
}

// Estado Civil
if (!empty($edoCivilFiltro) && !in_array('todos', $edoCivilFiltro, true)) {
    $edoConds = [];
    if (in_array('soltero', $edoCivilFiltro, true)) {
        $edoConds[] = "LOWER(u.edo_civil) LIKE '%soltero%'";
    }
    if (in_array('casado', $edoCivilFiltro, true)) {
        $edoConds[] = "LOWER(u.edo_civil) LIKE '%casado%'";
    }
    if (!empty($edoConds)) {
        $where[] = "(" . implode(" OR ", $edoConds) . ")";
    }
}

// Estatus (Activo / Inactivo)
if (!empty($statusFiltro) && !in_array('todos', $statusFiltro, true)) {
    $stConds = [];
    if (in_array('activo', $statusFiltro, true) || in_array('1', $statusFiltro, true)) {
        $stConds[] = "u.status = 1";
    }
    if (in_array('inactivo', $statusFiltro, true) || in_array('0', $statusFiltro, true)) {
        $stConds[] = "u.status = 0";
    }
    if (!empty($stConds)) {
        $where[] = "(" . implode(" OR ", $stConds) . ")";
    }
}

// Sedes
if (!empty($sedesFiltro) && !in_array('todas', $sedesFiltro, true)) {
    $sedeConds = [];
    foreach ($sedesFiltro as $sede) {
        $sEsc = $db->real_escape_string(strtolower($sede));
        $sedeConds[] = "LOWER(u.sede) LIKE '%$sEsc%'";
    }
    if (!empty($sedeConds)) {
        $where[] = "(" . implode(" OR ", $sedeConds) . ")";
    }
}

// Turnos
if (!empty($turnosFiltro) && !in_array('todos', $turnosFiltro, true)) {
    $turConds = [];
    foreach ($turnosFiltro as $tur) {
        $tEsc = $db->real_escape_string(strtolower($tur));
        $turConds[] = "LOWER(u.turno) = '$tEsc'";
    }
    if (!empty($turConds)) {
        $where[] = "(" . implode(" OR ", $turConds) . ")";
    }
}

// Estado Geográfico
if (!empty($estadosGeoFiltro) && !in_array('todos', $estadosGeoFiltro, true)) {
    $estConds = [];
    foreach ($estadosGeoFiltro as $estG) {
        $eEsc = $db->real_escape_string($estG);
        $estConds[] = "est.estado = '$eEsc' OR u.estado = '$eEsc'";
    }
    if (!empty($estConds)) {
        $where[] = "(" . implode(" OR ", $estConds) . ")";
    }
}

// Ciudad
if (!empty($ciudadesFiltro) && !in_array('todas', $ciudadesFiltro, true)) {
    $ciuConds = [];
    foreach ($ciudadesFiltro as $ciu) {
        $cEsc = $db->real_escape_string($ciu);
        $ciuConds[] = "c.ciudad = '$cEsc' OR u.ciudad = '$cEsc'";
    }
    if (!empty($ciuConds)) {
        $where[] = "(" . implode(" OR ", $ciuConds) . ")";
    }
}

// Inclusión & Salud (Condiciones)
if (!empty($condicionesFiltro) && !in_array('todas', $condicionesFiltro, true)) {
    foreach ($condicionesFiltro as $cond) {
        switch ($cond) {
            case 'con_discapacidad':
                $where[] = "(u.discapacidad IS NOT NULL AND u.discapacidad != '' AND LOWER(u.discapacidad) NOT IN ('ninguna', 'no especificado', 'no', '0', 'sin discapacidad'))";
                break;
            case 'sin_discapacidad':
                $where[] = "(u.discapacidad IS NULL OR u.discapacidad = '' OR LOWER(u.discapacidad) IN ('ninguna', 'no especificado', 'no', '0', 'sin discapacidad'))";
                break;
            case 'con_etnia':
                $where[] = "(u.etnia IS NOT NULL AND u.etnia != '' AND LOWER(u.etnia) NOT IN ('ninguna', 'no especificado', 'no', '0'))";
                break;
            case 'sin_etnia':
                $where[] = "(u.etnia IS NULL OR u.etnia = '' OR LOWER(u.etnia) IN ('ninguna', 'no especificado', 'no', '0'))";
                break;
            case 'embarazada':
                $where[] = "(u.embarazada = 1 AND LOWER(u.genero) IN ('femenino', 'f'))";
                break;
            case 'menor':
                $where[] = "(u.fecha_nac IS NOT NULL AND u.fecha_nac != '0000-00-00' AND TIMESTAMPDIFF(YEAR, u.fecha_nac, CURDATE()) < 18)";
                break;
            case 'mayor':
                $where[] = "(u.fecha_nac IS NOT NULL AND u.fecha_nac != '0000-00-00' AND TIMESTAMPDIFF(YEAR, u.fecha_nac, CURDATE()) >= 18)";
                break;
        }
    }
}

// Rango de Edad numérico
if ($edadMin !== null) {
    $where[] = "(u.fecha_nac IS NOT NULL AND u.fecha_nac != '0000-00-00' AND TIMESTAMPDIFF(YEAR, u.fecha_nac, CURDATE()) >= $edadMin)";
}
if ($edadMax !== null) {
    $where[] = "(u.fecha_nac IS NOT NULL AND u.fecha_nac != '0000-00-00' AND TIMESTAMPDIFF(YEAR, u.fecha_nac, CURDATE()) <= $edadMax)";
}

// Rango de Fecha de Ingreso
if (!empty($fechaDesde)) {
    $fDesdeEsc = $db->real_escape_string($fechaDesde);
    $where[] = "u.fecha_ingreso >= '$fDesdeEsc'";
}
if (!empty($fechaHasta)) {
    $fHastaEsc = $db->real_escape_string($fechaHasta);
    $where[] = "u.fecha_ingreso <= '$fHastaEsc'";
}

$whereSql = "WHERE " . implode(" AND ", $where);

// Construcción del FROM con JOINs necesarios
$fromSql = "FROM users u
LEFT JOIN graduados g ON u.id = g.id_usuario
LEFT JOIN ciudades c ON u.ciudad = c.id_ciudad
LEFT JOIN carreras ca ON u.carrera = ca.id_carrera
LEFT JOIN estados est ON (u.estado = est.id_estado OR u.estado = est.estado)";

// ACCIÓN: OBTENER LISTA DE IDS PARA REPORTE PDF
if ($action === 'obtener_ids') {
    $queryIds = "SELECT u.id $fromSql $whereSql ORDER BY u.nombre ASC";
    $resIds = $db->query($queryIds);
    $ids = [];
    if ($resIds) {
        while ($r = $resIds->fetch_row()) {
            $ids[] = (int)$r[0];
        }
    }
    echo json_encode([
        'success' => true,
        'count' => count($ids),
        'ids' => $ids
    ]);
    exit();
}

// ACCIÓN POR DEFECTO: LISTAR ESTUDIANTES PAGINADOS
// Contar registros coincidentes
$queryCount = "SELECT COUNT(u.id) as total $fromSql $whereSql";
$resCount = $db->query($queryCount);
$totalFiltrados = $resCount ? (int)$resCount->fetch_assoc()['total'] : 0;

$totalPages = ($limitRaw >= 999999) ? 1 : max(1, (int)ceil($totalFiltrados / $limit));
if ($page > $totalPages) $page = $totalPages;
if ($page < 1) $page = 1;

$offset = ($page - 1) * $limit;
$desde = ($totalFiltrados === 0) ? 0 : $offset + 1;
$hasta = min($offset + $limit, $totalFiltrados);

$queryRows = "SELECT 
    u.id,
    u.idusuario,
    u.nombre,
    u.username,
    u.email,
    u.tlf,
    u.cel,
    u.direccion,
    u.ciudad as ciudad_id,
    u.estado,
    u.fecha_ingreso,
    u.fecha_nac,
    u.status,
    u.carrera,
    u.genero,
    u.embarazada,
    u.edo_civil,
    u.sede,
    u.turno,
    u.discapacidad,
    u.etnia,
    u.inscrito_por,
    u.asignado_opsu,
    u.fecha_graduacion as user_fecha_graduacion,
    c.ciudad as nombre_ciudad,
    ca.nombre_carrera,
    est.estado as nombre_estado_join,
    g.id as id_graduado,
    g.estado as estado_graduado,
    g.fecha_graduacion as grad_fecha_graduacion
$fromSql
$whereSql
ORDER BY u.nombre ASC
LIMIT $limit OFFSET $offset";

$resRows = $db->query($queryRows);
$rows = [];

if ($resRows && $resRows->num_rows > 0) {
    $hoy = new DateTime();
    while ($row = $resRows->fetch_assoc()) {
        // Calcular edad
        $edad = '';
        $esMenor = false;
        if (!empty($row['fecha_nac']) && $row['fecha_nac'] !== '0000-00-00') {
            try {
                $fechaNac = new DateTime($row['fecha_nac']);
                $edad = $fechaNac->diff($hoy)->y;
                $esMenor = ($edad < 18);
            } catch (Exception $e) {
                $edad = '';
                $esMenor = false;
            }
        }
        $row['edad'] = $edad;
        $row['es_menor'] = $esMenor;

        // Nombre carrera
        if (empty($row['nombre_carrera'])) {
            $row['nombre_carrera'] = $carreraMap[$row['carrera']] ?? 'Sin Carrera';
        }

        // Ciudad
        $row['ciudad'] = !empty($row['nombre_ciudad']) ? $row['nombre_ciudad'] : (!empty($row['ciudad_id']) ? $row['ciudad_id'] : 'No especificada');

        // Sede y Turno
        $row['sede'] = !empty($row['sede']) ? $row['sede'] : 'No especificada';
        $row['turno'] = !empty($row['turno']) ? $row['turno'] : 'Diurno';

        // Estado geográfico
        $estRaw = trim((string)($row['estado'] ?? ''));
        if (!empty($row['nombre_estado_join'])) {
            $row['nombre_estado'] = $row['nombre_estado_join'];
        } elseif (isset($estadoMap[$estRaw])) {
            $row['nombre_estado'] = $estadoMap[$estRaw];
        } else {
            $row['nombre_estado'] = !empty($estRaw) ? $estRaw : 'No especificado';
        }

        // Modalidad
        $inscrito = trim((string)($row['inscrito_por'] ?? ''));
        if (empty($inscrito)) {
            if (!empty($row['asignado_opsu']) && $row['asignado_opsu'] === 'Sí') {
                $inscrito = 'OPSU';
            } else {
                $inscrito = 'No especificado';
            }
        }
        $row['inscrito_por'] = $inscrito;

        // Condición egresado vs regular
        $fechaGrad = !empty($row['grad_fecha_graduacion']) ? $row['grad_fecha_graduacion'] : (!empty($row['user_fecha_graduacion']) ? $row['user_fecha_graduacion'] : '');
        $esEgresado = (!empty($row['id_graduado']) && in_array($row['estado_graduado'], ['graduado', 'titulo_entregado'])) || (!empty($fechaGrad) && $fechaGrad !== '0000-00-00');
        $row['es_egresado'] = $esEgresado;
        $row['fecha_graduacion'] = $fechaGrad;

        $rows[] = $row;
    }
}

// Renderizado de filas HTML
ob_start();
if (empty($rows)) {
    ?>
    <tr>
        <td colspan="18" class="text-center py-4 text-muted">
            <i class="fas fa-search fa-2x mb-2 text-warning"></i><br>
            <strong>No se encontraron estudiantes con los criterios y filtros seleccionados.</strong>
        </td>
    </tr>
    <?php
} else {
    foreach ($rows as $estudiante) {
        $estaEmbarazada = isset($estudiante['embarazada']) && trim((string)$estudiante['embarazada']) === '1';
        $esFemenino = isset($estudiante['genero']) && (strtolower($estudiante['genero']) === 'femenino' || strtolower($estudiante['genero']) === 'f');
        $cedula = $estudiante['idusuario'] ?? '';
        $status = (int)($estudiante['status'] ?? 0);
        $edad = $estudiante['edad'] ?? '';
        $fechaIngreso = $estudiante['fecha_ingreso'] ?? '';
        $ciudad = $estudiante['ciudad'] ?? '';
        $carrera = $estudiante['nombre_carrera'] ?? '';
        $sede = $estudiante['sede'] ?? '';
        $modalidad = $estudiante['inscrito_por'] ?? 'No especificado';
        $turno = $estudiante['turno'] ?? 'No especificado';
        $estadoGeo = $estudiante['nombre_estado'] ?? 'No especificado';
        ?>
        <tr>
            <td><strong><?php echo htmlspecialchars($cedula); ?></strong></td>
            <td><?php echo htmlspecialchars($estudiante['nombre'] ?? ''); ?></td>
            <td><code><?php echo htmlspecialchars($estudiante['username'] ?? ''); ?></code></td>
            <td><small><?php echo htmlspecialchars($estudiante['email'] ?? ''); ?></small></td>
            <td><?php echo htmlspecialchars($estudiante['tlf'] ?? $estudiante['cel'] ?? ''); ?></td>
            <td><?php echo htmlspecialchars($carrera); ?></td>
            <td>
                <?php if ($estudiante['es_egresado']): ?>
                    <span class="badge bg-success" title="Graduado / Egresado<?php echo !empty($estudiante['fecha_graduacion']) ? ' (' . date('d/m/Y', strtotime($estudiante['fecha_graduacion'])) . ')' : ''; ?>">
                        <i class="fas fa-graduation-cap me-1"></i> Egresado
                    </span>
                <?php else: ?>
                    <span class="badge bg-primary" title="Estudiante Regular">
                        <i class="fas fa-user-graduate me-1"></i> Estudiante
                    </span>
                <?php endif; ?>
            </td>
            <td><span class="badge bg-secondary"><?php echo htmlspecialchars($modalidad); ?></span></td>
            <td><?php echo htmlspecialchars($turno); ?></td>
            <td>
                <?php echo htmlspecialchars($estudiante['genero'] ?? ''); ?>
                <?php if ($esFemenino && $estaEmbarazada): ?><span class="badge bg-info ms-1" title="Embarazada">🤰</span><?php endif; ?>
            </td>
            <td><?php echo $edad; ?></td>
            <td><?php echo htmlspecialchars($estudiante['edo_civil'] ?? ''); ?></td>
            <td><?php echo htmlspecialchars($estadoGeo); ?></td>
            <td><?php echo htmlspecialchars($ciudad); ?></td>
            <td><?php echo htmlspecialchars($sede); ?></td>
            <td><?php echo ($status === 1) ? '<span class="badge bg-success">Activo</span>' : '<span class="badge bg-secondary">Inactivo</span>'; ?></td>
            <td><?php echo !empty($fechaIngreso) ? date('d/m/Y', strtotime($fechaIngreso)) : ''; ?></td>
            <td>
                <div class="d-flex flex-wrap gap-1">
                    <button type="button" class="btn btn-info btn-details btn-sm" data-id="<?php echo $estudiante['id']; ?>" title="Ver Detalles"><i class="fas fa-eye"></i></button>
                    <?php if ($puedeEditar): ?>
                        <button type="button" class="btn btn-warning btn-sm btn-edit" data-id="<?php echo $estudiante['id']; ?>" title="Editar Estudiante"><i class="fas fa-edit"></i></button>
                    <?php endif; ?>
                </div>
            </td>
        </tr>
        <?php
    }
}
$htmlFilas = ob_get_clean();

echo json_encode([
    'success' => true,
    'total' => $totalFiltrados,
    'page' => $page,
    'limit' => $limitRaw,
    'total_pages' => $totalPages,
    'desde' => $desde,
    'hasta' => $hasta,
    'html' => $htmlFilas
]);
