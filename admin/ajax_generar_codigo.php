<?php
header('Content-Type: application/json');

include('../funciones/functions.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_carrera = (int)($_POST['id_carrera'] ?? 0);
    $turno = trim($_POST['turno'] ?? '');
    
    if ($id_carrera <= 0 || empty($turno)) {
        echo json_encode(['success' => false, 'message' => 'Debe seleccionar carrera y turno antes de generar el código.']);
        exit();
    }
    
    $codigo = generarCodigoSeccion($id_carrera, $turno);
    
    if ($codigo) {
        echo json_encode(['success' => true, 'codigo' => $codigo]);
    } else {
        echo json_encode(['success' => false, 'message' => 'No hay códigos disponibles para esta carrera y turno']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
}
?>