<?php
/**
 * Herramienta de Sincronización y Actualización de Base de Datos - UPTPC
 * Sincroniza los archivos SQL del proyecto control_de_estudio con la base de datos 'uptpc'.
 */

// Configuración de base de datos
$dbHost = 'localhost';
$dbUser = 'root';
$dbPass = '01012023';
$dbName = 'uptpc';
$baseDir = __DIR__;

$isCli = (php_sapi_name() === 'cli');

function logMsg($msg, $type = 'info') {
    global $isCli;
    $time = date('Y-m-d H:i:s');
    if ($isCli) {
        $prefix = match($type) {
            'success' => "\033[32m[ÉXITO]\033[0m",
            'error'   => "\033[31m[ERROR]\033[0m",
            'warning' => "\033[33m[AVISO]\033[0m",
            default   => "\033[34m[INFO]\033[0m"
        };
        echo "[{$time}] {$prefix} {$msg}\n";
    } else {
        $color = match($type) {
            'success' => 'green',
            'error'   => 'red',
            'warning' => 'orange',
            default   => 'blue'
        };
        echo "<div style='color: {$color}; margin: 4px 0; font-family: monospace;'>[{$time}] <strong>" . strtoupper($type) . ":</strong> " . htmlspecialchars($msg) . "</div>";
        ob_flush();
        flush();
    }
}

if (!$isCli) {
    echo "<!DOCTYPE html><html><head><meta charset='utf-8'><title>Sincronizador DB UPTPC</title></head><body style='background:#1e1e2e; color:#cdd6f4; padding:20px; font-family:sans-serif;'>";
    echo "<h2>Sincronización de Base de Datos UPTPC (<code>{$dbName}</code>)</h2><hr>";
}

logMsg("Iniciando proceso de sincronización de la base de datos '{$dbName}'...");

// 1. Probar conexión a MariaDB/MySQL
$mysqli = @new mysqli($dbHost, $dbUser, $dbPass);
if ($mysqli->connect_error) {
    logMsg("Error de conexión a MariaDB/MySQL: " . $mysqli->connect_error, 'error');
    exit(1);
}

// 2. Crear la base de datos si no existe
if (!$mysqli->query("CREATE DATABASE IF NOT EXISTS `{$dbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci")) {
    logMsg("No se pudo crear/verificar la base de datos '{$dbName}': " . $mysqli->error, 'error');
    exit(1);
}
$mysqli->select_db($dbName);
logMsg("Base de datos '{$dbName}' seleccionada correctamente.", 'success');

// Lista ordenada de archivos SQL prioritarios
$sqlFilesOrder = [
    'proyecto_tsu.sql',
    'crear_preinscripcion_table.sql',
    'crear_secretaria_cupos_table.sql',
    'crear_codigos_secciones_table.sql',
    'temp_create_secciones.sql'
];

// Buscar otros archivos .sql en la raíz que no estén en la lista inicial
$allRootSqlFiles = glob("{$baseDir}/*.sql");
foreach ($allRootSqlFiles as $file) {
    $basename = basename($file);
    if (!in_array($basename, $sqlFilesOrder)) {
        $sqlFilesOrder[] = $basename;
    }
}

// 3. Ejecutar sincronización de cada archivo SQL
$totalFiles = count($sqlFilesOrder);
$processed = 0;
$errors = 0;

$mysqlBin = file_exists('/usr/bin/mariadb') ? '/usr/bin/mariadb' : (file_exists('/usr/bin/mysql') ? '/usr/bin/mysql' : 'mysql');

foreach ($sqlFilesOrder as $sqlFilename) {
    $fullPath = "{$baseDir}/{$sqlFilename}";
    if (!file_exists($fullPath)) {
        logMsg("Archivo no encontrado: {$sqlFilename}. Saltando...", 'warning');
        continue;
    }

    $sizeKb = round(filesize($fullPath) / 1024, 2);
    logMsg("Procesando [{$sqlFilename}] ({$sizeKb} KB)...");

    $startTime = microtime(true);
    
    // Ejecución mediante cliente de línea de comandos de MariaDB/MySQL para máximo rendimiento e integridad SQL
    $cmd = sprintf(
        '%s -h %s -u %s %s %s --force --default-character-set=utf8mb4 %s < %s 2>&1',
        escapeshellcmd($mysqlBin),
        escapeshellarg($dbHost),
        escapeshellarg($dbUser),
        !empty($dbPass) ? '-p' . escapeshellarg($dbPass) : '',
        escapeshellarg($dbName),
        '--init-command="SET FOREIGN_KEY_CHECKS=0;"',
        escapeshellarg($fullPath)
    );

    exec($cmd, $output, $returnVar);

    $duration = round(microtime(true) - $startTime, 3);

    if ($returnVar === 0) {
        logMsg("Sincronizado con éxito: {$sqlFilename} ({$duration}s)", 'success');
        $processed++;
    } else {
        $outStr = implode("\n", $output);
        // Filtrar advertencias menores de mariadb sobre el nombre del programa
        $filteredOut = array_filter($output, fn($line) => !str_contains($line, 'Deprecated program name'));
        if (empty($filteredOut)) {
            logMsg("Sincronizado con éxito (con avisos menores): {$sqlFilename} ({$duration}s)", 'success');
            $processed++;
        } else {
            logMsg("Error al ejecutar {$sqlFilename}: " . implode(" ", $filteredOut), 'error');
            $errors++;
        }
    }
}

// 4. Verificación del estado final de las tablas en la base de datos
logMsg("Verificando resumen de tablas en '{$dbName}'...");
$res = $mysqli->query("SHOW TABLES FROM `{$dbName}`");
$tables = [];
if ($res) {
    while ($row = $res->fetch_array()) {
        $tableName = $row[0];
        $cntRes = $mysqli->query("SELECT COUNT(*) FROM `{$dbName}`.`{$tableName}`");
        $count = ($cntRes && $cRow = $cntRes->fetch_array()) ? $cRow[0] : 0;
        $tables[] = "{$tableName} ({$count} registros)";
    }
}

$totalTables = count($tables);
logMsg("--------------------------------------------------", 'info');
logMsg("RESUMEN DE SINCRONIZACIÓN:", 'success');
logMsg("Archivos procesados correctamente: {$processed} / {$totalFiles}");
logMsg("Errores detectados: {$errors}");
logMsg("Total de tablas activas en '{$dbName}': {$totalTables}");
logMsg("Lista de tablas sincronizadas:\n  - " . implode("\n  - ", $tables));
logMsg("--------------------------------------------------", 'info');

if (!$isCli) {
    echo "</body></html>";
}
