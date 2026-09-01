<?php
// Función para contar mensajes no leídos
if (!function_exists('contarMensajesNoLeidos')) {
    function contarMensajesNoLeidos($user_id) {
        global $db;
        $user_id = intval($user_id);
        if ($user_id <= 0) return 0;
        
        $query = "SELECT COUNT(*) as total 
                  FROM mensajeria 
                  WHERE id_usuario_destinatario = ? 
                  AND leido = 0 
                  AND eliminado_destinatario = 0";
        $stmt = $db->prepare($query);
        if (!$stmt) return 0;
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result ? $result->fetch_assoc() : null;
        $stmt->close();
        return $row ? intval($row['total']) : 0;
    }
}

// Contar mensajes no leídos para el usuario actual
$mensajes_no_leidos = 0;
if (isset($_SESSION['user']['id'])) {
    $mensajes_no_leidos = contarMensajesNoLeidos($_SESSION['user']['id']);
}

// Verificar autenticación y rol
if (!isLoggedIn() || !isAdmin()) {
    $_SESSION['msg'] = "Debes iniciar sesión como administrador para acceder";
    header('location: ../login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="es-Es">
<head>
<meta charset="UTF-8">
<meta http-equiv="Content-type" content="text/html; charset=UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<meta name="description" content="Gestion">
<meta name="author" content="Hector Marulanda">
<title><?php echo $titulopag; ?></title>

<!-- Bootstrap 4.6 CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<!-- Roles Theme CSS -->
<link rel="stylesheet" href="/control_de_estudio/css/roles_theme.css">

<style>
    /* ESTILOS GENERALES */
    body {
        padding-top: 80px;
        background-color: #f8f9fc;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    
    .nav-item-mensajes {
        position: relative;
    }
    
    .badge-notificacion {
        position: absolute;
        top: 3px;
        right: 3px;
        font-size: 0.6em;
        padding: 3px 6px;
    }

    /* NAVBAR FIJO - ESTILO SUPER ADMIN OSCURO */
    .navbar {
        box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        border-bottom: 2px solid #343a40;
    }

    /* Forzar color blanco en enlaces e iconos del Navbar */
    .navbar-dark .navbar-nav .nav-link {
        color: rgba(255, 255, 255, 0.9) !important;
        font-weight: 500;
    }

    .navbar-dark .navbar-nav .nav-link i {
        color: #ffffff !important; /* Iconos totalmente blancos */
        margin-right: 4px;
    }

    .navbar-dark .navbar-nav .nav-link:hover {
        color: #ffffff !important;
        text-shadow: 0 0 8px rgba(255,255,255,0.3);
    }

    /* DROPDOWNS MEJORADOS */
    .dropdown-menu {
        z-index: 1080;
        background-color: #ffffff;
        border: 1px solid #dee2e6;
        box-shadow: 0 8px 20px rgba(0,0,0,0.15);
    }

    .dropdown-item {
        padding: 0.75rem 1.5rem;
        font-size: 0.9rem;
        color: #333 !important; /* Texto oscuro en el menú desplegable blanco para contraste */
    }

    .dropdown-item i {
        color: #555 !important; /* Iconos grises en el menú blanco */
        width: 20px;
        text-align: center;
    }

    .dropdown-item:hover {
        background-color: #f1f1f1;
        color: #000 !important;
    }

    /* MEJORAS PARA MÓVILES */
    @media (max-width: 991.98px) {
        .navbar-collapse {
            background-color: #2c3136; 
            padding: 1.5rem;
            margin-top: 1rem;
            border-radius: 8px;
            border: 1px solid #495057;
        }
        
        .dropdown-menu {
            background-color: #3d4349; 
            border: none;
            margin-left: 0.5rem;
        }
        
        .dropdown-item {
            color: #ffffff !important; /* Texto blanco en móviles sobre fondo oscuro */
        }

        .dropdown-item i {
            color: #ffffff !important;
        }
        
        .nav-link {
            border-bottom: 1px solid #444;
        }
    }

    /* CONTENIDO PRINCIPAL */
    .container {
        margin-top: 20px;
    }
</style>
</head>
<body>
<!-- NAVEGACIÓN PRINCIPAL -->
<nav class="navbar navbar-expand-lg navbar-dark fixed-top navbar-theme-superuser">
    <div class="container-fluid">
        <!-- Logo -->
        <a title="Cargar Inicio" class="navbar-brand" href="index.php">
            <?php echo $logopertenencia; ?>
        </a>
        
        <!-- Botón hamburguesa para móviles -->
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive" 
                aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <!-- Contenido colapsable del navbar -->
        <div class="collapse navbar-collapse" id="navbarResponsive">
            <ul class="navbar-nav ml-auto">
                
                <!-- Inicio -->
                

                <!-- Mensajería con notificación -->
                

                <!-- Pagos (individual) -->
                <?php if (tienePermiso('pagos')): ?>
                <li class="nav-item">
                    <a title="Gestión de Pagos" class="nav-link" href="registro_pagos.php">
                        <i class="fas fa-money-bill-wave fa-fw"></i> Pagos
                    </a>
                </li>
                <?php endif; ?>

                <!-- DROPDOWN: Estudiantes -->
                <li class="nav-item dropdown">
                    <a title="Gestión de Estudiantes" class="nav-link dropdown-toggle" href="#" 
                       id="navbarDropdownEstudiantes" role="button" data-toggle="dropdown" 
                       aria-haspopup="true" aria-expanded="false">
                        <i class="fa fa-users fa-fw"></i> Estudiantes
                    </a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdownEstudiantes">
                        <a title="Ver Todos los Estudiantes" class="dropdown-item" href="estudiantes.php">
                            <i class="fa fa-users fa-fw"></i> Ver todos los Estudiantes
                        </a>
                        <?php if (tienePermiso('agregar_estudiante')): ?>
                            <a title="Agregar Estudiante" class="dropdown-item" href="agregar_estudiante.php">
                                <i class="fa fa-user-plus fa-fw"></i> Agregar Estudiante
                            </a>
                        <?php endif; ?>

                         <?php if (tienePermiso('admin')): ?>
        <a title="Inscribir Materias a Estudiantes" class="dropdown-item" href="inscripcion_materias.php">
            <i class="fas fa-clipboard-list fa-fw"></i> Inscribir Materias
        </a>
        <?php endif; ?>

                        <?php if (tienePermiso('secciones')): ?>
                        <a title="Gestionar Secciones" class="dropdown-item" href="gestion_seccion.php">
                            <i class="fas fa-object-group fa-fw"></i> Gestionar Secciones
                        </a>
                        <?php endif; ?>

                        <?php if (tienePermiso('grado')): ?>
                            <a title="Títulos y Relaciones con Materias" class="dropdown-item" href="grado.php">
                                <i class="fas fa-graduation-cap fa-fw"></i> Grado
                            </a>
                        <?php endif; ?>

                            <?php if (tienePermiso('admin')): ?>
            <a title="Generar Constancias" class="dropdown-item" href="constancias.php">
                <i class="fas fa-file-alt fa-fw"></i> Admicion y Control
            </a>
        <?php endif; ?>

                    </div>
                </li>

                <!-- DROPDOWN: Pensum -->
                <li class="nav-item dropdown">
                    <a title="Gestión de Pensum" class="nav-link dropdown-toggle" href="#" 
                       id="navbarDropdownPensum" role="button" data-toggle="dropdown" 
                       aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-book fa-fw"></i> Pensum
                    </a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdownPensum">
                        <a title="Agregar Nueva Carrera" class="dropdown-item" href="agregar_carrera.php">
                            <i class="fas fa-plus-circle fa-fw"></i> Gestion de Programas
                        </a>
                        
                        <a title="Asignaturas" class="dropdown-item" href="materia.php">
                            <i class="fas fa-book-open fa-fw"></i> Asignaturas
                        </a>
                        
                        <?php if (tienePermiso('rela_materia_carrera')): ?>
                        <a title="Relacionar Materias con Carreras" class="dropdown-item" href="carrera_materias.php">
                            <i class="fas fa-link fa-fw"></i> Relacionar Materias-Carreras
                        </a>
                        <?php endif; ?>

                        <?php if (tienePermiso('periodos_academicos')): ?>
                        <a title="Periodos Académicos" class="dropdown-item" href="periodos_academicos.php">
                            <i class="fas fa-calendar fa-fw"></i> Periodos Académicos
                        </a>
                        <?php endif; ?>

                            <a title="Gestionar Prelaciones" class="dropdown-item" href="prelaciones.php">
            <i class="fas fa-list-ol fa-fw"></i> Prelaciones
        </a>

                    </div>
                </li>

                <!-- DROPDOWN: Docentes -->
                <li class="nav-item dropdown">
                    <a title="Gestión de Docentes" class="nav-link dropdown-toggle" href="#" 
                       id="navbarDropdownDocentes" role="button" data-toggle="dropdown" 
                       aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-chalkboard-teacher fa-fw"></i> Docentes
                    </a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdownDocentes">
                        <a title="Registrar Nuevo Docente" class="dropdown-item" href="add_docente.php">
                            <i class="fas fa-user-plus fa-fw"></i> Gestionar Docente
                        </a>

                        <!-- Asignar Secciones -->
                        <?php if (tienePermiso('asig_secciones')): ?>
                        <a title="Asignación de Secciones" class="dropdown-item" href="asignar_secciones.php">
                            <i class="fas fa-object-group fa-fw"></i> Asignar Secciones
                        </a>
                        <?php endif; ?>
                        
                        <?php if (tienePermiso('asig_cursos')): ?>
                        <a title="Asignación de Cursos" class="dropdown-item" href="asignacion_cursos.php">
                            <i class="fas fa-tasks fa-fw"></i> Asignar Cursos
                        </a>
                        <?php endif; ?>

                        <?php if (tienePermiso('horarios')): ?>
                        <a title="Horarios Docentes" class="dropdown-item" href="horarios_docentes.php">
                            <i class="fas fa-calendar-alt fa-fw"></i> Horarios
                        </a>
                        <?php endif; ?>

                        <?php if (tienePermiso('gestion_director_carrera')): ?>
                        <a title="Gestionar Directores de Carrera" class="dropdown-item" href="directores_carrera.php">
                            <i class="fas fa-user-plus fa-fw"></i> Gestionar Directores de Carrera
                        </a>
                        <?php endif; ?>
                    </div>
                </li>

                <!-- DROPDOWN: Notas -->
                <li class="nav-item dropdown">
                    <a title="Gestión de Notas" class="nav-link dropdown-toggle" href="#" 
                       id="navbarDropdownNotas" role="button" data-toggle="dropdown" 
                       aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-graduation-cap fa-fw"></i> Notas
                    </a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdownNotas">
                        <?php if (tienePermiso('notas_cargadas')): ?>
                            <a title="Registrar Notas" class="dropdown-item" href="admin_notas_pendientes.php">
                                <i class="fas fa-edit fa-fw"></i> Notas Cargadas
                            </a>
                        <?php endif; ?>

                        <?php if (tienePermiso('consultar_notas')): ?>
                            <a title="Consultar Notas" class="dropdown-item" href="consulta_notas.php">
                                <i class="fas fa-search fa-fw"></i> Consultar Notas
                            </a>
                        <?php endif; ?>
                        
                        <!-- Consultar Notas Pasadas -->
                        <?php if (tienePermiso('consultar_notas_pasadas')): ?>
                        <a title="Consultar Notas Pasadas" class="dropdown-item" href="notas_pasadas.php">
                            <i class="fas fa-history fa-fw"></i> Consultar Notas Pasadas
                        </a>
                        <?php endif; ?>

                        <a title="Consultar Notas Pasadas" class="dropdown-item" href="correccion_notas.php">
                            <i class="fas fa-pencil-alt fa-fw"></i> Corrección de Notas
                        </a>
                    </div>
                </li>

                <!-- Admisión y Control: eliminado del menú principal (migrado a constancias.php) -->

                <!-- DROPDOWN: Ajustes -->
                <li class="nav-item dropdown">
                    <a title="Ir a Ajustes" class="nav-link dropdown-toggle" href="#" 
                       id="navbarDropdownAjustes" role="button" data-toggle="dropdown" 
                       aria-haspopup="true" aria-expanded="false">
                        <i class="fa fa-cogs fa-fw"></i> Ajustes
                    </a>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownAjustes">
                    <!-- Mensajería -->
                    <a title="Sistema de Mensajería" class="dropdown-item position-relative" href="mensajeria.php">
                        <i class="fas fa-envelope fa-fw"></i> Mensajes
                        <?php if ($mensajes_no_leidos > 0): ?>
                            <span class="badge badge-danger badge-notificacion ml-2">
                                <?= $mensajes_no_leidos ?>
                            </span>
                        <?php endif; ?>
                    </a>
                    <div class="dropdown-divider"></div>
                        
                        <!-- Cambiar Perfil -->
                        <a title="Cambiar Perfil de Usuario" class="dropdown-item" href="../profile_selector.php">
                            <i class="fas fa-user-edit fa-fw"></i> Cambiar Perfil
                        </a>
                        
                        <div class="dropdown-divider"></div>
                        
                        <!-- Auditoría -->
                        <?php if (tienePermiso('auditoria')): ?>
                        <a title="Auditoría del Sistema" class="dropdown-item" href="auditoria.php">
                            <i class="fas fa-clipboard-list fa-fw"></i> Auditoría
                        </a>
                        <?php endif; ?>

                          <!-- Visita - Seguimiento de Usuarios -->
                        <?php if (tienePermiso('visita')): ?>
                        <a title="Seguimiento de Movimientos de Usuarios" class="dropdown-item" href="visita.php">
                        <i class="fas fa-user-secret fa-fw"></i> Visita
                        </a>
                        <?php endif; ?>

                        <!-- Respaldo BD -->
                        <?php if (tienePermiso('respaldo_bd')): ?>
                        <a title="Respaldo de Base de Datos" class="dropdown-item" href="respaldo_bd.php">
                            <i class="fas fa-database fa-fw"></i> Respaldo BD
                        </a>
                        <?php endif; ?>
                        
                        <!-- Títulos y Relaciones con Materias -->
                        <div class="dropdown-divider"></div>
                        <?php if (tienePermiso('titulos_re_materia')): ?>
                        <a title="Títulos y Relaciones con Materias" class="dropdown-item" href="titulos_relaciones_materias.php">
                            <i class="fas fa-graduation-cap fa-fw"></i> Títulos y Relaciones con Materias
                        </a>
                        <?php endif; ?>
                        
                        <!-- Niveles de Acceso -->
                        <?php if (tienePermiso('editar_acceso')): ?>
                            <div class="dropdown-divider"></div>
                            <a title="Editar Niveles de Acceso" class="dropdown-item" href="editar_accesos.php">
                                <i class="fas fa-user-lock fa-fw"></i> Niveles de Acceso
                            </a>
                        <?php endif; ?>
                        
                        <!-- Valores Predefinidos -->
                        <div class="dropdown-divider"></div>
                        <?php if (tienePermiso('editar_valores')): ?>
                        <a title="Editar Valores del Sistema" class="dropdown-item" href="valores_predefinidos.php">
                            <i class="fas fa-edit fa-fw"></i> Valores Predefinidos
                        </a>
                        <?php endif; ?>

                        <!-- Tipos de Pago -->
                        <?php if (tienePermiso('tipos_pago')): ?>
                        <a title="Editar tipos de pago" class="dropdown-item" href="tipo_pago.php">
                            <i class="fas fa-money-bill fa-fw"></i> Tipos de Pago
                        </a>
                        <?php endif; ?>

                        <!-- Tipos de Horario -->
                        <?php if (tienePermiso('tipos_horario')): ?>
                        <a title="Gestionar tipos de horario" class="dropdown-item" href="tipos_horario.php">
                            <i class="fas fa-clock fa-fw"></i> Tipos de Horario
                        </a>
                        <?php endif; ?>

                        <!-- Horarios por Personal -->
                        <?php if (tienePermiso('horario_personal')): ?>
                        <a title="Gestionar horarios por personal" class="dropdown-item" href="gestion_horario_personal.php">
                            <i class="fas fa-user-clock fa-fw"></i> Horarios por Personal
                        </a>
                        <?php endif; ?>
                        
                        <!-- Cerrar Sesión -->
                        <div class="dropdown-divider"></div>
                        <a title="Salir del Sistema" class="dropdown-item" href="#" id="logoutLink">
                            <i class="fas fa-sign-out-alt fa-fw"></i> Cerrar Sesión
                        </a>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- CONTENIDO PRINCIPAL -->
<div class="container-fluid">
    <div class="row">
        <div class="col-sm-6">
            <b class="mt-5"><?php echo 'Bienvenido ' .$_SESSION['user']['nombre']; ?></b>
            <div class="mt-1 mb-2">
                <span class="badge badge-pill shadow-sm px-3 py-1 text-uppercase font-weight-bold text-white" style="background-color: #212529; font-size: 0.8rem; letter-spacing: 0.5px;">
                    <i class="fas fa-crown text-warning mr-1"></i> Panel de Super Usuario
                </span>
            </div>
        </div>
        <div class="col-sm-6">
            <?php
            echo '<p class="text-right">';
            echo $fads;
            echo "<br>";
           // echo $ip;
            echo "<br>";
           // echo $nombrepag;
            ?>
        </div>
    </div>
</div>

<!-- MODAL DE CONFIRMACIÓN PARA CERRAR SESIÓN -->
<div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="logoutModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title" id="logoutModalLabel">
                    <i class="fas fa-sign-out-alt mr-2"></i>Confirmar Cierre de Sesión
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle mr-2"></i>
                    <strong>¿Está seguro de que desea cerrar la sesión?</strong>
                </div>
                <p>Será redirigido a la página de inicio de sesión.</p>
                <div class="user-info bg-light p-3 rounded">
                    <p class="mb-1"><strong>Usuario:</strong> <?php echo $_SESSION['user']['nombre'] ?? 'Usuario'; ?></p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times mr-2"></i>Cancelar
                </button>
                <a href="../logout.php" class="btn btn-danger" id="confirmLogout">
                    <i class="fas fa-sign-out-alt mr-2"></i>Sí, Cerrar Sesión
                </a>
            </div>
        </div>
    </div>
</div>

<!-- SCRIPTS JAVASCRIPT -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js"></script>

<script>
// FUNCIÓN PARA ACTUALIZAR NOTIFICACIONES
function actualizarNotificaciones() {
    fetch('../funciones/contar_mensajes_no_leidos.php')
        .then(response => response.json())
        .then(data => {
            const link = document.querySelector('.nav-link[href="mensajeria.php"]') || document.querySelector('a[href*="mensajeria"]');
            if (link) {
                const badge = link.querySelector('.badge-notificacion');
                if (data.mensajes_no_leidos > 0) {
                    if (badge) {
                        badge.textContent = data.mensajes_no_leidos;
                    } else {
                        const newBadge = document.createElement('span');
                        newBadge.className = 'badge badge-danger badge-notificacion';
                        newBadge.textContent = data.mensajes_no_leidos;
                        link.appendChild(newBadge);
                    }
                } else {
                    if (badge) {
                        badge.remove();
                    }
                }
            }
        })
        .catch(error => console.error('Error:', error));
}

// SOLUCIÓN SIMPLE PARA DROPDOWNS EN MÓVILES
document.addEventListener('DOMContentLoaded', function() {
    // Manejar logout
    const logoutLink = document.getElementById('logoutLink');
    if (logoutLink) {
        logoutLink.addEventListener('click', function(e) {
            e.preventDefault();
            $('#logoutModal').modal('show');
        });
    }
    
    const confirmLogout = document.getElementById('confirmLogout');
    if (confirmLogout) {
        confirmLogout.addEventListener('click', function(e) {
            e.preventDefault();
            $('#logoutModal').modal('hide');
            setTimeout(function() {
                window.location.href = '../logout.php';
            }, 500);
        });
    }
    
    // SOLUCIÓN PARA DROPDOWNS EN MÓVILES
    if (window.innerWidth <= 991) {
        // Cerrar dropdowns al hacer clic en un enlace
        $('.dropdown-item').on('click', function() {
            $('.dropdown-menu').removeClass('show');
        });
        
        // Cerrar dropdowns al hacer clic fuera
        $(document).on('click', function(e) {
            if (!$(e.target).closest('.dropdown').length) {
                $('.dropdown-menu').removeClass('show');
            }
        });
        
        // Toggle dropdown al hacer clic en el toggle
        $('.dropdown-toggle').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            var $parent = $(this).closest('.dropdown');
            var $menu = $parent.find('.dropdown-menu');
            var isOpen = $menu.hasClass('show');
            
            // Cerrar todos los dropdowns
            $('.dropdown-menu').removeClass('show');
            
            // Abrir solo si no estaba abierto
            if (!isOpen) {
                $menu.addClass('show');
            }
        });
    }
    
    // Actualizar notificaciones
    actualizarNotificaciones();
});

// Actualizar notificaciones cada 30 segundos
setInterval(actualizarNotificaciones, 30000);
</script>