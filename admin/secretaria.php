<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

require_once('../funciones/functions.php');

cargarPermisosUsuario();
verificarPermiso('secretaria');
visita();

$success_message = '';
$error_message = '';

$carreras = obtenerTodasLasCarreras();
$cuposActuales = obtenerCuposSecretaria();
$mostrarPreinscripcion = obtenerConfiguracionSecretaria('mostrar_preinscripcion', '1');
$mostrarProsecucion = obtenerConfiguracionSecretaria('mostrar_prosecucion', '1');
$turnos = ['Diurno', 'Nocturno'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['action']) && $_POST['action'] === 'guardar') {
        
        $contadorGuardados = 0;
        
        foreach ($carreras as $carrera) {
            foreach ($turnos as $turno) {
                $valor = $_POST['cupos'][$carrera['id']][$turno] ?? '';
                $valor = (int) trim($valor);
                if ($valor < 0) $valor = 0;
                
                $numeroSecciones = $_POST['secciones'][$carrera['id']][$turno] ?? '';
                $numeroSecciones = (int) trim($numeroSecciones);
                if ($numeroSecciones < 1) $numeroSecciones = 1;
                
                $resultado = guardarCupoSecretaria($carrera['id'], $turno, $valor, $numeroSecciones);
                if ($resultado) $contadorGuardados++;
            }
        }

        // Guardar configuraciones de visibilidad
        $mostrarPreinscripcion = isset($_POST['mostrar_preinscripcion']) ? '1' : '0';
        $mostrarProsecucion = isset($_POST['mostrar_prosecucion']) ? '1' : '0';
        guardarConfiguracionSecretaria('mostrar_preinscripcion', $mostrarPreinscripcion);
        guardarConfiguracionSecretaria('mostrar_prosecucion', $mostrarProsecucion);

        // Guardar configuraciones de fechas para carga de notas por trimestre
        for ($t = 1; $t <= 3; $t++) {
            $fecha_inicio = $_POST['fecha_inicio'][$t] ?? '';
            $fecha_fin = $_POST['fecha_fin'][$t] ?? '';
            
            if (!empty($fecha_inicio) && !empty($fecha_fin)) {
                guardarConfiguracionCargaTrimestre($t, $fecha_inicio, $fecha_fin);
            }
        }

        $cuposActuales = obtenerCuposSecretaria();
        $success_message = "Se actualizaron $contadorGuardados registros correctamente.";
    }
}

$configuracionesCarga = obtenerTodasConfiguracionesCarga();

$titulopag = 'Secretaría - Gestión de cupos';
include('includes/head.php');
?>

