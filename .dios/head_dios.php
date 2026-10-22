<?php
// .dios/head_dios.php - Cabecera Ejecutiva del Panel MODO DIOS
if (!defined('DIOS_USER')) {
    require_once __DIR__ . '/config.php';
}
$csrf_token = $_SESSION['dios_csrf_token'] ?? '';
$ajax_token = $_SESSION['dios_ajax_token'] ?? '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>👑 MODO DIOS | Centro de Comando Soberano - UPTPC</title>
    <link rel="icon" type="image/png" href="../images/uptpc.png">
    <!-- Local Assets + Fallback -->
    <link rel="stylesheet" href="../funciones/bootstrap-4.6.2-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../funciones/fontawesome-free-5.15.4-web/css/all.min.css">
    <script src="../funciones/jquery/jquery-3.7.1.js"></script>
    <script src="../funciones/bootstrap-4.6.2-dist/js/bootstrap.bundle.min.js"></script>
    <script>
        window.DIOS_AJAX_TOKEN = <?php echo json_encode($ajax_token); ?>;
        window.DIOS_CSRF_TOKEN = <?php echo json_encode($csrf_token); ?>;
    </script>
    <style>
        :root {
            --dios-bg-deep: #060b16;
            --dios-bg-main: #0b1325;
            --dios-bg-card: #111c35;
            --dios-bg-card-hover: #162444;
            --dios-bg-input: #091020;
            --dios-border: rgba(251, 191, 36, 0.22);
            --dios-border-subtle: rgba(148, 163, 184, 0.16);
            --dios-gold: #fbbf24;
            --dios-gold-bright: #fde047;
            --dios-gold-dark: #d97706;
            --dios-cyan: #38bdf8;
            --dios-emerald: #10b981;
            --dios-crimson: #ef4444;
            --dios-purple: #a855f7;
            --dios-text: #f8fafc;
            --dios-text-muted: #94a3b8;
        }

        * {
            box-sizing: border-box;
        }

        body {
            background-color: var(--dios-bg-deep);
            background-image:
                radial-gradient(circle at 12% 10%, rgba(251, 191, 36, 0.08) 0%, transparent 40%),
                radial-gradient(circle at 88% 15%, rgba(56, 189, 248, 0.08) 0%, transparent 45%),
                radial-gradient(circle at 50% 90%, rgba(16, 185, 129, 0.05) 0%, transparent 50%);
            color: var(--dios-text);
            font-family: 'Segoe UI', system-ui, -apple-system, BlinkMacSystemFont, Roboto, sans-serif;
            min-height: 100vh;
            margin: 0;
            padding: 0 0 50px 0;
        }

        /* Top Institutional & Sovereign Banner */
        .dios-top-ribbon {
            background: linear-gradient(90deg, #040811 0%, #0f1d3a 50%, #040811 100%);
            border-bottom: 2px solid var(--dios-gold);
            padding: 10px 24px;
            box-shadow: 0 4px 25px rgba(0, 0, 0, 0.65);
            position: sticky;
            top: 0;
            z-index: 1030;
        }

        .dios-brand-logos {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .dios-logo-pill {
            background: rgba(255, 255, 255, 0.95);
            padding: 4px 10px;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 2px 10px rgba(251, 191, 36, 0.25);
        }

        .dios-logo-pill img {
            height: 36px;
            width: auto;
            object-fit: contain;
        }

        .dios-container {
            max-width: 1560px;
            margin: 22px auto;
            padding: 0 20px;
        }

        /* Hero Sovereign Header */
        .dios-hero {
            background: linear-gradient(135deg, rgba(17, 28, 53, 0.96) 0%, rgba(11, 19, 37, 0.98) 100%);
            border: 1px solid var(--dios-border);
            border-left: 5px solid var(--dios-gold);
            border-radius: 16px;
            padding: 22px 26px;
            margin-bottom: 22px;
            box-shadow: 0 14px 35px rgba(0, 0, 0, 0.45), inset 0 1px 0 rgba(255, 255, 255, 0.06);
            position: relative;
            overflow: hidden;
        }

        .dios-hero::after {
            content: '\f521';
            font-family: 'Font Awesome 5 Free';
            font-weight: 900;
            position: absolute;
            right: 25px;
            bottom: -25px;
            font-size: 130px;
            color: rgba(251, 191, 36, 0.04);
            pointer-events: none;
        }

        .dios-crown-title {
            font-size: 1.55rem;
            font-weight: 800;
            letter-spacing: 0.6px;
            color: var(--dios-gold);
            text-transform: uppercase;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 12px;
            text-shadow: 0 2px 12px rgba(251, 191, 36, 0.3);
        }

        .dios-subtitle {
            color: var(--dios-text-muted);
            font-size: 0.93rem;
            margin-top: 4px;
            margin-bottom: 0;
        }

        /* Omnipotent Quick-Jump Bar */
        .dios-omnipotent-bar {
            background: rgba(9, 16, 32, 0.85);
            border: 1px solid rgba(56, 189, 248, 0.25);
            border-radius: 14px;
            padding: 14px 18px;
            margin-bottom: 22px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.35);
        }

        .dios-omnipotent-label {
            font-weight: 700;
            font-size: 0.88rem;
            text-transform: uppercase;
            letter-spacing: 0.7px;
            color: var(--dios-cyan);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-jump-panel {
            background: linear-gradient(135deg, #162444 0%, #0f1a33 100%);
            color: #e2e8f0;
            border: 1px solid rgba(148, 163, 184, 0.28);
            border-radius: 9px;
            padding: 7px 14px;
            font-size: 0.84rem;
            font-weight: 600;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 7px;
            cursor: pointer;
        }

        .btn-jump-panel:hover {
            background: linear-gradient(135deg, var(--dios-gold) 0%, var(--dios-gold-dark) 100%);
            color: #060b16;
            border-color: var(--dios-gold);
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(251, 191, 36, 0.35);
            text-decoration: none;
        }

        /* Cards */
        .dios-card {
            background: var(--dios-bg-card);
            border: 1px solid var(--dios-border-subtle);
            border-radius: 15px;
            padding: 20px 22px;
            margin-bottom: 22px;
            box-shadow: 0 10px 28px rgba(0, 0, 0, 0.38);
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .dios-card:hover {
            border-color: rgba(251, 191, 36, 0.35);
        }

        .dios-card-gold {
            border: 1px solid var(--dios-border);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.45), 0 0 20px rgba(251, 191, 36, 0.05);
        }

        .dios-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid var(--dios-border-subtle);
            padding-bottom: 13px;
            margin-bottom: 18px;
            flex-wrap: wrap;
            gap: 10px;
        }

        .dios-card-title {
            font-size: 1.08rem;
            font-weight: 700;
            color: var(--dios-gold);
            margin: 0;
            display: flex;
            align-items: center;
            gap: 10px;
            letter-spacing: 0.3px;
        }

        /* Telemetry Stat Cards */
        .dios-stat-box {
            background: linear-gradient(145deg, #13203d 0%, #0c1528 100%);
            border: 1px solid var(--dios-border-subtle);
            border-radius: 14px;
            padding: 18px 16px;
            position: relative;
            overflow: hidden;
            height: 100%;
            transition: transform 0.2s ease, border-color 0.2s ease;
        }

        .dios-stat-box:hover {
            transform: translateY(-3px);
            border-color: var(--dios-gold);
        }

        .dios-stat-icon {
            width: 44px;
            height: 44px;
            border-radius: 11px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            margin-bottom: 12px;
        }

        .dios-stat-value {
            font-size: 1.95rem;
            font-weight: 800;
            line-height: 1.1;
            color: #ffffff;
            margin-bottom: 4px;
        }

        .dios-stat-label {
            font-size: 0.78rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.7px;
            color: var(--dios-text-muted);
        }

        .dios-stat-sub {
            font-size: 0.76rem;
            color: #cbd5e1;
            margin-top: 6px;
        }

        /* Master Control Switches */
        .dios-switch-tile {
            background: rgba(9, 16, 32, 0.75);
            border: 1px solid var(--dios-border-subtle);
            border-radius: 12px;
            padding: 15px 16px;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .dios-switch-tile.active-ok {
            border-left: 4px solid var(--dios-emerald);
        }

        .dios-switch-tile.active-warn {
            border-left: 4px solid var(--dios-gold);
        }

        .dios-switch-tile.active-danger {
            border-left: 4px solid var(--dios-crimson);
        }

        /* Tables */
        .table-dios {
            width: 100%;
            color: var(--dios-text);
            border-collapse: separate;
            border-spacing: 0;
        }

        .table-dios thead th {
            background: #091020;
            color: var(--dios-gold);
            font-size: 0.76rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.7px;
            padding: 12px 14px;
            border-top: 1px solid var(--dios-border-subtle);
            border-bottom: 2px solid rgba(251, 191, 36, 0.35);
            white-space: nowrap;
        }

        .table-dios tbody td {
            padding: 12px 14px;
            border-bottom: 1px solid rgba(148, 163, 184, 0.12);
            vertical-align: middle;
            font-size: 0.88rem;
            color: #e2e8f0;
        }

        .table-dios tbody tr:hover td {
            background: rgba(251, 191, 36, 0.05);
        }

        /* Badges */
        .badge-dios {
            padding: 5px 10px;
            border-radius: 999px;
            font-weight: 700;
            font-size: 0.72rem;
            letter-spacing: 0.4px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .badge-role-super {
            background: rgba(251, 191, 36, 0.18);
            color: #fde047;
            border: 1px solid rgba(251, 191, 36, 0.45);
        }

        .badge-role-admin {
            background: rgba(239, 68, 68, 0.18);
            color: #fca5a5;
            border: 1px solid rgba(239, 68, 68, 0.4);
        }

        .badge-role-director {
            background: rgba(168, 85, 247, 0.18);
            color: #d8b4fe;
            border: 1px solid rgba(168, 85, 247, 0.4);
        }

        .badge-role-docente {
            background: rgba(56, 189, 248, 0.18);
            color: #7dd3fc;
            border: 1px solid rgba(56, 189, 248, 0.4);
        }

        .badge-role-estudiante {
            background: rgba(16, 185, 129, 0.18);
            color: #6ee7b7;
            border: 1px solid rgba(16, 185, 129, 0.4);
        }

        .badge-status-ok {
            background: rgba(16, 185, 129, 0.2);
            color: #34d399;
            border: 1px solid rgba(16, 185, 129, 0.45);
        }

        .badge-status-blocked {
            background: rgba(239, 68, 68, 0.2);
            color: #f87171;
            border: 1px solid rgba(239, 68, 68, 0.45);
        }

        /* Inputs & Selects */
        .form-control-dios {
            background-color: var(--dios-bg-input) !important;
            border: 1px solid rgba(148, 163, 184, 0.3) !important;
            color: #ffffff !important;
            border-radius: 10px;
            padding: 9px 14px;
            font-size: 0.9rem;
        }

        .form-control-dios:focus {
            border-color: var(--dios-gold) !important;
            box-shadow: 0 0 0 3px rgba(251, 191, 36, 0.2) !important;
            outline: none;
        }

        .form-control-dios::placeholder {
            color: #64748b;
        }

        /* Buttons */
        .btn-dios-gold {
            background: linear-gradient(135deg, var(--dios-gold) 0%, var(--dios-gold-dark) 100%);
            color: #060b16;
            font-weight: 700;
            border: none;
            border-radius: 9px;
            padding: 8px 16px;
            transition: all 0.2s ease;
        }

        .btn-dios-gold:hover {
            filter: brightness(1.08);
            color: #000;
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(251, 191, 36, 0.35);
        }

        .btn-dios-action {
            padding: 5px 10px;
            font-size: 0.78rem;
            font-weight: 600;
            border-radius: 7px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: all 0.15s ease;
        }

        /* Filter Pills */
        .dios-filter-pill {
            background: #091020;
            color: var(--dios-text-muted);
            border: 1px solid var(--dios-border-subtle);
            border-radius: 999px;
            padding: 6px 14px;
            font-size: 0.8rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.18s ease;
        }

        .dios-filter-pill:hover,
        .dios-filter-pill.active {
            background: rgba(251, 191, 36, 0.16);
            color: var(--dios-gold);
            border-color: var(--dios-gold);
        }

        /* Modals in Dark/Gold Mode */
        .modal-dios .modal-content {
            background: #0f192f;
            color: var(--dios-text);
            border: 1px solid var(--dios-gold);
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.8);
        }

        .modal-dios .modal-header {
            background: #091020;
            border-bottom: 1px solid var(--dios-border);
            border-radius: 16px 16px 0 0;
        }

        .modal-dios .modal-title {
            color: var(--dios-gold);
            font-weight: 700;
        }

        .modal-dios .modal-footer {
            border-top: 1px solid var(--dios-border-subtle);
            background: #091020;
            border-radius: 0 0 16px 16px;
        }

        .modal-dios .close {
            color: #ffffff;
            text-shadow: none;
            opacity: 0.8;
        }

        /* Nav Tabs */
        .nav-tabs-dios {
            border-bottom: 1px solid var(--dios-border-subtle);
            gap: 8px;
        }

        .nav-tabs-dios .nav-link {
            background: #091020;
            color: var(--dios-text-muted);
            border: 1px solid var(--dios-border-subtle);
            border-bottom: none;
            border-radius: 10px 10px 0 0;
            padding: 10px 18px;
            font-weight: 700;
            font-size: 0.88rem;
            transition: all 0.2s ease;
        }

        .nav-tabs-dios .nav-link:hover {
            color: #ffffff;
            background: #13203d;
        }

        .nav-tabs-dios .nav-link.active {
            background: var(--dios-bg-card);
            color: var(--dios-gold);
            border-color: var(--dios-gold);
            border-bottom: 2px solid var(--dios-bg-card);
        }

        /* Pulse dot */
        .pulse-dot {
            display: inline-block;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: var(--dios-emerald);
            box-shadow: 0 0 0 rgba(16, 185, 129, 0.7);
            animation: pulseEmerald 2s infinite;
        }

        .pulse-dot.danger {
            background: var(--dios-crimson);
            animation: pulseCrimson 1.6s infinite;
        }

        @keyframes pulseEmerald {
            0% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
            70% { box-shadow: 0 0 0 9px rgba(16, 185, 129, 0); }
            100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
        }

        @keyframes pulseCrimson {
            0% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7); }
            70% { box-shadow: 0 0 0 9px rgba(239, 68, 68, 0); }
            100% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); }
        }

        @media (max-width: 768px) {
            .dios-crown-title { font-size: 1.2rem; }
            .dios-container { padding: 0 12px; }
            .dios-top-ribbon { padding: 8px 12px; }
        }
    </style>
