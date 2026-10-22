<?php
require_once(__DIR__ . '/../../funciones/functions.php');

cargarPermisosUsuario();
verificarPermiso('secciones');
visita();

$id_usuario = (int)$_POST['id_usuario'];
$id_seccion_origen = (int)$_POST['id_seccion_origen'];
$id_seccion_destino = (int)$_POST['id_seccion_destino'];
$id_admin = $_SESSION['user']['id'];

$resultado = moverEstudianteAOtraSeccion($id_usuario, $id_seccion_origen, $id_seccion_destino, $id_admin);

header('Content-Type: application/json');
echo json_encode($resultado);
?>