<?php
// admin/gestion_seccion/index.php

// Obtener la URL solicitada
$request_uri = $_SERVER['REQUEST_URI'];

// Extraer el nombre del archivo de la URL
// Ejemplo: /control_de_estudio/admin/gestion_seccion/estudiantes.php -> estudiantes.php
preg_match('/gestion_seccion\/([^?]+)/', $request_uri, $matches);

if (isset($matches[1])) {
    $filename = $matches[1];
} else {
    $filename = '';
}

// Obtener parámetros GET
$query_string = $_SERVER['QUERY_STRING'];

// ============================================
// PÁGINAS LOCALES (dentro de esta carpeta)
// ============================================
$paginas_locales = [
    'gestion_seccion.php',
    'crear_seccion.php', 
    'editar_seccion.php',
    'ver_seccion.php',
    'horario_seccion.php',
    'asignar_estudiantes.php',
    'procesar_retiro.php'
];

// Si es una página local, incluir el archivo
if (in_array($filename, $paginas_locales)) {
    $local_file = __DIR__ . '/' . $filename;
    if (file_exists($local_file)) {
        include($local_file);
        exit();
    }
}

// ============================================
// REDIRIGIR A ADMIN
// ============================================

// Si no hay archivo o es index.php
if (empty($filename) || $filename == 'index.php') {
    header("Location: ../index.php");
    exit();
}

// Construir la URL de redirección
$redirect = "../" . $filename;
if (!empty($query_string)) {
    $redirect .= "?" . $query_string;
}

// Redirigir
header("Location: " . $redirect);
exit();
?>