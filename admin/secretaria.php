<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

require_once('../funciones/functions.php');

cargarPermisosUsuario();
verificarPermiso('secretaria');
visita();

$success_message = '';
$error_message = '';

// Obtener periodos académicos
$periodos = obtenerPeriodosAcademicos($db);
$periodoActivo = obtenerPeriodoActivo();

// Determinar el período académico seleccionado
$idPeriodoSeleccionado = isset($_REQUEST['periodo_id']) ? (int)$_REQUEST['periodo_id'] : ($periodoActivo ? (int)$periodoActivo['id_periodo'] : 0);

// Si aún no hay período seleccionado y existen periodos, tomar el primero
if ($idPeriodoSeleccionado <= 0 && !empty($periodos)) {
    $idPeriodoSeleccionado = (int)$periodos[0]['id_periodo'];
}

// Obtener datos del período seleccionado
$infoPeriodoSeleccionado = null;
foreach ($periodos as $p) {
    if ((int)$p['id_periodo'] === $idPeriodoSeleccionado) {
        $infoPeriodoSeleccionado = $p;
        break;
    }
}

$carreras = obtenerTodasLasCarreras();
$mostrarPreinscripcion = obtenerConfiguracionSecretaria('mostrar_preinscripcion', '1');
$turnos = ['Diurno', 'Nocturno'];

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'guardar') {
        $contadorGuardados = 0;
        
        foreach ($carreras as $carrera) {
            foreach ($turnos as $turno) {
                $valor = $_POST['cupos'][$carrera['id']][$turno] ?? '';
                $valor = (int) trim($valor);
                if ($valor < 0) $valor = 0;
                
                $numeroSecciones = $_POST['secciones'][$carrera['id']][$turno] ?? '';
                $numeroSecciones = (int) trim($numeroSecciones);
                if ($numeroSecciones < 1) $numeroSecciones = 1;
                
                $resultado = guardarCupoSecretaria($carrera['id'], $turno, $valor, $numeroSecciones, $idPeriodoSeleccionado);
                if ($resultado) $contadorGuardados++;
            }
        }

        // Guardar configuraciones de visibilidad (global)
        $mostrarPreinscripcion = isset($_POST['mostrar_preinscripcion']) ? '1' : '0';
        guardarConfiguracionSecretaria('mostrar_preinscripcion', $mostrarPreinscripcion);

        // Guardar configuraciones de fechas para carga de notas por trimestre en este período
        for ($t = 1; $t <= 3; $t++) {
            $fecha_inicio = $_POST['fecha_inicio'][$t] ?? '';
            $fecha_fin = $_POST['fecha_fin'][$t] ?? '';
            
            if (!empty($fecha_inicio) && !empty($fecha_fin)) {
                guardarConfiguracionCargaTrimestre($t, $fecha_inicio, $fecha_fin, $idPeriodoSeleccionado);
            }
        }

        $nombreP = $infoPeriodoSeleccionado ? $infoPeriodoSeleccionado['nombre_periodo'] : "ID $idPeriodoSeleccionado";
        $success_message = "Se actualizaron correctamente $contadorGuardados registros de cupos y las fechas de carga para el período $nombreP.";
    } elseif ($action === 'copiar_cupos') {
        $periodoOrigen = (int)($_POST['periodo_origen'] ?? 0);
        if ($periodoOrigen > 0 && $periodoOrigen !== $idPeriodoSeleccionado) {
            $copiado = copiarCuposDePeriodo($periodoOrigen, $idPeriodoSeleccionado);
            if ($copiado) {
                $success_message = "Se copiaron exitosamente los cupos del período seleccionado a este período.";
            } else {
                $error_message = "No se pudieron copiar los cupos o ya existían configuraciones para este período.";
            }
        } else {
            $error_message = "Seleccione un período de origen válido diferente al actual.";
        }
    }
}

$cuposActuales = obtenerCuposSecretaria($idPeriodoSeleccionado);
$configuracionesCarga = obtenerTodasConfiguracionesCarga($idPeriodoSeleccionado);

