<?php 
require_once(__DIR__.'/../../funciones/functions.php');

// Obtener lista de carreras con manejo de errores
try {
    $carreras = obtenerListaCompletaCarreras();
    if ($carreras === false) {
        throw new Exception("Error al obtener la lista de carreras");
    }
} catch (Exception $e) {
    error_log($e->getMessage());
    $carreras = []; // Array vacío para evitar errores en la vista
    echo '<div class="alert alert-danger">Error al cargar los datos. Por favor intente nuevamente.</div>';
}
?>

<div class="table-responsive">
    <table class="table table-striped table-hover">
        <thead class="thead-dark">
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Código</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($carreras)): ?>
                <?php foreach ($carreras as $carrera): ?>
                <tr>
                    <td><?= htmlspecialchars($carrera['id_carrera']) ?></td>
                    <td><?= htmlspecialchars($carrera['nombre_carrera']) ?></td>
                    <td><?= htmlspecialchars($carrera['cod_carrera']) ?></td>
                    <td>
                        <span class="badge badge-<?= $carrera['activa'] ? 'success' : 'secondary' ?>">
                            <?= $carrera['activa'] ? 'Activa' : 'Inactiva' ?>
                        </span>
                    </td>
                    <td>

                    <?php if (tienePermiso('gestionar_carrera')): ?>
                        <button class="btn btn-sm btn-warning btn-editar" 
                                data-id="<?= intval($carrera['id_carrera']) ?>" 
                                onclick="cargarModalEditar(this)">
                            <i class="fas fa-edit"></i> Editar
                        </button>
                    <?php endif; ?>
                        
                        <?php if ($carrera['activa']): ?>
                            <?php if (tienePermiso('gestionar_carrera')): ?>
                            <button class="btn btn-sm btn-danger btn-cambiar-estado" 
                                    data-id="<?= $carrera['id_carrera'] ?>" 
                                    data-accion="desactivar">
                                <i class="fas fa-toggle-off"></i> Desactivar
                            </button>
                            <?php endif; ?>
                        <?php else: ?>
                            <button class="btn btn-sm btn-success btn-cambiar-estado" 
                                    data-id="<?= $carrera['id_carrera'] ?>" 
                                    data-accion="activar">
                                <i class="fas fa-toggle-on"></i> Activar
                            </button>
                        <?php endif; ?>
                        
                        <!-- Ver Pensum vía POST -->
                        <form method="POST" action="ver_pensum.php" class="d-inline m-0">
                            <input type="hidden" name="id_carrera" value="<?= intval($carrera['id_carrera']) ?>">
                            <button type="submit" class="btn btn-sm btn-info" title="Ver Pensum de la Carrera">
                                <i class="fas fa-book"></i> Ver Pensum
                            </button>
                        </form>

                        <a href="duplicar_carrera.php?id=<?= intval($carrera['id_carrera']) ?>" 
                           class="btn btn-sm btn-outline-primary ml-1" title="Crear versión de esta carrera">
                            <i class="fas fa-copy"></i> Duplicar
                        </a>

                        <?php // Mostrar selector de versiones vía POST ?>
                        <?php $versions = obtenerVersionesPorCodigoCarrera($carrera['cod_carrera']); ?>
                        <?php if (!empty($versions) || true): // siempre mostrar selector para incluir la versión base ?>
                            <?php $cbase = obtenerCarreraPorId(intval($carrera['id_carrera'])); $base_year = !empty($cbase['created_at']) ? date('Y', strtotime($cbase['created_at'])) : ''; ?>
                            <form method="POST" action="ver_pensum.php" class="d-inline-block ml-2 m-0" style="display:inline-block;">
                                <input type="hidden" name="id_carrera" value="<?= intval($carrera['id_carrera']) ?>" id="input_carrera_<?= intval($carrera['id_carrera']) ?>">
                                <input type="hidden" name="id_version" value="" id="input_version_<?= intval($carrera['id_carrera']) ?>">
                                <select class="form-control form-control-sm d-inline-block" style="width:auto; display:inline-block;" 
                                        onchange="if(this.value){ if(this.value.charAt(0)=='c'){ document.getElementById('input_carrera_<?= intval($carrera['id_carrera']) ?>').value=this.value.substring(1); document.getElementById('input_version_<?= intval($carrera['id_carrera']) ?>').value=''; } else { document.getElementById('input_version_<?= intval($carrera['id_carrera']) ?>').value=this.value; } this.form.submit(); }">
                                    <option value="">Versión (Año)</option>
                                    <?php if (!empty($base_year)): ?>
                                        <option value="c<?= intval($carrera['id_carrera']) ?>"><?= htmlspecialchars($base_year) ?> (Actual)</option>
                                    <?php else: ?>
                                        <option value="c<?= intval($carrera['id_carrera']) ?>">Actual</option>
                                    <?php endif; ?>
                                    <?php foreach ($versions as $v): ?>
                                        <?php $vyear = !empty($v['fecha_vigencia']) ? date('Y', strtotime($v['fecha_vigencia'])) : ($v['anio'] ?? ''); ?>
                                        <option value="<?= intval($v['id_version']) ?>"><?= htmlspecialchars($vyear) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" class="text-center">No hay carreras registradas</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
function cargarModalEditar(button) {
    const idCarrera = $(button).data('id');
    console.log("ID obtenido del botón:", idCarrera);
    
    if (!idCarrera || isNaN(idCarrera) || idCarrera <= 0) {
        console.error("ID inválido:", idCarrera);
        mostrarAlerta('danger', "Error: ID de carrera inválido");
        return;
    }

    $.ajax({
        url: 'partials/editar_carrera_modal.php',
        type: 'GET',
        data: { id: idCarrera },
        dataType: 'html',
        beforeSend: function() {
            console.log("Enviando ID válido:", idCarrera);
            // Mostrar spinner de carga
            $('#modalEditarCarrera .modal-content').html(`
                <div class="modal-body text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Cargando...</span>
                    </div>
                    <p class="mt-2">Cargando información de la carrera...</p>
                </div>
            `);
            $('#modalEditarCarrera').modal('show');
        },
        success: function(data) {
            $('#modalEditarCarrera .modal-content').html(data);
        },
        error: function(xhr, status, error) {
            console.error("Error en la solicitud:", {
                status: status,
                error: error,
                response: xhr.responseText
            });
            $('#modalEditarCarrera .modal-content').html(`
                <div class="modal-body">
                    <div class="alert alert-danger">
                        Error al cargar los datos. Por favor intente nuevamente.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                </div>
            `);
        }
    });
}

function mostrarAlerta(tipo, mensaje) {
    // Cerrar alertas existentes
    $('.alert-dismissible').alert('close');
    
    // Crear nueva alerta
    var alerta = `
        <div class="alert alert-${tipo} alert-dismissible fade show" role="alert">
            ${mensaje}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    `;
    
    // Insertar la alerta en el contenedor principal
    $('.container').prepend(alerta);
    
    // Cerrar automáticamente después de 5 segundos
    setTimeout(function() {
        $('.alert-dismissible').alert('close');
    }, 5000);
}
</script>