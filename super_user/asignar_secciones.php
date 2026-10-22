<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

$titulopag = "Asignación de Secciones a Docentes";
include('../funciones/functions.php');

// CARGAR PERMISOS
cargarPermisosUsuario();
verificarPermiso('asig_secciones');

// LLAMAR A LA FUNCIÓN DE VISITA
visita();

// Variables para mensajes
$tipo_mensaje = ''; // 'success' o 'error'
$texto_mensaje = '';

// Manejar petición AJAX para obtener materias del docente POR CARRERA
if(isset($_GET['ajax']) && $_GET['ajax'] == 'materias_docente_carrera' && isset($_GET['id_docente']) && isset($_GET['id_carrera'])) {
    header('Content-Type: application/json');
    $id_docente = $db->real_escape_string($_GET['id_docente']);
    $id_carrera = $db->real_escape_string($_GET['id_carrera']);
    
    $materias = obtenerMateriasDocentePorCarrera($id_docente, $id_carrera);
    echo json_encode($materias);
    exit();
}

// Procesar formulario
if(isset($_POST['asignar'])) {
    $id_usuario = $db->real_escape_string($_POST['id_usuario']);
    $id_seccion = $db->real_escape_string($_POST['id_seccion']);
    $id_materia = $db->real_escape_string($_POST['id_materia']);
    
    $resultado = procesarAsignacionSeccion($id_usuario, $id_seccion, $id_materia);
    
    if($resultado['success']) {
        $tipo_mensaje = 'success';
        $texto_mensaje = $resultado['message'];
    } else {
        $tipo_mensaje = 'error';
        $texto_mensaje = $resultado['message'];
    }
}

// Eliminar asignación
if(isset($_GET['eliminar'])) {
    $id = $db->real_escape_string($_GET['eliminar']);
    $resultado = eliminarAsignacionSeccion($id);
    
    if($resultado['success']) {
        $tipo_mensaje = 'success';
        $texto_mensaje = $resultado['message'];
    } else {
        $tipo_mensaje = 'error';
        $texto_mensaje = $resultado['message'];
    }
}

// Actualizar asignación existente
if(isset($_POST['actualizar_asignacion_seccion'])) {
    $id_docente_seccion = $db->real_escape_string($_POST['id_docente_seccion_editar']);
    $nueva_seccion = $db->real_escape_string($_POST['id_seccion_editar']);
    $nueva_materia = $db->real_escape_string($_POST['id_materia_editar']);
    
    $q_update = "UPDATE docente_seccion 
                 SET id_seccion = '$nueva_seccion', id_materia = '$nueva_materia' 
                 WHERE id_docente_seccion = '$id_docente_seccion'";
    if($db->query($q_update)) {
        $tipo_mensaje = 'success';
        $texto_mensaje = mb_strtoupper('ASIGNACIÓN DE SECCIÓN ACTUALIZADA CORRECTAMENTE', 'UTF-8');
    } else {
        $tipo_mensaje = 'error';
        $texto_mensaje = mb_strtoupper('ERROR AL ACTUALIZAR LA ASIGNACIÓN DE SECCIÓN EN EL SERVIDOR', 'UTF-8');
    }
}

