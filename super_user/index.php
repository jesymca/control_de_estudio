<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

$titulopag = "Sistema de Gestión - Panel de Administración";
include('../funciones/functions.php');
//CARGAR PERMISOS
cargarPermisosUsuario();
verificarPermiso('admin');

// LLAMAR A LA FUNCIÓN DE VISITA
visita();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <?php include("includes/head.php"); ?>
    <style>
        /* ESTILOS RESPONSIVE PARA MÓVILES */
        .dashboard-header {
            background: linear-gradient(120deg, #4e73df 0%, #224abe 100%);
            color: white;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            padding: 1.5rem !important;
            margin-bottom: 2rem;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }
        
        .dashboard-header h1 {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            line-height: 1.2;
            word-wrap: break-word;
        }
        
        .dashboard-header .lead {
            font-size: 1.1rem;
            margin-bottom: 0;
            line-height: 1.4;
        }
        
        /* MEJORAS PARA TARJETAS EN MÓVILES */
        .cards-container {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .card-wrapper {
            flex: 0 0 auto;
            width: 270px;
        }
        
        .feature-card {
            border: none;
            border-radius: 10px;
            transition: all 0.3s;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            height: 100%;
            min-height: 280px;
        }
        
        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }
        
        .card-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
        }
        
        .card-title {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 0.75rem;
            line-height: 1.2;
        }
        
        .card-text {
            font-size: 0.9rem;
            line-height: 1.4;
            margin-bottom: 1rem;
        }
        
        .btn-access {
            border-radius: 50px;
            padding: 0.5rem 1.5rem;
            font-weight: 600;
            transition: all 0.3s;
            font-size: 0.9rem;
        }
        
        /* Colores específicos para tarjetas */
        .pagos-card { border-bottom: 4px solid #28a745; }
        .pagos-card .card-icon { color: #28a745; }
        .soporte-card { border-bottom: 4px solid #ffc107; }
        .soporte-card .card-icon { color: #ffc107; }
        .mensajeria-card { border-bottom: 4px solid #17a2b8; }
        .mensajeria-card .card-icon { color: #17a2b8; }
        .auditoria-card { border-bottom: 4px solid #6f42c1; }
        .auditoria-card .card-icon { color: #6f42c1; }
        
        .btn-pagos {
            background-color: #28a745;
            border-color: #28a745;
            color: white;
        }
        
        .btn-soporte {
            background-color: #ffc107;
            border-color: #ffc107;
            color: #212529;
        }
        
        .btn-mensajeria {
            background-color: #17a2b8;
            border-color: #17a2b8;
            color: white;
        }
        
        .btn-auditoria {
            background-color: #6f42c1;
            border-color: #6f42c1;
            color: white;
        }
        
        .welcome-message {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            padding: 2rem !important;
        }
        
        .welcome-message h4 {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        
        /* MEDIA QUERIES PARA MÓVILES */
        @media (max-width: 768px) {
            .container {
                padding-left: 15px;
                padding-right: 15px;
            }
            
            .dashboard-header {
                padding: 1.25rem !important;
                margin-bottom: 1.5rem;
            }
            
            .dashboard-header h1 {
                font-size: 1.5rem !important;
            }
            
            .dashboard-header .lead {
                font-size: 1rem;
            }
            
            .cards-container {
                gap: 1rem;
                margin-bottom: 1.5rem;
            }
            
            .card-wrapper {
                width: 100%;
                max-width: 300px;
            }
            
            .feature-card {
                min-height: 250px;
            }
            
            .card-body {
                padding: 1.5rem !important;
            }
            
            .card-icon {
                font-size: 2.5rem;
            }
            
            .card-title {
                font-size: 1.1rem;
            }
            
            .card-text {
                font-size: 0.85rem;
            }
            
            .welcome-message {
                padding: 1.5rem !important;
            }
            
            .welcome-message h4 {
                font-size: 1.25rem;
            }
        }
        
        @media (max-width: 576px) {
            .dashboard-header h1 {
                font-size: 1.25rem !important;
            }
            
            .dashboard-header .lead {
                font-size: 0.9rem;
            }
            
            .card-wrapper {
                max-width: 100%;
            }
            
            .feature-card {
                min-height: 230px;
            }
            
            .card-body {
                padding: 1.25rem !important;
            }
            
            .card-icon {
                font-size: 2.25rem;
            }
        }
        
        /* Asegurar que el texto no se salga */
        .text-break {
            word-break: break-word;
            overflow-wrap: break-word;
        }
    </style>
</head>

<body>
    <div class="container-fluid py-4 py-md-5">
        <!-- Encabezado optimizado para móviles -->
        <div class="dashboard-header p-3 p-md-4 mb-4 mb-md-5 text-center">
            <h1 class="display-5 display-md-4 font-weight-bold text-break">
                <i class="fas fa-tachometer-alt mr-2 mr-md-3"></i>Panel de Administración
            </h1>
            <p class="lead mb-0 text-break">Bienvenido, <?php echo htmlspecialchars($_SESSION['user']['nombre'] ?? 'Administrador'); ?></p>
            <div class="mt-2">
                <span class="badge badge-pill shadow-sm px-3 py-2 text-uppercase font-weight-bold text-white" style="background-color: rgba(0,0,0,0.4); font-size: 0.88rem; letter-spacing: 0.5px; border: 1px solid rgba(255,255,255,0.3);">
                    <i class="fas fa-crown text-warning mr-1"></i> Panel de Super Usuario
                </span>
            </div>
        </div>

        <!-- Tarjetas de acceso responsive -->
        <div class="cards-container mb-4 mb-md-5">
            <?php if (tienePermiso('pagos')): ?>
            <!-- Tarjeta de Pagos -->
            <div class="card-wrapper">
                <div class="card feature-card pagos-card h-100">
                    <div class="card-body text-center p-3 p-md-4">
                        <div class="card-icon">
                            <i class="fas fa-money-bill-wave"></i>
                        </div>
                        <h3 class="card-title h5 font-weight-bold text-break">Pagos</h3>
                        <p class="card-text text-muted text-break">Gestionar sistema de pagos y transacciones</p>
                        <a href="registro_pagos.php" class="btn btn-access btn-pagos mt-2 mt-md-3">Acceder</a>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Tarjeta de Soporte -->
            <div class="card-wrapper">
                <div class="card feature-card soporte-card h-100">
                    <div class="card-body text-center p-3 p-md-4">
                        <div class="card-icon">
                            <i class="fas fa-life-ring"></i>
                        </div>
                        <h3 class="card-title h5 font-weight-bold text-break">Soporte</h3>
                        <p class="card-text text-muted text-break">Información de ayuda y soporte técnico</p>
                        <a href="soporte.php" class="btn btn-access btn-soporte mt-2 mt-md-3">Acceder</a>
                    </div>
                </div>
            </div>

            <!-- Tarjeta de Mensajería -->
            <div class="card-wrapper">
                <div class="card feature-card mensajeria-card h-100">
                    <div class="card-body text-center p-3 p-md-4">
                        <div class="card-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <h3 class="card-title h5 font-weight-bold text-break">Mensajería</h3>
                        <p class="card-text text-muted text-break">Sistema de mensajes y notificaciones</p>
                        <a href="mensajeria.php" class="btn btn-access btn-mensajeria mt-2 mt-md-3">Acceder</a>
                    </div>
                </div>
            </div>

            <?php if (tienePermiso('auditoria')): ?>
            <!-- Tarjeta de Auditoría -->
            <div class="card-wrapper">
                <div class="card feature-card auditoria-card h-100">
                    <div class="card-body text-center p-3 p-md-4">
                        <div class="card-icon">
                            <i class="fas fa-clipboard-list"></i>
                        </div>
                        <h3 class="card-title h5 font-weight-bold text-break">Auditoría</h3>
                        <p class="card-text text-muted text-break">Registro de actividades del sistema</p>
                        <a href="auditoria.php" class="btn btn-access btn-auditoria mt-2 mt-md-3">Acceder</a>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Mensaje de bienvenida responsive -->
        <div class="welcome-message p-3 p-md-4 text-center">
            <h4 class="font-weight-bold text-break">Bienvenido al Sistema de Gestión</h4>
            <p class="text-muted mb-0 text-break">Selecciona una de las opciones anteriores para comenzar</p>
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
            Push.create('Panel de Administración', {
                body: 'Bienvenido al sistema de gestión',
                icon: '../images/logo_mini.png',
                timeout: 4000
            }).catch(function(error) {
                // Silenciar error cuando los permisos de notificación son denegados por el usuario
            });
        }
    });
    </script>
</body>
</html>