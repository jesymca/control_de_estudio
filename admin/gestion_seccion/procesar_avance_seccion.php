<?php
require_once(__DIR__ . '/../../funciones/functions.php');

cargarPermisosUsuario();
verificarPermiso('secciones');
visita();

$id_seccion = (int)$_POST['id_seccion'];
$id_admin = $_SESSION['user']['id'];

$resultado = avanzarSeccionTrayecto($id_seccion, $id_admin);

header('Content-Type: application/json');
echo json_encode($resultado);
?>