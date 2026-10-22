<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
session_start();

$titulopag = "Crear Sección";
require_once(__DIR__ . '/../../funciones/functions.php');

cargarPermisosUsuario();
verificarPermiso('secciones');
visita();

$error_message = '';
$carreras = obtenerTodasLasCarreras();
$datos_selects = obtenerDatosSelects($db);
$trayectos = $datos_selects['trayectos'];
$periodos = $datos_selects['periodos'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_carrera = (int)$_POST['id_carrera'];
    $codigo_seccion = trim($_POST['codigo_seccion']);
    
    if (empty($codigo_seccion)) {
        $turno_seccion = trim($_POST['turno']);
        $codigo_seccion = generarCodigoSeccion($id_carrera, $turno_seccion);
        if (!$codigo_seccion) $error_message = 'No hay códigos disponibles. Configure rangos primero.';
    }
    
    if (empty($error_message)) {
        $datos = [
            'codigo_seccion' => $codigo_seccion,
            'id_carrera' => $id_carrera,
            'id_trayecto' => (int)$_POST['id_trayecto'],
            'id_periodo' => (int)$_POST['id_periodo'],
            'capacidad_maxima' => (int)$_POST['capacidad_maxima'],
            'turno' => trim($_POST['turno']),
            'inicia' => $_POST['inicia']
        ];
        $resultado = crearSeccion($db, $datos);
        if ($resultado['success']) {
            $_SESSION['success'] = $resultado['message'];
            header("Location: gestion_seccion.php"); exit();
        } else $error_message = $resultado['message'];
    }
}

include(__DIR__ . '/../includes/head.php');
?>

<div class="container-fluid py-2">
    <div class="row mb-2">
        <div class="col-12 d-flex justify-content-between">
            <h2 class="h4 mb-0">Crear Nueva Sección</h2>
            <a href="gestion_seccion.php" class="btn btn-secondary btn-sm">← Volver</a>
        </div>
    </div>
    <?php if (!empty($error_message)) echo '<div class="alert alert-danger py-1">'.$error_message.'</div>'; ?>
    <div class="card shadow">
        <div class="card-header py-2"><h6 class="m-0">Datos de la Sección</h6></div>
        <div class="card-body py-2">
            <div class="alert alert-info py-1 small">La sección se activará con <?= MINIMO_ESTUDIANTES ?> estudiantes.</div>
            <form method="POST">
                <div class="form-row">
                    <div class="form-group col-md-2">
                        <label class="small">Código</label>
                        <input type="text" class="form-control form-control-sm" id="codigo_seccion" name="codigo_seccion" readonly>
                    </div>
                    <div class="form-group col-md-3">
                        <label class="small">Carrera</label>
                        <select class="form-control form-control-sm" id="id_carrera" name="id_carrera" required>
                            <option value="">Seleccione...</option>
                            <?php foreach ($carreras as $carrera): ?>
                                <option value="<?= $carrera['id_carrera'] ?>"><?= htmlspecialchars($carrera['nombre_carrera']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group col-md-2">
                        <label class="small">Trayecto</label>
                        <select class="form-control form-control-sm" id="id_trayecto" name="id_trayecto" required>
                            <option value="">Seleccione...</option>
                            <?php foreach ($trayectos as $trayecto): ?>
                                <option value="<?= $trayecto['id_trayecto'] ?>"><?= $trayecto['numero_trayecto'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group col-md-2">
                        <label class="small">Turno</label>
                        <select class="form-control form-control-sm" id="turno" name="turno" required>
                            <option value="">Seleccione...</option>
                            <option value="Diurno">Diurno</option>
                            <option value="Nocturno">Nocturno</option>
                        </select>
                    </div>
                    <div class="form-group col-md-3">
                        <label class="small">Período</label>
                        <select class="form-control form-control-sm" id="id_periodo" name="id_periodo" required>
                            <option value="">Seleccione...</option>
                            <?php foreach ($periodos as $periodo): ?>
                                <option value="<?= $periodo['id_periodo'] ?>"><?= htmlspecialchars($periodo['nombre_periodo']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-3">
                        <label class="small">Capacidad</label>
                        <input type="number" class="form-control form-control-sm" name="capacidad_maxima" value="30" min="<?= MINIMO_ESTUDIANTES ?>" required>
                    </div>
                    <div class="form-group col-md-3">
                        <label class="small">Fecha Inicio</label>
                        <input type="datetime-local" class="form-control form-control-sm" name="inicia" required>
                    </div>
                </div>
                <button type="submit" name="crear_seccion" class="btn btn-primary btn-sm mt-2">Guardar</button>
                <a href="gestion_seccion.php" class="btn btn-secondary btn-sm mt-2">Cancelar</a>
            </form>
        </div>
    </div>
</div>
<script>
$(document).ready(function(){
    function generarCodigo(){
        var id_carrera=$('#id_carrera').val(), turno=$('#turno').val();
        if(id_carrera && turno){
            $.ajax({url:'../ajax_generar_codigo.php',type:'POST',data:{id_carrera:id_carrera,turno:turno},
                success:function(r){var d=JSON.parse(r);if(d.success)$('#codigo_seccion').val(d.codigo);else alert(d.message);}
            });
        } else $('#codigo_seccion').val('');
    }
    $('#id_carrera, #turno').change(generarCodigo);
});
</script>
<?php include(__DIR__ . '/../includes/footer.php'); ?>