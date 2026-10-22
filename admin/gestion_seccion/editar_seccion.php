<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
session_start();

$titulopag = "Editar Sección";
require_once(__DIR__ . '/../../funciones/functions.php');

cargarPermisosUsuario();
verificarPermiso('secciones');
visita();

$id_seccion = (int)$_GET['id'] ?? 0;
if (!$id_seccion) {
    $_SESSION['error'] = "ID de sección no válido";
    header("Location: gestion_seccion.php");
    exit();
}

// Obtener datos actuales de la sección
$query_seccion = "SELECT * FROM secciones WHERE id_seccion = $id_seccion";
$result_seccion = $db->query($query_seccion);
$seccion = $result_seccion->fetch_assoc();

if (!$seccion) {
    $_SESSION['error'] = "Sección no encontrada";
    header("Location: gestion_seccion.php");
    exit();
}

// Obtener carreras
$query_carreras = "SELECT id_carrera, nombre_carrera FROM carreras ORDER BY nombre_carrera ASC";
$result_carreras = $db->query($query_carreras);
$carreras = [];
while ($row = $result_carreras->fetch_assoc()) {
    $carreras[] = $row;
}

// Obtener trayectos
$query_trayectos = "SELECT id_trayecto, numero_trayecto, nombre_trayecto FROM trayectos ORDER BY numero_trayecto ASC";
$result_trayectos = $db->query($query_trayectos);
$trayectos = [];
while ($row = $result_trayectos->fetch_assoc()) {
    $trayectos[] = $row;
}

// Obtener períodos
$query_periodos = "SELECT id_periodo, nombre_periodo FROM periodos_academicos ORDER BY id_periodo DESC";
$result_periodos = $db->query($query_periodos);
$periodos = [];
while ($row = $result_periodos->fetch_assoc()) {
    $periodos[] = $row;
}

$error_message = '';
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $codigo_seccion = trim($_POST['codigo_seccion'] ?? '');
    $id_carrera = (int)($_POST['id_carrera'] ?? 0);
    $id_trayecto = (int)($_POST['id_trayecto'] ?? 0);
    $turno = trim($_POST['turno'] ?? '');
    $id_periodo = (int)($_POST['id_periodo'] ?? 0);
    $capacidad_maxima = (int)($_POST['capacidad_maxima'] ?? 0);
    $inicia = $_POST['inicia'] ?? '';
    $status = trim($_POST['status'] ?? '');
    
    if (empty($codigo_seccion)) {
        $error_message = "Debe ingresar un código de sección";
    } elseif ($id_carrera <= 0) {
        $error_message = "Debe seleccionar una carrera";
    } elseif ($id_trayecto <= 0) {
        $error_message = "Debe seleccionar un trayecto";
    } elseif (empty($turno)) {
        $error_message = "Debe seleccionar un turno";
    } elseif ($id_periodo <= 0) {
        $error_message = "Debe seleccionar un período";
    } elseif ($capacidad_maxima < 1) {
        $error_message = "La capacidad máxima debe ser al menos 1";
    } elseif (empty($inicia)) {
        $error_message = "Debe seleccionar una fecha de inicio";
    } elseif (empty($status)) {
        $error_message = "Debe seleccionar un estado";
    } else {
        // Verificar si el código ya existe en otra sección del MISMO período
        $check_query = "SELECT id_seccion FROM secciones 
                        WHERE codigo_seccion = '$codigo_seccion' 
                        AND id_periodo = $id_periodo
                        AND id_seccion != $id_seccion";
        $check_result = $db->query($check_query);
        
        if ($check_result && $check_result->num_rows > 0) {
            $error_message = "El código de sección '$codigo_seccion' ya existe en el período seleccionado";
        } else {
            // Actualizar sección
            $update_query = "UPDATE secciones SET 
                                codigo_seccion = '$codigo_seccion',
                                id_carrera = $id_carrera,
                                id_trayecto = $id_trayecto,
                                turno = '$turno',
                                id_periodo = $id_periodo,
                                capacidad_maxima = $capacidad_maxima,
                                inicia = '$inicia',
                                status = '$status'
                             WHERE id_seccion = $id_seccion";
            
            if ($db->query($update_query)) {
                $success_message = "Sección actualizada correctamente";
                // Recargar datos de la sección
                $query_seccion = "SELECT * FROM secciones WHERE id_seccion = $id_seccion";
                $result_seccion = $db->query($query_seccion);
                $seccion = $result_seccion->fetch_assoc();
            } else {
                $error_message = "Error al actualizar: " . $db->error;
            }
        }
    }
}

include(__DIR__ . '/../includes/head.php');
?>

