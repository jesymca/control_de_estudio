<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

$titulopag = "Panel del Docente";
include('../funciones/functions.php');

// Verificar autenticación y rol
if (!isLoggedIn() || !isDocente()) {
    $_SESSION['msg'] = "Debes iniciar sesión como docente para acceder";
    header('location: ../login.php');
    exit();
}

// LLAMAR A LA FUNCIÓN DE VISITA
visita();

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
        .notas-card {
            border-bottom: 4px solid #28a745;
        }
        .notas-card .card-icon {
            color: #28a745;
        }
        .horario-card {
            border-bottom: 4px solid #17a2b8;
        }
        .horario-card .card-icon {
            color: #17a2b8;
        }
        .btn-access {
            border-radius: 50px;
            padding: 0.5rem 1.5rem;
            font-weight: 600;
            transition: all 0.3s;
        }
        .btn-notas {
            background-color: #28a745;
            border-color: #28a745;
            color: white;
        }
        .btn-notas:hover {
            background-color: #218838;
            border-color: #1e7e34;
        }
        .btn-horario {
            background-color: #17a2b8;
            border-color: #17a2b8;
            color: white;
        }
        .btn-horario:hover {
            background-color: #138496;
            border-color: #117a8b;
        }
        .welcome-message {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        }
    </style>
</head>

<body>
    <div class="container-fluid py-5">
        <!-- Encabezado -->
        <div class="dashboard-header p-4 mb-5 text-center">
            <h1 class="display-4 font-weight-bold"><i class="fas fa-chalkboard-teacher mr-3"></i>Panel del Docente</h1>
            <p class="lead mb-0">Bienvenido, <?php echo $_SESSION['user']['nombre_completo'] ?? $_SESSION['user']['username']; ?></p>
            <div class="mt-2">
                <span class="badge badge-pill shadow-sm px-3 py-2 text-uppercase font-weight-bold text-white" style="background-color: rgba(255,255,255,0.25); font-size: 0.88rem; letter-spacing: 0.5px; border: 1px solid rgba(255,255,255,0.4);">
                    <i class="fas fa-chalkboard-teacher mr-1"></i> Panel del Docente
                </span>
            </div>
        </div>

        <!-- Tarjetas de acceso -->
        <div class="row justify-content-center mb-5">
            <!-- Tarjeta de Carga de Notas -->
            <div class="col-md-5 mb-4">
                <div class="card feature-card notas-card h-100">
                    <div class="card-body text-center p-4">
                        <div class="card-icon">
                            <i class="fas fa-graduation-cap"></i>
                        </div>
                        <h3 class="card-title h4 font-weight-bold">Carga de Notas</h3>
                        <p class="card-text text-muted">Gestionar y registrar calificaciones de estudiantes</p>
                        <a href="notas.php" class="btn btn-access btn-notas mt-3">Acceder</a>
                    </div>
                </div>
            </div>

            <!-- Tarjeta de Mi Horario -->
            <div class="col-md-5 mb-4">
                <div class="card feature-card horario-card h-100">
                    <div class="card-body text-center p-4">
                        <div class="card-icon">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <h3 class="card-title h4 font-weight-bold">Mi Horario</h3>
                        <p class="card-text text-muted">Consultar y gestionar horario de clases</p>
                        <a href="mi_horario.php" class="btn btn-access btn-horario mt-3">Acceder</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mensaje de bienvenida -->
        <div class="welcome-message p-4 text-center mx-3">
            <h4 class="font-weight-bold">Sistema de Gestión Docente</h4>
            <p class="text-muted mb-0">Selecciona una de las opciones para gestionar tus actividades académicas</p>
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
            Push.create('Panel del Docente', {
                body: 'Bienvenido al sistema de gestión docente',
                icon: '../images/docente_icon.png',
                timeout: 4000
            }).catch(function(error) {
                // Silenciar error cuando los permisos de notificación son denegados por el usuario
            });
        }
    });
    </script>
</body>
</html>