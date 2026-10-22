<?php
// .dios/ajax_rps.php - Telemetría en Tiempo Real para MODO DIOS
require_once __DIR__ . '/config.php';

header('Content-Type: application/json; charset=utf-8');

$token_recibido = $_GET['token'] ?? $_POST['token'] ?? '';
$autenticado = (isset($_SESSION['dios_autenticado']) && $_SESSION['dios_autenticado'] === true) ||
               (!empty($_SESSION['dios_ajax_token']) && hash_equals($_SESSION['dios_ajax_token'], (string)$token_recibido));

if (!$autenticado) {
    http_response_code(403);
    echo json_encode(['error' => 'No autorizado']);
    exit;
}

require_once __DIR__ . '/../funciones/functions.php';
require_once __DIR__ . '/../funciones/seguridad.php';

if (!isset($db) || !$db) {
    http_response_code(500);
    echo json_encode(['error' => 'Error de conexión a BD']);
    exit;
}

$seguridad = new Seguridad($db);

// Limpiar RPS antiguos (>10s)
mysqli_query($db, "DELETE FROM seguridad_rps WHERE fecha < DATE_SUB(NOW(), INTERVAL 10 SECOND)");

// RPS actual (últimos 10 segundos)
$res_rps = mysqli_query($db, "SELECT COUNT(*) as total FROM seguridad_rps WHERE fecha > DATE_SUB(NOW(), INTERVAL 10 SECOND)");
$rps_actual = (int)(mysqli_fetch_assoc($res_rps)['total'] ?? 0);

// Intentos hoy
$res_int = mysqli_query($db, "SELECT COUNT(*) as total FROM seguridad_intentos WHERE DATE(fecha) = CURDATE()");
$intentos_hoy = (int)(mysqli_fetch_assoc($res_int)['total'] ?? 0);

// Bloqueos activos
$res_bloq = mysqli_query($db, "SELECT COUNT(*) as total FROM seguridad_bloqueos WHERE activo = 1 AND desbloqueo_en > NOW()");
$bloqueos_activos = (int)(mysqli_fetch_assoc($res_bloq)['total'] ?? 0);

// Tokens inválidos hoy
$res_tok = mysqli_query($db, "SELECT COUNT(*) as total FROM seguridad_tokens_invalidos WHERE DATE(fecha) = CURDATE()");
$tokens_invalidos_hoy = (int)(mysqli_fetch_assoc($res_tok)['total'] ?? 0);

echo json_encode([
    'rps_actual' => $rps_actual,
    'intentos_hoy' => $intentos_hoy,
    'bloqueos_activos' => $bloqueos_activos,
    'tokens_invalidos_hoy' => $tokens_invalidos_hoy,
    'sistema_completo_activo' => $seguridad->sistemaCompletoActivo(),
    'sistema_activo' => $seguridad->sistemaEstaActivo(),
    'modo_mantenimiento' => $seguridad->sistemaEnMantenimiento(),
    'memoria_mb' => round(memory_get_usage(true) / 1048576, 2),
    'timestamp' => date('H:i:s')
]);
?>