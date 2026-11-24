<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

$titulopag = "Lista de estudiantes";
include('../funciones/functions.php');

cargarPermisosUsuario();
verificarPermiso('ver_estudiantes');

// Verificar permiso de edición de estudiantes
$puedeEditar = isset($_SESSION['user']['editar_estudiante']) && $_SESSION['user']['editar_estudiante'] == 1;

// LLAMAR A LA FUNCIÓN DE VISITA
visita();

// Obtener carreras para mostrar nombres
$carreras = obtenerTodasLasCarreras();
$carreraMap = [];
if ($carreras && is_array($carreras)) {
    foreach ($carreras as $carrera) {
        $carreraMap[$carrera['id']] = $carrera['nombre'];
    }
}

// Mapeo de estados federales
$estadoMap = [];
$resEst = $db->query("SELECT id_estado, estado FROM estados");
if ($resEst) {
    while ($re = $resEst->fetch_assoc()) {
        $estadoMap[$re['id_estado']] = $re['estado'];
    }
}

// Modalidades de ingreso disponibles
$modalidadesUnicas = [
    'OPSU',
    'Deporte',
    'Cultura',
    'Misión Sucre',
    'Ayuda Social',
    'Convenio',
    'Rendimiento Académico',
    'Autorizado por Secretaría',
    'Autorizado por Rector',
    'No especificado'
];

// Carreras únicas para filtros
$carrerasUnicas = array_column($carreras, 'nombre');

// Sedes únicas
$sedes = [];
$resSedes = $db->query("SELECT DISTINCT sede FROM users WHERE estudiante = 1 AND sede IS NOT NULL AND sede != '' ORDER BY sede ASC");
if ($resSedes) {
    while ($s = $resSedes->fetch_assoc()) $sedes[] = $s['sede'];
}
if (empty($sedes)) $sedes = ['Puerto Cabello', 'COEF'];

// Turnos únicos
$turnosUnicos = [];
$resTurnos = $db->query("SELECT DISTINCT turno FROM users WHERE estudiante = 1 AND turno IS NOT NULL AND turno != '' ORDER BY turno ASC");
if ($resTurnos) {
    while ($t = $resTurnos->fetch_assoc()) $turnosUnicos[] = $t['turno'];
}
if (empty($turnosUnicos)) $turnosUnicos = ['Diurno', 'Nocturno'];

// Estados geográficos únicos
$estadosUnicos = [];
$resEstados = $db->query("SELECT estado FROM estados ORDER BY estado ASC");
if ($resEstados) {
    while ($e = $resEstados->fetch_assoc()) $estadosUnicos[] = $e['estado'];
}

// Ciudades únicas
$ciudades = [];
$resCiudades = $db->query("SELECT DISTINCT c.ciudad FROM users u JOIN ciudades c ON u.ciudad = c.id_ciudad WHERE u.estudiante = 1 ORDER BY c.ciudad ASC LIMIT 100");
if ($resCiudades) {
    while ($c = $resCiudades->fetch_assoc()) $ciudades[] = $c['ciudad'];
}