$titulopag = 'Secretaría - Gestión de cupos y fechas de carga';
include('includes/head.php');
?>

<div class="container-fluid py-4">
    <div class="row mb-3">
        <div class="col-12">
            <h2 class="mb-2"><i class="fas fa-user-tie me-2"></i> Secretaría</h2>
            <p class="text-muted">Administre cupos por carrera y turno, fechas de carga de notas por trimestre según el período académico, y controle la visibilidad de la preinscripción.</p>
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

    <!-- Selector de Período Académico -->
    <div class="card mb-4 border-left-primary shadow-sm">
        <div class="card-body">
            <form method="get" action="" class="form-inline d-flex align-items-center justify-content-between flex-wrap">
                <div class="d-flex align-items-center mb-2 mb-md-0">
                    <label for="periodo_id" class="mr-3 font-weight-bold">
                        <i class="fas fa-calendar-alt text-primary"></i> Período Académico a Gestionar:
                    </label>
                    <select name="periodo_id" id="periodo_id" class="form-control form-control-lg" onchange="this.form.submit()">
                        <?php foreach ($periodos as $p): ?>
                            <option value="<?php echo (int)$p['id_periodo']; ?>" <?php echo ((int)$p['id_periodo'] === $idPeriodoSeleccionado) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($p['nombre_periodo']); ?> 
                                (<?php echo date('d/m/Y', strtotime($p['fecha_inicio'])); ?> al <?php echo date('d/m/Y', strtotime($p['fecha_fin'])); ?>)
                                <?php echo ($p['activo'] == 1) ? ' - [ACTIVO]' : ' - [CERRADO]'; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <?php if ($infoPeriodoSeleccionado): ?>
                        <?php if ($infoPeriodoSeleccionado['activo'] == 1): ?>
                            <span class="badge badge-success px-3 py-2" style="font-size: 14px;">
                                <i class="fas fa-check-circle"></i> Período Activo
                            </span>
                        <?php else: ?>
                            <span class="badge badge-secondary px-3 py-2" style="font-size: 14px;">
                                <i class="fas fa-lock"></i> Período Inactivo/Cerrado
                            </span>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </form>

            <?php if (empty($cuposActuales) && count($periodos) > 1): ?>
                <div class="alert alert-warning mt-3 mb-0">
                    <form method="post" action="" class="form-inline">
                        <input type="hidden" name="action" value="copiar_cupos">
                        <input type="hidden" name="periodo_id" value="<?php echo (int)$idPeriodoSeleccionado; ?>">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        <span>Este período no tiene cupos configurados todavía. ¿Desea copiarlos de otro período?</span>
                        <select name="periodo_origen" class="form-control form-control-sm mx-2" required>
                            <option value="">-- Seleccionar período origen --</option>
                            <?php foreach ($periodos as $p): ?>
                                <?php if ((int)$p['id_periodo'] !== $idPeriodoSeleccionado): ?>
                                    <option value="<?php echo (int)$p['id_periodo']; ?>">
                                        <?php echo htmlspecialchars($p['nombre_periodo']); ?>
                                    </option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                        <button type="submit" class="btn btn-warning btn-sm">
                            <i class="fas fa-copy"></i> Copiar Cupos
                        </button>
                    </form>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <form method="post" action="">
        <input type="hidden" name="action" value="guardar">
        <input type="hidden" name="periodo_id" value="<?php echo (int)$idPeriodoSeleccionado; ?>">

        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> 
            <strong>Instrucciones para el período <?php echo htmlspecialchars($infoPeriodoSeleccionado['nombre_periodo'] ?? ''); ?>:</strong><br>
            - <strong>Secciones autorizadas:</strong> Número máximo de secciones que el Director de Carrera puede crear para este período académico.<br>
            - <strong>Cupos totales:</strong> Cantidad de aspirantes que pueden preinscribirse para este período específico.<br>
            - <strong>Fechas de carga:</strong> Configure el rango de fechas en que los docentes podrán cargar notas para este período académico.
        </div>

        <!-- Tabla de Cupos -->
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <strong><i class="fas fa-chalkboard-user"></i> Cupos y Secciones autorizadas (Período: <?php echo htmlspecialchars($infoPeriodoSeleccionado['nombre_periodo'] ?? ''); ?>)</strong>
                <span class="badge badge-primary">Período ID: <?php echo (int)$idPeriodoSeleccionado; ?></span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover mb-0">
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
                                        $ocupados = contarPreinscripcionesPorCupo($carrera['id'], $turno, $idPeriodoSeleccionado);
                                        $libres = max(0, $total - $ocupados);
                                    ?>
                                    <tr>
                                        <td><strong><?php echo htmlspecialchars($carrera['nombre']); ?></strong></td>
                                        <td><span class="badge badge-info"><?php echo htmlspecialchars($turno); ?></span></td>
                                        <td><input type="number" min="0" class="form-control text-center" 
                                                   name="cupos[<?php echo (int)$carrera['id']; ?>][<?php echo htmlspecialchars($turno); ?>]" 
                                                   value="<?php echo htmlspecialchars($total); ?>"
                                                   style="width: 120px; margin: 0 auto;"></td>
                                        <td class="text-center align-middle"><span class="badge badge-warning"><?php echo number_format($ocupados); ?></span></td>
                                        <td class="text-center align-middle"><span class="badge badge-success"><?php echo number_format($libres); ?></span></td>
                                        <td><input type="number" min="1" class="form-control text-center" 
                                                   name="secciones[<?php echo (int)$carrera['id']; ?>][<?php echo htmlspecialchars($turno); ?>]" 
                                                   value="<?php echo htmlspecialchars($numeroSecciones); ?>"
                                                   style="width: 100px; margin: 0 auto;"></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Configuración de fechas para carga de notas -->
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <strong><i class="fas fa-calendar-alt"></i> Fechas para carga de notas por trimestre (Período: <?php echo htmlspecialchars($infoPeriodoSeleccionado['nombre_periodo'] ?? ''); ?>)</strong>
                <span class="text-muted small">Rango del período: <?php echo date('d/m/Y', strtotime($infoPeriodoSeleccionado['fecha_inicio'] ?? 'now')); ?> al <?php echo date('d/m/Y', strtotime($infoPeriodoSeleccionado['fecha_fin'] ?? 'now')); ?></span>
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
                                <td><input type="date" class="form-control" name="fecha_inicio[<?php echo $t; ?>]" value="<?php echo htmlspecialchars($fecha_inicio); ?>" required></td>
                                <td><input type="date" class="form-control" name="fecha_fin[<?php echo $t; ?>]" value="<?php echo htmlspecialchars($fecha_fin); ?>" required></td>
                                <td class="text-center">
                                    <?php if ($config && !empty($config['fecha_inicio']) && !empty($config['fecha_fin'])): ?>
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
                                </td>
                            </tr>
                            <?php endfor; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Visibilidad de botones públicos -->
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-light">
                <strong><i class="fas fa-eye"></i> Visibilidad de botones públicos</strong>
            </div>
            <div class="card-body">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="mostrar_preinscripcion" name="mostrar_preinscripcion" value="1" <?php echo $mostrarPreinscripcion === '1' ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="mostrar_preinscripcion">
                        <i class="fas fa-file-alt"></i> Mostrar botón de Preinscripción en la portada
                    </label>
                </div>
            </div>
        </div>

        <div class="text-right">
            <button type="submit" class="btn btn-primary btn-lg">
                <i class="fas fa-save"></i> Guardar cambios en período <?php echo htmlspecialchars($infoPeriodoSeleccionado['nombre_periodo'] ?? ''); ?>
            </button>
            <a href="secretaria.php?periodo_id=<?php echo (int)$idPeriodoSeleccionado; ?>" class="btn btn-secondary btn-lg">
                <i class="fas fa-undo"></i> Restablecer
            </a>
        </div>
    </form>
</div>

<?php include('includes/footer.php'); ?>