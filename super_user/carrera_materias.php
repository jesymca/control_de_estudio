<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');



$titulopag = "Gestión de Materias por Carrera";
include('../funciones/functions.php');

//CARGAR PERMISOS
cargarPermisosUsuario();
verificarPermiso('rela_materia_carrera');

// LLAMAR A LA FUNCIÓN DE VISITA
visita();


include("includes/head.php");




// Procesar formularios
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['agregar_materia'])) {
            $id_malla = isset($_POST['id_malla']) && $_POST['id_malla'] !== '' ? intval($_POST['id_malla']) : 0;
            if ($id_malla > 0) {
                $resultado = asignarMateriaAMalla($id_malla, intval($_POST['id_materia']), intval($_POST['semestre']));
            } else {
                $resultado = asignarMateriaACarrera(
                    intval($_POST['id_carrera']),
                    intval($_POST['id_materia']),
                    intval($_POST['semestre'])
                );
            }
        
        if (!is_array($resultado)) $resultado = ['success' => false, 'message' => 'Respuesta inválida'];
        if (!empty($resultado['success'])) {
            $mensaje_text = $resultado['message'] ?? 'Asignación realizada correctamente';
            $mensaje = htmlspecialchars($mensaje_text);
        } else {
            $error_text = $resultado['message'] ?? 'Ocurrió un error al asignar la materia';
            $error = htmlspecialchars($error_text);
        }
    }
    
    if (isset($_POST['eliminar_asignacion'])) {
        $resultado = eliminarAsignacionMateria(intval($_POST['id_relacion']));
        
        if (!is_array($resultado)) $resultado = ['success' => false, 'message' => 'Respuesta inválida'];
        if (!empty($resultado['success'])) {
            $mensaje_text = $resultado['message'] ?? 'Asignación eliminada correctamente';
            $mensaje = htmlspecialchars($mensaje_text);
        } else {
            $error_text = $resultado['message'] ?? 'Ocurrió un error al eliminar la asignación';
            $error = htmlspecialchars($error_text);
        }
    }
    
    if (isset($_POST['eliminar_asignacion_malla'])) {
        $resultado = eliminarAsignacionMalla(intval($_POST['id_relacion_malla']));

        if (!is_array($resultado)) $resultado = ['success' => false, 'message' => 'Respuesta inválida'];
        if (!empty($resultado['success'])) {
            $mensaje_text = $resultado['message'] ?? 'Asignación de malla eliminada';
            $mensaje = htmlspecialchars($mensaje_text);
        } else {
            $error_text = $resultado['message'] ?? 'Ocurrió un error al eliminar la asignación de malla';
            $error = htmlspecialchars($error_text);
        }
    }
}

// Obtener datos (usar todas las carreras para incluir versiones de carreras inactivas)
$carreras = function_exists('obtenerCarrerasCompleta') ? obtenerCarrerasCompleta() : obtenerTodasLasCarreras();

// Determinar carrera seleccionada: preferir GET (enlaces), luego POST (formularios), luego primera de la lista
$carrera_seleccionada = 0;
if (isset($_GET['id_carrera'])) {
    $carrera_seleccionada = intval($_GET['id_carrera']);
} elseif (isset($_POST['id_carrera'])) {
    $carrera_seleccionada = intval($_POST['id_carrera']);
} else {
    $carrera_seleccionada = $carreras[0]['id_carrera'] ?? 0;
}

// Si se pasa id_materia por GET, preseleccionarla en el formulario
$preselected_materia = isset($_GET['id_materia']) ? intval($_GET['id_materia']) : 0;

$materias_disponibles = obtenerMateriasDisponibles($carrera_seleccionada);
$materias_asignadas = obtenerMateriasAsignadas($carrera_seleccionada);

// (Depuraciones removidas para limpiar la interfaz)
?>

<div class="container-fluid">
    <h1 class="mt-4"><?= htmlspecialchars($titulopag) ?></h1>
    
    
    <?php if (isset($mensaje)): ?>
    <div class="alert alert-success">
        <?= is_array($mensaje) ? implode('<br>', $mensaje) : $mensaje ?>
    </div>
