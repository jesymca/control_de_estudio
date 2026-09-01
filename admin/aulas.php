<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

$titulopag = "Gestión de Aulas";
include('../funciones/functions.php');

cargarPermisosUsuario();
verificarPermiso('aulas');
visita();



// Procesar formularios
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['crear'])) {
        // Crear nueva aula
        $nave = $db->real_escape_string($_POST['nave']);
        $aula = $db->real_escape_string($_POST['aula']);
        
        $query = "INSERT INTO aulas (nave, aula) VALUES ('$nave', '$aula')";
        if ($db->query($query)) {
            $_SESSION['mensaje'] = "Aula creada correctamente";
            $_SESSION['tipo_mensaje'] = "success";
        } else {
            $_SESSION['mensaje'] = "Error al crear aula: " . $db->error;
            $_SESSION['tipo_mensaje'] = "danger";
        }
    } elseif (isset($_POST['actualizar'])) {
        // Actualizar aula existente
        $id = $db->real_escape_string($_POST['id']);
        $nave = $db->real_escape_string($_POST['nave']);
        $aula = $db->real_escape_string($_POST['aula']);
        
        $query = "UPDATE aulas SET nave='$nave', aula='$aula' WHERE id='$id'";
        if ($db->query($query)) {
            $_SESSION['mensaje'] = "Aula actualizada correctamente";
            $_SESSION['tipo_mensaje'] = "success";
        } else {
            $_SESSION['mensaje'] = "Error al actualizar aula: " . $db->error;
            $_SESSION['tipo_mensaje'] = "danger";
        }
    } elseif (isset($_POST['eliminar'])) {
        // Eliminar aula
        $id = $db->real_escape_string($_POST['id']);
        
        $query = "DELETE FROM aulas WHERE id='$id'";
        if ($db->query($query)) {
            $_SESSION['mensaje'] = "Aula eliminada correctamente";
            $_SESSION['tipo_mensaje'] = "success";
        } else {
            $_SESSION['mensaje'] = "Error al eliminar aula: " . $db->error;
            $_SESSION['tipo_mensaje'] = "danger";
        }
    }
    
    header("Location: aulas.php");
    exit();
}

include("includes/head.php");
?>

<div class="container-fluid py-3">
    <h2 class="mb-4">Gestión de Aulas</h2>
    
    <?php if (isset($_SESSION['mensaje'])): ?>
        <div class="alert alert-<?php echo $_SESSION['tipo_mensaje']; ?> alert-dismissible fade show" role="alert">
            <?php echo $_SESSION['mensaje']; ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <?php unset($_SESSION['mensaje'], $_SESSION['tipo_mensaje']); ?>
    <?php endif; ?>
    
    <!-- Botón para agregar nueva aula con modal -->
    <button type="button" class="btn btn-primary mb-3" data-toggle="modal" data-target="#modalCrearAula">
        <i class="fas fa-plus"></i> Agregar Nueva Aula
    </button>
    
    <!-- Lista de aulas -->
    <div class="card">
        <div class="card-header">
            <h5>Listado de Aulas</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="thead-dark">
                        <tr>
                            <th>ID</th>
                            <th>Nave</th>
                            <th>Aula</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query = "SELECT * FROM aulas ORDER BY nave, aula";
                        $result = $db->query($query);
                        
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . $row['id'] . "</td>";
                                echo "<td>" . htmlspecialchars($row['nave']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['aula']) . "</td>";
                                echo "<td>";
                                echo "<button class='btn btn-sm btn-warning mr-2 btn-editar' 
                                      data-id='".$row['id']."'
                                      data-nave='".htmlspecialchars($row['nave'])."'
                                      data-aula='".htmlspecialchars($row['aula'])."'
                                      data-toggle='modal' data-target='#modalEditarAula'>
                                      <i class='fas fa-edit'></i> Editar
                                      </button>";
                                echo "<button class='btn btn-sm btn-danger btn-eliminar' 
                                      data-id='".$row['id']."'
                                      data-toggle='modal' data-target='#modalEliminarAula'>
                                      <i class='fas fa-trash'></i> Eliminar
                                      </button>";
                                echo "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='4' class='text-center'>No hay aulas registradas</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal para crear nueva aula -->
<div class="modal fade" id="modalCrearAula" tabindex="-1" role="dialog" aria-labelledby="modalCrearAulaLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalCrearAulaLabel">Agregar Nueva Aula</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" action="aulas.php">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="nave">Nave:</label>
                        <input type="text" class="form-control" id="nave" name="nave" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="aula">Aula:</label>
                        <input type="text" class="form-control" id="aula" name="aula" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" name="crear">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para editar aula -->
<div class="modal fade" id="modalEditarAula" tabindex="-1" role="dialog" aria-labelledby="modalEditarAulaLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEditarAulaLabel">Editar Aula</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" action="aulas.php">
                <input type="hidden" name="id" id="edit_id">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="edit_nave">Nave:</label>
                        <input type="text" class="form-control" id="edit_nave" name="nave" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="edit_aula">Aula:</label>
                        <input type="text" class="form-control" id="edit_aula" name="aula" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" name="actualizar">Actualizar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para confirmar eliminación -->
<div class="modal fade" id="modalEliminarAula" tabindex="-1" role="dialog" aria-labelledby="modalEliminarAulaLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEliminarAulaLabel">Confirmar Eliminación</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" action="aulas.php">
                <input type="hidden" name="id" id="delete_id">
                <div class="modal-body">
                    ¿Estás seguro de que deseas eliminar esta aula? Esta acción no se puede deshacer.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger" name="eliminar">Eliminar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Script para manejar los modales de edición y eliminación
$(document).ready(function() {
    // Cuando se hace clic en editar, cargar los datos en el modal
    $('.btn-editar').click(function() {
        var id = $(this).data('id');
        var nave = $(this).data('nave');
        var aula = $(this).data('aula');
        
        $('#edit_id').val(id);
        $('#edit_nave').val(nave);
        $('#edit_aula').val(aula);
    });
    
    // Cuando se hace clic en eliminar, establecer el ID en el modal
    $('.btn-eliminar').click(function() {
        var id = $(this).data('id');
        $('#delete_id').val(id);
    });
});
</script>

<?php include("includes/footer.php"); ?>