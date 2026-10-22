<?php
// 1. Configuración inicial
error_reporting(E_ALL);
ini_set('display_errors', 1);

$titulopag = "Mis Secciones Inscritas";
require_once('../funciones/functions.php');
include("includes/head.php");

// 2. Verificación directa (si no es estudiante, va al login)
if (empty($_SESSION['user']['estudiante']) || $_SESSION['user']['estudiante'] != 1) {
    header('Location: ../login.php');
    exit();
}

// LLAMAR A LA FUNCIÓN DE VISITA
visita();

// 4. Obtener ID de usuario seguro
$user_id = (int)$_SESSION['user']['id'];
?>

<div class="container-fluid px-2 px-sm-3 px-md-4">
    <div class="row">
        <div class="col-12">
            <h1 class="mt-4 h2 h1-sm"><?= htmlspecialchars($titulopag) ?></h1>
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
                <li class="breadcrumb-item active"><?= htmlspecialchars($titulopag) ?></li>
            </ol>
            
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-primary text-white d-flex flex-column flex-sm-row justify-content-between align-items-center">
                    <div class="mb-2 mb-sm-0">
                        <i class="fas fa-table mr-1"></i> Tus secciones activas
                    </div>
                    <div class="d-flex">
                        <span class="badge badge-light">
                            <i class="fas fa-calendar-alt"></i> <?= date('d/m/Y') ?>
                        </span>
                    </div>
                </div>
                <div class="card-body p-2 p-sm-3">
                    <?php
                    global $db;
                    
                    // Consulta optimizada con el campo inicia
                    $query = "SELECT 
                                s.codigo_seccion,
                                c.nombre_carrera,
                                t.nombre_trayecto,
                                t.numero_trayecto,
                                pa.nombre_periodo,
                                es.fecha_inscripcion,
                                s.inicia
                              FROM estudiante_seccion es
                              JOIN secciones s ON es.id_seccion = s.id_seccion
                              JOIN carreras c ON s.id_carrera = c.id_carrera
                              JOIN trayectos t ON s.id_trayecto = t.id_trayecto
                              JOIN periodos_academicos pa ON s.id_periodo = pa.id_periodo
                              WHERE es.id_usuario = ?
                              AND (es.estatus = 'activo' OR es.estatus = 'aprobado')
                              ORDER BY pa.fecha_inicio DESC";

                    $stmt = $db->prepare($query);
                    
                    if ($stmt) {
                        $stmt->bind_param("i", $user_id);
                        
                        if ($stmt->execute()) {
                            $result = $stmt->get_result();
                            
                            if ($result->num_rows > 0) {
                                // Vista para escritorio: tabla
                                echo '<div class="table-responsive d-none d-md-block">
                                        <table class="table table-bordered table-hover">
                                            <thead class="thead-dark">
                                                <tr>
                                                    <th>Sección</th>
                                                    <th>Carrera</th>
                                                    <th>Trayecto</th>
                                                    <th>Nivel</th>
                                                    <th>Periodo</th>
                                                    <th>Fecha de Inicio</th>
                                                    <th>Inscrito el</th>
                                                </tr>
                                            </thead>
                                            <tbody>';
                                
                                // Almacenar datos para vista móvil
                                $secciones_data = [];
                                
                                while ($row = $result->fetch_assoc()) {
                                    $secciones_data[] = $row;
                                    echo '<tr>
                                            <td class="align-middle"><strong>'.htmlspecialchars($row['codigo_seccion']).'</strong></td>
                                            <td class="align-middle">'.htmlspecialchars($row['nombre_carrera']).'</td>
                                            <td class="align-middle">'.htmlspecialchars($row['nombre_trayecto']).'</td>
                                            <td class="align-middle">'.htmlspecialchars($row['numero_trayecto']).'</td>
                                            <td class="align-middle">'.htmlspecialchars($row['nombre_periodo']).'</td>
                                            <td class="align-middle">'.date('d/m/Y', strtotime($row['inicia'])).'</td>
                                            <td class="align-middle">'.date('d/m/Y', strtotime($row['fecha_inscripcion'])).'</td>
                                        </tr>';
                                }
                                
                                echo '</tbody></table></div>';
                                
                                // Vista para móviles: tarjetas
                                echo '<div class="d-block d-md-none">';
                                foreach ($secciones_data as $row) {
                                    echo '<div class="card mb-3 shadow-sm">
                                            <div class="card-header bg-primary text-white">
                                                <strong><i class="fas fa-chalkboard"></i> Sección ' . htmlspecialchars($row['codigo_seccion']) . '</strong>
                                            </div>
                                            <div class="card-body p-3">
                                                <div class="row mb-2">
                                                    <div class="col-5 text-muted">
                                                        <i class="fas fa-graduation-cap"></i> Carrera:
                                                    </div>
                                                    <div class="col-7">
                                                        <strong>' . htmlspecialchars($row['nombre_carrera']) . '</strong>
                                                    </div>
                                                </div>
                                                <div class="row mb-2">
                                                    <div class="col-5 text-muted">
                                                        <i class="fas fa-layer-group"></i> Trayecto:
                                                    </div>
                                                    <div class="col-7">
                                                        ' . htmlspecialchars($row['nombre_trayecto']) . ' (' . htmlspecialchars($row['numero_trayecto']) . ')'
                                                    . '</div>
                                                </div>
                                                <div class="row mb-2">
                                                    <div class="col-5 text-muted">
                                                        <i class="fas fa-calendar-alt"></i> Periodo:
                                                    </div>
                                                    <div class="col-7">
                                                        ' . htmlspecialchars($row['nombre_periodo']) . '
                                                    </div>
                                                </div>
                                                <div class="row mb-2">
                                                    <div class="col-5 text-muted">
                                                        <i class="fas fa-play-circle"></i> Inicia:
                                                    </div>
                                                    <div class="col-7">
                                                        ' . date('d/m/Y', strtotime($row['inicia'])) . '
                                                    </div>
                                                </div>
                                                <div class="row mb-2">
                                                    <div class="col-5 text-muted">
                                                        <i class="fas fa-calendar-check"></i> Inscrito:
                                                    </div>
                                                    <div class="col-7">
                                                        ' . date('d/m/Y', strtotime($row['fecha_inscripcion'])) . '
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card-footer bg-light">
                                                <small class="text-muted">
                                                    <i class="fas fa-info-circle"></i> Estado: Activo
                                                </small>
                                            </div>
                                        </div>';
                                }
                                echo '</div>';
                                
                            } else {
                                echo '<div class="alert alert-info text-center">
                                        <i class="fas fa-info-circle fa-2x mb-2"></i><br>
                                        No estás inscrito en ninguna sección activa.
                                      </div>';
                            }
                        } else {
                            echo '<div class="alert alert-danger">
                                    <i class="fas fa-exclamation-triangle"></i> 
                                    Error al cargar secciones: ' . htmlspecialchars($stmt->error) . '
                                  </div>';
                        }
                        $stmt->close();
                    } else {
                        echo '<div class="alert alert-danger">
                                <i class="fas fa-exclamation-triangle"></i> 
                                Error en la consulta: ' . htmlspecialchars($db->error) . '
                              </div>';
                    }
                    ?>
                </div>
                <div class="card-footer text-muted d-flex flex-column flex-sm-row justify-content-between align-items-center">
                    <small>
                        <i class="fas fa-user"></i> Usuario: <?= htmlspecialchars($_SESSION['user']['id'] ?? 'N/D') ?>
                    </small>
                    <small class="mt-2 mt-sm-0">
                        <i class="fas fa-clock"></i> Actualizado: <?= date('d/m/Y H:i:s') ?>
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Estilos responsivos adicionales */
@media (max-width: 767.98px) {
    .h1-sm {
        font-size: 1.5rem;
    }
    
    .card-header {
        flex-direction: column;
        text-align: center;
    }
    
    .breadcrumb {
        font-size: 0.85rem;
        padding: 0.5rem 0.75rem;
    }
    
    .breadcrumb-item + .breadcrumb-item::before {
        padding-left: 0.3rem;
        padding-right: 0.3rem;
    }
    
    .table td, .table th {
        padding: 0.5rem;
        font-size: 0.85rem;
    }
}