// Cálculo rápido y optimizado de estadísticas directamente en MariaDB/MySQL (instantáneo)
$anioActual = date('Y');
$resStats = $db->query("SELECT 
    COUNT(*) as total,
    SUM(status = 1) as activos,
    SUM(status = 0) as inactivos,
    SUM(LOWER(genero) IN ('masculino', 'm')) as masc,
    SUM(LOWER(genero) IN ('femenino', 'f')) as fem,
    SUM(LOWER(genero) IN ('femenino', 'f') AND embarazada = 1) as embarazadas,
    SUM(YEAR(fecha_ingreso) = YEAR(CURDATE())) as nuevos,
    AVG(TIMESTAMPDIFF(YEAR, fecha_nac, CURDATE())) as edad_promedio,
    SUM(TIMESTAMPDIFF(YEAR, fecha_nac, CURDATE()) < 18) as menores,
    SUM(TIMESTAMPDIFF(YEAR, fecha_nac, CURDATE()) >= 18) as mayores,
    SUM(LOWER(edo_civil) LIKE '%soltero%') as solteros,
    SUM(LOWER(edo_civil) LIKE '%casado%') as casados,
    SUM(discapacidad IS NOT NULL AND discapacidad != '' AND LOWER(discapacidad) NOT IN ('ninguna', 'no especificado', 'no', '0', 'sin discapacidad')) as discapacidad,
    SUM(etnia IS NOT NULL AND etnia != '' AND LOWER(etnia) NOT IN ('ninguna', 'no especificado', 'no', '0')) as etnia,
    SUM(LOWER(inscrito_por) = 'opsu' OR asignado_opsu = 'Sí') as opsu,
    SUM(LOWER(sede) LIKE '%puerto cabello%') as sede_pc,
    SUM(LOWER(sede) LIKE '%coef%') as sede_coef
FROM users WHERE estudiante = 1");

$stats = $resStats ? $resStats->fetch_assoc() : [];

$resEgresados = $db->query("SELECT COUNT(DISTINCT u.id) as total_egresados
FROM users u
LEFT JOIN graduados g ON u.id = g.id_usuario
WHERE u.estudiante = 1 
  AND ((g.id IS NOT NULL AND g.estado IN ('graduado', 'titulo_entregado')) 
       OR (u.fecha_graduacion IS NOT NULL AND u.fecha_graduacion != '0000-00-00' AND u.fecha_graduacion != ''))");
$totalEgresados = $resEgresados ? (int)$resEgresados->fetch_assoc()['total_egresados'] : 0;

$totalEstudiantes = (int)($stats['total'] ?? 0);
$totalEstudiantesSolo = max(0, $totalEstudiantes - $totalEgresados);
$activos = (int)($stats['activos'] ?? 0);
$inactivos = (int)($stats['inactivos'] ?? 0);
$nuevosIngresos = (int)($stats['nuevos'] ?? 0);
$embarazadas = (int)($stats['embarazadas'] ?? 0);
$menores = (int)($stats['menores'] ?? 0);
$mayores = (int)($stats['mayores'] ?? 0);
$masculinos = (int)($stats['masc'] ?? 0);
$femeninos = (int)($stats['fem'] ?? 0);
$solteros = (int)($stats['solteros'] ?? 0);
$casados = (int)($stats['casados'] ?? 0);
$conDiscapacidad = (int)($stats['discapacidad'] ?? 0);
$conEtnia = (int)($stats['etnia'] ?? 0);
$asignadosOpsu = (int)($stats['opsu'] ?? 0);
$sedePC = (int)($stats['sede_pc'] ?? 0);
$sedeCOEF = (int)($stats['sede_coef'] ?? 0);
$edadPromedio = !empty($stats['edad_promedio']) ? round((float)$stats['edad_promedio'], 1) : 0;

include("includes/head.php");
?>

<style>
.pagination-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    margin-bottom: 15px;
    gap: 10px;
}
.records-selector {
    display: flex;
    align-items: center;
    gap: 8px;
}
.records-selector select {
    width: auto;
    padding: 4px 8px;
}
.pagination {
    margin: 0;
    flex-wrap: wrap;
}
.page-link {
    cursor: pointer;
}
@media (max-width: 768px) {
    .pagination-bar {
        flex-direction: column;
        text-align: center;
    }
}
</style>

<div class="container-fluid px-2 px-sm-3 px-md-4">
    <div class="py-3 py-sm-4">
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white d-flex flex-column flex-sm-row justify-content-between align-items-center">
                        <h5 class="mb-2 mb-sm-0"><i class="fas fa-users me-2"></i>Listado de Estudiantes</h5>
                        <div>
                            <button type="button" id="toggleEstadisticas" class="btn btn-outline-light btn-sm mb-1 mb-sm-0 me-2" onclick="toggleEstadisticas()">
                                <i class="fas fa-chart-bar"></i> Estadísticas
                            </button>
                            <button type="button" id="toggleFiltros" class="btn btn-outline-light btn-sm mb-1 mb-sm-0 me-2">
                                <i class="fas fa-filter"></i> <span id="toggleFiltrosText">Ocultar Filtros</span>
                            </button>
                            <button type="button" id="btnReporteEstudiantes" class="btn btn-danger btn-sm mb-1 mb-sm-0 me-2" title="Generar Reporte Oficial de Estudiantes">
                                <i class="fas fa-file-pdf me-1"></i> Lista de Estudiantes
                            </button>
                            <button type="button" id="btnReporteEgresados" class="btn btn-dark btn-sm mb-1 mb-sm-0 me-2" style="background-color: #3b093d; border-color: #2b062d;" title="Generar Reporte Oficial de Egresados y Graduados">
                                <i class="fas fa-graduation-cap me-1"></i> Lista de Egresados
                            </button>
                            <?php if (tienePermiso('agregar_estudiante')): ?>
                                <button type="button" class="btn btn-success btn-sm mb-1 mb-sm-0" onclick="abrirModalNuevoEstudiante()">
                                    <i class="fas fa-plus-circle"></i> Nuevo Estudiante
                                </button>
                            <?php endif; ?>
                            <a href="index.php" class="btn btn-outline-light btn-sm">
                                <i class="fas fa-arrow-left"></i> Regresar
                            </a>
                        </div>
                    </div>
                    <div class="card-body p-2 p-sm-3">
                        <div id="estadisticas-row" class="row mb-4">
                            <!-- Total Estudiantes -->
                            <div class="col-6 col-sm-4 col-md-3 col-xl-2 mb-3">
                                <div class="card bg-primary text-white h-100 shadow-sm border-0">
                                    <div class="card-body text-center p-2">
                                        <i class="fas fa-users fa-lg mb-1"></i>
                                        <h4 class="card-title mb-0" id="statTotalEstudiantes"><?php echo $totalEstudiantes; ?></h4>
                                        <small class="card-text text-white-50">Total Estudiantes</small>
                                    </div>
                                </div>
                            </div>
                            <!-- Activos -->
                            <div class="col-6 col-sm-4 col-md-3 col-xl-2 mb-3">
                                <div class="card bg-success text-white h-100 shadow-sm border-0">
                                    <div class="card-body text-center p-2">
                                        <i class="fas fa-user-check fa-lg mb-1"></i>
                                        <h4 class="card-title mb-0" id="statActivos"><?php echo $activos; ?></h4>
                                        <small class="card-text text-white-50">Activos</small>
                                    </div>
                                </div>
                            </div>
                            <!-- Inactivos -->
                            <div class="col-6 col-sm-4 col-md-3 col-xl-2 mb-3">
                                <div class="card bg-secondary text-white h-100 shadow-sm border-0">
                                    <div class="card-body text-center p-2">
                                        <i class="fas fa-user-times fa-lg mb-1"></i>
                                        <h4 class="card-title mb-0" id="statInactivos"><?php echo $inactivos; ?></h4>
                                        <small class="card-text text-white-50">Inactivos</small>
                                    </div>
                                </div>
                            </div>
                            <!-- Nuevos Ingresos -->
                            <div class="col-6 col-sm-4 col-md-3 col-xl-2 mb-3">
                                <div class="card bg-info text-white h-100 shadow-sm border-0">
                                    <div class="card-body text-center p-2">
                                        <i class="fas fa-user-plus fa-lg mb-1"></i>
                                        <h4 class="card-title mb-0" id="statNuevosIngresos"><?php echo $nuevosIngresos; ?></h4>
                                        <small class="card-text text-white-50">Ingreso <?php echo $anioActual; ?></small>
                                    </div>
                                </div>
                            </div>
                            <!-- Masculinos -->
                            <div class="col-6 col-sm-4 col-md-3 col-xl-2 mb-3">
                                <div class="card bg-danger text-white h-100 shadow-sm border-0">
                                    <div class="card-body text-center p-2">
                                        <i class="fas fa-mars fa-lg mb-1"></i>
                                        <h4 class="card-title mb-0" id="statMasculinos"><?php echo $masculinos; ?></h4>
                                        <small class="card-text text-white-50">Masculinos</small>
                                    </div>
                                </div>
                            </div>
                            <!-- Femeninos -->
                            <div class="col-6 col-sm-4 col-md-3 col-xl-2 mb-3">
                                <div class="card text-white h-100 shadow-sm border-0" style="background-color: #6f42c1;">
                                    <div class="card-body text-center p-2">
                                        <i class="fas fa-venus fa-lg mb-1"></i>
                                        <h4 class="card-title mb-0" id="statFemeninos"><?php echo $femeninos; ?></h4>
                                        <small class="card-text text-white-50">Femeninos</small>
                                    </div>
                                </div>
                            </div>
                            <!-- Menores de Edad -->
                            <div class="col-6 col-sm-4 col-md-3 col-xl-2 mb-3">
                                <div class="card bg-warning text-dark h-100 shadow-sm border-0">
                                    <div class="card-body text-center p-2">
                                        <i class="fas fa-child fa-lg mb-1"></i>
                                        <h4 class="card-title mb-0" id="statMenores"><?php echo $menores; ?></h4>
                                        <small class="card-text text-muted">Menores (&lt;18)</small>
                                    </div>
                                </div>
                            </div>
                            <!-- Mayores de Edad -->
                            <div class="col-6 col-sm-4 col-md-3 col-xl-2 mb-3">
                                <div class="card bg-dark text-white h-100 shadow-sm border-0">
                                    <div class="card-body text-center p-2">
                                        <i class="fas fa-user-graduate fa-lg mb-1"></i>
                                        <h4 class="card-title mb-0" id="statMayores"><?php echo $mayores; ?></h4>
                                        <small class="card-text text-white-50">Mayores (&ge;18)</small>
                                    </div>
                                </div>
                            </div>
                            <!-- Edad Promedio -->
                            <div class="col-6 col-sm-4 col-md-3 col-xl-2 mb-3">
                                <div class="card text-white h-100 shadow-sm border-0" style="background-color: #fd7e14;">
                                    <div class="card-body text-center p-2">
                                        <i class="fas fa-birthday-cake fa-lg mb-1"></i>
                                        <h4 class="card-title mb-0" id="statEdadPromedio"><?php echo $edadPromedio > 0 ? $edadPromedio . ' años' : '-'; ?></h4>
                                        <small class="card-text text-white-50">Edad Promedio</small>
                                    </div>
                                </div>
                            </div>
                            <!-- Mujeres Embarazadas -->
                            <div class="col-6 col-sm-4 col-md-3 col-xl-2 mb-3">
                                <div class="card text-white h-100 shadow-sm border-0" style="background-color: #e83e8c;">
                                    <div class="card-body text-center p-2">
                                        <i class="fas fa-baby-carriage fa-lg mb-1"></i>
                                        <h4 class="card-title mb-0" id="statEmbarazadas"><?php echo $embarazadas; ?></h4>
                                        <small class="card-text text-white-50">Embarazadas</small>
                                    </div>
                                </div>
                            </div>
                            <!-- Con Discapacidad -->
                            <div class="col-6 col-sm-4 col-md-3 col-xl-2 mb-3">
                                <div class="card text-white h-100 shadow-sm border-0" style="background-color: #17a2b8;">
                                    <div class="card-body text-center p-2">
                                        <i class="fas fa-wheelchair fa-lg mb-1"></i>
                                        <h4 class="card-title mb-0" id="statDiscapacidad"><?php echo $conDiscapacidad; ?></h4>
                                        <small class="card-text text-white-50">Discapacidad</small>
                                    </div>
                                </div>
                            </div>
                            <!-- Pueblos Indígenas / Etnia -->
                            <div class="col-6 col-sm-4 col-md-3 col-xl-2 mb-3">
                                <div class="card text-white h-100 shadow-sm border-0" style="background-color: #20c997;">
                                    <div class="card-body text-center p-2">
                                        <i class="fas fa-feather-alt fa-lg mb-1"></i>
                                        <h4 class="card-title mb-0" id="statEtnia"><?php echo $conEtnia; ?></h4>
                                        <small class="card-text text-white-50">Etnia Indígena</small>
                                    </div>
                                </div>
                            </div>
                            <!-- Asignados OPSU -->
                            <div class="col-6 col-sm-4 col-md-3 col-xl-2 mb-3">
                                <div class="card text-white h-100 shadow-sm border-0" style="background-color: #0056b3;">
                                    <div class="card-body text-center p-2">
                                        <i class="fas fa-university fa-lg mb-1"></i>
                                        <h4 class="card-title mb-0" id="statOpsu"><?php echo $asignadosOpsu; ?></h4>
                                        <small class="card-text text-white-50">Vía OPSU</small>
                                    </div>
                                </div>
                            </div>
                            <!-- Sede Puerto Cabello -->
                            <div class="col-6 col-sm-4 col-md-3 col-xl-2 mb-3">
                                <div class="card text-white h-100 shadow-sm border-0" style="background-color: #495057;">
                                    <div class="card-body text-center p-2">
                                        <i class="fas fa-anchor fa-lg mb-1"></i>
                                        <h4 class="card-title mb-0" id="statSedePC"><?php echo $sedePC; ?></h4>
                                        <small class="card-text text-white-50">Sede Pto. Cabello</small>
                                    </div>
                                </div>
                            </div>
                            <!-- Sede COEF -->
                            <div class="col-6 col-sm-4 col-md-3 col-xl-2 mb-3">
                                <div class="card text-white h-100 shadow-sm border-0" style="background-color: #343a40;">
                                    <div class="card-body text-center p-2">
                                        <i class="fas fa-building fa-lg mb-1"></i>
                                        <h4 class="card-title mb-0" id="statSedeCOEF"><?php echo $sedeCOEF; ?></h4>
                                        <small class="card-text text-white-50">Sede COEF</small>
                                    </div>
                                </div>
                            </div>
                            <!-- Egresados / Graduados -->
                            <div class="col-6 col-sm-4 col-md-3 col-xl-2 mb-3">
                                <div class="card text-white h-100 shadow-sm border-0" style="background-color: #3b093d;">
                                    <div class="card-body text-center p-2">
                                        <i class="fas fa-graduation-cap fa-lg mb-1"></i>
                                        <h4 class="card-title mb-0" id="statEgresados"><?php echo $totalEgresados; ?></h4>
                                        <small class="card-text text-white-50">Egresados</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="filtrosContainer" class="row mb-4">
                            <div class="col-12">
                                <div class="card border-primary">
                                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0"><i class="fas fa-filter me-2"></i>Filtros Avanzados</h6>
                                        <button type="button" id="btnOcultarFiltros" class="btn btn-sm btn-outline-light">
                                            <i class="fas fa-chevron-up"></i> Ocultar
                                        </button>
                                    </div>
                                    <div class="card-body" id="filtrosBody">
                                        <div class="row">
                                            <!-- Carrera -->
                                            <div class="col-md-3 mb-3">
                                                <div class="card h-100">
                                                    <div class="card-header bg-light"><strong><i class="fas fa-graduation-cap text-primary me-1"></i> Carrera</strong></div>
                                                    <div class="card-body" style="max-height: 220px; overflow-y: auto;">
                                                        <div class="form-check">
                                                            <input class="form-check-input filtro-carrera" type="checkbox" value="todas" id="filtroTodasCarreras" checked>
                                                            <label class="form-check-label text-primary" for="filtroTodasCarreras"><strong>Seleccionar Todas</strong></label>
                                                        </div>
                                                        <hr class="my-2">
                                                        <?php foreach ($carrerasUnicas as $carrera): ?>
                                                            <?php if (!empty($carrera)): ?>
                                                            <div class="form-check">
                                                                <input class="form-check-input filtro-carrera" type="checkbox" value="<?php echo htmlspecialchars($carrera); ?>" id="filtroCarrera_<?php echo md5($carrera); ?>">
                                                                <label class="form-check-label" for="filtroCarrera_<?php echo md5($carrera); ?>"><?php echo htmlspecialchars($carrera); ?></label>
                                                            </div>
                                                            <?php endif; ?>
                                                        <?php endforeach; ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Modalidad / Inscrito por -->
                                            <div class="col-md-3 mb-3">
                                                <div class="card h-100">
                                                    <div class="card-header bg-light"><strong><i class="fas fa-clipboard-check text-primary me-1"></i> Inscrito por (Modalidad)</strong></div>
                                                    <div class="card-body" style="max-height: 220px; overflow-y: auto;">
                                                        <div class="form-check">
                                                            <input class="form-check-input filtro-modalidad-master" type="checkbox" value="todas" id="filtroTodasModalidades" checked>
                                                            <label class="form-check-label text-primary" for="filtroTodasModalidades"><strong>Seleccionar Todas</strong></label>
                                                        </div>
                                                        <hr class="my-2">
                                                        <?php foreach ($modalidadesUnicas as $mod): ?>
                                                            <div class="form-check">
                                                                <input class="form-check-input filtro-modalidad" type="checkbox" value="<?php echo htmlspecialchars($mod); ?>" id="filtroMod_<?php echo md5($mod); ?>">
                                                                <label class="form-check-label" for="filtroMod_<?php echo md5($mod); ?>"><?php echo htmlspecialchars($mod); ?></label>
                                                            </div>
                                                        <?php endforeach; ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Estado / Status y Condición -->
                                            <div class="col-md-3 mb-3">
                                                <div class="card h-100">
                                                    <div class="card-header bg-light"><strong><i class="fas fa-user-check text-primary me-1"></i> Estatus y Condición</strong></div>
                                                    <div class="card-body">
                                                        <div class="form-check">
                                                            <input class="form-check-input filtro-status-master" type="checkbox" value="todos" id="filtroTodosStatus" checked>
                                                            <label class="form-check-label text-primary" for="filtroTodosStatus"><strong>Todos los Estatus</strong></label>
                                                        </div>
                                                        <hr class="my-2">
                                                        <div class="form-check"><input class="form-check-input filtro-status" type="checkbox" value="activo" id="filtroActivo"><label class="form-check-label" for="filtroActivo">Activo</label></div>
                                                        <div class="form-check"><input class="form-check-input filtro-status" type="checkbox" value="inactivo" id="filtroInactivo"><label class="form-check-label" for="filtroInactivo">Inactivo</label></div>
                                                        <hr class="my-2">
                                                        <label class="small text-muted font-weight-bold d-block">Condición:</label>
                                                        <div class="form-check"><input class="form-check-input filtro-tipo-alumno" type="checkbox" value="estudiante" id="filtroTipoEstudiante" checked><label class="form-check-label" for="filtroTipoEstudiante"><i class="fas fa-user-graduate text-primary me-1"></i> Estudiantes</label></div>
                                                        <div class="form-check"><input class="form-check-input filtro-tipo-alumno" type="checkbox" value="egresado" id="filtroTipoEgresado" checked><label class="form-check-label" for="filtroTipoEgresado"><i class="fas fa-graduation-cap text-success me-1"></i> Egresados</label></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Género -->
                                            <div class="col-md-3 mb-3">
                                                <div class="card h-100">
                                                    <div class="card-header bg-light"><strong><i class="fas fa-venus-mars text-primary me-1"></i> Género</strong></div>
                                                    <div class="card-body">
                                                        <div class="form-check">
                                                            <input class="form-check-input filtro-genero-master" type="checkbox" value="todos" id="filtroTodosGeneros" checked>
                                                            <label class="form-check-label text-primary" for="filtroTodosGeneros"><strong>Seleccionar Todos</strong></label>
                                                        </div>
                                                        <hr class="my-2">
                                                        <div class="form-check"><input class="form-check-input filtro-genero" type="checkbox" value="masculino" id="filtroMasculino"><label class="form-check-label" for="filtroMasculino">Masculino</label></div>
                                                        <div class="form-check"><input class="form-check-input filtro-genero" type="checkbox" value="femenino" id="filtroFemenino"><label class="form-check-label" for="filtroFemenino">Femenino</label></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mt-2">
                                            <!-- Sede y Turno -->
                                            <div class="col-md-3 mb-3">
                                                <div class="card h-100">
                                                    <div class="card-header bg-light"><strong><i class="fas fa-building text-primary me-1"></i> Sede y Turno</strong></div>
                                                    <div class="card-body">
                                                        <div class="form-check">
                                                            <input class="form-check-input filtro-sede-master" type="checkbox" value="todas" id="filtroTodasSedes" checked>
                                                            <label class="form-check-label text-primary" for="filtroTodasSedes"><strong>Todas las Sedes</strong></label>
                                                        </div>
                                                        <div class="form-check">
                                                            <input class="form-check-input filtro-sede" type="checkbox" value="puerto cabello" id="filtroSedePuertoCabello">
                                                            <label class="form-check-label" for="filtroSedePuertoCabello">Puerto Cabello</label>
                                                        </div>
                                                        <div class="form-check">
                                                            <input class="form-check-input filtro-sede" type="checkbox" value="coef" id="filtroSedeCOEF">
                                                            <label class="form-check-label" for="filtroSedeCOEF">COEF</label>
                                                        </div>
                                                        <hr class="my-2">
                                                        <div class="form-check">
                                                            <input class="form-check-input filtro-turno-master" type="checkbox" value="todos" id="filtroTodosTurnos" checked>
                                                            <label class="form-check-label text-primary" for="filtroTodosTurnos"><strong>Todos los Turnos</strong></label>
                                                        </div>
                                                        <?php foreach ($turnosUnicos as $tur): ?>
                                                            <div class="form-check">
                                                                <input class="form-check-input filtro-turno" type="checkbox" value="<?php echo htmlspecialchars(strtolower($tur)); ?>" id="filtroTurno_<?php echo md5($tur); ?>">
                                                                <label class="form-check-label" for="filtroTurno_<?php echo md5($tur); ?>"><?php echo htmlspecialchars($tur); ?></label>
                                                            </div>
                                                        <?php endforeach; ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Estado Geográfico -->
                                            <div class="col-md-3 mb-3">
                                                <div class="card h-100">
                                                    <div class="card-header bg-light"><strong><i class="fas fa-map-marked-alt text-primary me-1"></i> Estado Geográfico</strong></div>
                                                    <div class="card-body" style="max-height: 220px; overflow-y: auto;">
                                                        <div class="form-check">
                                                            <input class="form-check-input filtro-estado-geo-master" type="checkbox" value="todos" id="filtroTodosEstadosGeo" checked>
                                                            <label class="form-check-label text-primary" for="filtroTodosEstadosGeo"><strong>Seleccionar Todos</strong></label>
                                                        </div>
                                                        <hr class="my-2">
                                                        <?php foreach ($estadosUnicos as $estGeo): ?>
                                                            <div class="form-check">
                                                                <input class="form-check-input filtro-estado-geo" type="checkbox" value="<?php echo htmlspecialchars($estGeo); ?>" id="filtroEstGeo_<?php echo md5($estGeo); ?>">
                                                                <label class="form-check-label" for="filtroEstGeo_<?php echo md5($estGeo); ?>"><?php echo htmlspecialchars($estGeo); ?></label>
                                                            </div>
                                                        <?php endforeach; ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Ciudad -->
                                            <div class="col-md-3 mb-3">
                                                <div class="card h-100">
                                                    <div class="card-header bg-light"><strong><i class="fas fa-city text-primary me-1"></i> Ciudad</strong></div>
                                                    <div class="card-body" style="max-height: 220px; overflow-y: auto;">
                                                        <div class="form-check">
                                                            <input class="form-check-input filtro-ciudad-master" type="checkbox" value="todas" id="filtroTodasCiudades" checked>
                                                            <label class="form-check-label text-primary" for="filtroTodasCiudades"><strong>Seleccionar Todas</strong></label>
                                                        </div>
                                                        <hr class="my-2">
                                                        <?php foreach ($ciudades as $ciudad): ?>
                                                            <?php if (!empty($ciudad)): ?>
                                                            <div class="form-check">
                                                                <input class="form-check-input filtro-ciudad" type="checkbox" value="<?php echo htmlspecialchars($ciudad); ?>" id="filtroCiudad_<?php echo md5($ciudad); ?>">
                                                                <label class="form-check-label" for="filtroCiudad_<?php echo md5($ciudad); ?>"><?php echo htmlspecialchars($ciudad); ?></label>
                                                            </div>
                                                            <?php endif; ?>
                                                        <?php endforeach; ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Inclusión y Condición Especial -->
                                            <div class="col-md-3 mb-3">
                                                <div class="card h-100">
                                                    <div class="card-header bg-light"><strong><i class="fas fa-universal-access text-primary me-1"></i> Inclusión & Salud</strong></div>
                                                    <div class="card-body">
                                                        <div class="form-check">
                                                            <input class="form-check-input filtro-condicion-master" type="checkbox" value="todas" id="filtroTodasCondiciones" checked>
                                                            <label class="form-check-label text-primary" for="filtroTodasCondiciones"><strong>Sin Filtro Especial</strong></label>
                                                        </div>
                                                        <hr class="my-2">
                                                        <label class="small text-muted font-weight-bold d-block">Discapacidad:</label>
                                                        <div class="form-check"><input class="form-check-input filtro-condicion" type="checkbox" value="con_discapacidad" id="filtroConDiscapacidad"><label class="form-check-label" for="filtroConDiscapacidad">Con Discapacidad</label></div>
                                                        <div class="form-check"><input class="form-check-input filtro-condicion" type="checkbox" value="sin_discapacidad" id="filtroSinDiscapacidad"><label class="form-check-label" for="filtroSinDiscapacidad">Sin Discapacidad</label></div>
                                                        <hr class="my-2">
                                                        <label class="small text-muted font-weight-bold d-block">Pueblos Indígenas / Etnia:</label>
                                                        <div class="form-check"><input class="form-check-input filtro-condicion" type="checkbox" value="con_etnia" id="filtroConEtnia"><label class="form-check-label" for="filtroConEtnia">Con Etnia Indígena</label></div>
                                                        <div class="form-check"><input class="form-check-input filtro-condicion" type="checkbox" value="sin_etnia" id="filtroSinEtnia"><label class="form-check-label" for="filtroSinEtnia">Sin Etnia</label></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mt-2">
                                            <!-- Estado Civil -->
                                            <div class="col-md-3 mb-3">
                                                <div class="card h-100">
                                                    <div class="card-header bg-light"><strong><i class="fas fa-heart text-primary me-1"></i> Estado Civil</strong></div>
                                                    <div class="card-body">
                                                        <div class="form-check">
                                                            <input class="form-check-input filtro-edocivil-master" type="checkbox" value="todos" id="filtroTodosEdoCivil" checked>
                                                            <label class="form-check-label text-primary" for="filtroTodosEdoCivil"><strong>Seleccionar Todos</strong></label>
                                                        </div>
                                                        <hr class="my-2">
                                                        <div class="form-check"><input class="form-check-input filtro-edocivil" type="checkbox" value="soltero" id="filtroSoltero"><label class="form-check-label" for="filtroSoltero">Soltero/a</label></div>
                                                        <div class="form-check"><input class="form-check-input filtro-edocivil" type="checkbox" value="casado" id="filtroCasado"><label class="form-check-label" for="filtroCasado">Casado/a</label></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Embarazo -->
                                            <div class="col-md-3 mb-3">
                                                <div class="card h-100">
                                                    <div class="card-header bg-light"><strong><i class="fas fa-baby-carriage text-primary me-1"></i> Embarazo</strong></div>
                                                    <div class="card-body">
                                                        <div class="form-check"><input class="form-check-input filtro" type="checkbox" value="embarazada" id="filtroEmbarazada"><label class="form-check-label" for="filtroEmbarazada">Solo Mujeres Embarazadas</label></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Edad -->
                                            <div class="col-md-3 mb-3">
                                                <div class="card h-100">
                                                    <div class="card-header bg-light"><strong><i class="fas fa-child text-primary me-1"></i> Edad</strong></div>
                                                    <div class="card-body">
                                                        <div class="form-check">
                                                            <input class="form-check-input filtro-edad-master" type="checkbox" value="todas" id="filtroTodasEdades" checked>
                                                            <label class="form-check-label text-primary" for="filtroTodasEdades"><strong>Seleccionar Todas</strong></label>
                                                        </div>
                                                        <hr class="my-2">
                                                        <div class="form-check"><input class="form-check-input filtro-edad" type="checkbox" value="menor" id="filtroMenor" checked><label class="form-check-label" for="filtroMenor">Menores de 18 años</label></div>
                                                        <div class="form-check"><input class="form-check-input filtro-edad" type="checkbox" value="mayor" id="filtroMayor" checked><label class="form-check-label" for="filtroMayor">Mayores de 18 años</label></div>
                                                        <div class="row mt-2"><div class="col-6"><input type="number" class="form-control form-control-sm" id="edadMin" placeholder="Edad mín"></div><div class="col-6"><input type="number" class="form-control form-control-sm" id="edadMax" placeholder="Edad máx"></div></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Fecha Ingreso -->
                                            <div class="col-md-3 mb-3">
                                                <div class="card h-100">
                                                    <div class="card-header bg-light"><strong><i class="fas fa-calendar-alt text-primary me-1"></i> Fecha Ingreso</strong></div>
                                                    <div class="card-body">
                                                        <div class="row">
                                                            <div class="col-6"><label class="small">Desde</label><input type="date" class="form-control form-control-sm" id="fechaIngresoDesde"></div>
                                                            <div class="col-6"><label class="small">Hasta</label><input type="date" class="form-control form-control-sm" id="fechaIngresoHasta"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- BARRA DE BÚSQUEDA GENERAL (Ubicada DEBAJO de los Filtros y ENCIMA de la Tabla) -->
                        <div id="buscadorContainer" class="row mb-3">
                            <div class="col-12">
                                <div class="input-group input-group-lg shadow-sm">
                                    <span class="input-group-text bg-primary text-white"><i class="fas fa-search"></i></span>
                                    <input type="text" id="buscadorCedula" class="form-control" 
                                           placeholder="Escriba aquí para buscar por Nombre, Apellidos, Cédula, Correo o Usuario..." autocomplete="off">
                                    <button type="button" id="limpiarBusqueda" class="btn btn-secondary">
                                        <i class="fas fa-times me-1"></i> Limpiar filtros
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- SELECTOR / FILTRO RÁPIDO: ESTUDIANTES VS EGRESADOS -->
                        <div class="row mb-3">
                            <div class="col-12">
                                <div class="d-flex flex-wrap justify-content-between align-items-center p-2 bg-light rounded border shadow-sm">
                                    <div class="d-flex align-items-center flex-wrap gap-2">
                                        <span class="text-muted small font-weight-bold"><i class="fas fa-filter text-primary me-1"></i> Filtrar por condición:</span>
                                        <div class="btn-group btn-group-sm" role="group" id="btnGroupTipoAlumno">
                                            <input type="radio" class="btn-check" name="quickTipoAlumno" id="quickTipoTodos" value="todos" autocomplete="off" checked>
                                            <label class="btn btn-outline-primary" for="quickTipoTodos">
                                                <i class="fas fa-users me-1"></i> Todos (<span id="countTodos"><?php echo $totalEstudiantes; ?></span>)
                                            </label>

                                            <input type="radio" class="btn-check" name="quickTipoAlumno" id="quickTipoEstudiantes" value="estudiante" autocomplete="off">
                                            <label class="btn btn-outline-primary" for="quickTipoEstudiantes">
                                                <i class="fas fa-user-graduate me-1"></i> Lista de Estudiantes (<span id="countEstudiantes"><?php echo $totalEstudiantesSolo; ?></span>)
                                            </label>

                                            <input type="radio" class="btn-check" name="quickTipoAlumno" id="quickTipoEgresados" value="egresado" autocomplete="off">
                                            <label class="btn btn-outline-success" for="quickTipoEgresados">
                                                <i class="fas fa-graduation-cap me-1"></i> Lista de Egresados (<span id="countEgresados"><?php echo $totalEgresados; ?></span>)
                                            </label>
                                        </div>
                                    </div>
                                    <div class="small text-muted mt-2 mt-md-0">
                                        <span class="badge bg-primary me-1"><i class="fas fa-user-graduate"></i> Estudiante regular</span>
                                        <span class="badge bg-success"><i class="fas fa-graduation-cap"></i> Egresado / Graduado</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Barra de Paginación y Límite de Registros (Superior) -->
                        <div class="pagination-bar p-2 bg-light rounded border shadow-sm mb-3">
                            <div class="records-selector">
                                <label class="small font-weight-bold mb-0" for="registrosPorPagina">
                                    <i class="fas fa-list-ol text-primary me-1"></i> Mostrar:
                                </label>
                                <select id="registrosPorPagina" class="form-control form-control-sm font-weight-bold">
                                    <option value="10">10</option>
                                    <option value="20" selected>20</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                    <option value="999999">Todos</option>
                                </select>
                                <span class="small text-muted font-weight-bold">registros por página</span>
                            </div>
                            <div class="small text-muted font-weight-bold">
                                Mostrando <span id="mostrandoDesde" class="text-primary font-weight-bold">0</span> - <span id="mostrandoHasta" class="text-primary font-weight-bold">0</span> de <span id="totalRegistros" class="text-dark font-weight-bold"><?php echo $totalEstudiantes; ?></span> estudiantes
                            </div>
                            <nav aria-label="Navegación de páginas">
                                <ul class="pagination pagination-sm mb-0" id="paginationControls"></ul>
                            </nav>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-striped table-hover table-bordered" id="tablaEstudiantes">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Cédula</th>
                                        <th>Nombre</th>
                                        <th>Usuario</th>
                                        <th>Email</th>
                                        <th>Teléfono</th>
                                        <th>Carrera</th>
                                        <th>Condición</th>
                                        <th>Modalidad</th>
                                        <th>Turno</th>
                                        <th>Género</th>
                                        <th>Edad</th>
                                        <th>Estado Civil</th>
                                        <th>Estado</th>
                                        <th>Ciudad</th>
                                        <th>Sede</th>
                                        <th>Status</th>
                                        <th>Fecha Ingreso</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="tablaBody">
                                    <tr>
                                        <td colspan="18" class="text-center py-5 text-muted">
                                            <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                                            <strong>Cargando estudiantes...</strong>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modales -->
<div class="modal fade" id="reporteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <input type="hidden" id="reporteTipoSeleccionado" value="estudiantes">
            <div class="modal-header bg-danger text-white" id="reporteModalHeader">
                <h5 class="modal-title" id="reporteModalTitle"><i class="fas fa-file-pdf me-2"></i> Generar Reporte</h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info" id="reporteModalAlert">
                    <i class="fas fa-info-circle"></i> Se generará un reporte oficial en PDF con los registros actualmente filtrados.
                </div>
                <div class="form-group">
                    <label><i class="fas fa-chart-bar"></i> Incluir estadísticas y resumen</label>
                    <select id="incluirEstadisticas" class="form-control">
                        <option value="si">Sí (Recomendado)</option>
                        <option value="no">No (Solo listado tabular)</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" id="btnGenerarPDF" class="btn btn-danger">
                    <i class="fas fa-file-pdf"></i> Descargar Reporte PDF
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="detalleModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content" id="detalleEstudianteContent">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title font-weight-bold"><i class="fas fa-id-card mr-2"></i> Detalles del Estudiante</h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="text-center my-5"><div class="spinner-border text-primary"></div><p class="mt-2">Cargando datos...</p></div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="editarEstudianteModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content" id="editarEstudianteContent">
            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title font-weight-bold text-dark"><i class="fas fa-user-edit mr-2"></i> Editar Estudiante</h5>
                <button type="button" class="close text-dark" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="text-center my-5"><div class="spinner-border text-warning"></div><p class="mt-2">Cargando formulario...</p></div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="agregarEstudianteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-success text-white"><h5 class="modal-title"><i class="fas fa-user-plus me-2"></i> Agregar Nuevo Estudiante</h5><button type="button" class="close text-white" data-dismiss="modal">&times;</button></div>
            <div class="modal-body">
                <?php 
                $tiposCedula = obtenerTiposCedula($db);
                $estadosCiviles = obtenerEstadosCiviless($db);
                $tiposVivienda = obtenerTiposVivienda($db);
                $tenenciasVivienda = obtenerTenenciaViviendas($db);
                $opcionesStatus = obtenerOpcionesStatus($db);
                $carreras = obtenerTodasLasCarreras();
                $ingresos = obtenerIngresos($db);
                $esModal = true;
                ?>
                <div class="tab-content"><div class="tab-pane fade show active" id="individual"><form id="formEstudianteModal" method="post" enctype="multipart/form-data"><?php $esModal = true; include('_formulario_estudiante.php'); ?></form></div></div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="resultadoModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header" id="resultadoModalHeader"><h5 class="modal-title">Resultado</h5><button type="button" class="close" data-dismiss="modal">&times;</button></div>
            <div class="modal-body" id="resultadoModalBody"></div>
            <div class="modal-footer"><button type="button" class="btn btn-primary" data-dismiss="modal">Aceptar</button></div>
        </div>
    </div>
</div>

<style>
.close { font-size: 1.5rem; font-weight: bold; opacity: 0.8; padding: 0.5rem; line-height: 1; background: transparent; border: none; cursor: pointer; }
.close:hover { opacity: 1; }
.modal-header { position: relative; display: flex; justify-content: space-between; align-items: center; }
.modal-content { border-radius: 0.5rem; border: none; box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15); }
.bg-purple { background-color: #6f42c1 !important; }
.filtrosHidden .card-body { display: none !important; }
@media (max-width: 767.98px) {
    .card-header h5 { font-size: 1rem; }
    .btn-sm { padding: 0.25rem 0.5rem; font-size: 0.7rem; }
    .badge { font-size: 0.65rem; padding: 0.2rem 0.4rem; }
    .table td, .table th { font-size: 0.75rem; padding: 0.4rem; vertical-align: middle; }
    .modal-body { padding: 0.75rem; }
    .d-flex.flex-wrap { gap: 0.25rem !important; }
}
</style>

<script>
let filtrosOcultos = false;
let paginaActual = 1;
let registrosPorPagina = 20;
let debounceTimeout = null;
let currentAbortController = null;

document.addEventListener('DOMContentLoaded', function() {
    cargarEstadoGuardado();
    configurarFiltros();
    
    const selectorRegistros = document.getElementById('registrosPorPagina');
    if (selectorRegistros) {
        selectorRegistros.addEventListener('change', function() {
            registrosPorPagina = parseInt(this.value);
            cargarEstudiantes(1);
        });
    }
    cargarEstudiantes(1);
});

function setupMasterToggle(masterId, childSelector) {
    const master = document.getElementById(masterId);
    if (!master) return;
    master.addEventListener('change', function() {
        const checkAll = this.checked;
        document.querySelectorAll(childSelector).forEach(cb => {
            if (cb !== master) {
                cb.checked = checkAll;
                cb.disabled = checkAll;
            }
        });
        cargarEstudiantes(1);
    });
    if (master.checked) {
        document.querySelectorAll(childSelector).forEach(cb => {
            if (cb !== master) {
                cb.checked = true;
                cb.disabled = true;
            }
        });
    }
}

function configurarFiltros() {
    setupMasterToggle('filtroTodasCarreras', '.filtro-carrera');
    setupMasterToggle('filtroTodasModalidades', '.filtro-modalidad');
    setupMasterToggle('filtroTodosGeneros', '.filtro-genero');
    setupMasterToggle('filtroTodosEdoCivil', '.filtro-edocivil');
    setupMasterToggle('filtroTodosStatus', '.filtro-status');
    setupMasterToggle('filtroTodasSedes', '.filtro-sede');
    setupMasterToggle('filtroTodosTurnos', '.filtro-turno');
    setupMasterToggle('filtroTodosEstadosGeo', '.filtro-estado-geo');
    setupMasterToggle('filtroTodasCiudades', '.filtro-ciudad');
    setupMasterToggle('filtroTodasCondiciones', '.filtro-condicion');
    setupMasterToggle('filtroTodasEdades', '.filtro-edad');

    document.querySelectorAll('.filtro-carrera, .filtro-modalidad, .filtro-ciudad, .filtro-sede, .filtro-turno, .filtro-estado-geo, .filtro-condicion, .filtro-genero, .filtro-edocivil, .filtro-status, .filtro-edad, .filtro').forEach(cb => {
        cb.addEventListener('change', function() {
            cargarEstudiantes(1);
        });
    });

    // Filtros de Condición: Estudiante vs Egresado
    document.querySelectorAll('.filtro-tipo-alumno').forEach(cb => {
        cb.addEventListener('change', function() {
            const estCheck = document.getElementById('filtroTipoEstudiante')?.checked;
            const egrCheck = document.getElementById('filtroTipoEgresado')?.checked;
            const rTodos = document.getElementById('quickTipoTodos');
            const rEst = document.getElementById('quickTipoEstudiantes');
            const rEgr = document.getElementById('quickTipoEgresados');
            if (estCheck && egrCheck) {
                if (rTodos) rTodos.checked = true;
            } else if (estCheck && !egrCheck) {
                if (rEst) rEst.checked = true;
            } else if (!estCheck && egrCheck) {
                if (rEgr) rEgr.checked = true;
            } else {
                if (rTodos) rTodos.checked = false;
                if (rEst) rEst.checked = false;
                if (rEgr) rEgr.checked = false;
            }
            cargarEstudiantes(1);
        });
    });

    // Selector rápido superior: Todos / Lista de Estudiantes / Lista de Egresados
    document.querySelectorAll('input[name="quickTipoAlumno"]').forEach(radio => {
        radio.addEventListener('change', function() {
            const cbEst = document.getElementById('filtroTipoEstudiante');
            const cbEgr = document.getElementById('filtroTipoEgresado');
            if (this.value === 'todos') {
                if (cbEst) cbEst.checked = true;
                if (cbEgr) cbEgr.checked = true;
            } else if (this.value === 'estudiante') {
                if (cbEst) cbEst.checked = true;
                if (cbEgr) cbEgr.checked = false;
            } else if (this.value === 'egresado') {
                if (cbEst) cbEst.checked = false;
                if (cbEgr) cbEgr.checked = true;
            }
            cargarEstudiantes(1);
        });
    });

    // Filtros numéricos y de fecha
    ['edadMin', 'edadMax'].forEach(id => {
        const el = document.getElementById(id);
        if (el) {
            el.addEventListener('input', function() {
                clearTimeout(debounceTimeout);
                debounceTimeout = setTimeout(() => cargarEstudiantes(1), 350);
            });
        }
    });

    ['fechaIngresoDesde', 'fechaIngresoHasta'].forEach(id => {
        const el = document.getElementById(id);
        if (el) {
            el.addEventListener('change', function() {
                cargarEstudiantes(1);
            });
        }
    });

    // Buscador textual en vivo con debounce de 300ms
    const buscador = document.getElementById('buscadorCedula');
    if (buscador) {
        buscador.addEventListener('input', function() {
            clearTimeout(debounceTimeout);
            debounceTimeout = setTimeout(() => {
                cargarEstudiantes(1);
            }, 300);
        });
    }
}

function obtenerFiltrosActivos() {
    const params = new URLSearchParams();

    // Texto de búsqueda
    const busqueda = document.getElementById('buscadorCedula')?.value.trim() || '';
    if (busqueda) params.append('busqueda', busqueda);

    // Tipo de Alumno (Radio rápido o checkboxes)
    const radioTipo = document.querySelector('input[name="quickTipoAlumno"]:checked')?.value;
    if (radioTipo) {
        params.append('tipo_alumno', radioTipo);
    } else {
        const estCheck = document.getElementById('filtroTipoEstudiante')?.checked;
        const egrCheck = document.getElementById('filtroTipoEgresado')?.checked;
        if (estCheck && !egrCheck) params.append('tipo_alumno', 'estudiante');
        else if (!estCheck && egrCheck) params.append('tipo_alumno', 'egresado');
        else params.append('tipo_alumno', 'todos');
    }

    // Carreras
    const todasCarreras = document.getElementById('filtroTodasCarreras')?.checked;
    if (!todasCarreras) {
        document.querySelectorAll('.filtro-carrera:checked').forEach(cb => {
            if (cb.value !== 'todas') params.append('carreras[]', cb.value);
        });
    }

    // Modalidades
    const todasModalidades = document.getElementById('filtroTodasModalidades')?.checked;
    if (!todasModalidades) {
        document.querySelectorAll('.filtro-modalidad:checked').forEach(cb => {
            if (cb.value !== 'todas') params.append('modalidades[]', cb.value);
        });
    }

    // Status
    const todosStatus = document.getElementById('filtroTodosStatus')?.checked;
    if (!todosStatus) {
        document.querySelectorAll('.filtro-status:checked').forEach(cb => {
            if (cb.value !== 'todos') params.append('status[]', cb.value);
        });
    }

    // Género
    const todosGeneros = document.getElementById('filtroTodosGeneros')?.checked;
    if (!todosGeneros) {
        document.querySelectorAll('.filtro-genero:checked').forEach(cb => {
            if (cb.value !== 'todos') params.append('generos[]', cb.value);
        });
    }

    // Sedes
    const todasSedes = document.getElementById('filtroTodasSedes')?.checked;
    if (!todasSedes) {
        document.querySelectorAll('.filtro-sede:checked').forEach(cb => {
            if (cb.value !== 'todas') params.append('sedes[]', cb.value);
        });
    }

    // Turnos
    const todosTurnos = document.getElementById('filtroTodosTurnos')?.checked;
    if (!todosTurnos) {
        document.querySelectorAll('.filtro-turno:checked').forEach(cb => {
            if (cb.value !== 'todos') params.append('turnos[]', cb.value);
        });
    }

    // Estados Geográficos
    const todosEstadosGeo = document.getElementById('filtroTodosEstadosGeo')?.checked;
    if (!todosEstadosGeo) {
        document.querySelectorAll('.filtro-estado-geo:checked').forEach(cb => {
            if (cb.value !== 'todos') params.append('estados_geo[]', cb.value);
        });
    }

    // Ciudades
    const todasCiudades = document.getElementById('filtroTodasCiudades')?.checked;
    if (!todasCiudades) {
        document.querySelectorAll('.filtro-ciudad:checked').forEach(cb => {
            if (cb.value !== 'todas') params.append('ciudades[]', cb.value);
        });
    }

    // Inclusión y condiciones de salud
    const todasCondiciones = document.getElementById('filtroTodasCondiciones')?.checked;
    if (!todasCondiciones) {
        document.querySelectorAll('.filtro-condicion:checked').forEach(cb => {
            if (cb.value !== 'todas') params.append('condiciones[]', cb.value);
        });
    }

    // Embarazo
    if (document.getElementById('filtroEmbarazada')?.checked) {
        params.append('condiciones[]', 'embarazada');
    }

    // Estado Civil
    const todosEdoCivil = document.getElementById('filtroTodosEdoCivil')?.checked;
    if (!todosEdoCivil) {
        document.querySelectorAll('.filtro-edocivil:checked').forEach(cb => {
            if (cb.value !== 'todos') params.append('edo_civil[]', cb.value);
        });
    }

    // Edades (menor / mayor / rango)
    const todasEdades = document.getElementById('filtroTodasEdades')?.checked;
    if (!todasEdades) {
        const esMenor = document.getElementById('filtroMenor')?.checked;
        const esMayor = document.getElementById('filtroMayor')?.checked;
        if (esMenor && !esMayor) params.append('condiciones[]', 'menor');
        else if (!esMenor && esMayor) params.append('condiciones[]', 'mayor');
    }
    const edadMin = document.getElementById('edadMin')?.value.trim();
    if (edadMin) params.append('edad_min', edadMin);
    const edadMax = document.getElementById('edadMax')?.value.trim();
    if (edadMax) params.append('edad_max', edadMax);

    // Fechas de ingreso
    const fDesde = document.getElementById('fechaIngresoDesde')?.value.trim();
    if (fDesde) params.append('fecha_desde', fDesde);
    const fHasta = document.getElementById('fechaIngresoHasta')?.value.trim();
    if (fHasta) params.append('fecha_hasta', fHasta);

    return params;
}

function cargarEstudiantes(nuevaPagina = 1) {
    if (currentAbortController) {
        currentAbortController.abort();
    }
    currentAbortController = new AbortController();

    paginaActual = nuevaPagina;
    const params = obtenerFiltrosActivos();
    params.append("action", "listar");
    params.append("page", paginaActual);
    params.append("limit", registrosPorPagina);

    const tbody = document.getElementById("tablaBody");
    tbody.innerHTML = `<tr><td colspan="18" class="text-center py-4 text-muted"><div class="spinner-border spinner-border-sm text-primary me-2"></div> Cargando registros...</td></tr>`;

    fetch("ajax_estudiantes.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: params.toString(),
        signal: currentAbortController.signal
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            tbody.innerHTML = data.html;
            document.getElementById("mostrandoDesde").textContent = data.desde;
            document.getElementById("mostrandoHasta").textContent = data.hasta;
            document.getElementById("totalRegistros").textContent = data.total;
            renderPaginacion(data.page, data.total_pages);
        } else {
            tbody.innerHTML = `<tr><td colspan="18" class="text-center py-4 text-danger"><i class="fas fa-exclamation-triangle me-1"></i> Error al cargar datos: ${data.error || "Error desconocido"}</td></tr>`;
        }
    })
    .catch(err => {
        if (err.name !== "AbortError") {
            tbody.innerHTML = `<tr><td colspan="18" class="text-center py-4 text-danger"><i class="fas fa-exclamation-triangle me-1"></i> Error de conexión al cargar datos.</td></tr>`;
        }
    });
}

