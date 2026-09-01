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
    $id = isset($_POST['aceptar_id']) ? (int)$_POST['aceptar_id'] : (int)($_POST['rechazar_id'] ?? 0);

    if (!empty($_POST['aceptar_id'])) {
        $resultado = aceptarPreinscripcionConSeccion($id, $_SESSION['user']['id']);
        if ($resultado['success']) {
            $success_message = $resultado['message'];
            if (isset($resultado['seccion_asignada'])) {
                $success_message .= ' ' . $resultado['seccion_asignada'];
            }
        } else {
            $error_message = $resultado['message'];
        }
    }

    if (!empty($_POST['rechazar_id'])) {
        $motivo = trim($_POST['motivo'] ?? 'Preinscripción rechazada por el administrador.');
        $resultado = rechazarPreinscripcion($id, $_SESSION['user']['id'], $motivo);
        if ($resultado['success']) {
            $success_message = $resultado['message'];
        } else {
            $error_message = $resultado['message'];
        }
    }
} else {
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
}

$preinscripcion = obtenerPreinscripcionPorId($id);
if (!$preinscripcion) {
    header('Location: preinscripciones.php');
    exit;
}

// Obtener secciones disponibles del TRAYECTO 0
$seccionesDisponibles = [];
$mensaje_secciones = '';
if ($preinscripcion['status'] === 'Pendiente') {
    $carrera_id = $preinscripcion['carrera'];
    $turno = !empty($preinscripcion['turno']) ? $preinscripcion['turno'] : 'Diurno';
    
    // Solo obtener secciones del Trayecto 0
    $seccionesDisponibles = obtenerSeccionesDisponiblesPorCarreraYTurnoYTrayecto($carrera_id, $turno, 0);
    
    if (empty($seccionesDisponibles)) {
        $mensaje_secciones = "No hay secciones disponibles para el Trayecto 0 (Inicial) en esta carrera y turno.";
    }
}

$carreras = obtenerTodasLasCarreras();
$carreraMap = [];
foreach ($carreras as $carrera) {
    $carreraMap[$carrera['id']] = $carrera['nombre'];
}

$titulopag = 'Detalle de preinscripción';

$nombresUbicacion = obtenerNombresUbicacion(
    $preinscripcion['estado'] ?? null,
    $preinscripcion['municipio'] ?? null,
    $preinscripcion['parroquia'] ?? null
);
$estadoNombre = $nombresUbicacion['estado_nombre'] ?: ($preinscripcion['estado'] ?? 'No especificado');
$municipioNombre = $nombresUbicacion['municipio_nombre'] ?: ($preinscripcion['municipio'] ?? 'No especificado');
$parroquiaNombre = $nombresUbicacion['parroquia_nombre'] ?: ($preinscripcion['parroquia'] ?? 'No especificado');

global $db;
$ingresos = obtenerIngresos($db);
$fuenteIngresoNombre = $ingresos[$preinscripcion['fuente_ingresos']] ?? 'No especificado';

$titulos = !empty($preinscripcion['titulos']) ? explode('|||', $preinscripcion['titulos']) : [];
$institutos = !empty($preinscripcion['institutos']) ? explode('|||', $preinscripcion['institutos']) : [];
$fotoPerfilUrl = !empty($preinscripcion['foto_perfil']) ? '../foto_perfil/' . $preinscripcion['foto_perfil'] : null;
$embarazadaTexto = strtolower(trim($preinscripcion['genero'])) === 'femenino'
    ? ((string)$preinscripcion['embarazada'] === '1' ? 'Sí' : 'No')
    : 'No aplica';

include('includes/head.php');
?>

