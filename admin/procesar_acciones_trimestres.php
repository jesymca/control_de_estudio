<?php
require_once('../funciones/functions.php');

header('Content-Type: application/json; charset=utf-8');

if (!isLoggedIn() || !isAdmin()) {
    echo json_encode(['success' => false, 'message' => 'Acceso denegado']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

$accion = $_POST['accion'] ?? '';
$materia_id = (int)($_POST['materia_id'] ?? 0);
$periodo_id = (int)($_POST['periodo_id'] ?? 0);
$mensaje = $_POST['mensaje'] ?? '';

if (!$materia_id || !$periodo_id) {
    echo json_encode(['success' => false, 'message' => 'Parámetros incompletos']);
    exit;
}

global $db;
$admin_id = $_SESSION['user']['id'];

// Acción grupal (todos los estudiantes del docente/materia/periodo)
if (isset($_POST['accion_grupo']) && $_POST['accion_grupo'] === 'true') {
    $docente_id = (int)($_POST['docente_id'] ?? 0);
    if (!$docente_id) {
        echo json_encode(['success' => false, 'message' => 'Docente no especificado']);
        exit;
    }
    
    $nuevo_estado = ($accion === 'aprobar') ? 'aprobada' : 'rechazada';
    
    $update_query = "UPDATE notas_trimestres 
                     SET estado = '$nuevo_estado', 
                         id_admin_aprobador = $admin_id
                     WHERE id_docente = $docente_id 
                     AND id_materia = $materia_id 
                     AND id_periodo = $periodo_id
                     AND estado = 'pendiente'";
    
    if ($db->query($update_query)) {
        $afectadas = $db->affected_rows;
        registrarLogAdmin($admin_id, $accion, $materia_id, $periodo_id, null, $mensaje, true);
        echo json_encode(['success' => true, 'message' => "$afectadas nota(s) $nuevo_estado correctamente"]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al ejecutar acción grupal: ' . $db->error]);
    }
    exit;
}

// Acción individual o por selección
$usuario_ids = $_POST['usuario_ids'] ?? [];
if (empty($usuario_ids)) {
    echo json_encode(['success' => false, 'message' => 'No se especificaron estudiantes']);
    exit;
}

if (!is_array($usuario_ids)) {
    $usuario_ids = [$usuario_ids];
}

$nuevo_estado = ($accion === 'aprobar') ? 'aprobada' : 'rechazada';
$ids_str = implode(',', array_map('intval', $usuario_ids));

$update_query = "UPDATE notas_trimestres 
                 SET estado = '$nuevo_estado', 
                     id_admin_aprobador = $admin_id
                 WHERE id_usuario IN ($ids_str) 
                 AND id_materia = $materia_id 
                 AND id_periodo = $periodo_id
                 AND estado = 'pendiente'";

if ($db->query($update_query)) {
    $afectadas = $db->affected_rows;
    registrarLogAdmin($admin_id, $accion, $materia_id, $periodo_id, $usuario_ids, $mensaje);
    echo json_encode(['success' => true, 'message' => "$afectadas nota(s) $nuevo_estado correctamente"]);
} else {
    echo json_encode(['success' => false, 'message' => 'Error al procesar: ' . $db->error]);
}

function registrarLogAdmin($admin_id, $accion, $materia_id, $periodo_id, $usuario_ids, $mensaje, $es_grupal = false) {
    global $db;
    $tipo = $es_grupal ? 'GRUPAL' : 'INDIVIDUAL';
    $estudiantes = $es_grupal ? 'TODOS' : (is_array($usuario_ids) ? implode(',', $usuario_ids) : $usuario_ids);
    
    $log_query = "INSERT INTO logs_administrativos 
                  (id_admin, accion, materia_id, periodo_id, estudiantes_afectados, mensaje, tipo_accion, fecha) 
                  VALUES 
                  ($admin_id, '$accion', $materia_id, $periodo_id, '$estudiantes', 
                   '" . $db->real_escape_string($mensaje) . "', '$tipo', NOW())";
    $db->query($log_query);
}
?>