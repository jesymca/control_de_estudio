<?php
/**
 * MANUAL OFICIAL DE USUARIO - SISTEMA DE CONTROL DE ESTUDIOS
 * Universidad Politécnica Territorial de Puerto Cabello (UPTPC)
 * Unidad de Ciencia y Tecnología (UCyT)
 */
require_once __DIR__ . '/funciones/functions.php';

// Solo accesible para usuarios autenticados dentro de los paneles
if (!isLoggedIn()) {
    $_SESSION['msg'] = "Debes iniciar sesión para consultar el Manual de Usuario";
    header('Location: login.php');
    exit();
}

visita();

$anio_actual = date('Y');

// Determinar ruta de retorno al panel actual del usuario
$panel_retorno = 'profile_selector.php';
if (!empty($_SESSION['current_profile'])) {
    $rutas_panel = [
        'admin' => 'admin/index.php',
        'super_user' => 'super_user/index.php',
        'docente' => 'docente/index.php',
        'estudiante' => 'estudiante/index.php',
        'director_de_carrera' => 'director_de_carrera/index.php'
    ];
    $panel_retorno = $rutas_panel[$_SESSION['current_profile']] ?? 'profile_selector.php';
} elseif (isAdmin()) {
    $panel_retorno = 'admin/index.php';
} elseif (isDocente()) {
    $panel_retorno = 'docente/index.php';
} elseif (isEstudiante()) {
    $panel_retorno = 'estudiante/index.php';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manual Oficial de Usuario | Sistema de Control de Estudios UPTPC</title>
    <link rel="icon" type="image/svg+xml" href="images/favicon/favicon.svg">
    <link rel="stylesheet" href="funciones/bootstrap-4.6.2-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="funciones/fontawesome-free-5.15.4-web/css/all.min.css">
    <style>
        :root {
            --uptpc-blue: #0d2b56;
            --uptpc-blue-light: #1e4b8f;
            --uptpc-gold: #d4af37;
            --uptpc-accent: #007bff;
            --role-estudiante: #2196F3;
            --role-docente: #4CAF50;
            --role-director: #fd7e14;
            --role-admin: #2c5282;
            --role-super: #212529;
            --bg-body: #f4f7fa;
            --text-main: #2d3748;
        }

        * {
            scroll-behavior: smooth;
        }

        body {
            background-color: var(--bg-body);
            color: var(--text-main);
            font-family: 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.65;
            padding-top: 72px;
        }

        /* ==========================================================================
           BARRA SUPERIOR FIJA (TOPBAR)
           ========================================================================== */
        .manual-navbar {
            background: linear-gradient(135deg, #091e3a 0%, #133b6e 100%);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.18);
            border-bottom: 3px solid var(--uptpc-gold);
            height: 72px;
            z-index: 1050;
        }

        .navbar-brand-box {
            display: flex;
            align-items: center;
            gap: 12px;
            color: #fff !important;
            text-decoration: none !important;
        }

        .navbar-brand-box img {
            height: 46px;
            width: auto;
            background: #fff;
            padding: 3px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.25);
        }

        .brand-text-group {
            display: flex;
            flex-direction: column;
            line-height: 1.2;
        }

        .brand-title {
            font-weight: 800;
            font-size: 1.05rem;
            letter-spacing: 0.4px;
            color: #ffffff;
        }

        .brand-subtitle {
            font-size: 0.75rem;
            color: #cbd5e0;
            font-weight: 500;
        }

        .search-manual-wrap {
            position: relative;
            width: 300px;
        }

        .search-manual-input {
            background: rgba(255, 255, 255, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.25);
            color: #fff !important;
            border-radius: 50px;
            padding-left: 38px;
            font-size: 0.88rem;
            height: 38px;
            transition: all 0.25s;
        }

        .search-manual-input::placeholder {
            color: rgba(255, 255, 255, 0.7);
        }

        .search-manual-input:focus {
            background: #fff;
            color: #1a202c !important;
            box-shadow: 0 0 0 3px rgba(212, 175, 55, 0.35);
            outline: none;
        }

        .search-manual-input:focus + .search-icon {
            color: var(--uptpc-blue);
        }

        .search-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(255, 255, 255, 0.75);
            pointer-events: none;
        }

        /* ==========================================================================
           LAYOUT PRINCIPAL (SIDEBAR + CONTENT)
           ========================================================================== */
        .manual-layout {
            display: flex;
            max-width: 1440px;
            margin: 0 auto;
            padding: 24px 16px;
            gap: 28px;
        }

        .manual-sidebar {
            width: 300px;
            flex-shrink: 0;
        }

        .sidebar-sticky {
            position: sticky;
            top: 92px;
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.06);
            border: 1px solid #e2e8f0;
            max-height: calc(100vh - 115px);
            overflow-y: auto;
            padding: 16px 0;
        }

        .sidebar-heading {
            font-size: 0.75rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #718096;
            padding: 6px 20px 10px;
            border-bottom: 1px solid #edf2f7;
            margin-bottom: 8px;
        }

        .toc-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .toc-item a {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 20px;
            color: #4a5568;
            font-size: 0.88rem;
            font-weight: 600;
            text-decoration: none;
            border-left: 3px solid transparent;
            transition: all 0.2s;
        }

        .toc-item a:hover,
        .toc-item a.active {
            background-color: #ebf8ff;
            color: var(--uptpc-blue);
            border-left-color: var(--uptpc-blue);
        }

        .toc-sublist {
            list-style: none;
            padding-left: 34px;
            margin: 0 0 6px 0;
        }

        .toc-sublist li a {
            display: block;
            padding: 4px 12px;
            font-size: 0.81rem;
            font-weight: 500;
            color: #718096;
            text-decoration: none;
            border-left: 2px solid #e2e8f0;
            transition: all 0.2s;
        }

        .toc-sublist li a:hover {
            color: var(--uptpc-blue);
            border-left-color: var(--uptpc-blue);
            background: transparent;
        }

        .manual-content {
            flex: 1;
            min-width: 0;
        }

        /* ==========================================================================
           PORTADA OFICIAL INSTITUCIONAL
           ========================================================================== */
        .cover-page {
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(13, 43, 86, 0.08);
            border: 1px solid #e2e8f0;
            overflow: hidden;
            margin-bottom: 32px;
            position: relative;
        }

        .cover-top-banner {
            background: #fff;
            padding: 14px 30px;
            border-bottom: 2px solid #edf2f7;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }

        .cover-mppeu-img {
            max-height: 55px;
            width: auto;
        }

        .cover-hero {
            background: linear-gradient(135deg, #0d2b56 0%, #1a498b 60%, #102a4c 100%);
            color: #ffffff;
            padding: 50px 40px;
            text-align: center;
            position: relative;
            border-bottom: 5px solid var(--uptpc-gold);
        }

        .cover-logos-showcase {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 28px;
            margin-bottom: 28px;
        }

        .cover-logo-circle {
            width: 120px;
            height: 120px;
            background: #ffffff;
            border-radius: 50%;
            padding: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.35), 0 0 0 4px rgba(212, 175, 55, 0.6);
        }

        .cover-logo-circle img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }

        .cover-republic {
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: #e2e8f0;
            margin-bottom: 12px;
            font-weight: 600;
        }

        .cover-main-title {
            font-size: 2.3rem;
            font-weight: 800;
            margin-bottom: 12px;
            letter-spacing: 0.5px;
            text-shadow: 0 2px 10px rgba(0,0,0,0.3);
        }

        .cover-subtitle {
            font-size: 1.2rem;
            color: #fbd38d;
            font-weight: 600;
            margin-bottom: 20px;
        }

        .cover-badges {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 20px;
        }

        .cover-badge {
            background: rgba(255, 255, 255, 0.14);
            border: 1px solid rgba(255, 255, 255, 0.3);
            padding: 6px 16px;
            border-radius: 50px;
            font-size: 0.83rem;
            font-weight: 600;
            color: #fff;
        }

        .cover-meta-footer {
            padding: 24px 36px;
            background: #f8fafc;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
            font-size: 0.85rem;
        }

        .meta-box {
            border-left: 3px solid var(--uptpc-blue);
            padding-left: 12px;
        }

        .meta-label {
            font-size: 0.72rem;
            text-transform: uppercase;
            color: #718096;
            font-weight: 700;
            display: block;
        }

        .meta-value {
            font-weight: 700;
            color: #1a202c;
        }

        /* ==========================================================================
           FILTROS RÁPIDOS DE ROL
           ========================================================================== */
        .role-filter-bar {
            background: #fff;
            padding: 14px 20px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.04);
            border: 1px solid #e2e8f0;
            margin-bottom: 28px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 10px;
        }

        .filter-btn {
            border-radius: 50px;
            font-size: 0.82rem;
            font-weight: 700;
            padding: 6px 16px;
            border: 1px solid #cbd5e0;
            background: #fff;
            color: #4a5568;
            cursor: pointer;
            transition: all 0.2s;
        }

        .filter-btn:hover, .filter-btn.active {
            background: var(--uptpc-blue);
            color: #fff;
            border-color: var(--uptpc-blue);
        }

        /* ==========================================================================
           SECCIONES Y CAPÍTULOS DEL MANUAL
           ========================================================================== */
        .manual-chapter {
            background: #ffffff;
            border-radius: 14px;
            box-shadow: 0 4px 18px rgba(0, 0, 0, 0.05);
            border: 1px solid #e2e8f0;
            margin-bottom: 32px;
            overflow: hidden;
        }

        .chapter-header {
            padding: 22px 30px;
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .chapter-header.theme-general { background: linear-gradient(90deg, #0d2b56, #1e4b8f); }
        .chapter-header.theme-estudiante { background: linear-gradient(90deg, #1565C0, #2196F3); }
        .chapter-header.theme-docente { background: linear-gradient(90deg, #2E7D32, #4CAF50); }
        .chapter-header.theme-director { background: linear-gradient(90deg, #d9530f, #fd7e14); }
        .chapter-header.theme-admin { background: linear-gradient(90deg, #1a365d, #2b6cb0); }
        .chapter-header.theme-super { background: linear-gradient(90deg, #1a202c, #343a40); }

        .chapter-title-wrap {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .chapter-icon {
            width: 52px;
            height: 52px;
            background: rgba(255, 255, 255, 0.18);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .chapter-number {
            font-size: 0.78rem;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            opacity: 0.9;
            font-weight: 700;
            display: block;
        }

        .chapter-title {
            font-size: 1.45rem;
            font-weight: 800;
            margin: 0;
        }

        .chapter-body {
            padding: 30px;
        }

        .section-block {
            margin-bottom: 34px;
            padding-bottom: 26px;
            border-bottom: 1px dashed #e2e8f0;
        }

        .section-block:last-child {
            margin-bottom: 0;
            padding-bottom: 0;
            border-bottom: none;
        }

        .section-subtitle {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--uptpc-blue);
            margin-bottom: 14px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .section-subtitle i {
            color: var(--uptpc-accent);
        }

        /* ==========================================================================
           PASOS NUMERADOS Y COMPONENTES DIDÁCTICOS
           ========================================================================== */
        .step-list {
            list-style: none;
            padding: 0;
            margin: 16px 0;
        }

        .step-item {
            display: flex;
            align-items: flex-start;
            gap: 16px;
            background: #f8fafc;
            border: 1px solid #edf2f7;
            border-radius: 10px;
            padding: 14px 18px;
            margin-bottom: 12px;
            transition: transform 0.15s, box-shadow 0.15s;
        }

        .step-item:hover {
            transform: translateX(4px);
            box-shadow: 0 3px 10px rgba(0,0,0,0.04);
            border-color: #cbd5e0;
        }

        .step-badge {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: var(--uptpc-blue);
            color: #fff;
            font-weight: 800;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            margin-top: 2px;
        }

        .step-content h6 {
            font-weight: 700;
            margin-bottom: 4px;
            color: #1a202c;
            font-size: 0.96rem;
        }

        .step-content p {
            margin: 0;
            font-size: 0.9rem;
            color: #4a5568;
        }

        .callout-box {
            border-left: 4px solid var(--uptpc-blue);
            background: #ebf8ff;
            padding: 14px 18px;
            border-radius: 0 8px 8px 0;
            margin: 16px 0;
            font-size: 0.9rem;
        }

        .callout-box.callout-success {
            border-left-color: #38a169;
            background: #f0fff4;
        }

        .ui-mockup {
            border: 1px solid #cbd5e0;
            border-radius: 10px;
            overflow: hidden;
            margin: 18px 0;
            background: #ffffff;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }

        .ui-mockup-bar {
            background: #edf2f7;
            padding: 8px 14px;
            border-bottom: 1px solid #cbd5e0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 0.78rem;
            color: #4a5568;
            font-weight: 600;
        }

        .ui-dots {
            display: flex;
            gap: 6px;
        }

        .ui-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
        }

        .ui-mockup-body {
            padding: 20px;
            background: #f8f9fc;
        }

        /* ==========================================================================
           ESTILOS DE IMPRESIÓN (EXPORTACIÓN A PDF NATIVO)
           ========================================================================== */
        @media print {
            body {
                background: #fff !important;
                padding-top: 0 !important;
                font-size: 11pt;
            }
            .manual-navbar,
            .manual-sidebar,
            .role-filter-bar,
            .no-print {
                display: none !important;
            }
            .manual-layout {
                display: block !important;
                padding: 0 !important;
                max-width: 100% !important;
            }
            .cover-page {
                page-break-after: always;
                box-shadow: none !important;
                border: 2px solid #0d2b56 !important;
            }
            .manual-chapter {
                page-break-inside: avoid;
                page-break-before: always;
                box-shadow: none !important;
                border: 1px solid #cbd5e0 !important;
            }
            .chapter-header, .cover-hero {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
        }

        @media (max-width: 991px) {
            .manual-layout {
                flex-direction: column;
            }
            .manual-sidebar {
                width: 100%;
            }
            .sidebar-sticky {
                position: static;
                max-height: none;
            }
            .search-manual-wrap {
                display: none;
            }
        }
    </style>
</head>
<body>

    <!-- ========================================================================
         BARRA DE NAVEGACIÓN SUPERIOR FIJA
         ======================================================================== -->
    <nav class="navbar navbar-expand-lg fixed-top manual-navbar">
        <div class="container-fluid px-4">
            <a class="navbar-brand-box" href="#portada">
                <img src="images/uptpc.png" alt="Logo UPTPC" onerror="this.src='images/logo.png'">
                <div class="brand-text-group">
                    <span class="brand-title">MANUAL DE USUARIO &bull; UPTPC</span>
                    <span class="brand-subtitle">Sistema Integral de Control de Estudios</span>
                </div>
            </a>

            <div class="d-flex align-items-center ml-auto" style="gap: 12px;">
                <div class="search-manual-wrap">
                    <input type="text" id="manualSearchInput" class="form-control search-manual-input" placeholder="Buscar sección o función...">
                    <i class="fas fa-search search-icon"></i>
                </div>

                <a href="Manual_Usuario_Control_de_Estudios_UPTPC.pdf" download class="btn btn-warning btn-sm font-weight-bold px-3 shadow-sm" title="Descargar Manual en formato PDF">
                    <i class="fas fa-file-pdf mr-1"></i> Descargar PDF
                </a>

                <button onclick="window.print()" class="btn btn-light btn-sm font-weight-bold px-3 shadow-sm" title="Imprimir Manual">
                    <i class="fas fa-print mr-1 text-primary"></i> Imprimir
                </button>

                <a href="<?php echo htmlspecialchars($panel_retorno); ?>" class="btn btn-outline-light btn-sm font-weight-bold px-3">
                    <i class="fas fa-arrow-left mr-1"></i> Volver a mi Panel
                </a>
            </div>
        </div>
    </nav>

    <!-- ========================================================================
         CONTENEDOR PRINCIPAL
         ======================================================================== -->
    <div class="manual-layout">

        <!-- BARRA LATERAL: TABLA DE CONTENIDO -->
        <aside class="manual-sidebar">
            <div class="sidebar-sticky">
                <div class="sidebar-heading">
                    <i class="fas fa-list-ul mr-1"></i> Índice del Manual
                </div>
                <ul class="toc-list" id="tocList">
                    <li class="toc-item">
                        <a href="#portada"><i class="fas fa-university text-primary"></i> Portada Institucional</a>
                    </li>
                    <li class="toc-item">
                        <a href="#cap-1"><i class="fas fa-door-open text-info"></i> 1. Acceso y Uso General</a>
                        <ul class="toc-sublist">
                            <li><a href="#sec-1-1">1.1 Inicio de Sesión</a></li>
                            <li><a href="#sec-1-2">1.2 Preinscripción de Nuevos Ingresos</a></li>
                            <li><a href="#sec-1-3">1.3 Recuperación de Contraseña</a></li>
                            <li><a href="#sec-1-4">1.4 Selector de Perfiles Multi-Rol</a></li>
                        </ul>
                    </li>
                    <li class="toc-item">
                        <a href="#cap-2"><i class="fas fa-user-graduate" style="color: var(--role-estudiante);"></i> 2. Módulo Estudiante</a>
                        <ul class="toc-sublist">
                            <li><a href="#sec-2-1">2.1 Panel Principal Estudiantil</a></li>
                            <li><a href="#sec-2-2">2.2 Mi Horario y Mi Pensum</a></li>
                            <li><a href="#sec-2-3">2.3 Historial Académico de Notas</a></li>
                            <li><a href="#sec-2-4">2.4 Solicitudes y Constancias PDF</a></li>
                            <li><a href="#sec-2-5">2.5 Declaración de Pagos</a></li>
                            <li><a href="#sec-2-6">2.6 Prosecución Académica</a></li>
                            <li><a href="#sec-2-7">2.7 Panel del Vocero</a></li>
                            <li><a href="#sec-2-8">2.8 Mensajería Estudiantil</a></li>
                        </ul>
                    </li>
                    <li class="toc-item">
                        <a href="#cap-3"><i class="fas fa-chalkboard-teacher" style="color: var(--role-docente);"></i> 3. Módulo Docente</a>
                        <ul class="toc-sublist">
                            <li><a href="#sec-3-1">3.1 Panel del Docente</a></li>
                            <li><a href="#sec-3-2">3.2 Carga de Notas por Trimestre</a></li>
                            <li><a href="#sec-3-3">3.3 Plantillas CSV/PDF y Actas</a></li>
                            <li><a href="#sec-3-4">3.4 Mi Horario Docente</a></li>
                            <li><a href="#sec-3-5">3.5 Mensajería para el Docente</a></li>
                        </ul>
                    </li>
                    <li class="toc-item">
                        <a href="#cap-4"><i class="fas fa-user-tie" style="color: var(--role-director);"></i> 4. Director de Carrera</a>
                        <ul class="toc-sublist">
                            <li><a href="#sec-4-1">4.1 Creación y Gestión de Secciones</a></li>
                            <li><a href="#sec-4-2">4.2 Asignación de Estudiantes</a></li>
                            <li><a href="#sec-4-3">4.3 Asignación de Cursos a Docentes</a></li>
                            <li><a href="#sec-4-4">4.4 Horarios y Voceros de Sección</a></li>
                            <li><a href="#sec-4-5">4.5 Seguimiento de Carga de Notas</a></li>
                        </ul>
                    </li>
                    <li class="toc-item">
                        <a href="#cap-5"><i class="fas fa-cogs" style="color: var(--role-admin);"></i> 5. Módulo Administrador</a>
                        <ul class="toc-sublist">
                            <li><a href="#sec-5-1">5.1 Gestión de Estudiantes y Docentes</a></li>
                            <li><a href="#sec-5-2">5.2 Secretaría: Cupos y Fechas</a></li>
                            <li><a href="#sec-5-3">5.3 Admisión de Preinscripciones</a></li>
                            <li><a href="#sec-5-4">5.4 Aprobación y Corrección de Notas</a></li>
                            <li><a href="#sec-5-5">5.5 Carreras, Mallas y Materias</a></li>
                            <li><a href="#sec-5-6">5.6 Constancias, Pagos y Auditoría</a></li>
                        </ul>
                    </li>
                    <li class="toc-item">
                        <a href="#cap-6"><i class="fas fa-shield-alt text-dark"></i> 6. Super Usuario y Seguridad</a>
                    </li>
                    <li class="toc-item">
                        <a href="#cap-7"><i class="fas fa-headset text-success"></i> 7. Soporte Técnico</a>
                    </li>
                </ul>
            </div>
        </aside>

        <!-- ÁREA PRINCIPAL DE CONTENIDO -->
        <main class="manual-content">

            <!-- ================================================================
                 PORTADA INSTITUCIONAL
                 ================================================================ -->
            <section class="cover-page" id="portada">
                <div class="cover-top-banner">
                    <img src="images/mppeu_bb.png" alt="MPPEU" class="cover-mppeu-img" onerror="this.style.display='none'">
                    <div class="text-right">
                        <span class="badge badge-primary px-3 py-2"><i class="fas fa-check-circle mr-1"></i> DOCUMENTACIÓN OFICIAL</span>
                    </div>
                </div>

                <div class="cover-hero">
                    <div class="cover-republic">
                        República Bolivariana de Venezuela<br>
                        Ministerio del Poder Popular para la Educación Universitaria<br>
                        Universidad Politécnica Territorial de Puerto Cabello (UPTPC)
                    </div>

                    <div class="cover-logos-showcase">
                        <div class="cover-logo-circle" title="Universidad Politécnica Territorial de Puerto Cabello">
                            <img src="images/uptpc.png" alt="Logo UPTPC" onerror="this.src='images/logo.png'">
                        </div>
                        <div class="cover-logo-circle" title="Unidad de Ciencia y Tecnología">
                            <img src="images/cyt.svg" alt="Logo UCyT" onerror="this.src='images/logo.png'">
                        </div>
                    </div>

                    <h1 class="cover-main-title">MANUAL DE USUARIO</h1>
                    <div class="cover-subtitle">Sistema Integral de Gestión y Control de Estudios (SICE-UPTPC)</div>
                    <p class="mb-0 mx-auto" style="max-width: 720px; color: #e2e8f0; font-size: 0.96rem;">
                        Guía operativa paso a paso para Estudiantes, Personal Docente, Directores de Carrera, Secretaría, Control de Estudios y Administradores del Sistema.
                    </p>

                    <div class="cover-badges">
                        <span class="cover-badge"><i class="fas fa-user-graduate mr-1"></i> Rol Estudiante</span>
                        <span class="cover-badge"><i class="fas fa-chalkboard-teacher mr-1"></i> Rol Docente</span>
                        <span class="cover-badge"><i class="fas fa-user-tie mr-1"></i> Rol Director de Carrera</span>
                        <span class="cover-badge"><i class="fas fa-user-shield mr-1"></i> Rol Administrador</span>
                        <span class="cover-badge"><i class="fas fa-server mr-1"></i> Rol Super Usuario</span>
                    </div>
                </div>

                <div class="cover-meta-footer">
                    <div class="meta-box">
                        <span class="meta-label">Institución</span>
                        <span class="meta-value">UPTPC (Sede Principal y COEF)</span>
                    </div>
                    <div class="meta-box">
                        <span class="meta-label">Desarrollo Tecnológico</span>
                        <span class="meta-value">Unidad de Ciencia y Tecnología</span>
                    </div>
                    <div class="meta-box">
                        <span class="meta-label">Unidad Académica</span>
                        <span class="meta-value">Dirección de Control de Estudios</span>
                    </div>
                    <div class="meta-box">
                        <span class="meta-label">Edición y Vigencia</span>
                        <span class="meta-value">Versión Producción &bull; <?php echo $anio_actual; ?></span>
                    </div>
                </div>
            </section>

            <!-- BARRA DE FILTRADO RÁPIDO POR ROL -->
            <div class="role-filter-bar no-print">
                <div class="d-flex align-items-center flex-wrap" style="gap: 8px;">
                    <span class="small font-weight-bold text-muted mr-2"><i class="fas fa-filter mr-1"></i> Filtrar por Rol:</span>
                    <button class="filter-btn active" onclick="filtrarCapitulo('todos', this)">Todos los Capítulos</button>
                    <button class="filter-btn" onclick="filtrarCapitulo('cap-1', this)">1. Acceso General</button>
                    <button class="filter-btn" onclick="filtrarCapitulo('cap-2', this)">2. Estudiante</button>
                    <button class="filter-btn" onclick="filtrarCapitulo('cap-3', this)">3. Docente</button>
                    <button class="filter-btn" onclick="filtrarCapitulo('cap-4', this)">4. Director</button>
                    <button class="filter-btn" onclick="filtrarCapitulo('cap-5', this)">5. Administrador</button>
                    <button class="filter-btn" onclick="filtrarCapitulo('cap-6', this)">6. Super Usuario</button>
                </div>
            </div>

            <!-- ================================================================
                 CAPÍTULO 1: ACCESO Y USO GENERAL DEL SISTEMA
                 ================================================================ -->
            <article class="manual-chapter" id="cap-1" data-chapter="cap-1">
                <div class="chapter-header theme-general">
                    <div class="chapter-title-wrap">
                        <div class="chapter-icon"><i class="fas fa-door-open"></i></div>
                        <div>
                            <span class="chapter-number">Capítulo 01</span>
                            <h2 class="chapter-title">Acceso al Sistema, Preinscripción y Multi-Perfil</h2>
                        </div>
                    </div>
                    <span class="badge badge-light px-3 py-2 font-weight-bold">Todos los Usuarios</span>
                </div>

                <div class="chapter-body">
                    <!-- 1.1 Inicio de Sesión -->
                    <div class="section-block" id="sec-1-1">
                        <h3 class="section-subtitle"><i class="fas fa-sign-in-alt"></i> 1.1. Inicio de Sesión (Autenticación)</h3>
                        <p>
                            Para ingresar al Sistema de Control de Estudios de la UPTPC, acceda a la pantalla principal de bienvenida e identificación del portal institucional.
                        </p>

                        <ul class="step-list">
                            <li class="step-item">
                                <span class="step-badge">1</span>
                                <div class="step-content">
                                    <h6>Ingresar Credenciales Institucionales</h6>
                                    <p>En el campo <strong>Número de Cédula o Correo Electrónico</strong> introduzca su identificación registrada y en el campo inferior digite su <strong>Contraseña de Acceso</strong>.</p>
                                </div>
                            </li>
                            <li class="step-item">
                                <span class="step-badge">2</span>
                                <div class="step-content">
                                    <h6>Validación y Auditoría</h6>
                                    <p>Haga clic en el botón azul <strong>Entrar</strong>. El sistema verificará sus permisos activos y registrará el ingreso en la bitácora de seguridad institucional.</p>
                                </div>
                            </li>
                        </ul>

                        <div class="ui-mockup">
                            <div class="ui-mockup-bar">
                                <div class="ui-dots">
                                    <span class="ui-dot" style="background:#fc8181;"></span>
                                    <span class="ui-dot" style="background:#f6e05e;"></span>
                                    <span class="ui-dot" style="background:#68d391;"></span>
                                </div>
                                <span>Portal de Acceso Institucional &bull; SICE-UPTPC</span>
                            </div>
                            <div class="ui-mockup-body">
                                <div class="row align-items-center">
                                    <div class="col-md-5 text-center border-right">
                                        <img src="images/uptpc.png" alt="Logo" style="max-height: 85px;" class="mb-2">
                                        <h6 class="font-weight-bold text-dark mb-0">UPTPC &bull; Control de Estudios</h6>
                                        <small class="text-muted">Portal de Autenticación Unificado</small>
                                    </div>
                                    <div class="col-md-7 pl-md-4 mt-3 mt-md-0">
                                        <div class="mb-2">
                                            <span class="badge badge-success mr-1"><i class="fa fa-edit"></i> Preinscripción</span>
                                            <span class="badge badge-danger"><i class="fa fa-unlock-alt"></i> Recuperar Contraseña</span>
                                        </div>
                                        <div class="p-3 bg-white rounded border">
                                            <div class="small font-weight-bold text-secondary mb-1"><i class="fa fa-id-card mr-1"></i> Usuario / Cédula: <code>V-12345678</code></div>
                                            <div class="small font-weight-bold text-secondary mb-2"><i class="fa fa-lock mr-1"></i> Contraseña: <code>••••••••</code></div>
                                            <span class="btn btn-primary btn-sm btn-block disabled">Entrar al Sistema</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 1.2 Preinscripción -->
                    <div class="section-block" id="sec-1-2">
                        <h3 class="section-subtitle"><i class="fas fa-user-plus"></i> 1.2. Preinscripción en Línea (Nuevos Ingresos)</h3>
                        <p>
                            Cuando la Secretaría mantiene habilitado el período de captación, en la esquina superior derecha de la pantalla de inicio aparece el botón verde <strong><i class="fa fa-edit"></i> Preinscripción</strong>.
                        </p>
                        <ul class="step-list">
                            <li class="step-item">
                                <span class="step-badge">1</span>
                                <div class="step-content">
                                    <h6>Verificación de Cupos en Tiempo Real</h6>
                                    <p>Al seleccionar el <strong>Programa Nacional de Formación (Carrera)</strong> y el <strong>Turno (Diurno / Nocturno)</strong>, el sistema consulta automáticamente si existen cupos disponibles autorizados por Secretaría.</p>
                                </div>
                            </li>
                            <li class="step-item">
                                <span class="step-badge">2</span>
                                <div class="step-content">
                                    <h6>Datos Personales, Académicos y Territoriales</h6>
                                    <p>Complete sus datos de identificación (Tipo y Número de Cédula, Nombres, Apellidos, Fecha de Nacimiento, Estado Civil), dirección de habitación (Estado, Municipio, Parroquia, Ciudad) y datos socioeconómicos.</p>
                                </div>
                            </li>
                            <li class="step-item">
                                <span class="step-badge">3</span>
                                <div class="step-content">
                                    <h6>Emisión de Comprobante</h6>
                                    <p>Al enviar el formulario, la solicitud queda registrada con estado <strong>Pendiente</strong> para su revisión y admisión por parte de Control de Estudios.</p>
                                </div>
                            </li>
                        </ul>
                    </div>

                    <!-- 1.3 Recuperar Contraseña -->
                    <div class="section-block" id="sec-1-3">
                        <h3 class="section-subtitle"><i class="fas fa-key"></i> 1.3. Recuperación de Contraseña</h3>
                        <p>
                            En caso de extravío u olvido de su clave de acceso, puede restablecerla de forma segura desde la pantalla principal haciendo clic en el botón <strong>Recuperar Contraseña</strong>:
                        </p>
                        <ul>
                            <li>Ingrese su <strong>Correo Electrónico Institucional</strong> registrado en el sistema y presione <strong>Enviar enlace de recuperación</strong>.</li>
                            <li>Recibirá en su bandeja de entrada un correo con un enlace único (válido por 1 hora) que le permitirá definir y confirmar su nueva contraseña.</li>
                        </ul>
                    </div>

                    <!-- 1.4 Selector de Perfiles -->
                    <div class="section-block" id="sec-1-4">
                        <h3 class="section-subtitle"><i class="fas fa-id-badge"></i> 1.4. Selector de Perfiles Multi-Rol</h3>
                        <p>
                            Si un miembro de la comunidad universitaria posee más de un rol asignado (por ejemplo, es <strong>Docente</strong> y a la vez <strong>Director de Carrera</strong> o <strong>Administrador</strong>), luego de iniciar sesión se desplegará automáticamente la ventana de <strong>Selección de Perfil</strong>.
                        </p>
                        <div class="callout-box callout-success">
                            <i class="fas fa-exchange-alt mr-1"></i> <strong>Cambio Rápido de Perfil sin Cerrar Sesión:</strong> En cualquier momento puede alternar entre sus roles autorizados haciendo clic en el menú superior <strong>Ajustes &rarr; Cambiar Perfil</strong>.
                        </div>
                    </div>
                </div>
            </article>

            <!-- ================================================================
                 CAPÍTULO 2: MÓDULO DEL ESTUDIANTE
                 ================================================================ -->
            <article class="manual-chapter" id="cap-2" data-chapter="cap-2">
                <div class="chapter-header theme-estudiante">
                    <div class="chapter-title-wrap">
                        <div class="chapter-icon"><i class="fas fa-user-graduate"></i></div>
                        <div>
                            <span class="chapter-number">Capítulo 02</span>
                            <h2 class="chapter-title">Módulo del Estudiante</h2>
                        </div>
                    </div>
                    <span class="badge badge-light px-3 py-2 font-weight-bold" style="color: var(--role-estudiante);">Barra Azul Institucional</span>
                </div>

                <div class="chapter-body">
                    <!-- 2.1 Panel Principal -->
                    <div class="section-block" id="sec-2-1">
                        <h3 class="section-subtitle"><i class="fas fa-tachometer-alt"></i> 2.1. Panel Principal del Estudiante</h3>
                        <p>
                            Al ingresar como estudiante, visualizará la barra de navegación superior en <strong>color azul</strong> distintivo de su rol y tarjetas de acceso directo a todas sus gestiones académicas.
                        </p>
                    </div>

                    <!-- 2.2 Horario y Pensum -->
                    <div class="section-block" id="sec-2-2">
                        <h3 class="section-subtitle"><i class="fas fa-calendar-alt"></i> 2.2. Consulta de Horario, Secciones y Pensum</h3>
                        <ul class="step-list">
                            <li class="step-item">
                                <span class="step-badge">1</span>
                                <div class="step-content">
                                    <h6>Menú Mis Clases &rarr; Mi Horario</h6>
                                    <p>Muestra su grilla horaria semanal organizada por bloques de hora, indicando Unidad Curricular, Docente asignado, Aula y Sección. Incluye el botón <strong>Descargar Horario en PDF</strong>.</p>
                                </div>
                            </li>
                            <li class="step-item">
                                <span class="step-badge">2</span>
                                <div class="step-content">
                                    <h6>Menú Mis Clases &rarr; Mis Secciones</h6>
                                    <p>Detalla las secciones en las cuales se encuentra inscrito durante el período académico activo y el listado de materias cursadas.</p>
                                </div>
                            </li>
                            <li class="step-item">
                                <span class="step-badge">3</span>
                                <div class="step-content">
                                    <h6>Menú Mis Clases &rarr; Mi Pensum</h6>
                                    <p>Despliega la malla curricular oficial de su carrera dividida por Trayectos, mostrando códigos, créditos, horas académicas y permitiéndole exportar el Pensum en PDF.</p>
                                </div>
                            </li>
                        </ul>
                    </div>

                    <!-- 2.3 Historial Académico -->
                    <div class="section-block" id="sec-2-3">
                        <h3 class="section-subtitle"><i class="fas fa-award"></i> 2.3. Historial Académico de Notas</h3>
                        <p>
                            En el menú <strong>Mis Clases &rarr; Mi Historial</strong> puede consultar todas las calificaciones obtenidas a lo largo de su carrera, desglosadas por Trayecto (Trayecto Inicial, I, II, III, IV), visualizando las notas de los Trimestres I, II y III, la Nota Definitiva y el estado de aprobación de cada unidad curricular.
                        </p>
                    </div>

                    <!-- 2.4 Constancias y Solicitudes -->
                    <div class="section-block" id="sec-2-4">
                        <h3 class="section-subtitle"><i class="fas fa-file-pdf"></i> 2.4. Solicitudes Académicas y Constancias en PDF</h3>
                        <p>
                            Accediendo a la opción <strong>Solicitudes</strong> de la barra superior, podrá generar documentos oficiales sellados o gestionar trámites ante Control de Estudios:
                        </p>
                        <div class="row mt-3">
                            <div class="col-md-6 mb-3">
                                <div class="p-3 border rounded bg-light h-100">
                                    <h6 class="font-weight-bold text-primary"><i class="fas fa-download mr-1"></i> Descarga Directa en PDF</h6>
                                    <p class="small mb-0 text-muted">Emisión instantánea de <strong>Constancia de Estudio</strong>, <strong>Constancia de Horario</strong> y <strong>Record Académico / Notas Certificadas</strong> en formato PDF tamaño A4 con membrete oficial de la UPTPC.</p>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="p-3 border rounded bg-light h-100">
                                    <h6 class="font-weight-bold text-primary"><i class="fas fa-paper-plane mr-1"></i> Trámites y Solicitudes</h6>
                                    <p class="small mb-0 text-muted">Permite solicitar Carta de Postulación de Pasantías (si cumple los requisitos de avance), Cambio de Carrera, Cambio de Turno, Retiro Temporal o Reincorporación.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 2.5 Declaración de Pagos -->
                    <div class="section-block" id="sec-2-5">
                        <h3 class="section-subtitle"><i class="fas fa-money-bill-wave"></i> 2.5. Declaración de Pagos y Aranceles</h3>
                        <p>
                            Desde la opción <strong>Declarar Pagos</strong> en la barra superior, puede reportar el pago de aranceles universitarios:
                        </p>
                        <ol>
                            <li>Seleccione el <strong>Concepto / Tipo de Pago</strong> y el <strong>Método de Pago</strong> (Transferencia, Pago Móvil, Depósito).</li>
                            <li>Indique el <strong>Banco Origen</strong>, <strong>Banco Destino</strong>, <strong>Número de Referencia</strong>, <strong>Monto</strong> y <strong>Fecha de Operación</strong>.</li>
                            <li>Adjunte el comprobante digital si es requerido y presione <strong>Registrar Pago</strong>. Podrá consultar el estatus en su historial (<em>Pendiente</em>, <em>Aprobado</em> o <em>Rechazado</em>).</li>
                        </ol>
                    </div>

                    <!-- 2.6 Prosecución Académica -->
                    <div class="section-block" id="sec-2-6">
                        <h3 class="section-subtitle"><i class="fas fa-graduation-cap"></i> 2.6. Prosecución Académica: De TSU a Ingeniería / Licenciatura</h3>
                        <p>
                            El sistema evalúa automáticamente su elegibilidad para continuar estudios desde el nivel Técnico Superior Universitario (TSU) hacia Ingeniería o Licenciatura:
                        </p>
                        <div class="callout-box">
                            <i class="fas fa-check-double mr-1"></i> <strong>Verificación Automática de Requisitos:</strong> Si ha aprobado las unidades curriculares requeridas de los Trayectos I y II (TSU), el botón <strong>Prosecución</strong> en la barra superior le permitirá registrar su solicitud y descargar la <strong>Planilla Oficial de Prosecución en PDF</strong>. Si aún tiene materias pendientes, una ventana informativa le indicará qué requisitos le faltan por completar.
                        </div>
                    </div>

                    <!-- 2.7 Panel del Vocero -->
                    <div class="section-block" id="sec-2-7">
                        <h3 class="section-subtitle"><i class="fas fa-microphone-alt"></i> 2.7. Panel del Vocero Estudiantil</h3>
                        <p>
                            Esta opción aparece en la barra de navegación <strong>únicamente a aquellos estudiantes que han sido designados oficialmente como Voceros</strong> de su sección por el Director de Carrera. Permite:
                        </p>
                        <ul>
                            <li>Visualizar la nómina completa de compañeros integrantes de su sección.</li>
                            <li>Consultar el estado de carga de notas de su sección por materia para apoyar la contraloría estudiantil y el enlace con los docentes.</li>
                        </ul>
                    </div>

                    <!-- 2.8 Mensajería -->
                    <div class="section-block" id="sec-2-8">
                        <h3 class="section-subtitle"><i class="fas fa-envelope"></i> 2.8. Mensajería Interna Estudiantil</h3>
                        <p>
                            Bandeja de entrada y salida disponible en el menú <strong>Ajustes &rarr; Mensajes</strong> para enviar y recibir comunicaciones directas con sus Docentes, Directores de Carrera y personal de Control de Estudios.
                        </p>
                    </div>
                </div>
            </article>

            <!-- ================================================================
                 CAPÍTULO 3: MÓDULO DEL DOCENTE
                 ================================================================ -->
            <article class="manual-chapter" id="cap-3" data-chapter="cap-3">
                <div class="chapter-header theme-docente">
                    <div class="chapter-title-wrap">
                        <div class="chapter-icon"><i class="fas fa-chalkboard-teacher"></i></div>
                        <div>
                            <span class="chapter-number">Capítulo 03</span>
                            <h2 class="chapter-title">Módulo del Personal Docente</h2>
                        </div>
                    </div>
                    <span class="badge badge-light px-3 py-2 font-weight-bold" style="color: var(--role-docente);">Barra Verde Institucional</span>
                </div>

                <div class="chapter-body">
                    <!-- 3.1 Panel del Docente -->
                    <div class="section-block" id="sec-3-1">
                        <h3 class="section-subtitle"><i class="fas fa-th-large"></i> 3.1. Panel Principal del Docente</h3>
                        <p>
                            El entorno del docente se identifica con la barra superior en <strong>color verde</strong> y presenta tres tarjetas principales en su escritorio de trabajo:
                        </p>
                        <div class="row text-center mt-3">
                            <div class="col-md-4 mb-2">
                                <div class="p-3 border rounded bg-white shadow-sm">
                                    <i class="fas fa-graduation-cap fa-2x text-success mb-2"></i>
                                    <h6 class="font-weight-bold mb-1">Carga de Notas</h6>
                                    <small class="text-muted">Registro de calificaciones por trimestre</small>
                                </div>
                            </div>
                            <div class="col-md-4 mb-2">
                                <div class="p-3 border rounded bg-white shadow-sm">
                                    <i class="fas fa-calendar-alt fa-2x text-info mb-2"></i>
                                    <h6 class="font-weight-bold mb-1">Mi Horario</h6>
                                    <small class="text-muted">Consulta de bloques y aulas asignadas</small>
                                </div>
                            </div>
                            <div class="col-md-4 mb-2">
                                <div class="p-3 border rounded bg-white shadow-sm">
                                    <i class="fas fa-envelope fa-2x mb-2" style="color:#6f42c1;"></i>
                                    <h6 class="font-weight-bold mb-1">Mensajería</h6>
                                    <small class="text-muted">Comunicación con alumnos y directivos</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 3.2 Carga de Notas -->
                    <div class="section-block" id="sec-3-2">
                        <h3 class="section-subtitle"><i class="fas fa-clipboard-check"></i> 3.2. Proceso de Carga de Calificaciones</h3>
                        <ul class="step-list">
                            <li class="step-item">
                                <span class="step-badge">1</span>
                                <div class="step-content">
                                    <h6>Selección de Sección y Unidad Curricular</h6>
                                    <p>En el módulo de <strong>Carga de Notas</strong>, visualice sus secciones asignadas en el período académico activo y haga clic en <strong>Cargar Estudiantes</strong> sobre la materia que desea calificar.</p>
                                </div>
                            </li>
                            <li class="step-item">
                                <span class="step-badge">2</span>
                                <div class="step-content">
                                    <h6>Ingreso de Calificaciones por Trimestre (I, II y III)</h6>
                                    <p>El sistema habilita las casillas de los trimestres cuyas fechas de carga estén abiertas según el calendario configurado por Secretaría. Ingrese las notas en escala del <code>1</code> al <code>20</code>.</p>
                                </div>
                            </li>
                            <li class="step-item">
                                <span class="step-badge">3</span>
                                <div class="step-content">
                                    <h6>Adjuntar Soporte de Acta y Guardar</h6>
                                    <p>Adjunte el archivo digital de respaldo (imagen o PDF del acta física firmada) y presione <strong>Guardar Notas</strong>. Las calificaciones pasan a revisión y validación administrativa.</p>
                                </div>
                            </li>
                        </ul>
                    </div>

                    <!-- 3.3 Plantillas CSV y PDF -->
                    <div class="section-block" id="sec-3-3">
                        <h3 class="section-subtitle"><i class="fas fa-file-csv"></i> 3.3. Descarga de Nóminas e Importación Masiva (CSV / PDF)</h3>
                        <p>
                            Dentro de la vista de carga de estudiantes de cada sección, el docente cuenta con herramientas de productividad:
                        </p>
                        <ul>
                            <li><strong>Descargar Planilla en PDF:</strong> Genera la nómina oficial impresa con casillas vacías para el control de asistencia y evaluación manual en el aula.</li>
                            <li><strong>Descargar Plantilla CSV:</strong> Descarga un archivo compatible con hojas de cálculo con la lista de cédulas y nombres de la sección.</li>
                            <li><strong>Importar Notas desde CSV:</strong> Permite subir el archivo CSV con las notas ya llenadas y previsualizarlas antes de guardar.</li>
                        </ul>
                    </div>

                    <!-- 3.4 Horario y 3.5 Mensajería -->
                    <div class="section-block" id="sec-3-4">
                        <h3 class="section-subtitle"><i class="fas fa-clock"></i> 3.4. Mi Horario Docente</h3>
                        <p>
                            Permite visualizar todas las horas de clase asignadas durante la semana, diferenciando sede, aula, materia y sección, además de gestionar su disponibilidad horaria personal.
                        </p>
                    </div>

                    <div class="section-block" id="sec-3-5">
                        <h3 class="section-subtitle"><i class="fas fa-comments"></i> 3.5. Sistema de Mensajería para el Docente</h3>
                        <p>
                            Disponible tanto desde la tarjeta <strong>Mensajería</strong> en el panel principal como desde el ícono <strong><i class="fas fa-envelope"></i> Mensajes</strong> en la barra superior:
                        </p>
                        <ul>
                            <li><strong>Búsqueda inteligente de destinatarios:</strong> Filtra por rol (Estudiantes, Docentes, Directores de Carrera, Administradores) o busca por número de cédula o nombre.</li>
                            <li><strong>Bandeja de Recibidos y Enviados:</strong> Lectura instantánea de mensajes con indicador de notificaciones no leídas en tiempo real.</li>
                        </ul>
                    </div>
                </div>
            </article>

            <!-- ================================================================
                 CAPÍTULO 4: MÓDULO DEL DIRECTOR DE CARRERA
                 ================================================================ -->
            <article class="manual-chapter" id="cap-4" data-chapter="cap-4">
                <div class="chapter-header theme-director">
                    <div class="chapter-title-wrap">
                        <div class="chapter-icon"><i class="fas fa-user-tie"></i></div>
                        <div>
                            <span class="chapter-number">Capítulo 04</span>
                            <h2 class="chapter-title">Módulo del Director de Carrera</h2>
                        </div>
                    </div>
                    <span class="badge badge-light px-3 py-2 font-weight-bold" style="color: var(--role-director);">Barra Naranja Institucional</span>
                </div>

                <div class="chapter-body">
                    <p>
                        El panel del <strong>Director de Carrera</strong> (identificado con la barra superior naranja) centraliza la planificación académica del Programa Nacional de Formación (PNF) bajo su coordinación.
                    </p>

                    <!-- 4.1 Crear Secciones -->
                    <div class="section-block" id="sec-4-1">
                        <h3 class="section-subtitle"><i class="fas fa-layer-group"></i> 4.1. Creación y Gestión de Secciones</h3>
                        <p>
                            Desde el menú <strong>Secciones &rarr; Crear Sección</strong>, permite aperturar las secciones del período académico para su carrera respetando el límite de secciones autorizadas por Secretaría:
                        </p>
                        <ol>
                            <li>Seleccione el <strong>Período Académico</strong>, <strong>Malla Curricular</strong>, <strong>Trayecto</strong> y <strong>Turno (Diurno / Nocturno)</strong>.</li>
                            <li>El sistema genera automáticamente el código estandarizado de la sección y valida la capacidad máxima de estudiantes.</li>
                        </ol>
                    </div>

                    <!-- 4.2 Asignar Estudiantes -->
                    <div class="section-block" id="sec-4-2">
                        <h3 class="section-subtitle"><i class="fas fa-users"></i> 4.2. Asignación de Estudiantes a Secciones</h3>
                        <p>
                            En el menú <strong>Secciones &rarr; Asignar Estudiantes</strong>, permite matricular a los estudiantes activos de la carrera en sus respectivas secciones según el trayecto que les corresponde cursar, controlando que no se exceda la capacidad del aula.
                        </p>
                    </div>

                    <!-- 4.3 Asignación de Cursos a Docentes -->
                    <div class="section-block" id="sec-4-3">
                        <h3 class="section-subtitle"><i class="fas fa-chalkboard"></i> 4.3. Asignación de Carga Académica a Docentes</h3>
                        <p>
                            En la opción <strong>Asignación de Cursos</strong>, para cada sección creada el Director de Carrera asigna el profesor responsable de cada unidad curricular. El sistema sugiere docentes según su perfil profesional, títulos y disponibilidad horaria, evitando choques de horario.
                        </p>
                    </div>

                    <!-- 4.4 Horarios y Voceros -->
                    <div class="section-block" id="sec-4-4">
                        <h3 class="section-subtitle"><i class="fas fa-calendar-check"></i> 4.4. Gestión de Horarios por Sección y Designación de Voceros</h3>
                        <ul>
                            <li><strong>Horarios de Sección:</strong> Asigna los bloques horarios (día, hora de inicio, hora de fin y aula) a cada materia de la sección, verificando en tiempo real que ni el aula ni el docente estén ocupados en ese bloque.</li>
                            <li><strong>Asignación de Voceros:</strong> Permite elegir a un estudiante de la sección como Vocero Oficial, habilitándole automáticamente el <em>Panel Vocero</em> en su cuenta estudiantil.</li>
                        </ul>
                    </div>

                    <!-- 4.5 Seguimiento de Notas -->
                    <div class="section-block" id="sec-4-5">
                        <h3 class="section-subtitle"><i class="fas fa-chart-line"></i> 4.5. Seguimiento de Carga de Notas Docente</h3>
                        <p>
                            En la opción <strong>Notas de Profesores</strong>, el Director visualiza el porcentaje de avance de carga de notas de todos los docentes de su carrera, pudiendo ver el detalle de calificaciones por sección y enviar recordatorios directos por mensajería a los profesores pendientes por cargar.
                        </p>
                    </div>
                </div>
            </article>

            <!-- ================================================================
                 CAPÍTULO 5: MÓDULO DE ADMINISTRACIÓN Y CONTROL DE ESTUDIOS
                 ================================================================ -->
            <article class="manual-chapter" id="cap-5" data-chapter="cap-5">
                <div class="chapter-header theme-admin">
                    <div class="chapter-title-wrap">
                        <div class="chapter-icon"><i class="fas fa-cogs"></i></div>
                        <div>
                            <span class="chapter-number">Capítulo 05</span>
                            <h2 class="chapter-title">Módulo de Administración y Control de Estudios</h2>
                        </div>
                    </div>
                    <span class="badge badge-light px-3 py-2 font-weight-bold" style="color: var(--role-admin);">Gestión Institucional</span>
                </div>

                <div class="chapter-body">
                    <!-- 5.1 Estudiantes y Docentes -->
                    <div class="section-block" id="sec-5-1">
                        <h3 class="section-subtitle"><i class="fas fa-address-book"></i> 5.1. Gestión Integral de Estudiantes y Docentes</h3>
                        <ul>
                            <li><strong>Directorio y Expediente de Estudiantes:</strong> Búsqueda instantánea por cédula, nombres, carrera o estatus. Permite editar datos personales, académicos, ver historial desglosado de TSU e Ingeniería y gestionar avances de trayecto.</li>
                            <li><strong>Registro Individual y Masivo:</strong> Inscripción manual de estudiantes o importación por lotes mediante plantilla CSV.</li>
                            <li><strong>Gestión de Personal Docente:</strong> Registro de profesores, títulos de pregrado/postgrado, especialidades y activación/desactivación de cuentas.</li>
                        </ul>
                    </div>

                    <!-- 5.2 Secretaría -->
                    <div class="section-block" id="sec-5-2">
                        <h3 class="section-subtitle"><i class="fas fa-landmark"></i> 5.2. Módulo de Secretaría</h3>
                        <p>
                            Centro de control normativo de la Secretaría General de la universidad. Permite configurar tres aspectos clave:
                        </p>
                        <ul class="step-list">
                            <li class="step-item">
                                <span class="step-badge">1</span>
                                <div class="step-content">
                                    <h6>Cupos y Secciones Autorizadas por Carrera y Turno</h6>
                                    <p>Define la cantidad máxima de <strong>Cupos Totales</strong> para nuevos ingresos y el número de <strong>Secciones Autorizadas</strong> tanto para el turno Diurno como Nocturno en cada PNF.</p>
                                </div>
                            </li>
                            <li class="step-item">
                                <span class="step-badge">2</span>
                                <div class="step-content">
                                    <h6>Calendario de Fechas para Carga de Notas por Trimestre</h6>
                                    <p>Establece la <strong>Fecha de Inicio</strong> y <strong>Fecha de Fin</strong> para los Trimestres 1, 2 y 3. El sistema bloquea o desbloquea automáticamente la carga de notas de los docentes según este rango.</p>
                                </div>
                            </li>
                            <li class="step-item">
                                <span class="step-badge">3</span>
                                <div class="step-content">
                                    <h6>Visibilidad de Botones Públicos</h6>
                                    <p>Permite activar o desactivar con un clic el interruptor <strong>"Mostrar botón de Preinscripción en la portada"</strong> cuando inicia o culmina el proceso de admisión.</p>
                                </div>
                            </li>
                        </ul>
                    </div>

                    <!-- 5.3 Preinscripciones -->
                    <div class="section-block" id="sec-5-3">
                        <h3 class="section-subtitle"><i class="fas fa-user-check"></i> 5.3. Admisión de Preinscripciones</h3>
                        <p>
                            Lista todos los aspirantes que han completado el formulario público de preinscripción:
                        </p>
                        <ul>
                            <li>Permite revisar el expediente completo del aspirante y descargar su <strong>Planilla de Preinscripción en PDF</strong>.</li>
                            <li>Al presionar <strong>Aceptar Preinscripción</strong>, el sistema convierte automáticamente al aspirante en un usuario <strong>Estudiante Activo</strong>, le asigna sección y descuenta el cupo disponible de la carrera.</li>
                        </ul>
                    </div>

                    <!-- 5.4 Aprobación y Corrección de Notas -->
                    <div class="section-block" id="sec-5-4">
                        <h3 class="section-subtitle"><i class="fas fa-check-double"></i> 5.4. Validación de Notas Pendientes y Corrección de Calificaciones</h3>
                        <ul>
                            <li><strong>Notas Pendientes por Aprobar:</strong> Permite a Control de Estudios verificar las notas cargadas por los docentes junto con el soporte digital del acta, aprobándolas para que pasen al historial definitivo del estudiante.</li>
                            <li><strong>Corrección de Notas y Notas Históricas:</strong> Permite asentar calificaciones anteriores o realizar rectificaciones autorizadas bajo estricta trazabilidad en el registro de auditoría.</li>
                        </ul>
                    </div>

                    <!-- 5.5 Estructura Académica -->
                    <div class="section-block" id="sec-5-5">
                        <h3 class="section-subtitle"><i class="fas fa-sitemap"></i> 5.5. Carreras, Mallas Curriculares, Materias y Períodos</h3>
                        <ul>
                            <li><strong>Carreras y Pensum:</strong> Creación de PNF, versiones de mallas curriculares y vinculación de unidades curriculares por trayecto.</li>
                            <li><strong>Unidades Curriculares y Prelaciones:</strong> Catálogo de materias, unidades de crédito, horas académicas y reglas de prelación entre materias.</li>
                            <li><strong>Períodos Académicos y Aulas:</strong> Apertura de lapsos académicos y catálogo de aulas físicas.</li>
                        </ul>
                    </div>

                    <!-- 5.6 Constancias, Pagos y Auditoría -->
                    <div class="section-block" id="sec-5-6">
                        <h3 class="section-subtitle"><i class="fas fa-file-signature"></i> 5.6. Constancias, Registro de Pagos y Auditoría</h3>
                        <ul>
                            <li><strong>Centro de Constancias y Solicitudes:</strong> Emisión administrativa de constancias de estudio, notas certificadas y atención a solicitudes estudiantiles.</li>
                            <li><strong>Conciliación de Pagos:</strong> Verificación, aprobación o rechazo de pagos reportados por los estudiantes y configuración de aranceles.</li>
                            <li><strong>Auditoría y Registro de Actividad:</strong> Bitácora inalterable de todas las acciones ejecutadas en el sistema (usuario responsable, fecha, hora, dirección IP y detalle de la operación) con exportación de reportes.</li>
                        </ul>
                    </div>
                </div>
            </article>

            <!-- ================================================================
                 CAPÍTULO 6: SUPER USUARIO Y SEGURIDAD
                 ================================================================ -->
            <article class="manual-chapter" id="cap-6" data-chapter="cap-6">
                <div class="chapter-header theme-super">
                    <div class="chapter-title-wrap">
                        <div class="chapter-icon"><i class="fas fa-shield-alt"></i></div>
                        <div>
                            <span class="chapter-number">Capítulo 06</span>
                            <h2 class="chapter-title">Módulo de Super Usuario y Seguridad</h2>
                        </div>
                    </div>
                    <span class="badge badge-warning px-3 py-2 font-weight-bold text-dark">Máximo Nivel de Privilegio</span>
                </div>

                <div class="chapter-body">
                    <div class="section-block">
                        <h3 class="section-subtitle"><i class="fas fa-user-lock"></i> 6.1. Control de Accesos, Valores Predefinidos y Respaldos</h3>
                        <p>
                            El perfil de <strong>Super Usuario</strong> dispone de todas las capacidades administrativas más las herramientas críticas de infraestructura:
                        </p>
                        <ul>
                            <li><strong>Permisos Granulares por Usuario:</strong> Permite habilitar o restringir módulos específicos a cada funcionario (ej. permitir solo <em>Pagos</em>, o solo <em>Secretaría</em>, o solo <em>Constancias</em>).</li>
                            <li><strong>Valores Predefinidos:</strong> Administración de catálogos base (Bancos, Métodos de Pago, Estados Civiles, Tipos de Vivienda, Tipos de Ingreso).</li>
                            <li><strong>Respaldo de Base de Datos:</strong> Generación y descarga inmediata de copias de seguridad completas de la base de datos institucional para resguardo ante contingencias.</li>
                        </ul>
                    </div>
                </div>
            </article>

            <!-- ================================================================
                 CAPÍTULO 7: SOPORTE TÉCNICO Y CONTACTO INSTITUCIONAL
                 ================================================================ -->
            <article class="manual-chapter" id="cap-7" data-chapter="cap-7">
                <div class="chapter-header theme-general">
                    <div class="chapter-title-wrap">
                        <div class="chapter-icon"><i class="fas fa-headset"></i></div>
                        <div>
                            <span class="chapter-number">Capítulo 07</span>
                            <h2 class="chapter-title">Soporte Técnico y Canales de Atención</h2>
                        </div>
                    </div>
                    <span class="badge badge-light px-3 py-2 font-weight-bold">Asistencia UPTPC</span>
                </div>

                <div class="chapter-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="p-4 border rounded h-100 bg-light">
                                <h5 class="font-weight-bold text-primary"><i class="fas fa-laptop-code mr-2"></i> Unidad de Ciencia y Tecnología (UCyT)</h5>
                                <p class="small text-muted mb-2">Soporte técnico de la plataforma, cuentas de acceso, perfiles y mantenimiento del sistema.</p>
                                <p class="mb-1"><strong>Correo:</strong> <a href="mailto:cienciaytecnologia@uptpc.edu.ve">cienciaytecnologia@uptpc.edu.ve</a></p>
                                <p class="mb-0 small text-secondary">Horario: Lunes a Viernes (8:00 AM a 4:00 PM)</p>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="p-4 border rounded h-100 bg-light">
                                <h5 class="font-weight-bold text-success"><i class="fas fa-university mr-2"></i> Dirección de Control de Estudios</h5>
                                <p class="small text-muted mb-2">Atención de expedientes académicos, admisión, convalidaciones, actas de notas y certificaciones.</p>
                                <p class="mb-1"><strong>Correo:</strong> <a href="mailto:control_de_estudios@uptpc.edu.ve">control_de_estudios@uptpc.edu.ve</a></p>
                                <p class="mb-0 small text-secondary">Horario: Lunes a Viernes (8:00 AM a 4:00 PM)</p>
                            </div>
                        </div>
                    </div>

                    <div class="text-center mt-4 pt-3 border-top">
                        <p class="mb-1 font-weight-bold text-dark">
                            Universidad Politécnica Territorial de Puerto Cabello (UPTPC)
                        </p>
                        <p class="small text-muted mb-0">
                            Desarrollado y potenciado por la <strong>Unidad de Ciencia y Tecnología</strong> &bull; &copy; <?php echo $anio_actual; ?> Todos los derechos reservados.
                        </p>
                    </div>
                </div>
            </article>

        </main>
    </div>

    <script>
        // Filtro por Rol / Capítulo
        function filtrarCapitulo(capId, btn) {
            document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');

            const chapters = document.querySelectorAll('.manual-chapter');
            chapters.forEach(ch => {
                if (capId === 'todos' || ch.getAttribute('data-chapter') === capId) {
                    ch.style.display = 'block';
                } else {
                    ch.style.display = 'none';
                }
            });
        }

        // Buscador en tiempo real dentro del manual
        document.getElementById('manualSearchInput').addEventListener('input', function(e) {
            const term = e.target.value.toLowerCase().trim();
            const chapters = document.querySelectorAll('.manual-chapter');

            chapters.forEach(ch => {
                const text = ch.innerText.toLowerCase();
                if (term === '' || text.includes(term)) {
                    ch.style.display = 'block';
                } else {
                    ch.style.display = 'none';
                }
            });
        });
    </script>
</body>
</html>