<?php endif; ?>


    
    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-plus-circle mr-1"></i>
                    Asignar Materia
                </div>
                <div class="card-body">
                    <form method="POST">
                        <div class="form-group">
                            <label>Carrera (versión / año):</label>
                            <select name="id_carrera" class="form-control" required>
                                <?php foreach ($carreras as $carrera): ?>
                                    <?php $anio = !empty($carrera['created_at']) ? date('Y', strtotime($carrera['created_at'])) : '';?>
                                    <?php $mallas_for_c = obtenerMallasPorCarrera($carrera['id_carrera']); $vcount = count($mallas_for_c); ?>
                                    <option value="<?= intval($carrera['id_carrera']) ?>" 
                                        <?= ($carrera['id_carrera'] == $carrera_seleccionada) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($carrera['nombre_carrera']) ?>
                                        <?= $carrera['cod_carrera'] ? ' (' . htmlspecialchars($carrera['cod_carrera']) . ')' : '' ?>
                                        <?= $anio ? ' - ' . $anio : '' ?>
                                        <?= $vcount ? ' — mallas: ' . $vcount : '' ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label>Materia:</label>
                            <select name="id_materia" class="form-control" required>
                                <?= renderizarOpcionesMateriasPorTrayecto($materias_disponibles, $preselected_materia ?: null, '-- Seleccionar Materia --') ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Malla (opcional):</label>
                            <select name="id_malla" id="id_malla" class="form-control">
                                <option value="">-- Usar carrera base --</option>
                                <?php
                                    // Mostrar mallas disponibles para la carrera seleccionada
                                    $mallas = [];
                                    if (!empty($carreras)) {
                                        foreach ($carreras as $c) {
                                            if ($c['id_carrera'] == $carrera_seleccionada) {
                                                $mallas = obtenerMallasPorCarrera($c['id_carrera']);
                                                break;
                                            }
                                        }
                                    }
                                ?>
                                <?php foreach ($mallas as $m): ?>
                                    <option value="<?= intval($m['id_malla']) ?>"><?= htmlspecialchars($m['codigo_malla'] . ' (' . $m['anio'] . ')') ?></option>
                                <?php endforeach; ?>
                            </select>
                            <small class="form-text text-muted">Si selecciona una malla, la asignación se guardará en esa malla (pensum) específica.</small>
                        </div>

                                <!-- Lista de versiones eliminada para mantener la interfaz limpia -->

                                
                        
                        <div class="form-group">
                            <label>Trimestre/Semestre:</label>
                            <select name="semestre" class="form-control" required>
                                <?php for ($i = 1; $i <= 3; $i++): ?>
                                    <option value="<?= $i ?>">Periodo <?= $i ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        
                        <button type="submit" name="agregar_materia" class="btn btn-primary">
                            <i class="fas fa-save"></i> Guardar
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-list mr-1"></i>
                    Materias Asignadas
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Carrera</th>
                                    <th>Materia</th>
                                    <th>Semestre</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($carreras as $carrera): ?>
                                        <?php
                                        // Materias base asignadas
                                        $materias = obtenerMateriasAsignadas($carrera['id_carrera']);
                                        foreach ($materias as $materia):
                                    ?>
                                        <tr>
                                            <td><?= htmlspecialchars($carrera['cod_carrera']) ?></td>
                                            <td><?= htmlspecialchars($materia['cod_materia']) ?> - <?= htmlspecialchars($materia['nombre_materia']) ?></td>
                                            <td><?= intval($materia['semestre']) ?> (base)</td>
                                            <td>
                                                <form method="POST" style="display:inline;">
                                                    <input type="hidden" name="id_relacion" value="<?= intval($materia['id_relacion']) ?>">
                                                    <button type="submit" name="eliminar_asignacion" class="btn btn-sm btn-danger"
                                                            onclick="return confirm('¿Eliminar esta asignación?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>

                                        <?php
                                        // Mostrar mallas y sus materias
                                        $mallas_list = obtenerMallasPorCarrera($carrera['id_carrera']);
                                        foreach ($mallas_list as $malla) {
                                            $mm = obtenerMateriasDeMalla($malla['id_malla']);
                                            foreach ($mm as $materia):
                                    ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($carrera['cod_carrera']) ?></td>
                                                    <td><?= htmlspecialchars($materia['cod_materia']) ?> - <?= htmlspecialchars($materia['nombre_materia']) ?></td>
                                                    <td><?= intval($materia['semestre']) ?> (<?= htmlspecialchars($malla['codigo_malla']) ?>)</td>
                                                    <td>
                                                        <form method="POST" style="display:inline;">
                                                            <input type="hidden" name="id_relacion_malla" value="<?= intval($materia['id']) ?>">
                                                            <button type="submit" name="eliminar_asignacion_malla" class="btn btn-sm btn-danger"
                                                                    onclick="return confirm('¿Eliminar esta asignación de malla?')">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            <?php endforeach;
                                        }
                                    ?>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include("includes/footer.php"); ?>

<script>
document.addEventListener('DOMContentLoaded', function(){
    const carreraSelect = document.querySelector('select[name="id_carrera"]');
    const mallaSelect = document.getElementById('id_malla');

    function renderNoMallas() {
        mallaSelect.innerHTML = '<option value="">-- Usar carrera base --</option>';
    }

    function loadMallas(id) {
        if (!id || parseInt(id) <= 0) { renderNoMallas(); return; }
        fetch('ajax_get_mallas.php?id_carrera=' + encodeURIComponent(id))
            .then(res => res.json())
            .then(data => {
                mallaSelect.innerHTML = '<option value="">-- Usar carrera base --</option>';
                // soportar tanto responses antiguas (versions) como nuevas (mallas)
                const list = (data && data.success && Array.isArray(data.mallas) && data.mallas.length) ? data.mallas : (data && data.success && Array.isArray(data.versions) ? data.versions : []);
                if (list.length) {
                    list.forEach(v => {
                        const opt = document.createElement('option');
                        opt.value = v.id_malla || v.id_version || v.id || 0;
                        opt.textContent = (v.codigo_malla ? v.codigo_malla + ' (' + (v.anio||'') + ')' : (v.anio || ''));
                        mallaSelect.appendChild(opt);
                    });
                } else {
                    renderNoMallas();
                }
            }).catch(err => {
                // silencioso: mantener select vacío
                renderNoMallas();
            });
    }

    if (carreraSelect) {
        carreraSelect.addEventListener('change', function(){ loadMallas(this.value); });
        // cargar inicial
        loadMallas(carreraSelect.value);
    }
});
</script>