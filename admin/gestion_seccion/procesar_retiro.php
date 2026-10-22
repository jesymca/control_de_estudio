<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
session_start();

require_once(__DIR__ . '/../../funciones/functions.php');

cargarPermisosUsuario();
verificarPermiso('secciones');

$usuario_id = (int)$_POST['id_usuario'];
$seccion_id = (int)$_POST['id_seccion'];

$resultado = retirarEstudiante($db, $seccion_id, $usuario_id);

if ($resultado['success']) $_SESSION['success'] = $resultado['message'];
else $_SESSION['error'] = $resultado['message'];

header("Location: ver_seccion.php?id=".$seccion_id);
exit();
?>