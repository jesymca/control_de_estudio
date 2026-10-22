<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

$titulopag = "Códigos de Secciones";
include('../funciones/functions.php');

// CARGAR PERMISOS
cargarPermisosUsuario();
verificarPermiso('admin');

if (!isAdmin()) {
    header('location: ../usuario/home.php');
    exit();
}

// LLAMAR A LA FUNCIÓN DE VISITA
visita();

// Procesar formularios
$mensaje = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['accion'])) {
        $accion = $_POST['accion'];
        
        if ($accion === 'agregar') {
            $id_carrera = (int)$_POST['id_carrera'];
            $codigo_inicio = (int)$_POST['codigo_inicio'];
            $codigo_fin = (int)$_POST['codigo_fin'];
            $descripcion = trim($_POST['descripcion']);
            
            $resultado = insertarCodigoSeccion($id_carrera, $codigo_inicio, $codigo_fin, $descripcion);
            
        } elseif ($accion === 'editar') {
            $id = (int)$_POST['id'];
            $id_carrera = (int)$_POST['id_carrera'];
            $codigo_inicio = (int)$_POST['codigo_inicio'];
            $codigo_fin = (int)$_POST['codigo_fin'];
            $descripcion = trim($_POST['descripcion']);
            
            $resultado = actualizarCodigoSeccion($id, $id_carrera, $codigo_inicio, $codigo_fin, $descripcion);
            
        } elseif ($accion === 'eliminar') {
            $id = (int)$_POST['id'];
            $resultado = eliminarCodigoSeccion($id);
        }
        
        if ($resultado['success']) {
            $_SESSION['mensaje'] = $resultado['message'];
        } else {
            $_SESSION['error'] = $resultado['message'];
        }
        
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}

// Obtener datos
$codigos_secciones = obtenerCodigosSecciones();
$carreras = obtenerCarrerasActivas();

include("includes/head.php");
?>

<div class="container-fluid py-3">
    <h2>Códigos de Secciones</h2>
    
    <?php if (isset($_SESSION['mensaje'])): ?>
        <div class="alert alert-success"><?php echo $_SESSION['mensaje']; unset($_SESSION['mensaje']); ?></div>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>
    
    <!-- Botón para agregar nuevo -->
    <button type="button" class="btn btn-primary mb-3" data-toggle="modal" data-target="#modalAgregar">
        <i class="fas fa-plus"></i> Agregar Código de Sección
    </button>
    
    <!-- Tabla de códigos -->
    <div class="table-responsive">
        <table class="table table-striped table-bordered">
            <thead class="thead-dark">
                <tr>
                    <th>Carrera</th>
                    <th>Rango de Códigos</th>
                    <th>Descripción</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($codigos_secciones)): ?>
                    <tr>
                        <td colspan="4" class="text-center">No hay códigos de secciones registrados.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($codigos_secciones as $codigo): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($codigo['nombre_carrera']); ?></td>
                            <td><?php echo $codigo['codigo_inicio'] . ' - ' . $codigo['codigo_fin']; ?></td>
                            <td><?php echo htmlspecialchars($codigo['descripcion'] ?: ''); ?></td>
                            <td>
                                <button type="button" class="btn btn-sm btn-warning" data-toggle="modal" data-target="#modalEditar" 
                                        onclick="editarCodigo(<?php echo $codigo['id']; ?>, <?php echo $codigo['id_carrera']; ?>, <?php echo $codigo['codigo_inicio']; ?>, <?php echo $codigo['codigo_fin']; ?>, '<?php echo addslashes($codigo['descripcion']); ?>')">
                                    <i class="fas fa-edit"></i> Editar
                                </button>
                                <button type="button" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#modalEliminar" 
                                        onclick="eliminarCodigo(<?php echo $codigo['id']; ?>)">
                                    <i class="fas fa-trash"></i> Eliminar
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Agregar -->
<div class="modal fade" id="modalAgregar" tabindex="-1" role="dialog" aria-labelledby="modalAgregarLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalAgregarLabel">Agregar Código de Sección</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="post" action="">
                <div class="modal-body">
                    <input type="hidden" name="accion" value="agregar">
                    
                    <div class="form-group">
                        <label for="id_carrera_agregar">Carrera:</label>
                        <select class="form-control" id="id_carrera_agregar" name="id_carrera" required>
                            <option value="">Seleccione una carrera</option>
                            <?php foreach ($carreras as $carrera): ?>
                                <option value="<?php echo $carrera['id_carrera']; ?>"><?php echo htmlspecialchars($carrera['nombre_carrera']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="codigo_inicio_agregar">Código Inicio:</label>
                        <input type="number" class="form-control" id="codigo_inicio_agregar" name="codigo_inicio" min="1" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="codigo_fin_agregar">Código Fin:</label>
                        <input type="number" class="form-control" id="codigo_fin_agregar" name="codigo_fin" min="1" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="descripcion_agregar">Descripción:</label>
                        <input type="text" class="form-control" id="descripcion_agregar" name="descripcion">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Editar -->
<div class="modal fade" id="modalEditar" tabindex="-1" role="dialog" aria-labelledby="modalEditarLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEditarLabel">Editar Código de Sección</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="post" action="">
                <div class="modal-body">
                    <input type="hidden" name="accion" value="editar">
                    <input type="hidden" id="id_editar" name="id">
                    
                    <div class="form-group">
                        <label for="id_carrera_editar">Carrera:</label>
                        <select class="form-control" id="id_carrera_editar" name="id_carrera" required>
                            <option value="">Seleccione una carrera</option>
                            <?php foreach ($carreras as $carrera): ?>
                                <option value="<?php echo $carrera['id_carrera']; ?>"><?php echo htmlspecialchars($carrera['nombre_carrera']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="codigo_inicio_editar">Código Inicio:</label>
                        <input type="number" class="form-control" id="codigo_inicio_editar" name="codigo_inicio" min="1" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="codigo_fin_editar">Código Fin:</label>
                        <input type="number" class="form-control" id="codigo_fin_editar" name="codigo_fin" min="1" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="descripcion_editar">Descripción:</label>
                        <input type="text" class="form-control" id="descripcion_editar" name="descripcion">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Actualizar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Eliminar -->
<div class="modal fade" id="modalEliminar" tabindex="-1" role="dialog" aria-labelledby="modalEliminarLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEliminarLabel">Eliminar Código de Sección</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="post" action="">
                <div class="modal-body">
                    <input type="hidden" name="accion" value="eliminar">
                    <input type="hidden" id="id_eliminar" name="id">
                    <p>¿Está seguro de que desea eliminar este código de sección?</p>
                    <p class="text-danger">Esta acción no se puede deshacer.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Eliminar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editarCodigo(id, id_carrera, codigo_inicio, codigo_fin, descripcion) {
    document.getElementById('id_editar').value = id;
    document.getElementById('id_carrera_editar').value = id_carrera;
    document.getElementById('codigo_inicio_editar').value = codigo_inicio;
    document.getElementById('codigo_fin_editar').value = codigo_fin;
    document.getElementById('descripcion_editar').value = descripcion;
}

function eliminarCodigo(id) {
    document.getElementById('id_eliminar').value = id;
}
</script>

<?php include("includes/footer.php"); ?>