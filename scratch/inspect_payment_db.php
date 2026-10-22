<?php
require_once 'funciones/functions.php';
global $db;

echo "=== TABLAS EN BASE DE DATOS ===\n";
$tables = ['pagos', 'tipo_pago', 'bancos', 'cuentas_bancarias', 'metodos_pago'];
foreach ($tables as $t) {
    $res = $db->query("SHOW TABLES LIKE '{$t}'");
    if ($res && $res->num_rows > 0) {
        echo "Tabla [{$t}] EXISTE:\n";
        $cols = $db->query("SHOW COLUMNS FROM `{$t}`");
        while ($c = $cols->fetch_assoc()) {
            echo "   - {$c['Field']} ({$c['Type']}) NULL: {$c['Null']} DEFAULT: {$c['Default']}\n";
        }
    } else {
        echo "Tabla [{$t}] NO EXISTE.\n";
    }
}