<div class="container-fluid py-4">
    <div class="row mb-3">
        <div class="col-12">
            <h2 class="mb-4"><i class="fas fa-user-tie me-2"></i> Secretaría</h2>
            <p class="text-muted">Administre cupos por carrera y turno, fechas de carga de notas por trimestre, y controle si los botones de preinscripción y prosecución quedan visibles.</p>
        </div>
    </div>

    <?php if (!empty($success_message)): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success_message); ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Cerrar">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <?php if (!empty($error_message)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle"></i> <?php echo htmlspecialchars($error_message); ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Cerrar">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <form method="post" action="">
        <input type="hidden" name="action" value="guardar">

        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> 
            <strong>Instrucciones:</strong><br>
            - <strong>Secciones autorizadas:</strong> Número máximo de secciones que se pueden crear para esta carrera/turno.<br>
            - <strong>Cupos totales:</strong> Cantidad total de estudiantes que pueden preinscribirse.<br>
            - <strong>Fechas de carga:</strong> Configure las fechas de inicio y fin para cada trimestre. Los docentes solo podrán cargar notas dentro del rango establecido.
        </div>

        <!-- Tabla de Cupos -->
        <div class="card mb-4">
            <div class="card-header bg-light">
                <strong><i class="fas fa-chalkboard-user"></i> Cupos por carrera y turno</strong>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th width="25%">Programa</th>
                                <th width="10%">Turno</th>
                                <th width="15%">Cupos totales</th>
                                <th width="15%">Cupos ocupados</th>
                                <th width="15%">Cupos disponibles</th>
                                <th width="20%">Secciones autorizadas</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($carreras as $carrera): ?>
                                <?php foreach ($turnos as $turno): ?>
                                    <?php
                                        $config = $cuposActuales[$carrera['id']][$turno] ?? ['cupos_totales' => 0, 'numero_secciones' => 1];
                                        $total = $config['cupos_totales'];
                                        $numeroSecciones = $config['numero_secciones'];
                                        $ocupados = contarPreinscripcionesPorCupo($carrera['id'], $turno);
                                        $libres = max(0, $total - $ocupados);
                                    ?>
                                    <tr>
                                        <td><strong><?php echo htmlspecialchars($carrera['nombre']); ?></strong></td>
                                        <td><span class="badge badge-info"><?php echo htmlspecialchars($turno); ?></span></td>
                                        <td><input type="number" min="0" class="form-control text-center" 
                                                   name="cupos[<?php echo (int)$carrera['id']; ?>][<?php echo htmlspecialchars($turno); ?>]" 
                                                   value="<?php echo htmlspecialchars($total); ?>"
                                                   style="width: 120px; margin: 0 auto;"></td>
                                        <td class="text-center align-middle"><span class="badge badge-warning"><?php echo number_format($ocupados); ?></span></div>
                                        <td class="text-center align-middle"><span class="badge badge-success"><?php echo number_format($libres); ?></span></div>
                                        <td><input type="number" min="1" class="form-control text-center" 
                                                   name="secciones[<?php echo (int)$carrera['id']; ?>][<?php echo htmlspecialchars($turno); ?>]" 
                                                   value="<?php echo htmlspecialchars($numeroSecciones); ?>"
                                                   style="width: 100px; margin: 0 auto;"></div>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Configuración de fechas para carga de notas -->
        <div class="card mb-4">
            <div class="card-header bg-light">
                <strong><i class="fas fa-calendar-alt"></i> Fechas para carga de notas por trimestre</strong>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="thead-light">
                            <tr>
                                <th width="20%">Trimestre</th>
                                <th width="35%">Fecha de Inicio</th>
                                <th width="35%">Fecha de Fin</th>
                                <th width="10%">Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php for ($t = 1; $t <= 3; $t++): 
                                $config = $configuracionesCarga[$t] ?? null;
                                $fecha_inicio = $config ? $config['fecha_inicio'] : '';
                                $fecha_fin = $config ? $config['fecha_fin'] : '';
                                $hoy = date('Y-m-d');
                                $esta_activo = $config && $hoy >= $config['fecha_inicio'] && $hoy <= $config['fecha_fin'];
                            ?>
                            <tr>
                                <td class="text-center"><strong>Trimestre <?php echo $t; ?></strong></td>
                                <td><input type="date" class="form-control" name="fecha_inicio[<?php echo $t; ?>]" value="<?php echo htmlspecialchars($fecha_inicio); ?>" required></div>
                                <td><input type="date" class="form-control" name="fecha_fin[<?php echo $t; ?>]" value="<?php echo htmlspecialchars($fecha_fin); ?>" required></div>
                                <td class="text-center">
                                    <?php if ($config): ?>
                                        <?php if ($esta_activo): ?>
                                            <span class="badge badge-success">Activo</span>
                                        <?php elseif ($hoy < $config['fecha_inicio']): ?>
                                            <span class="badge badge-warning">Próximamente</span>
                                        <?php else: ?>
                                            <span class="badge badge-secondary">Cerrado</span>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="badge badge-danger">No configurado</span>
                                    <?php endif; ?>
                                </div>
                            </tr>
                            <?php endfor; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Visibilidad de botones públicos -->
        <div class="card mb-4">
            <div class="card-header bg-light">
                <strong><i class="fas fa-eye"></i> Visibilidad de botones públicos</strong>
            </div>
            <div class="card-body">
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" id="mostrar_preinscripcion" name="mostrar_preinscripcion" value="1" <?php echo $mostrarPreinscripcion === '1' ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="mostrar_preinscripcion">
                        <i class="fas fa-file-alt"></i> Mostrar botón de Preinscripción en la portada
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="mostrar_prosecucion" name="mostrar_prosecucion" value="1" <?php echo $mostrarProsecucion === '1' ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="mostrar_prosecucion">
                        <i class="fas fa-graduation-cap"></i> Mostrar botón de Prosecución en la portada
                    </label>
                </div>
            </div>
        </div>

        <div class="text-right">
            <button type="submit" class="btn btn-primary btn-lg">
                <i class="fas fa-save"></i> Guardar cambios
            </button>
            <button type="reset" class="btn btn-secondary btn-lg">
                <i class="fas fa-undo"></i> Restablecer
            </button>
        </div>
    </form>
</div>

<?php include('includes/footer.php'); ?>