@media (min-width: 768px) and (max-width: 991.98px) {
    .table td, .table th {
        padding: 0.6rem;
        font-size: 0.9rem;
    }
}

/* Mejoras para la vista de tarjetas en móviles */
.d-block.d-md-none .card {
    transition: transform 0.2s;
}

.d-block.d-md-none .card:active {
    transform: scale(0.98);
}

.d-block.d-md-none .card-header {
    padding: 0.75rem 1rem;
    font-size: 0.95rem;
}

.d-block.d-md-none .card-body {
    padding: 1rem;
}

.d-block.d-md-none .row {
    margin-left: 0;
    margin-right: 0;
}

.d-block.d-md-none .col-5, 
.d-block.d-md-none .col-7 {
    padding-left: 0.25rem;
    padding-right: 0.25rem;
}

/* Estilos para la tabla responsive */
.table-responsive {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}

.table {
    min-width: 600px;
}

/* Mejoras visuales */
.card {
    border-radius: 0.5rem;
    overflow: hidden;
}

.card-header {
    border-bottom: none;
}

.shadow-sm {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.badge-light {
    background-color: rgba(255,255,255,0.2);
    color: white;
}

/* Animación suave */
.card {
    transition: all 0.3s ease;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

@media (max-width: 575.98px) {
    .card:hover {
        transform: none;
    }
}
</style>

<?php
include("includes/footer.php");
?>