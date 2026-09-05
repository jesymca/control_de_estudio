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
if (!isLoggedIn() || !isDocente()) {
    $_SESSION['msg'] = "Debes iniciar sesión como docente para acceder";
    header('location: ../login.php');
    exit();
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html lang="es-Es" xmlns="http://www.w3.org/1999/xhtml">
<head>

<meta charset="UTF8">
<meta http-equiv="Content-type" content="text/html; charset=UTF8" />
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<meta name="description" content="Gestion">
<meta name="author" content="Jose Herrera">

<title><?php echo $titulopag; ?></title>

<?php echo $bootstrap_head; ?>
<link rel="stylesheet" href="/control_de_estudio/css/roles_theme.css">

<style>
    /* ESTILOS GENERALES MEJORADOS */
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

    /* NAVBAR FIJO */
    .navbar {
        z-index: 1060;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }

    /* DROPDOWNS MEJORADOS */
    .dropdown-menu {
        z-index: 1080;
        border: 1px solid rgba(0,0,0,.15);
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }

    .dropdown-item {
        padding: 0.75rem 1.5rem;
        font-size: 0.9rem;
    }

    /* MEJORAS ESPECÍFICAS PARA MÓVILES */
    @media (max-width: 991.98px) {
        .navbar-collapse {
            background-color: #4CAF50;
            padding: 1rem;
            margin-top: 1rem;
            border-radius: 8px;
            max-height: 80vh;
            overflow-y: auto;
        }
        
        .dropdown-menu {
            background-color: rgba(255,255,255,0.9);
            border: none;
            box-shadow: none;
            margin-left: 1rem;
        }
        
        .dropdown-item {
            padding: 0.6rem 1.5rem;
        }
        
        /* Botón hamburguesa más grande para móviles */
        .navbar-toggler {
            padding: 0.4rem 0.75rem;
            font-size: 1.25rem;
            position: relative;
            z-index: 1065;
        }
        
        /* Mejorar contraste en móviles */
        .navbar-nav .nav-link {
            color: white !important;
            padding: 0.5rem 1rem;
        }
        
        .dropdown-toggle::after {
            border-top-color: white;
        }

        /* Overlay móvil para cerrar el navbar colapsado al tocar fuera */
        .mobile-navbar-backdrop {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.35);
            z-index: 1020;
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
            transition: opacity 0.25s ease, visibility 0.25s ease;
        }

        .mobile-navbar-backdrop.show {
            opacity: 1;
            visibility: visible;
            pointer-events: auto;
        }

        .navbar-collapse {
            position: relative;
            z-index: 1060;
        }
    }

    /* CONTENIDO PRINCIPAL */
    .container {
        margin-top: 20px;
    }

    /* DESPLEGAR DROPDOWNS AL PASAR EL MOUSE (HOVER) EN PANTALLAS GRANDES */
    @media (min-width: 992px) {
        .navbar-nav .dropdown:hover > .dropdown-menu,
        .navbar-nav .nav-item.dropdown:hover > .dropdown-menu {
            display: block !important;
            margin-top: 0;
            opacity: 1;
            visibility: visible;
            animation: navDropdownFade 0.22s ease-in-out;
        }

        .navbar-nav .dropdown > .dropdown-menu {
            margin-top: 0;
            transition: opacity 0.2s ease, visibility 0.2s ease;
        }

        .navbar-nav .dropdown > .dropdown-toggle::after {
            transition: transform 0.2s ease;
        }

        .navbar-nav .dropdown:hover > .dropdown-toggle::after {
            transform: rotate(180deg);
        }
    }

    @keyframes navDropdownFade {
        from {
            opacity: 0;
            transform: translateY(6px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>
</head>

<body>

<div class="container-fluid">
<!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light fixed-top navbar-theme-docente">
      <div class="container-fluid">
        <a title="Cargar Inicio" class="navbar-brand" href="index.php">
          <?php echo $logopertenencia; ?>
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarResponsive">
          <ul class="navbar-nav ml-auto">
            

            <!-- Icono de Mensajería con Notificación para Docentes -->
            

            <li id="dropdown-evaluaciones" class="nav-item dropdown">
              <a title="Gestión de Evaluaciones" class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  <i class="fas fa-clipboard-check fa-fw"></i> Notas
              </a>
              <div id="dropdown-evaluaciones-menu" class="dropdown-menu" aria-labelledby="navbarDropdown">
                  <a title="Crear Evaluación" class="dropdown-item" href="notas.php">
                      <i class="fas fa-plus-circle fa-fw"></i> Cargar Notas
                  </a>
                  
              </div>
            </li>

            <li id="dropdown-ajustes" class="nav-item dropdown">
              <a title="Ir a Ajustes" class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              <i class="fa fa-cogs fa-fw"></i>  Ajustes
              </a>
              <div id="dropdown-ajus" class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                <!-- Nueva opción: Cambiar Perfil -->
                <a title="Cambiar Perfil de Usuario" class="dropdown-item" href="../profile_selector.php">
                  <i class="fas fa-user-edit fa-fw"></i> Cambiar Perfil
                </a>
                
                <a title="Horario" class="dropdown-item" href="mi_horario.php"><i class="fas fa-calendar-alt fa-fw"></i> Mi Horario</a>
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
    <div id="mobileNavbarBackdrop" class="mobile-navbar-backdrop"></div>
    <div class="container-fluid">
    <div class="row">
<div class="col-sm-6">
    <b class="mt-5"><?php echo 'Bienvenido ' .$_SESSION['user']['nombre']; ?></b>
            <div class="mt-1 mb-2">
                <span class="badge badge-pill shadow-sm px-3 py-1 text-uppercase font-weight-bold text-white" style="background-color: #28a745; font-size: 0.8rem; letter-spacing: 0.5px;">
                    <i class="fas fa-chalkboard-teacher mr-1"></i> Panel del Docente
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
</div>


<!-- Modal de Confirmación para Cerrar Sesión -->
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

<!-- Script para actualizar notificaciones cada 30 segundos -->
<script>
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
                        // Crear el badge si no existe
                        const newBadge = document.createElement('span');
                        newBadge.className = 'badge badge-danger badge-notificacion';
                        newBadge.textContent = data.mensajes_no_leidos;
                        link.appendChild(newBadge);
                    }
                } else {
                    // Eliminar el badge si no hay mensajes
                    if (badge) {
                        badge.remove();
                    }
                }
            }
        })
        .catch(error => console.error('Error:', error));
}