function renderPaginacion(pagina, totalPaginas) {
    const container = document.getElementById("paginationControls");
    container.innerHTML = "";
    if (totalPaginas <= 1) return;

    // Botón Anterior
    const liPrev = document.createElement("li");
    liPrev.className = `page-item ${pagina === 1 ? "disabled" : ""}`;
    liPrev.innerHTML = `<a class="page-link" href="#"><i class="fas fa-chevron-left"></i> Anterior</a>`;
    if (pagina > 1) {
        liPrev.querySelector("a").addEventListener("click", function(e) {
            e.preventDefault();
            cargarEstudiantes(pagina - 1);
        });
    }
    container.appendChild(liPrev);

    // Números de páginas con elipsis
    let inicio = Math.max(1, pagina - 2);
    let fin = Math.min(totalPaginas, pagina + 2);

    if (inicio > 1) {
        container.appendChild(crearBotonPagina(1, pagina));
        if (inicio > 2) container.appendChild(crearPuntosPagina());
    }

    for (let i = inicio; i <= fin; i++) {
        container.appendChild(crearBotonPagina(i, pagina));
    }

    if (fin < totalPaginas) {
        if (fin < totalPaginas - 1) container.appendChild(crearPuntosPagina());
        container.appendChild(crearBotonPagina(totalPaginas, pagina));
    }

    // Botón Siguiente
    const liNext = document.createElement("li");
    liNext.className = `page-item ${pagina === totalPaginas ? "disabled" : ""}`;
    liNext.innerHTML = `<a class="page-link" href="#">Siguiente <i class="fas fa-chevron-right"></i></a>`;
    if (pagina < totalPaginas) {
        liNext.querySelector("a").addEventListener("click", function(e) {
            e.preventDefault();
            cargarEstudiantes(pagina + 1);
        });
    }
    container.appendChild(liNext);
}

