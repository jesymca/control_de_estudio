<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

$titulopag = "Gestión de Prelaciones (Prerrequisitos)";
include('../funciones/functions.php');

cargarPermisosUsuario();
verificarPermiso('gestionar_carrera');

// Manejo de formularios
$mensaje = null; $error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['agregar_prelacion'])) {
        $id_carrera = intval($_POST['id_carrera'] ?? 0);
        $id_materia = intval($_POST['id_materia'] ?? 0);
        $id_prereq = intval($_POST['id_prerequisito'] ?? 0);
        $tipo = trim($_POST['tipo'] ?? '');
        if ($id_carrera && $id_materia && $id_prereq) {
            $r = agregarPrelacion($id_carrera, $id_materia, $id_prereq, $tipo ?: null);
            if (!empty($r['success'])) $mensaje = $r['message']; else $error = $r['message'];
        } else {
            $error = 'Seleccione carrera, materia y prerrequisito';
        }
    }
    if (isset($_POST['eliminar_prelacion'])) {
        $id = intval($_POST['id_prelacion'] ?? 0);
        if ($id) {
            $r = eliminarPrelacion($id);
            if (!empty($r['success'])) $mensaje = 'Prelación eliminada'; else $error = $r['message'];
        }
    }
}

// Obtener datos para el formulario
$carreras = function_exists('obtenerCarrerasCompleta') ? obtenerCarrerasCompleta() : obtenerTodasLasCarreras();
$id_carrera_sel = isset($_GET['id_carrera']) ? intval($_GET['id_carrera']) : ($carreras[0]['id_carrera'] ?? 0);

$materias = [];
if ($id_carrera_sel) {
    // obtener materias asignadas a esta carrera
    $materias = obtenerMateriasAsignadas($id_carrera_sel);
}

$prelaciones = $id_carrera_sel ? obtenerPrelacionesPorCarrera($id_carrera_sel) : [];

include('includes/head.php');
?>
<div class="container-fluid">
    <h1 class="mt-4"><?php echo htmlspecialchars($titulopag); ?></h1>

    <?php if ($mensaje): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($mensaje); ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <div class="card mb-4">
        <div class="card-header">Crear Prelación</div>
        <div class="card-body">
            <form method="GET" id="form-select-carrera">
                <div class="form-group">
                    <label>Carrera:</label>
                    <select name="id_carrera" class="form-control" onchange="document.getElementById('form-select-carrera').submit()">
                        <?php foreach ($carreras as $c): ?>
                            <option value="<?php echo intval($c['id_carrera']); ?>" <?php echo ($c['id_carrera']==$id_carrera_sel)?'selected':''; ?>>
                                <?php echo htmlspecialchars($c['nombre_carrera']); ?> <?php echo $c['cod_carrera']? '('.htmlspecialchars($c['cod_carrera']).')' : ''; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </form>

            <form method="POST" class="mt-3">
                <input type="hidden" name="id_carrera" value="<?php echo intval($id_carrera_sel); ?>">
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Materia (la que requiere):</label>
                        <select name="id_materia" class="form-control" required>
                            <?= renderizarOpcionesMateriasPorTrayecto($materias, null, '-- Seleccione Materia --') ?>
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Prerrequisito (materia previa):</label>
                        <select name="id_prerequisito" class="form-control" required>
                            <?= renderizarOpcionesMateriasPorTrayecto($materias, null, '-- Seleccione Prerrequisito --') ?>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label>Tipo (opcional):</label>
                        <input type="text" name="tipo" class="form-control" placeholder="ej: obligatorio, optativo">
                    </div>
                </div>
                <button type="submit" name="agregar_prelacion" class="btn btn-primary">Agregar Prelación</button>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">Prelaciones existentes</div>
        <div class="card-body">
            <?php if (empty($prelaciones)): ?>
                <div class="alert alert-info">No hay prelaciones registradas para esta carrera.</div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-sm">
                        <thead>
                            <tr>
                                <th>Materia</th>
                                <th>Prerrequisito</th>
                                <th>Tipo</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($prelaciones as $p): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($p['cod_materia']) . ' - ' . htmlspecialchars($p['nombre_materia']); ?></td>
                                    <td><?php echo htmlspecialchars($p['cod_prereq']) . ' - ' . htmlspecialchars($p['nombre_prereq']); ?></td>
                                    <td><?php echo htmlspecialchars($p['tipo'] ?? ''); ?></td>
                                    <td>
                                        <form method="POST" style="display:inline;">
                                            <input type="hidden" name="id_prelacion" value="<?php echo intval($p['id']); ?>">
                                            <button type="submit" name="eliminar_prelacion" class="btn btn-sm btn-danger" onclick="return confirm('Eliminar prelación?')">Eliminar</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

</div>

<?php include('includes/footer.php');
