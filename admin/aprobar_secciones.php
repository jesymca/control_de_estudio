<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

require_once('../funciones/functions.php');

cargarPermisosUsuario();
verificarPermiso('aprobar_secciones');
visita();

$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $seccionId = (int)($_POST['seccion_id'] ?? 0);
    $action = $_POST['action'] ?? '';

    if ($action === 'aprobar') {
        if (aprobarSeccion($seccionId, $_SESSION['user']['id'])) {
            $success_message = 'Sección aprobada correctamente.';
        } else {
            $error_message = 'Error al aprobar la sección.';
        }
    } elseif ($action === 'rechazar') {
        if (rechazarSeccion($seccionId, $_SESSION['user']['id'])) {
            $success_message = 'Sección rechazada y eliminada correctamente.';
        } else {
            $error_message = 'Error al rechazar y eliminar la sección. Revisa los logs para más detalles.';
        }
    }
}

$seccionesPendientes = obtenerSeccionesPendientes();

$titulopag = 'Aprobar Secciones';
include('includes/head.php');
?>

<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <div>
                <h2 class="mb-0"><i class="fas fa-check-circle me-2"></i>Aprobar Secciones</h2>
                <p class="text-muted mb-0">Revisa y aprueba las secciones creadas por los directores.</p>
            </div>
            <a href="home.php" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Volver al Panel
            </a>
        </div>
    </div>

    <?php if (!empty($success_message)): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($success_message); ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Cerrar">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <?php if (!empty($error_message)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($error_message); ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Cerrar">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-light">
                    <strong>Secciones Pendientes de Aprobación</strong>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($seccionesPendientes)): ?>
                        <div class="p-4 text-center text-muted">
                            No hay secciones pendientes de aprobación.
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-bordered mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Carrera</th>
                                        <th>Turno</th>
                                        <th>Número de Sección</th>
                                        <th>Capacidad</th>
                                        <th>Horario</th>
                                        <th>Creado por</th>
                                        <th>Fecha Creación</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($seccionesPendientes as $seccion): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($seccion['carrera_nombre'] ?? ''); ?></td>
                                            <td><?php echo htmlspecialchars($seccion['turno'] ?? ''); ?></td>
                                            <td><?php echo htmlspecialchars((string)($seccion['numero_seccion'] ?? '')); ?></td>
                                            <td><?php echo htmlspecialchars((string)($seccion['capacidad'] ?? $seccion['capacidad_maxima'] ?? '')); ?></td>
                                            <td><?php echo nl2br(htmlspecialchars($seccion['horario'] ?? '')); ?></td>
                                            <td><?php echo htmlspecialchars($seccion['creador_nombre'] ?? $seccion['created_by'] ?? ''); ?></td>
                                            <td><?php echo htmlspecialchars($seccion['created_at'] ?? ''); ?></td>
                                            <td>
                                                <form method="post" class="d-inline">
                                                    <input type="hidden" name="seccion_id" value="<?php echo (int)$seccion['id_seccion']; ?>">
                                                    <input type="hidden" name="action" value="aprobar">
                                                    <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('¿Aprobar esta sección?');">
                                                        <i class="fas fa-check"></i> Aprobar
                                                    </button>
                                                </form>
                                                <form method="post" class="d-inline">
                                                    <input type="hidden" name="seccion_id" value="<?php echo (int)$seccion['id_seccion']; ?>">
                                                    <input type="hidden" name="action" value="rechazar">
                                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Rechazar esta sección?');">
                                                        <i class="fas fa-times"></i> Rechazar
                                                    </button>
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
    </div>
</div>

<?php include('includes/footer.php'); ?>

</body>
</html>