<?php
/**
 * =========================================================================================
 * SISTEMA DE CONTROL DE ESTUDIOS - MÓDULO CENTRALIZADO DE PROTECCIÓN DE DIRECTORIOS
 * =========================================================================================
 * Archivo: funciones/proteger_directorio.php
 * Propósito: Módulo reutilizable de seguridad que centraliza la política de protección y
 *            redirección de carpetas confidenciales (soportes, comprobantes de pago, fotos,
 *            imágenes, etc.), eliminando al 100% la duplicación de código en el sistema.
 * 
 * Política de Redirección y Acceso:
 * 1. Usuario NO autenticado (!isLoggedIn()) -> Redirigir a login.php
 * 2. Usuario autenticado con múltiples perfiles -> Redirigir al selector (profile_selector.php)
 * 3. Usuario autenticado con un solo perfil -> Redirigir directamente al panel de su perfil
 * =========================================================================================
 */

// Cargar librerías centrales y arranque de sesión
require_once __DIR__ . '/functions.php';

// Obtener la carpeta base del sistema
$base_url = $GLOBALS['carpeta'] ?? '/control_de_estudio';

// 1. Validar autenticación activa
if (!isLoggedIn()) {
    header('Location: ' . $base_url . '/login.php');
    exit();
}

// 2. Determinar perfiles disponibles del usuario autenticado
$availableProfiles = [];
if (function_exists('getAvailableProfiles')) {
    $availableProfiles = getAvailableProfiles();
} elseif (isset($_SESSION['user']['available_profiles']) && is_array($_SESSION['user']['available_profiles'])) {
    $availableProfiles = $_SESSION['user']['available_profiles'];
}

// Si no están en sesión, calcular directamente por funciones de verificación de rol
if (!is_array($availableProfiles) || empty($availableProfiles)) {
    $availableProfiles = [];
    if (isAdmin()) {
        $availableProfiles[] = 'admin';
    }
    if (function_exists('isSuperUser') && isSuperUser()) {
        $availableProfiles[] = 'super_user';
    }
    if (isDocente()) {
        $availableProfiles[] = 'docente';
    }
    if (isEstudiante()) {
        $availableProfiles[] = 'estudiante';
    }
    if (isUser()) {
        $availableProfiles[] = 'director_de_carrera';
    }
}

// 3. Mapeo de rutas hacia cada panel
$profileRoutes = [
    'admin'               => $base_url . '/admin/index.php',
    'super_user'          => $base_url . '/super_user/index.php',
    'docente'             => $base_url . '/docente/index.php',
    'estudiante'          => $base_url . '/estudiante/index.php',
    'director_de_carrera' => $base_url . '/director_de_carrera/index.php'
];

// 4. Redirección según la cantidad de perfiles
$cantidadPerfiles = count($availableProfiles);

if ($cantidadPerfiles === 1) {
    // Un solo perfil: enviar directamente a su panel correspondiente
    $perfilUnico = $availableProfiles[0];
    $_SESSION['current_profile'] = $perfilUnico;
    $destino = $profileRoutes[$perfilUnico] ?? ($base_url . '/index.php');
    header('Location: ' . $destino);
    exit();
} elseif ($cantidadPerfiles > 1) {
    // Múltiples perfiles: enviar al selector de perfiles
    header('Location: ' . $base_url . '/profile_selector.php');
    exit();
} else {
    // Caso extraordinario sin perfiles identificados
    header('Location: ' . $base_url . '/login.php');
    exit();
}
