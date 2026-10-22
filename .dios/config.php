<?php
// .dios/config.php - Configuración de Máxima Ciberseguridad del MODO DIOS
// ⚠️ ACCESO RESTRINGIDO EXCLUSIVAMENTE AL AMO Y DUEÑO DEL SISTEMA ⚠️

// 1. Identidad Maestra
define('DIOS_USER', 'Dios');

// 2. Contraseña Maestra: "dios123" (Bcrypt cost 12)
define('DIOS_PASS_HASH', '$2y$12$3r1zF7LRaDTpUDSoBwGhju9nG/B7Ph5X1W1nmm7gP1IorazzS/KOW');

// 3. PIN Maestro de Bóveda (2FA): "7777" (Bcrypt cost 12)
define('DIOS_PIN_HASH', '$2y$12$U9C1cMPtlsyezEGD9bZeB.HF5cOMnfRH1zO7bV2vMshdjtpQIjtX2');

// 4. Parámetros de Seguridad de Sesión
define('DIOS_MAX_ATTEMPTS', 3);          // Máximo 3 intentos antes de bloqueo
define('DIOS_LOCKOUT_TIME', 1200);       // 20 minutos de bloqueo tras fallos
define('DIOS_SESSION_TIMEOUT', 1200);    // 20 minutos de inactividad máxima
define('DIOS_FOLDER', '/control_de_estudio/.dios/');

// Iniciar sesión aislada DIOS_SESSION con atributos de máxima seguridad
if (session_status() === PHP_SESSION_NONE) {
    session_name('DIOS_SESSION');
    $secureCookie = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (!empty($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443);

    session_set_cookie_params([
        'path' => '/control_de_estudio/.dios/',
        'httponly' => true,
        'samesite' => 'Strict',
        'secure' => $secureCookie
    ]);
    session_start();
}

// Control Anti-Hijacking y Auto-Bloqueo por Inactividad
if (!empty($_SESSION['dios_autenticado'])) {
    // A. Verificar inactividad
    if (isset($_SESSION['dios_last_activity']) && (time() - $_SESSION['dios_last_activity'] > DIOS_SESSION_TIMEOUT)) {
        $_SESSION = [];
        if (session_id()) {
            session_destroy();
        }
        header('Location: ' . DIOS_FOLDER . 'login.php?motivo=expirado');
        exit;
    }
    $_SESSION['dios_last_activity'] = time();

    // B. Huella de sesión (IP + User-Agent)
    $client_ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    $client_ua = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
    $current_fp = hash('sha256', $client_ip . '|' . $client_ua);

    if (!empty($_SESSION['dios_fingerprint']) && !hash_equals($_SESSION['dios_fingerprint'], $current_fp)) {
        // Alerta de secuestro de sesión
        $_SESSION = [];
        if (session_id()) {
            session_destroy();
        }
        header('Location: ' . DIOS_FOLDER . 'login.php?motivo=secuestro');
        exit;
    }
}

// Cabeceras HTTP de Ciberseguridad Avanzada
if (!headers_sent()) {
    header('X-Frame-Options: SAMEORIGIN');
    header('X-Content-Type-Options: nosniff');
    header('X-XSS-Protection: 1; mode=block');
    header('Referrer-Policy: no-referrer');
    header('Permissions-Policy: interest-cohort=(), camera=(), microphone=(), geolocation=()');
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    header('Pragma: no-cache');
}

// Inicializar tokens de protección CSRF y AJAX
if (session_status() === PHP_SESSION_ACTIVE) {
    if (empty($_SESSION['dios_ajax_token'])) {
        $_SESSION['dios_ajax_token'] = bin2hex(random_bytes(16));
    }
    if (empty($_SESSION['dios_csrf_token'])) {
        $_SESSION['dios_csrf_token'] = bin2hex(random_bytes(32));
    }
}
?>