<div class="container-fluid py-2">
    <div class="row mb-2">
        <div class="col-12 d-flex justify-content-between">
            <h2 class="h4 mb-0">Editar Sección</h2>
            <a href="gestion_seccion.php" class="btn btn-secondary btn-sm">← Volver</a>
        </div>
    </div>
    
    <?php if (!empty($success_message)): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($success_message) ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>
    
    <?php if (!empty($error_message)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($error_message) ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>
    
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h6 class="m-0">Datos de la Sección</h6>
        </div>
        <div class="card-body">
            <form method="POST">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Código de Sección <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="codigo_seccion" 
                               value="<?= htmlspecialchars($seccion['codigo_seccion']) ?>" required>
                        <small class="text-muted">Puede modificar el código. Debe ser único dentro del mismo período.</small>
                    </div>
                    
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Carrera <span class="text-danger">*</span></label>
                        <select class="form-control" name="id_carrera" required>
                            <option value="">Seleccione una carrera</option>
                            <?php foreach ($carreras as $carrera): ?>
                                <option value="<?= $carrera['id_carrera'] ?>" 
                                    <?= ($seccion['id_carrera'] == $carrera['id_carrera']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($carrera['nombre_carrera']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="col-md-2 mb-3">
                        <label class="form-label">Trayecto <span class="text-danger">*</span></label>
                        <select class="form-control" name="id_trayecto" required>
                            <option value="">Seleccione un trayecto</option>
                            <?php foreach ($trayectos as $trayecto): ?>
                                <option value="<?= $trayecto['id_trayecto'] ?>" 
                                    <?= ($seccion['id_trayecto'] == $trayecto['id_trayecto']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($trayecto['nombre_trayecto']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="col-md-2 mb-3">
                        <label class="form-label">Turno <span class="text-danger">*</span></label>
                        <select class="form-control" name="turno" required>
                            <option value="Diurno" <?= ($seccion['turno'] == 'Diurno') ? 'selected' : '' ?>>Diurno</option>
                            <option value="Nocturno" <?= ($seccion['turno'] == 'Nocturno') ? 'selected' : '' ?>>Nocturno</option>
                        </select>
                    </div>
                    
                    <div class="col-md-2 mb-3">
                        <label class="form-label">Período <span class="text-danger">*</span></label>
                        <select class="form-control" name="id_periodo" required>
                            <option value="">Seleccione un período</option>
                            <?php foreach ($periodos as $periodo): ?>
                                <option value="<?= $periodo['id_periodo'] ?>" 
                                    <?= ($seccion['id_periodo'] == $periodo['id_periodo']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($periodo['nombre_periodo']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Capacidad Máxima <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="capacidad_maxima" 
                               value="<?= $seccion['capacidad_maxima'] ?>" min="1" max="50" required>
                    </div>
                    
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Fecha de Inicio <span class="text-danger">*</span></label>
                        <input type="datetime-local" class="form-control" name="inicia" 
                               value="<?= date('Y-m-d\TH:i', strtotime($seccion['inicia'])) ?>" required>
                    </div>
                    
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Estado <span class="text-danger">*</span></label>
                        <select class="form-control" name="status" required>
                            <option value="">Seleccione un estado</option>
                            <option value="Pendiente" <?= ($seccion['status'] == 'Pendiente') ? 'selected' : '' ?>>Pendiente</option>
                            <option value="Aprobada" <?= ($seccion['status'] == 'Aprobada') ? 'selected' : '' ?>>Aprobada</option>
                            <option value="Rechazada" <?= ($seccion['status'] == 'Rechazada') ? 'selected' : '' ?>>Rechazada</option>
                        </select>
                        <small class="text-muted">Estado actual: <?= htmlspecialchars($seccion['status']) ?></small>
                    </div>
                </div>
                
                <div class="row mt-3">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Guardar Cambios
                        </button>
                        <a href="gestion_seccion.php" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancelar
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.querySelector('form').addEventListener('submit', function(e) {
    const codigo = document.querySelector('input[name="codigo_seccion"]').value.trim();
    if (!codigo) {
        e.preventDefault();
        alert('Debe ingresar un código de sección');
        return false;
    }
    
    const capacidad = document.querySelector('input[name="capacidad_maxima"]').value;
    if (capacidad < 1) {
        e.preventDefault();
        alert('La capacidad máxima debe ser al menos 1');
        return false;
    }
    
    const fechaInicio = document.querySelector('input[name="inicia"]').value;
    if (!fechaInicio) {
        e.preventDefault();
        alert('Debe seleccionar una fecha de inicio');
        return false;
    }
    
    const status = document.querySelector('select[name="status"]').value;
    if (!status) {
        e.preventDefault();
        alert('Debe seleccionar un estado');
        return false;
    }
    
    return true;
});
</script>

<?php include(__DIR__ . '/../includes/footer.php'); ?>