<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <div>
                <h2 class="mb-0"><i class="fas fa-file-alt me-2"></i> Planilla de Preinscripción</h2>
                <p class="text-muted mb-0">Detalle completo de la preinscripción enviada por el aspirante.</p>
            </div>
            <a href="preinscripciones.php" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Volver a Preinscripciones
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

    <div class="row mb-3">
        <div class="col-12">
            <?php if ($preinscripcion['status'] === 'Pendiente'): ?>
                <?php if (!empty($seccionesDisponibles)): ?>
                    <div class="alert alert-info mb-3">
                        <i class="fas fa-info-circle"></i> 
                        <strong>Secciones disponibles para Trayecto 0 (Inicial) - <?= htmlspecialchars($carreraMap[$preinscripcion['carrera']] ?? 'la carrera') ?> (Turno: <?= htmlspecialchars($preinscripcion['turno'] ?? 'Diurno') ?>):</strong>
                        <ul class="mb-0 mt-2">
                            <?php foreach ($seccionesDisponibles as $sec): ?>
                                <li>
                                    <strong>Código: <?= htmlspecialchars($sec['codigo_seccion']) ?></strong><br>
                                    <small>Capacidad: <?= $sec['capacidad_maxima'] ?> | Inscritos: <?= $sec['inscritos'] ?> | Cupos disponibles: <?= $sec['cupos_disponibles'] ?></small>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php else: ?>
                    <div class="alert alert-warning mb-3">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>¡Atención!</strong> <?= $mensaje_secciones ?>
                        <div class="mt-2">
                            <a href="gestion_seccion.php" class="btn btn-sm btn-primary">
                                <i class="fas fa-plus"></i> Gestionar Secciones
                            </a>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="d-flex flex-wrap gap-2 mb-3">
                    <form method="post" class="d-inline-block" id="formAceptar">
                        <input type="hidden" name="aceptar_id" value="<?php echo (int)$preinscripcion['id']; ?>">
                        <button type="submit" class="btn btn-success" 
                                id="btnAceptar"
                                <?php echo empty($seccionesDisponibles) ? 'disabled' : ''; ?>
                                onclick="return confirm('¿Aceptar esta preinscripción? Se creará el usuario y se asignará automáticamente a una sección del Trayecto 0.');">
                            <i class="fas fa-check"></i> Aprobar y Asignar a Sección
                        </button>
                    </form>
                    <form method="post" id="rechazarForm" class="d-inline-block">
                        <input type="hidden" name="rechazar_id" value="<?php echo (int)$preinscripcion['id']; ?>">
                        <input type="hidden" name="motivo" id="motivoRechazoInput" value="Preinscripción rechazada por el administrador.">
                        <button type="submit" class="btn btn-danger" id="rechazarBtn">
                            <i class="fas fa-times"></i> Rechazar preinscripción
                        </button>
                    </form>
                </div>
            <?php else: ?>
                <div class="alert alert-info mb-3">
                    Esta preinscripción ya está <strong><?php echo htmlspecialchars($preinscripcion['status']); ?></strong>.
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Resto del código igual (los detalles de la preinscripción) -->
    <div class="row">
        <div class="col-12 col-xl-8">
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <strong>Datos principales</strong>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6"><strong>ID:</strong> <?php echo htmlspecialchars($preinscripcion['id']); ?></div>
                        <div class="col-md-6"><strong>Status:</strong> <?php echo htmlspecialchars($preinscripcion['status']); ?></div>
                        <div class="col-md-6"><strong>Cédula:</strong> <?php echo htmlspecialchars($preinscripcion['idusuario']); ?></div>
                        <div class="col-md-6"><strong>Usuario:</strong> <?php echo htmlspecialchars($preinscripcion['username']); ?></div>
                        <div class="col-md-12"><strong>Nombre:</strong> <?php echo htmlspecialchars($preinscripcion['nombre']); ?></div>
                        <div class="col-md-6"><strong>Email:</strong> <?php echo htmlspecialchars($preinscripcion['email']); ?></div>
                        <div class="col-md-6"><strong>Teléfono:</strong> <?php echo htmlspecialchars($preinscripcion['tlf']); ?></div>
                        <div class="col-md-6"><strong>Celular:</strong> <?php echo htmlspecialchars($preinscripcion['cel']); ?></div>
                        <div class="col-md-6"><strong>Fecha de nacimiento:</strong> <?php echo htmlspecialchars($preinscripcion['fecha_nac']); ?></div>
                        <div class="col-md-6"><strong>Género:</strong> <?php echo htmlspecialchars($preinscripcion['genero']); ?></div>
                        <div class="col-md-6"><strong>Estado civil:</strong> <?php echo htmlspecialchars($preinscripcion['edo_civil']); ?></div>
                        <div class="col-md-6"><strong>Embarazada:</strong> <?php echo htmlspecialchars($embarazadaTexto); ?></div>
                        <div class="col-md-6"><strong>Carrera solicitada:</strong> <?php echo htmlspecialchars($carreraMap[$preinscripcion['carrera']] ?? 'No especificada'); ?></div>
                        <div class="col-md-6"><strong>Turno:</strong> <?php echo htmlspecialchars($preinscripcion['turno'] ?? 'No especificado'); ?></div>
                        <div class="col-md-6"><strong>Fecha de solicitud:</strong> <?php echo htmlspecialchars($preinscripcion['fecha_ingreso']); ?></div>
                        <div class="col-md-6"><strong>Sede:</strong> <?php echo htmlspecialchars($preinscripcion['sede'] ?? 'No especificada'); ?></div>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header bg-light">
                    <strong>Ubicación y vivienda</strong>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-12"><strong>Dirección:</strong> <?php echo nl2br(htmlspecialchars($preinscripcion['direccion'])); ?></div>
                        <div class="col-md-4"><strong>Estado:</strong> <?php echo htmlspecialchars($estadoNombre); ?></div>
                        <div class="col-md-4"><strong>Municipio:</strong> <?php echo htmlspecialchars($municipioNombre); ?></div>
                        <div class="col-md-4"><strong>Parroquia:</strong> <?php echo htmlspecialchars($parroquiaNombre); ?></div>
                        <div class="col-md-4"><strong>Comuna:</strong> <?php echo htmlspecialchars($preinscripcion['comuna'] ?? 'No especificada'); ?></div>
                        <div class="col-md-4"><strong>Punto de referencia:</strong> <?php echo htmlspecialchars($preinscripcion['punto_referencia']); ?></div>
                        <div class="col-md-4"><strong>Personas a cargo:</strong> <?php echo htmlspecialchars($preinscripcion['acargo_usted']); ?></div>
                        <div class="col-md-4"><strong>Grupo familiar:</strong> <?php echo htmlspecialchars($preinscripcion['grupo_familiar']); ?></div>
                        <div class="col-md-4"><strong>Fuente de ingresos:</strong> <?php echo htmlspecialchars($fuenteIngresoNombre); ?></div>
                        <div class="col-md-4"><strong>Tipo de vivienda:</strong> <?php echo htmlspecialchars($preinscripcion['tipo_vivienda']); ?></div>
                        <div class="col-md-4"><strong>Tenencia de vivienda:</strong> <?php echo htmlspecialchars($preinscripcion['tenencia_vivienda']); ?></div>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header bg-light">
                    <strong>Salud y condición</strong>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6"><strong>Etnia:</strong> <?php echo htmlspecialchars($preinscripcion['etnia'] ?: 'No especificada'); ?></div>
                        <div class="col-md-6"><strong>Discapacidad:</strong> <?php echo htmlspecialchars($preinscripcion['discapacidad'] ?: 'No especificada'); ?></div>
                        <div class="col-md-12"><strong>Enfermedades:</strong> <?php echo nl2br(htmlspecialchars($preinscripcion['enfermedad'] ?: 'Ninguna')); ?></div>
                        <div class="col-md-12"><strong>Potencialidades:</strong> <?php echo nl2br(htmlspecialchars($preinscripcion['potencialidades'] ?: 'No especificadas')); ?></div>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header bg-light">
                    <strong>Títulos obtenidos e instituciones</strong>
                </div>
                <div class="card-body">
                    <?php if (count($titulos) > 0 && !empty($titulos[0])): ?>
                        <div class="list-group">
                            <?php foreach ($titulos as $index => $titulo): ?>
                                <?php if (!empty($titulo)): ?>
                                    <div class="list-group-item">
                                        <strong>Título:</strong> <?php echo htmlspecialchars($titulo); ?><br>
                                        <strong>Institución:</strong> <?php echo htmlspecialchars($institutos[$index] ?? ''); ?>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-muted">No se registraron títulos ni universidades.</div>
                    <?php endif; ?>
                    <div class="mt-3">
                        <strong>País del título:</strong> <?php echo htmlspecialchars($preinscripcion['pais_titulo'] ?? 'No especificado'); ?><br>
                        <?php if (strtolower(trim($preinscripcion['pais_titulo'] ?? '')) !== 'venezuela' && !empty($preinscripcion['pais_titulo'])): ?>
                            <strong>Legalizado en Venezuela:</strong> <?php echo ($preinscripcion['legalizado_titulo'] ?? 0) ? 'Sí' : 'No'; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-4">
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <strong>Contacto</strong>
                </div>
                <div class="card-body">
                    <p><strong>Correo:</strong> <?php echo htmlspecialchars($preinscripcion['email']); ?></p>
                    <p><strong>Teléfono principal:</strong> <?php echo htmlspecialchars($preinscripcion['tlf']); ?></p>
                    <p><strong>Teléfono alternativo:</strong> <?php echo htmlspecialchars($preinscripcion['num_telf_opc'] ?: 'No especificado'); ?></p>
                    <p><strong>Fecha de creación:</strong> <?php echo htmlspecialchars($preinscripcion['created_at']); ?></p>
                    <p><strong>Última actualización:</strong> <?php echo htmlspecialchars($preinscripcion['updated_at']); ?></p>
                    <?php if (!empty($preinscripcion['aprobado_por'])): ?>
                        <p><strong>Aprobado por:</strong> <?php echo htmlspecialchars($preinscripcion['aprobado_por']); ?></p>
                        <p><strong>Fecha aprobación:</strong> <?php echo htmlspecialchars($preinscripcion['fecha_aprobado']); ?></p>
                    <?php endif; ?>
                    <?php if (!empty($preinscripcion['rechazado_por'])): ?>
                        <p><strong>Rechazado por:</strong> <?php echo htmlspecialchars($preinscripcion['rechazado_por']); ?></p>
                        <p><strong>Fecha rechazo:</strong> <?php echo htmlspecialchars($preinscripcion['fecha_rechazo']); ?></p>
                        <p><strong>Motivo de rechazo:</strong> <?php echo nl2br(htmlspecialchars($preinscripcion['motivo_rechazo'])); ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <?php if ($fotoPerfilUrl): ?>
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <strong>Foto / Documento</strong>
                    </div>
                    <div class="card-body text-center">
                        <?php $ext = pathinfo($fotoPerfilUrl, PATHINFO_EXTENSION); ?>
                        <?php if (in_array(strtolower($ext), ['jpg','jpeg','png','webp'])): ?>
                            <img src="<?php echo htmlspecialchars($fotoPerfilUrl); ?>" class="img-fluid rounded" alt="Foto de perfil" style="max-width: 200px;">
                        <?php else: ?>
                            <a href="<?php echo htmlspecialchars($fotoPerfilUrl); ?>" target="_blank" class="btn btn-outline-primary">
                                <i class="fas fa-file-download"></i> Descargar documento
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const rechazarBtn = document.getElementById('rechazarBtn');
        const motivoInput = document.getElementById('motivoRechazoInput');

        if (rechazarBtn && motivoInput) {
            rechazarBtn.addEventListener('click', function(event) {
                const motivo = prompt('Ingrese el motivo del rechazo:', 'Preinscripción rechazada por el administrador.');
                if (motivo === null) {
                    event.preventDefault();
                    return;
                }
                motivoInput.value = motivo.trim() || 'Preinscripción rechazada por el administrador.';
            });
        }
    });
</script>

<?php include('includes/footer.php'); ?>