</head>
<body>

<!-- Barra Institucional Soberana -->
<div class="dios-top-ribbon">
    <div class="d-flex align-items-center justify-content-between flex-wrap" style="max-width: 1560px; margin: 0 auto; gap: 10px;">
        <div class="dios-brand-logos">
            <div class="dios-logo-pill">
                <img src="../images/uptpc.png" alt="UPTPC" onerror="this.style.display='none'">
                <img src="../images/mppeu_bb.png" alt="MPPEU" onerror="this.style.display='none'">
            </div>
            <div>
                <div style="font-weight: 800; font-size: 0.92rem; color: var(--dios-gold); letter-spacing: 0.5px;">
                     NÚCLEO DE COMANDO SOBERANO • UPTPC
                </div>
                <div style="font-size: 0.76rem; color: var(--dios-text-muted);">
                    Control Absoluto de Infraestructura, Usuarios, Roles, Ciberseguridad y Base de Datos
                </div>
            </div>
        </div>
        <div class="d-flex align-items-center" style="gap: 10px;">
            <span class="badge-dios" style="background: rgba(56,189,248,0.12); color: #7dd3fc; border: 1px solid rgba(56,189,248,0.3);">
                <i class="fas fa-network-wired"></i> IP: <?php echo htmlspecialchars($_SERVER['REMOTE_ADDR'] ?? '127.0.0.1'); ?>
            </span>
            <span class="badge-dios" style="background: rgba(251,191,36,0.14); color: var(--dios-gold); border: 1px solid rgba(251,191,36,0.35);">
                <i class="fas fa-clock"></i> <span id="diosLiveClock"><?php echo date('d/m/Y H:i:s'); ?></span>
            </span>
            <a href="logout.php" class="btn btn-sm btn-danger font-weight-bold" style="border-radius: 8px;">
                <i class="fas fa-power-off"></i> Salir de Bóveda
            </a>
        </div>
    </div>
</div>

<div class="dios-container">