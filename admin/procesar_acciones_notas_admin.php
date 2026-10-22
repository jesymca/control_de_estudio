<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once('../funciones/functions.php');

header('Content-Type: application/json; charset=utf-8');

if (!isLoggedIn() || !isAdmin()) {
    echo json_encode(['success' => false, 'message' => 'Acceso denegado']);
    exit;
}

$accion = $_POST['accion'] ?? '';
$materia_id = (int)($_POST['materia_id'] ?? 0);
$periodo_id = (int)($_POST['periodo_id'] ?? 0);
$mensaje_admin = $_POST['mensaje'] ?? '';
$admin_id = $_SESSION['user']['id'];

if (!$materia_id || !$periodo_id) {
    echo json_encode(['success' => false, 'message' => 'Parámetros incompletos']);
    exit;
}

global $db;

// Obtener información de la materia para el título
$info_query = "SELECT m.nombre_materia, pa.nombre_periodo 
               FROM materias m 
               CROSS JOIN periodos_academicos pa 
               WHERE m.id_materia = $materia_id AND pa.id_periodo = $periodo_id";
$info_result = $db->query($info_query);
$info = $info_result->fetch_assoc();
$nombre_materia = $info['nombre_materia'] ?? 'Materia';
$nombre_periodo = $info['nombre_periodo'] ?? 'Periodo';

// Acción grupal
if (isset($_POST['accion_grupo']) && $_POST['accion_grupo'] === 'true') {
    $docente_id = (int)($_POST['docente_id'] ?? 0);
    if (!$docente_id) {
        echo json_encode(['success' => false, 'message' => 'Docente no especificado']);
        exit;
    }
    
    $nuevo_estado = ($accion === 'aprobar') ? 'aprobada' : 'rechazada';
    $accion_texto = ($accion === 'aprobar') ? 'APROBADAS' : 'RECHAZADAS';
    $color = ($accion === 'aprobar') ? '✅' : '❌';
    
    $query_update = "UPDATE notas_trimestres 
                     SET estado = '$nuevo_estado', 
                         id_admin_aprobador = $admin_id
                     WHERE id_docente = $docente_id 
                     AND id_materia = $materia_id 
                     AND id_periodo = $periodo_id
                     AND estado = 'en_revision'";
    
    if ($db->query($query_update)) {
        $afectadas = $db->affected_rows;
        
        // Título del mensaje
        $titulo = "$color Notas $accion_texto - $nombre_materia ($nombre_periodo)";
        
        // Insertar mensaje automáticamente
        $stmt = $db->prepare("INSERT INTO mensajeria (id_usuario_remitente, id_usuario_destinatario, titulo, mensaje, fecha_envio, leido) VALUES (?, ?, ?, ?, NOW(), 0)");
        $stmt->bind_param("iiss", $admin_id, $docente_id, $titulo, $mensaje_admin);
        $stmt->execute();
        $stmt->close();
        
        echo json_encode(['success' => true, 'message' => "$afectadas nota(s) $nuevo_estado correctamente. Mensaje enviado al docente."]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error DB: ' . $db->error]);
    }
    exit;
}

// Acción individual o por selección
$notas_ids = $_POST['notas_ids'] ?? [];
if (empty($notas_ids)) {
    echo json_encode(['success' => false, 'message' => 'No se especificaron notas']);
    exit;
}

if (!is_array($notas_ids)) {
    $notas_ids = [$notas_ids];
}

// Obtener el docente_id
$primera_nota = $notas_ids[0];
$query_docente = "SELECT id_docente FROM notas_trimestres WHERE id = $primera_nota";
$result_docente = $db->query($query_docente);
$docente_data = $result_docente->fetch_assoc();
$docente_id = $docente_data['id_docente'];

$nuevo_estado = ($accion === 'aprobar') ? 'aprobada' : 'rechazada';
$accion_texto = ($accion === 'aprobar') ? 'APROBADAS' : 'RECHAZADAS';
$color = ($accion === 'aprobar') ? '✅' : '❌';
$ids_str = implode(',', array_map('intval', $notas_ids));

$query_update = "UPDATE notas_trimestres 
                 SET estado = '$nuevo_estado', 
                     id_admin_aprobador = $admin_id
                 WHERE id IN ($ids_str) 
                 AND estado = 'en_revision'";

if ($db->query($query_update)) {
    $afectadas = $db->affected_rows;
    
    // Título del mensaje
    $titulo = "$color Notas $accion_texto - $nombre_materia ($nombre_periodo)";
    
    // Insertar mensaje automáticamente
    $stmt = $db->prepare("INSERT INTO mensajeria (id_usuario_remitente, id_usuario_destinatario, titulo, mensaje, fecha_envio, leido) VALUES (?, ?, ?, ?, NOW(), 0)");
    $stmt->bind_param("iiss", $admin_id, $docente_id, $titulo, $mensaje_admin);
    $stmt->execute();
    $stmt->close();
    
    echo json_encode(['success' => true, 'message' => "$afectadas nota(s) $nuevo_estado correctamente. Mensaje enviado al docente."]);
} else {
    echo json_encode(['success' => false, 'message' => 'Error DB: ' . $db->error]);
}
?>