<?php
require_once __DIR__ . '/functions.php';

if (!isLoggedIn()) {
    header('HTTP/1.1 401 Unauthorized');
    echo json_encode(['error' => 'No autorizado']);
    exit();
}

$user_id = $_SESSION['user']['id'] ?? $_SESSION['user_id'] ?? 0;

header('Content-Type: application/json; charset=utf-8');
echo json_encode([
    'success' => true,
    'mensajes_no_leidos' => contarMensajesNoLeidos($user_id)
]);
?>