// Actualizar cada 30 segundos
setInterval(actualizarNotificaciones, 30000);

// Actualizar también al cargar la página
document.addEventListener('DOMContentLoaded', actualizarNotificaciones);

// Script para manejar el modal de logout
document.addEventListener('DOMContentLoaded', function() {
    // Manejar el clic en el enlace de logout
    const logoutLink = document.getElementById('logoutLink');
    if (logoutLink) {
        logoutLink.addEventListener('click', function(e) {
            e.preventDefault(); // Prevenir el comportamiento por defecto
            $('#logoutModal').modal('show'); // Mostrar el modal
        });
    }
    
    // Manejar la confirmación de logout
    const confirmLogout = document.getElementById('confirmLogout');
    if (confirmLogout) {
        confirmLogout.addEventListener('click', function(e) {
            e.preventDefault(); // Prevenir cualquier acción por defecto
            
            // Cerrar el modal
            $('#logoutModal').modal('hide');
            
            // Redirigir después de que el modal se haya ocultado
            setTimeout(function() {
                window.location.href = '../logout.php';
            }, 500);
        });
    }
    
        // ABRIR DROPDOWNS AL PASAR EL MOUSE EN ESCRITORIO (>= 992px)
    $('.navbar-nav .dropdown').on('mouseenter', function() {
        if (window.innerWidth >= 992) {
            $(this).addClass('show');
            $(this).find('> .dropdown-menu').addClass('show');
            $(this).find('> .dropdown-toggle').attr('aria-expanded', 'true');
        }
    }).on('mouseleave', function() {
        if (window.innerWidth >= 992) {
            $(this).removeClass('show');
            $(this).find('> .dropdown-menu').removeClass('show');
            $(this).find('> .dropdown-toggle').attr('aria-expanded', 'false');
        }
    });

    // En escritorio, prevenir cierre brusco al hacer clic sobre el botón principal
    $('.navbar-nav .dropdown-toggle').on('click', function(e) {
        if (window.innerWidth >= 992) {
            e.preventDefault();
        }
    });

    // MEJORA PARA DROPDOWNS EN MÓVILES
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
    
    // Overlay móvil para cerrar el navbar con un clic fuera
    var $navbarCollapse = $('#navbarResponsive');
    var $mobileBackdrop = $('#mobileNavbarBackdrop');

    if ($navbarCollapse.length && $mobileBackdrop.length) {
        $navbarCollapse.on('show.bs.collapse', function() {
            $mobileBackdrop.addClass('show');
        });

        $navbarCollapse.on('hidden.bs.collapse', function() {
            $mobileBackdrop.removeClass('show');
        });

        $mobileBackdrop.on('click', function() {
            $navbarCollapse.collapse('hide');
        });
    }

    var $navbarToggler = $('.navbar-toggler');
    if ($navbarToggler.length && $navbarCollapse.length) {
        $navbarToggler.on('click', function(e) {
            if (window.innerWidth <= 991) {
                e.preventDefault();
                e.stopPropagation();
                $navbarCollapse.collapse('toggle');
            }
        });
    }
});

</script>

<!-- Incluir jsPDF y html2canvas para generar el PDF -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>



</body>


<style>
    @media print {
        body * {
            visibility: hidden;
        }
        #printable-area, #printable-area * {
            visibility: visible;
        }
        #printable-area {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }
        .no-print {
            display: none !important;
        }
        .card {
            border: none;
            box-shadow: none;
        }
        .table {
            font-size: 12px;
        }
        h4 {
            page-break-after: avoid;
        }
        .card-body {
            padding: 0;
        }
        .accordion .collapse {
            display: block !important;
            opacity: 1;
        }
    }
</style>
</html>