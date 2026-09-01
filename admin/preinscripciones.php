<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

require_once('../funciones/functions.php');

cargarPermisosUsuario();
verificarPermiso('preinscripciones');
visita();

$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['aceptar_id'])) {
        $resultado = aceptarPreinscripcion((int)$_POST['aceptar_id'], $_SESSION['user']['id']);
        if ($resultado['success']) {
            $success_message = $resultado['message'];
        } else {
            $error_message = $resultado['message'];
        }
    }
    if (!empty($_POST['rechazar_id'])) {
        $motivo = trim($_POST['motivo'] ?? '');
        $resultado = rechazarPreinscripcion((int)$_POST['rechazar_id'], $_SESSION['user']['id'], $motivo);
        if ($resultado['success']) {
            $success_message = $resultado['message'];
        } else {
            $error_message = $resultado['message'];
        }
    }
}

$busqueda = trim($_GET['busqueda'] ?? '');
$preinscripciones = obtenerPreinscripcionesPendientes($busqueda);
$carreras = obtenerTodasLasCarreras();
$carreraMap = [];
foreach ($carreras as $carrera) {
    $carreraMap[$carrera['id']] = $carrera['nombre'];
}

$titulopag = 'Preinscripciones pendientes';
include('includes/head.php');
?>

<div class="container-fluid py-4">
    <div class="row mb-3">
        <div class="col-12 col-md-8">
            <h2 class="mb-4"><i class="fas fa-file-signature me-2"></i> Preinscripciones Pendientes</h2>
        </div>
        <div class="col-12 col-md-4">
            <form method="get" action="preinscripciones.php" class="mb-3">
                <div class="input-group">
                    <input id="searchInput" type="search" name="busqueda" class="form-control" placeholder="Buscar por cédula, nombre, email, teléfono..." value="<?php echo htmlspecialchars($busqueda); ?>">
                    <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i> Buscar</button>
                    <a href="preinscripciones.php" class="btn btn-secondary"><i class="fas fa-times"></i> Limpiar</a>
                </div>
            </form>
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

    <?php if (empty($preinscripciones)): ?>
        <div class="alert alert-info">No hay preinscripciones pendientes por revisar.</div>
    <?php else: ?>
        <div class="table-responsive">
            <table id="preinscripcionesTable" class="table table-bordered table-hover align-middle">
                <thead class="thead-light">
                    <tr>
                        <th>ID</th>
                        <th>Cédula</th>
                        <th>Nombre</th>
                        <th>Carrera</th>
                        <th>Turno</th>
                        <th>Correo</th>
                        <th>Teléfono</th>
                        <th>Fecha Solicitud</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($preinscripciones as $pre): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($pre['id']); ?></td>
                            <td><?php echo htmlspecialchars($pre['idusuario']); ?></td>
                            <td><?php echo htmlspecialchars($pre['nombre']); ?></td>
                            <td><?php echo htmlspecialchars($carreraMap[$pre['carrera']] ?? 'No especificada'); ?></td>
                            <td><?php echo htmlspecialchars($pre['turno'] ?? 'No especificado'); ?></td>
                            <td><?php echo htmlspecialchars($pre['email']); ?></td>
                            <td><?php echo htmlspecialchars($pre['tlf']); ?></td>
                            <td><?php echo htmlspecialchars($pre['fecha_ingreso']); ?></td>
                            <td>
                                <a href="preinscripcion_detalle.php?id=<?php echo (int)$pre['id']; ?>" class="btn btn-sm btn-info mb-1">
                                    <i class="fas fa-eye"></i> Ver planilla
                                </a>
                                
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchInput');
        const rows = document.querySelectorAll('#preinscripcionesTable tbody tr');

        if (!searchInput || !rows.length) {
            return;
        }

        searchInput.addEventListener('input', function() {
            const term = this.value.toLowerCase().trim();

            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(term) ? '' : 'none';
            });
        });
    });
</script>

<?php include('includes/footer.php'); ?>