function crearBotonPagina(num, pagActual) {
    const li = document.createElement("li");
    li.className = `page-item ${num === pagActual ? "active" : ""}`;
    li.innerHTML = `<a class="page-link" href="#">${num}</a>`;
    li.querySelector("a").addEventListener("click", function(e) {
        e.preventDefault();
        if (num !== pagActual) cargarEstudiantes(num);
    });
    return li;
}

function crearPuntosPagina() {
    const li = document.createElement("li");
    li.className = "page-item disabled";
    li.innerHTML = `<span class="page-link">...</span>`;
    return li;
}
function toggleFiltros() {
    const filtrosBody = document.getElementById('filtrosBody');
    const toggleText = document.getElementById('toggleFiltrosText');
    const btnOcultarFiltros = document.getElementById('btnOcultarFiltros');
    if (filtrosOcultos) {
        filtrosBody.style.display = 'block';
        filtrosOcultos = false;
        if (toggleText) toggleText.innerHTML = 'Ocultar Filtros';
        if (btnOcultarFiltros) btnOcultarFiltros.innerHTML = '<i class="fas fa-chevron-up"></i> Ocultar';
        localStorage.setItem('filtrosOcultos', 'false');
    } else {
        filtrosBody.style.display = 'none';
        filtrosOcultos = true;
        if (toggleText) toggleText.innerHTML = 'Mostrar Filtros';
        if (btnOcultarFiltros) btnOcultarFiltros.innerHTML = '<i class="fas fa-chevron-down"></i> Mostrar';
        localStorage.setItem('filtrosOcultos', 'true');
    }
}

