<?php
require_once(__DIR__ . '/../../funciones/functions.php');

cargarPermisosUsuario();
// Evitar la redirección en peticiones AJAX: si no está autenticado o no tiene permiso, devolver una opción informativa
if (!isset($_SESSION['user']) || !isset($_SESSION['user']['id'])) {
    echo '<option value="">Sesión expirada. Inicie sesión nuevamente.</option>';
    exit();
}
if (!tienePermiso('secciones')) {
    echo '<option value="">No autorizado para ver secciones</option>';
    exit();
}

$id_carrera = (int)$_POST['id_carrera'];
$id_trayecto = (int)$_POST['id_trayecto'];

// Cargamos todas las secciones activas del mismo trayecto y carrera con cupo disponible.
$secciones = obtenerSeccionesDisponiblesParaRezagados($id_carrera, $id_trayecto, null);
// Registro de depuración temporal
$log = sprintf("[%s] ajax_obtener_secciones: carrera=%d trayecto=%d\n", date('Y-m-d H:i:s'), $id_carrera, $id_trayecto);
file_put_contents(sys_get_temp_dir() . '/secciones_debug.log', $log, FILE_APPEND);

if (empty($secciones)) {
    file_put_contents(sys_get_temp_dir() . '/secciones_debug.log', "No se encontraron secciones\n", FILE_APPEND);
    echo '<option value="">No hay secciones disponibles con cupo</option>';
} else {
    file_put_contents(sys_get_temp_dir() . '/secciones_debug.log', "Se encontraron " . count($secciones) . " secciones\n", FILE_APPEND);
    echo '<option value="">Seleccione una sección...</option>';
    foreach ($secciones as $s) {
        $disponibles = $s['capacidad_maxima'] - $s['inscritos'];
        echo "<option value='{$s['id_seccion']}'>[{$s['codigo_seccion']}] Periodo: {$s['nombre_periodo']} - Cupos: {$disponibles}/{$s['capacidad_maxima']}</option>";
    }
}
?>