<?php
// .dios/logout.php - Desconexión Soberana y Limpieza de Huellas de Sesión
require_once __DIR__ . '/config.php';

// Caso 1: Salir solo del Modo Fantasma (Salto) y volver a Bóveda Dios
if (isset($_GET['salir_fantasma']) || isset($_GET['salir_salto'])) {
    if (!empty($_COOKIE['PHPSESSID'])) {
        session_name('PHPSESSID');
        session_id($_COOKIE['PHPSESSID']);
        @session_start();
        unset($_SESSION['user'], $_SESSION['dios_fantasma'], $_SESSION['current_profile']);
        session_write_close();
    }
    header('Location: index.php?msg=salto_terminado');
    exit;
}

// Caso 2: Cierre total de sesión de MODO DIOS
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (session_status() === PHP_SESSION_ACTIVE) {
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 3600, $params['path'], $params['domain'] ?? '', $params['secure'] ?? false, $params['httponly'] ?? true);
    }
    session_destroy();
}

// Limpiar flags de la sesión principal (PHPSESSID)
if (!empty($_COOKIE['PHPSESSID'])) {
    session_name('PHPSESSID');
    session_id($_COOKIE['PHPSESSID']);
    @session_start();
    unset($_SESSION['dios_autenticado'], $_SESSION['dios_fantasma']);
    session_write_close();
}

// Redirigir a la Bóveda de Acceso Maestro
header('Location: login.php');
exit;
?>