function cargarEstadoGuardado() {
    const estadisticasOcultas = localStorage.getItem('estadisticasOcultas');
    if (estadisticasOcultas === 'true') {
        document.getElementById('estadisticas-row').style.display = 'none';
        document.getElementById('toggleEstadisticas').innerHTML = '<i class="fas fa-chart-bar"></i> Mostrar Estadísticas';
    }
    const filtrosGuardados = localStorage.getItem('filtrosOcultos');
    if (filtrosGuardados === 'true') {
        document.getElementById('filtrosBody').style.display = 'none';
        filtrosOcultos = true;
        document.getElementById('toggleFiltrosText').innerHTML = 'Mostrar Filtros';
        const btnOcultar = document.getElementById('btnOcultarFiltros');
        if (btnOcultar) btnOcultar.innerHTML = '<i class="fas fa-chevron-down"></i> Mostrar';
    }
}

window.toggleEstadisticas = function() {
    const row = document.getElementById('estadisticas-row');
    const button = document.getElementById('toggleEstadisticas');
    if (row.style.display === 'none') {
        row.style.display = 'flex';
        button.innerHTML = '<i class="fas fa-chart-bar"></i> Ocultar Estadísticas';
        localStorage.setItem('estadisticasOcultas', 'false');
    } else {
        row.style.display = 'none';
        button.innerHTML = '<i class="fas fa-chart-bar"></i> Mostrar Estadísticas';
        localStorage.setItem('estadisticasOcultas', 'true');
    }
};

