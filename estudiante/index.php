<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

$titulopag = "Panel del Estudiante";
include('../funciones/functions.php');

// Verificar autenticación y rol
if (!isLoggedIn() || !isEstudiante()) {
    $_SESSION['msg'] = "Debes iniciar sesión como estudiante para acceder";
    header('location: ../login.php');
    exit();
}

// LLAMAR A LA FUNCIÓN DE VISITA
visita();

// Determinar si el usuario es vocero (usar sesión si está, sino consultar DB)
$is_vocero = false;
if (isset($_SESSION['user']['vocero'])) {
    $is_vocero = intval($_SESSION['user']['vocero']) === 1;
} else {
    if (isset($_SESSION['user']['id'])) {
        $uid = intval($_SESSION['user']['id']);
        $qv = $db->prepare("SELECT vocero FROM users WHERE id = ? LIMIT 1");
        $qv->bind_param('i', $uid);
        $qv->execute();
        $rv = $qv->get_result();
        if ($rv && $rv->num_rows > 0) {
            $rowv = $rv->fetch_assoc();
            $is_vocero = intval($rowv['vocero']) === 1;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <?php include("includes/head.php"); ?>
    <!-- Bootstrap 4.6 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fc;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .dashboard-header {
            background: linear-gradient(120deg, #4e73df 0%, #224abe 100%);
            color: white;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
        }
        .feature-card {
            border: none;
            border-radius: 10px;
            transition: all 0.3s;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            height: 100%;
        }
        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }
        .card-icon {
            font-size: 3.5rem;
            margin-bottom: 1rem;
        }
        .horario-card {
            border-bottom: 4px solid #4e73df;
        }
        .horario-card .card-icon {
            color: #4e73df;
        }
        .secciones-card {
            border-bottom: 4px solid #36b9cc;
        }
        .secciones-card .card-icon {
            color: #36b9cc;
        }
        .pensum-card {
            border-bottom: 4px solid #1cc88a;
        }
        .pensum-card .card-icon {
            color: #1cc88a;
        }
        .historial-card {
            border-bottom: 4px solid #f6c23e;
        }
        .historial-card .card-icon {
            color: #f6c23e;
        }
        /* Estilos para la nueva tarjeta de Constancias y Solicitudes */
        .constancias-card {
            border-bottom: 4px solid #e74a3b;
        }
        .constancias-card .card-icon {
            color: #e74a3b;
        }
        .vocero-card {
            border-bottom: 4px solid #28a745;
        }
        .vocero-card .card-icon {
            color: #28a745;
        }
        .btn-access {
            border-radius: 50px;
            padding: 0.5rem 1.5rem;
            font-weight: 600;
            transition: all 0.3s;
        }
        .btn-horario {
            background-color: #4e73df;
            border-color: #4e73df;
            color: white;
        }
        .btn-horario:hover {
            background-color: #3a5fc8;
            border-color: #2e59d9;
        }
        .btn-secciones {
            background-color: #36b9cc;
            border-color: #36b9cc;
            color: white;
        }
        .btn-secciones:hover {
            background-color: #2c9faf;
            border-color: #2a96a5;
        }
        .btn-pensum {
            background-color: #1cc88a;
            border-color: #1cc88a;
            color: white;
        }
        .btn-pensum:hover {
            background-color: #17a673;
            border-color: #169b6b;
        }
        .btn-historial {
            background-color: #f6c23e;
            border-color: #f6c23e;
            color: #212529;
        }
        .btn-historial:hover {
            background-color: #f4b619;
            border-color: #f4b30d;
        }
        /* Estilo para el botón de constancias */
        .btn-constancias {
            background-color: #e74a3b;
            border-color: #e74a3b;
            color: white;
        }
        .btn-constancias:hover {
            background-color: #d13a2b;
            border-color: #c73627;
        }
        .welcome-message {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        }
        .notification-section {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            margin-top: 2rem;
        }
        /* Ajuste para fila de 5 tarjetas */
        @media (min-width: 1200px) {
            .col-xl-custom {
                flex: 0 0 20%;
                max-width: 20%;
            }
        }
    </style>
</head>

<body>
    <div class="container-fluid py-5">
        <!-- Encabezado -->
        <div class="dashboard-header p-4 mb-5 text-center">
            <h1 class="display-4 font-weight-bold"><i class="fas fa-user-graduate mr-3"></i>Panel del Estudiante</h1>
            <p class="lead mb-0">Bienvenido, <?php echo $_SESSION['user']['nombre_completo'] ?? $_SESSION['user']['username']; ?></p>
            <div class="mt-2">
                <span class="badge badge-pill shadow-sm px-3 py-2 text-uppercase font-weight-bold text-white" style="background-color: rgba(255,255,255,0.25); font-size: 0.88rem; letter-spacing: 0.5px; border: 1px solid rgba(255,255,255,0.4);">
                    <i class="fas fa-user-graduate mr-1"></i> Panel del Estudiante
                </span>
            </div>
        </div>

        <!-- Tarjetas de acceso - Ahora con 5 tarjetas -->
        <div class="row justify-content-center mb-5">
            <!-- Tarjeta de Mi Horario -->
            <div class="col-md-5 col-lg-3 col-xl-custom mb-4">
                <div class="card feature-card horario-card h-100">
                    <div class="card-body text-center p-4">
                        <div class="card-icon">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <h3 class="card-title h4 font-weight-bold">Mi Horario</h3>
                        <p class="card-text text-muted">Consulta tu horario de clases semanal</p>
                        <a href="mi_horario.php" class="btn btn-access btn-horario mt-3">Acceder</a>
                    </div>
                </div>
            </div>

            <!-- Tarjeta de Mis Secciones -->
            <div class="col-md-5 col-lg-3 col-xl-custom mb-4">
                <div class="card feature-card secciones-card h-100">
                    <div class="card-body text-center p-4">
                        <div class="card-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <h3 class="card-title h4 font-weight-bold">Mis Secciones</h3>
                        <p class="card-text text-muted">Gestiona las secciones en las que estás inscrito</p>
                        <a href="mis_secciones.php" class="btn btn-access btn-secciones mt-3">Acceder</a>
                    </div>
                </div>
            </div>

            <!-- Tarjeta de Mi Pensum -->
            <div class="col-md-5 col-lg-3 col-xl-custom mb-4">
                <div class="card feature-card pensum-card h-100">
                    <div class="card-body text-center p-4">
                        <div class="card-icon">
                            <i class="fas fa-book"></i>
                        </div>
                        <h3 class="card-title h4 font-weight-bold">Mi Pensum</h3>
                        <p class="card-text text-muted">Consulta el plan de estudios de tu carrera</p>
                        <a href="mi_pensum.php" class="btn btn-access btn-pensum mt-3">Acceder</a>
                    </div>
                </div>
            </div>

            <!-- Tarjeta de Historial Académico -->
            <div class="col-md-5 col-lg-3 col-xl-custom mb-4">
                <div class="card feature-card historial-card h-100">
                    <div class="card-body text-center p-4">
                        <div class="card-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <h3 class="card-title h4 font-weight-bold">Historial Académico</h3>
                        <p class="card-text text-muted">Revisa tu progreso y calificaciones</p>
                        <a href="mi_historial.php" class="btn btn-access btn-historial mt-3">Acceder</a>
                    </div>
                </div>
            </div>

            <!-- NUEVA TARJETA: Constancias y Solicitudes -->
            <div class="col-md-5 col-lg-3 col-xl-custom mb-4">
                <div class="card feature-card constancias-card h-100">
                    <div class="card-body text-center p-4">
                        <div class="card-icon">
                            <i class="fas fa-file-alt"></i>
                        </div>
                        <h3 class="card-title h4 font-weight-bold">Constancias y Solicitudes</h3>
                        <p class="card-text text-muted">Solicita constancias de estudio, notas y otros documentos</p>
                        <a href="mis_constancias.php" class="btn btn-access btn-constancias mt-3">Acceder</a>
                    </div>
                </div>
            </div>
                <?php if ($is_vocero): ?>
                <!-- Tarjeta de Vocero (visible solo para voceros) -->
                <div class="col-md-5 col-lg-3 col-xl-custom mb-4">
                    <div class="card feature-card vocero-card h-100">
                        <div class="card-body text-center p-4">
                            <div class="card-icon">
                                <i class="fas fa-user-tie"></i>
                            </div>
                            <h3 class="card-title h4 font-weight-bold">Vocero</h3>
                            <p class="card-text text-muted">Accede a herramientas y opciones para voceros estudiantiles</p>
                            <a href="vocero.php" class="btn btn-access btn-vocero mt-3">Acceder</a>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
        </div>

        <!-- Mensaje de bienvenida -->
        <div class="welcome-message p-4 text-center mx-3">
            <h4 class="font-weight-bold">Sistema de Gestión Estudiantil</h4>
            <p class="text-muted mb-0">Selecciona una de las opciones para gestionar tu información académica</p>
        </div>

        

    </div>

    <?php include("includes/footer.php"); ?>

    <!-- Popper and Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js"></script>

    <script>
    // Notificación de bienvenida
    document.addEventListener('DOMContentLoaded', function() {
        // Verificar si Push está disponible antes de usarlo
        if (typeof Push !== 'undefined') {
            Push.create('Panel del Estudiante', {
                body: 'Bienvenido al sistema de gestión estudiantil',
                icon: '../images/estudiante_icon.png',
                timeout: 4000
            }).catch(function(error) {
                // Silenciar error cuando los permisos de notificación son denegados por el usuario
            });
        }
    });
    </script>
</body>
</html>