include("includes/head.php");
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <h1 class="mt-4"><?php echo $titulopag; ?></h1>
            
            <!-- Modal de Éxito -->
            <div class="modal fade" id="successModal" tabindex="-1" role="dialog" aria-labelledby="successModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-success text-white">
                            <h5 class="modal-title" id="successModalLabel">¡Operación Exitosa!</h5>
                            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body text-center">
                            <div class="mb-3">
                                <i class="fas fa-check-circle fa-3x text-success"></i>
                            </div>
                            <p id="successMessage" class="lead"></p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-success" data-dismiss="modal">Aceptar</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal de Error -->
            <div class="modal fade" id="errorModal" tabindex="-1" role="dialog" aria-labelledby="errorModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title" id="errorModalLabel">Error en la Operación</h5>
                            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body text-center">
                            <div class="mb-3">
                                <i class="fas fa-exclamation-circle fa-3x text-danger"></i>
                            </div>
                            <p id="errorMessage" class="lead"></p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal de Confirmación para Eliminar -->
            <div class="modal fade" id="confirmDeleteModal" tabindex="-1" role="dialog" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title" id="confirmDeleteModalLabel">Confirmar Eliminación</h5>
                            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="text-center mb-3">
                                <i class="fas fa-trash-alt fa-2x text-danger"></i>
                            </div>
                            <p class="text-center">¿Está seguro de eliminar esta asignación?</p>
                            <p class="text-center text-muted"><small>Esta acción no se puede deshacer.</small></p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                            <a id="confirmDeleteButton" href="#" class="btn btn-danger">
                                <i class="fas fa-trash-alt mr-1"></i> Eliminar
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal para Editar Asignación de Sección -->
            <div class="modal fade" id="modalEditar" tabindex="-1" role="dialog" aria-labelledby="modalEditarLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title font-weight-bold" id="modalEditarLabel">
                                <i class="fas fa-edit mr-1"></i> EDITAR ASIGNACIÓN DE SECCIÓN
                            </h5>
                            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form method="post" action="">
                            <div class="modal-body">
                                <input type="hidden" name="id_docente_seccion_editar" id="id_docente_seccion_editar">
                                <div class="form-group">
                                    <label><i class="fas fa-user-tie mr-1"></i> DOCENTE:</label>
                                    <input type="text" class="form-control text-uppercase font-weight-bold" id="docente_editar_nombre" readonly>
                                </div>
                                <div class="form-group">
                                    <label for="id_seccion_editar"><i class="fas fa-door-open mr-1"></i> NUEVA SECCIÓN:</label>
                                    <select class="form-control text-uppercase" id="id_seccion_editar" name="id_seccion_editar" required onchange="cargarMateriasEditarModal()">
                                        <option value="">SELECCIONE UNA SECCIÓN</option>
                                        <?php
                                        $secciones_modal = obtenerSeccionesActivas();
                                        if(count($secciones_modal) > 0) {
                                            foreach($secciones_modal as $sec_m) {
                                                $id_c_m = $sec_m['id_carrera'] ?? '0';
                                                $nom_c_m = $sec_m['nombre_carrera'] ?? 'Sin carrera asignada';
                                                echo "<option value='".$sec_m['id_seccion']."' data-carrera='".$id_c_m."'>".htmlspecialchars(mb_strtoupper($sec_m['codigo_seccion'].' - '.$nom_c_m, 'UTF-8'))."</option>";
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="id_materia_editar"><i class="fas fa-book mr-1"></i> NUEVA MATERIA:</label>
                                    <select class="form-control text-uppercase" id="id_materia_editar" name="id_materia_editar" required>
                                        <option value="">SELECCIONE UNA MATERIA</option>
                                    </select>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                    <i class="fas fa-times mr-1"></i> CANCELAR
                                </button>
                                <button type="submit" name="actualizar_asignacion_seccion" class="btn btn-primary font-weight-bold">
                                    <i class="fas fa-save mr-1"></i> GUARDAR CAMBIOS
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Mostrar mensaje en toast -->
            <?php if($tipo_mensaje): ?>
            <div id="toastMessage" data-type="<?php echo $tipo_mensaje; ?>" data-message="<?php echo htmlspecialchars($texto_mensaje); ?>"></div>
            <?php endif; ?>

            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-success text-white font-weight-bold">
                    <i class="fas fa-user-plus mr-1"></i>
                    ASIGNAR NUEVA SECCIÓN A DOCENTE
                </div>
                <div class="card-body">
                    <form method="post" action="" id="asignarForm">
                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label for="id_usuario" class="font-weight-bold"><i class="fas fa-user-tie mr-1"></i> DOCENTE:</label>
                                <input type="text" id="buscar_docente_input" class="form-control form-control-sm mb-1 text-uppercase" placeholder="🔍 BUSCAR DOCENTE POR NOMBRE O CÉDULA..." autocomplete="off">
                                <select class="form-control text-uppercase" id="id_usuario" name="id_usuario" required onchange="cargarMateriasPorCarrera()">
                                    <option value="">SELECCIONE UN DOCENTE</option>
                                    <?php
                                    $docentes = obtenerDocentesActivos();
                                    foreach($docentes as $docente) {
                                        $searchMeta = htmlspecialchars(mb_strtolower($docente['nombre'] . ' ' . $docente['idusuario'], 'UTF-8'));
                                        $nombre_fmt = htmlspecialchars(mb_strtoupper($docente['nombre'], 'UTF-8'));
                                        $cedula_fmt = htmlspecialchars($docente['idusuario']);
                                        echo "<option value='".$docente['id']."' data-search='".$searchMeta."'>".$nombre_fmt." (".$cedula_fmt.")</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="id_seccion" class="font-weight-bold"><i class="fas fa-door-open mr-1"></i> SECCIÓN:</label>
                                <select class="form-control text-uppercase" id="id_seccion" name="id_seccion" required onchange="cargarMateriasPorCarrera()">
                                    <option value="">SELECCIONE UNA SECCIÓN</option>
                                    <?php
                                    $secciones = obtenerSeccionesActivas();
                                    if(count($secciones) > 0) {
                                        foreach($secciones as $seccion) {
                                            $id_carrera = $seccion['id_carrera'] ?? '0';
                                            $nombre_carrera = $seccion['nombre_carrera'] ?? 'Sin carrera asignada';
                                            echo "<option value='".$seccion['id_seccion']."' data-carrera='".$id_carrera."'>".mb_strtoupper($seccion['codigo_seccion']." - ".$nombre_carrera, 'UTF-8')."</option>";
                                        }
                                    } else {
                                        echo "<option value=''>NO HAY SECCIONES ACTIVAS DISPONIBLES</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="id_materia" class="font-weight-bold"><i class="fas fa-book mr-1"></i> MATERIA:</label>
                                <select class="form-control text-uppercase" id="id_materia" name="id_materia" required disabled>
                                    <option value="">PRIMERO SELECCIONE DOCENTE Y SECCIÓN</option>
                                </select>
                            </div>
                        </div>
                        <button type="submit" name="asignar" class="btn btn-success font-weight-bold px-4">
                            <i class="fas fa-save mr-1"></i> ASIGNAR SECCIÓN
                        </button>
                        <button type="reset" class="btn btn-secondary font-weight-bold">
                            <i class="fas fa-undo mr-1"></i> LIMPIAR
                        </button>
                    </form>
                </div>
            </div>
            
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-primary text-white font-weight-bold">
                    <i class="fas fa-table mr-1"></i>
                    ASIGNACIONES ACTUALES
                </div>
                <div class="card-body">
                    <!-- Buscador en tiempo real arriba de la tabla -->
                    <div class="form-group mb-3">
                        <label for="buscar_tabla_asignaciones" class="font-weight-bold text-primary"><i class="fas fa-search mr-1"></i> BUSCAR DOCENTE, SECCIÓN O MATERIA EN TIEMPO REAL:</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-filter"></i></span>
                            </div>
                            <input type="text" id="buscar_tabla_asignaciones" class="form-control text-uppercase" placeholder="ESCRIBA PARA BUSCAR DOCENTE (CÉDULA O NOMBRE), SECCIÓN O MATERIA..." autocomplete="off">
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary" type="button" id="btn_limpiar_tabla">
                                    <i class="fas fa-times"></i> LIMPIAR
                                </button>
                            </div>
                        </div>
                        <small class="form-text text-muted font-weight-bold" id="tabla_coincidencias_info">FILTRADO EN TIEMPO REAL ACTIVADO.</small>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover text-uppercase" id="dataTable" width="100%" cellspacing="0">
                            <thead class="thead-dark">
                                <tr>
                                    <th>DOCENTE</th>
                                    <th>SECCIÓN</th>
                                    <th>CARRERA</th>
                                    <th>MATERIA</th>
                                    <th>FECHA ASIGNACIÓN</th>
                                    <th>ACCIONES</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $asignaciones = obtenerAsignacionesSecciones();
                                if(count($asignaciones) > 0) {
                                    foreach($asignaciones as $row) {
                                        $docente_upper = htmlspecialchars(mb_strtoupper($row['docente'], 'UTF-8'));
                                        $cedula_upper = htmlspecialchars($row['cedula_docente'] ?? $row['docente_cedula'] ?? $row['idusuario'] ?? '');
                                        $seccion_upper = htmlspecialchars(mb_strtoupper($row['codigo_seccion'], 'UTF-8'));
                                        $carrera_upper = htmlspecialchars(mb_strtoupper($row['nombre_carrera'] ?? 'Sin carrera asignada', 'UTF-8'));
                                        $materia_upper = htmlspecialchars(mb_strtoupper($row['nombre_materia'], 'UTF-8'));
                                        $cod_mat_upper = htmlspecialchars(mb_strtoupper($row['cod_materia'], 'UTF-8'));

                                        echo "<tr>
                                                <td>
                                                    <strong>".$docente_upper."</strong><br>
                                                    <small class='text-muted'>CÉDULA: ".$cedula_upper."</small>
                                                </td>
                                                <td><span class='badge badge-primary'>".$seccion_upper."</span></td>
                                                <td>".$carrera_upper."</td>
                                                <td>
                                                    <strong>".$materia_upper."</strong><br>
                                                    <small class='text-muted'>CÓDIGO: ".$cod_mat_upper."</small>
                                                </td>
                                                <td>".date('d/m/Y H:i', strtotime($row['fecha_asignacion']))."</td>
                                                <td class='text-nowrap'>
                                                    <button class='btn btn-sm btn-primary editar-asignacion mr-1' 
                                                            data-toggle='modal' 
                                                            data-target='#modalEditar'
                                                            data-id='".$row['id_docente_seccion']."'
                                                            data-docente='".$docente_upper."'
                                                            data-cedula='".$cedula_upper."'
                                                            data-id-docente='".htmlspecialchars((string)($row['id_usuario'] ?? $row['id_docente'] ?? ''))."'
                                                            data-id-seccion='".$row['id_seccion']."'
                                                            data-id-materia='".$row['id_materia']."'>
                                                        <i class='fas fa-edit mr-1'></i> CAMBIAR
                                                    </button>
                                                    <button class='btn btn-sm btn-danger eliminar-asignacion' 
                                                            data-toggle='modal' 
                                                            data-target='#confirmDeleteModal'
                                                            data-id='".$row['id_docente_seccion']."'
                                                            data-docente='".$docente_upper."'
                                                            data-seccion='".$seccion_upper."'>
                                                        <i class='fas fa-trash-alt mr-1'></i> ELIMINAR
                                                    </button>
                                                </td>
                                              </tr>";
                                    }
                                } else {
                                    echo "<tr>
                                            <td colspan='6' class='text-center'>
                                                <div class='alert alert-info mb-0'>
                                                    <i class='fas fa-info-circle mr-2'></i>
                                                    NO HAY ASIGNACIONES REGISTRADAS
                                                </div>
                                            </td>
                                          </tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function cargarMateriasPorCarrera() {
    var idDocente = document.getElementById('id_usuario').value;
    var selectSeccion = document.getElementById('id_seccion');
    var selectMaterias = document.getElementById('id_materia');
    
    var idCarrera = selectSeccion.options[selectSeccion.selectedIndex]?.getAttribute('data-carrera') || '0';
    
    if(idDocente === '' || selectSeccion.value === '') {
        selectMaterias.innerHTML = '<option value="">PRIMERO SELECCIONE DOCENTE Y SECCIÓN</option>';
        selectMaterias.disabled = true;
        return;
    }
    
    selectMaterias.innerHTML = '<option value="">CARGANDO MATERIAS...</option>';
    selectMaterias.disabled = true;
    
    var xhr = new XMLHttpRequest();
    xhr.open('GET', '?ajax=materias_docente_carrera&id_docente=' + idDocente + '&id_carrera=' + idCarrera, true);
    
    xhr.onload = function() {
        if(this.status == 200) {
            try {
                var materias = JSON.parse(this.responseText);
                
                if(materias.length > 0) {
                    selectMaterias.innerHTML = '';
                    materias.forEach(function(materia) {
                        var option = document.createElement('option');
                        option.value = materia.id_materia;
                        option.textContent = (materia.nombre_materia + ' (' + materia.cod_materia + ')').toUpperCase();
                        selectMaterias.appendChild(option);
                    });
                    selectMaterias.disabled = false;
                } else {
                    selectMaterias.innerHTML = '<option value="">ESTE DOCENTE NO TIENE MATERIAS PARA ESTA CARRERA</option>';
                    selectMaterias.disabled = true;
                    showToast('warning', 'EL DOCENTE NO TIENE MATERIAS ASIGNADAS PARA ESTA CARRERA');
                }
            } catch(e) {
                selectMaterias.innerHTML = '<option value="">ERROR AL PROCESAR MATERIAS</option>';
                selectMaterias.disabled = true;
                showToast('error', 'ERROR AL CARGAR LAS MATERIAS');
            }
        } else {
            selectMaterias.innerHTML = '<option value="">ERROR AL CARGAR MATERIAS</option>';
            selectMaterias.disabled = true;
            showToast('error', 'ERROR DE CONEXIÓN AL SERVIDOR');
        }
    };
    
    xhr.onerror = function() {
        selectMaterias.innerHTML = '<option value="">ERROR DE CONEXIÓN</option>';
        selectMaterias.disabled = true;
        showToast('error', 'ERROR DE CONEXIÓN CON EL SERVIDOR');
    };
    
    xhr.send();
}

function cargarMateriasEditarModal(idMateriaSeleccionar) {
    var idDocente = $('#docente_editar_nombre').attr('data-id-docente');
    var selectSeccion = document.getElementById('id_seccion_editar');
    var selectMaterias = document.getElementById('id_materia_editar');
    
    if(!selectSeccion) return;
    var idCarrera = selectSeccion.options[selectSeccion.selectedIndex]?.getAttribute('data-carrera') || '0';
    
    if(!idDocente || !selectSeccion.value) {
        selectMaterias.innerHTML = '<option value="">PRIMERO SELECCIONE SECCIÓN</option>';
        selectMaterias.disabled = true;
        return;
    }
    
    selectMaterias.innerHTML = '<option value="">CARGANDO MATERIAS...</option>';
    selectMaterias.disabled = true;
    
    var xhr = new XMLHttpRequest();
    xhr.open('GET', '?ajax=materias_docente_carrera&id_docente=' + idDocente + '&id_carrera=' + idCarrera, true);
    
    xhr.onload = function() {
        if(this.status == 200) {
            try {
                var materias = JSON.parse(this.responseText);
                if(materias.length > 0) {
                    selectMaterias.innerHTML = '';
                    materias.forEach(function(materia) {
                        var option = document.createElement('option');
                        option.value = materia.id_materia;
                        option.textContent = (materia.nombre_materia + ' (' + materia.cod_materia + ')').toUpperCase();
                        if(idMateriaSeleccionar && materia.id_materia == idMateriaSeleccionar) {
                            option.selected = true;
                        }
                        selectMaterias.appendChild(option);
                    });
                    selectMaterias.disabled = false;
                } else {
                    selectMaterias.innerHTML = '<option value="">ESTE DOCENTE NO TIENE MATERIAS PARA ESTA SECCIÓN</option>';
                    selectMaterias.disabled = true;
                }
            } catch(e) {
                selectMaterias.innerHTML = '<option value="">ERROR AL PROCESAR MATERIAS</option>';
                selectMaterias.disabled = true;
            }
        }
    };
    xhr.send();
}

function showToast(type, message) {
    var alertClass = type === 'success' ? 'alert-success' : 
                     type === 'error' ? 'alert-danger' : 
                     type === 'warning' ? 'alert-warning' : 'alert-info';
    
    var toast = document.createElement('div');
    toast.className = 'alert ' + alertClass + ' alert-dismissible fade show font-weight-bold';
    toast.style.position = 'fixed';
    toast.style.top = '20px';
    toast.style.right = '20px';
    toast.style.zIndex = '9999';
    toast.style.minWidth = '300px';
    toast.innerHTML = `
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <strong>${type === 'success' ? 'ÉXITO!' : type === 'error' ? 'ERROR!' : 'ADVERTENCIA!'}</strong> ${message.toUpperCase()}
    `;
    
    document.body.appendChild(toast);
    
    setTimeout(function() {
        $(toast).alert('close');
    }, 5000);
}

$(document).ready(function() {
    // BUSCADOR EN TIEMPO REAL PARA SELECTOR DE DOCENTE (POR NOMBRE O CÉDULA)
    const inputBuscarDocente = document.getElementById('buscar_docente_input');
    if (inputBuscarDocente) {
        inputBuscarDocente.addEventListener('input', function() {
            const val = this.value.toLowerCase().trim();
            const select = document.getElementById('id_usuario');
            const options = select.querySelectorAll('option');
            let visibleCount = 0;
            let lastVisibleOption = null;

            options.forEach(opt => {
                if (opt.value === '') {
                    opt.hidden = false;
                    return;
                }
                const searchMeta = opt.getAttribute('data-search') || opt.textContent.toLowerCase();
                if (searchMeta.includes(val)) {
                    opt.hidden = false;
                    visibleCount++;
                    lastVisibleOption = opt;
                } else {
                    opt.hidden = true;
                }
            });

            if (visibleCount === 1 && lastVisibleOption) {
                select.value = lastVisibleOption.value;
                cargarMateriasPorCarrera();
            }
        });
    }

    // BUSCADOR EN TIEMPO REAL DE LA TABLA DE ASIGNACIONES
    const inputBuscarTabla = document.getElementById('buscar_tabla_asignaciones');
    const btnLimpiarTabla = document.getElementById('btn_limpiar_tabla');
    const tablaInfo = document.getElementById('tabla_coincidencias_info');

    if (inputBuscarTabla) {
        function filtrarTabla() {
            const filter = inputBuscarTabla.value.toLowerCase().trim();
            const filas = document.querySelectorAll('#dataTable tbody tr');
            let visibles = 0;

            filas.forEach(fila => {
                const texto = fila.textContent.toLowerCase();
                if (texto.includes(filter)) {
                    fila.style.display = '';
                    visibles++;
                } else {
                    fila.style.display = 'none';
                }
            });

            if (tablaInfo) {
                if (filter === '') {
                    tablaInfo.textContent = 'FILTRADO EN TIEMPO REAL ACTIVADO.';
                } else {
                    tablaInfo.textContent = 'MOSTRANDO ' + visibles + ' COINCIDENCIA(S) DE ' + filas.length + ' ASIGNACIONES.';
                }
            }
        }

        inputBuscarTabla.addEventListener('input', filtrarTabla);
        if (btnLimpiarTabla) {
            btnLimpiarTabla.addEventListener('click', function() {
                inputBuscarTabla.value = '';
                filtrarTabla();
            });
        }
    }

    // MODAL EDITAR ASIGNACIÓN DE SECCIÓN
    $(document).on('click', '.editar-asignacion', function() {
        var id = $(this).data('id');
        var docente = $(this).data('docente');
        var cedula = $(this).data('cedula');
        var idDocente = $(this).data('id-docente');
        var idSeccion = $(this).data('id-seccion');
        var idMateria = $(this).data('id-materia');
        
        $('#id_docente_seccion_editar').val(id);
        $('#docente_editar_nombre').val(docente + ' (CÉDULA: ' + cedula + ')').attr('data-id-docente', idDocente);
        $('#id_seccion_editar').val(idSeccion);
        
        cargarMateriasEditarModal(idMateria);
    });

    // MODAL ELIMINAR ASIGNACIÓN DE SECCIÓN
    $(document).on('click', '.eliminar-asignacion', function() {
        var id = $(this).data('id');
        var docente = $(this).data('docente');
        var seccion = $(this).data('seccion');
        
        $('#confirmDeleteButton').attr('href', '?eliminar=' + id);
        
        $('#confirmDeleteModal .modal-body p:first').html(
            '¿ESTÁ SEGURO DE ELIMINAR LA ASIGNACIÓN DE <strong>' + docente + '</strong> A LA SECCIÓN <strong>' + seccion + '</strong>?'
        );
    });
    
    var toastMessage = $('#toastMessage');
    if(toastMessage.length) {
        var type = toastMessage.data('type');
        var message = toastMessage.data('message');
        
        if(type === 'success') {
            $('#successMessage').text(message);
            $('#successModal').modal('show');
        } else if(type === 'error') {
            $('#errorMessage').text(message);
            $('#errorModal').modal('show');
        }
        
        setTimeout(function() {
            toastMessage.remove();
        }, 100);
    }
    
    $('#asignarForm').on('submit', function(e) {
        var idMateria = $('#id_materia');
        if(idMateria.is(':disabled') || idMateria.val() === '') {
            e.preventDefault();
            showToast('error', 'POR FAVOR SELECCIONE UNA MATERIA VÁLIDA');
            return false;
        }
        return true;
    });
    
    $('#asignarForm button[type="reset"]').on('click', function() {
        $('#id_materia').html('<option value="">PRIMERO SELECCIONE DOCENTE Y SECCIÓN</option>').prop('disabled', true);
        if(inputBuscarDocente) inputBuscarDocente.value = '';
        const options = document.querySelectorAll('#id_usuario option');
        options.forEach(opt => opt.hidden = false);
        showToast('info', 'FORMULARIO LIMPIADO');
    });

    // CONVERSIÓN AUTOMÁTICA A MAYÚSCULAS EN ENTRADAS DE TEXTO
    $(document).on('input', 'input[type="text"]', function() {
        this.value = this.value.toUpperCase();
    });
});
</script>

<?php include("includes/footer.php"); ?>