function abrirModalReporte(tipo) {
    const inputTipo = document.getElementById("reporteTipoSeleccionado");
    if (inputTipo) inputTipo.value = tipo;

    const modalHeader = document.getElementById("reporteModalHeader");
    const modalTitle = document.getElementById("reporteModalTitle");
    const modalAlert = document.getElementById("reporteModalAlert");
    const btnPDF = document.getElementById("btnGenerarPDF");

    if (tipo === "egresados") {
        if (modalHeader) {
            modalHeader.className = "modal-header text-white";
            modalHeader.style.backgroundColor = "#3b093d";
        }
        if (modalTitle) modalTitle.innerHTML = "<i class=\"fas fa-graduation-cap me-2\"></i> Reporte Oficial de Egresados y Graduados";
        if (btnPDF) {
            btnPDF.className = "btn text-white";
            btnPDF.style.backgroundColor = "#3b093d";
            btnPDF.style.borderColor = "#2b062d";
            btnPDF.innerHTML = "<i class=\"fas fa-file-pdf me-1\"></i> Generar Reporte de Egresados PDF";
        }
        if (modalAlert) {
            modalAlert.className = "alert alert-info";
            modalAlert.innerHTML = "<i class=\"fas fa-info-circle me-1\"></i> Se generará el reporte oficial en PDF con los egresados y graduados seleccionados según los filtros activos.";
        }
    } else {
        if (modalHeader) {
            modalHeader.className = "modal-header bg-danger text-white";
            modalHeader.style.backgroundColor = "";
        }
        if (modalTitle) modalTitle.innerHTML = "<i class=\"fas fa-file-pdf me-2\"></i> Reporte Oficial de Estudiantes";
        if (btnPDF) {
            btnPDF.className = "btn btn-danger";
            btnPDF.style.backgroundColor = "";
            btnPDF.style.borderColor = "";
            btnPDF.innerHTML = "<i class=\"fas fa-file-pdf me-1\"></i> Generar Reporte de Estudiantes PDF";
        }
        if (modalAlert) {
            modalAlert.className = "alert alert-info";
            modalAlert.innerHTML = "<i class=\"fas fa-info-circle me-1\"></i> Se generará el reporte oficial en PDF con los estudiantes regulares seleccionados según los filtros activos.";
        }
    }

    $("#reporteModal").modal("show");
}

function generarReportePDF() {
    const tipoReporte = document.getElementById("reporteTipoSeleccionado")?.value || "estudiantes";
    const incluirEstadisticas = document.getElementById("incluirEstadisticas").value;
    const btnPDF = document.getElementById("btnGenerarPDF");
    const originalText = btnPDF.innerHTML;

    btnPDF.disabled = true;
    btnPDF.innerHTML = "<i class=\"fas fa-spinner fa-spin me-1\"></i> Preparando datos del reporte...";

    const params = obtenerFiltrosActivos();
    params.set("action", "obtener_ids");
    params.set("tipo_alumno", (tipoReporte === "egresados") ? "egresado" : "estudiante");

    fetch("ajax_estudiantes.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: params.toString()
    })
    .then(r => r.json())
    .then(data => {
        btnPDF.disabled = false;
        btnPDF.innerHTML = originalText;

        if (!data.success || !data.ids || data.ids.length === 0) {
            mostrarMensaje("Sin resultados", "No se encontraron registros para generar el reporte con los filtros seleccionados.", false);
            return;
        }

        const form = document.createElement("form");
        form.method = "POST";
        form.action = "reporte_estudiantes_pdf.php";
        form.target = "_blank";
        form.style.display = "none";

        const inputIds = document.createElement("input");
        inputIds.type = "hidden";
        inputIds.name = "ids";
        inputIds.value = data.ids.join(",");
        form.appendChild(inputIds);

        const inputEstadisticas = document.createElement("input");
        inputEstadisticas.type = "hidden";
        inputEstadisticas.name = "estadisticas";
        inputEstadisticas.value = incluirEstadisticas;
        form.appendChild(inputEstadisticas);

        const inputTipo = document.createElement("input");
        inputTipo.type = "hidden";
        inputTipo.name = "tipo_reporte";
        inputTipo.value = tipoReporte;
        form.appendChild(inputTipo);

        document.body.appendChild(form);
        form.submit();
        document.body.removeChild(form);

        $("#reporteModal").modal("hide");
    })
    .catch(err => {
        btnPDF.disabled = false;
        btnPDF.innerHTML = originalText;
        mostrarMensaje("Error", "Error al comunicarse con el servidor: " + err.message, false);
    });
}
document.getElementById('limpiarBusqueda').addEventListener('click', function() {
    document.getElementById('buscadorCedula').value = '';
    [
        'filtroTodasCarreras', 'filtroTodasModalidades', 'filtroTodosGeneros', 
        'filtroTodosEdoCivil', 'filtroTodosStatus', 'filtroTodasSedes', 
        'filtroTodosTurnos', 'filtroTodosEstadosGeo', 'filtroTodasCiudades', 
        'filtroTodasCondiciones', 'filtroTodasEdades'
    ].forEach(id => {
        const el = document.getElementById(id);
        if (el) {
            el.checked = true;
            el.dispatchEvent(new Event('change'));
        }
    });
    document.querySelectorAll('.filtro').forEach(cb => cb.checked = false);

    // Restaurar filtros de condición
    const cbEst = document.getElementById('filtroTipoEstudiante');
    const cbEgr = document.getElementById('filtroTipoEgresado');
    const rTodos = document.getElementById('quickTipoTodos');
    if (cbEst) cbEst.checked = true;
    if (cbEgr) cbEgr.checked = true;
    if (rTodos) rTodos.checked = true;

    document.getElementById('edadMin').value = '';
    document.getElementById('edadMax').value = '';
    document.getElementById('fechaIngresoDesde').value = '';
    document.getElementById('fechaIngresoHasta').value = '';
    cargarEstudiantes(1);
});

const btnReporteEst = document.getElementById('btnReporteEstudiantes');
if (btnReporteEst) {
    btnReporteEst.addEventListener('click', function() {
        abrirModalReporte('estudiantes');
    });
}

const btnReporteEgr = document.getElementById('btnReporteEgresados');
if (btnReporteEgr) {
    btnReporteEgr.addEventListener('click', function() {
        abrirModalReporte('egresados');
    });
}

const btnGenReporteLegacy = document.getElementById('btnGenerarReporte');
if (btnGenReporteLegacy) {
    btnGenReporteLegacy.addEventListener('click', function() {
        abrirModalReporte('estudiantes');
    });
}

document.getElementById('btnGenerarPDF').addEventListener('click', generarReportePDF);
document.getElementById('toggleFiltros').addEventListener('click', toggleFiltros);
document.getElementById('btnOcultarFiltros').addEventListener('click', toggleFiltros);

$(document).on('click', '.btn-details', function(e) {
    e.preventDefault();
    e.stopPropagation();
    var id = $(this).attr('data-id') || $(this).data('id');
    loadStudentDetails(id);
});

$(document).on('click', '.btn-edit', function(e) {
    e.preventDefault();
    e.stopPropagation();
    var id = $(this).attr('data-id') || $(this).data('id');
    loadEditStudentForm(id);
});

function loadStudentDetails(studentId) {
    const modalContent = document.getElementById('detalleEstudianteContent');
    modalContent.innerHTML = `<div class="text-center py-5"><div class="spinner-border text-primary"></div><p>Cargando...</p></div>`;
    $('#detalleModal').modal('show');
    fetch(`detalle_estudiante.php?id=${studentId}`).then(r => r.text()).then(d => modalContent.innerHTML = d).catch(e => modalContent.innerHTML = `<div class="alert alert-danger">Error: ${e.message}</div>`);
}

function loadEditStudentForm(studentId) {
    const modalContent = document.getElementById('editarEstudianteContent');
    modalContent.innerHTML = `<div class="text-center py-5"><div class="spinner-border text-primary"></div><p>Cargando...</p></div>`;
    $('#editarEstudianteModal').modal('show');
    fetch(`editar_estudiante_modal.php?id=${studentId}`).then(r => r.text()).then(d => {
        modalContent.innerHTML = d;
        const editForm = document.getElementById('formEditarEstudiante');
        if (editForm) editForm.addEventListener('submit', function(e) { e.preventDefault(); submitEditForm(this); });
    }).catch(e => modalContent.innerHTML = `<div class="alert alert-danger">Error: ${e.message}</div>`);
}

function submitEditForm(form) {
    const formData = new FormData(form);
    const submitButton = form.querySelector('button[type="submit"]');
    const originalText = submitButton.innerHTML;
    submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando...';
    submitButton.disabled = true;
    fetch('actualizar_estudiante.php', { method: 'POST', body: formData })
        .then(r => r.json()).then(d => {
            if (d.success) {
                $('#editarEstudianteModal').modal('hide');
                mostrarMensaje('Éxito', d.message || 'Estudiante actualizado', true, true);
            } else {
                mostrarMensaje('Error', d.message || 'Error al actualizar', false);
                submitButton.innerHTML = originalText;
                submitButton.disabled = false;
            }
        }).catch(e => {
            mostrarMensaje('Error', 'Error de conexión', false);
            submitButton.innerHTML = originalText;
            submitButton.disabled = false;
        });
}

function mostrarMensaje(titulo, mensaje, esExito = true, recargar = false) {
    const header = document.getElementById('resultadoModalHeader');
    const body = document.getElementById('resultadoModalBody');
    const label = document.querySelector('#resultadoModal .modal-title');
    if (header && body) {
        header.className = `modal-header bg-${esExito ? 'success' : 'danger'} text-white`;
        body.innerHTML = `<div class="text-center py-3"><i class="fas fa-${esExito ? 'check-circle' : 'exclamation-circle'} fa-3x mb-3 text-${esExito ? 'success' : 'danger'}"></i><h4>${titulo}</h4><p class="mb-0">${mensaje}</p></div>`;
        if (label) label.textContent = titulo;
        $('#resultadoModal').modal('show');
        if (recargar) $('#resultadoModal').off('hidden.bs.modal').on('hidden.bs.modal', () => location.reload());
    }
}

$('#detalleModal, #editarEstudianteModal, #agregarEstudianteModal').on('hidden.bs.modal', function() {
    const contentId = this.id === 'detalleModal' ? 'detalleEstudianteContent' : (this.id === 'editarEstudianteModal' ? 'editarEstudianteContent' : null);
    if (contentId && document.getElementById(contentId)) { if (this.id !== 'agregarEstudianteModal') document.getElementById(contentId).innerHTML = ''; }
});

const formEstudiante = document.getElementById('formEstudianteModal');
if (formEstudiante) {
    formEstudiante.addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const submitButton = this.querySelector('button[type="submit"]');
        const originalText = submitButton.innerHTML;
        submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando...';
        submitButton.disabled = true;
        fetch('procesar_estudiante.php', { method: 'POST', body: formData })
            .then(r => r.json()).then(d => {
                if (d.success) {
                    $('#agregarEstudianteModal').modal('hide');
                    mostrarMensaje('Éxito', d.message || 'Estudiante registrado', true, true);
                } else {
                    mostrarMensaje('Error', d.message || 'Error al guardar', false);
                    submitButton.innerHTML = originalText;
                    submitButton.disabled = false;
                }
            }).catch(e => {
                mostrarMensaje('Error', 'Error de conexión', false);
                submitButton.innerHTML = originalText;
                submitButton.disabled = false;
            });
    });
}

function abrirModalNuevoEstudiante() { $('#agregarEstudianteModal').modal('show'); }
</script>

<?php include("includes/footer.php"); ?>