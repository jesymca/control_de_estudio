import subprocess

php_code = """<?php
require_once 'funciones/functions.php';
global $db;

$tables = ['estudiante_materias', 'notas_definitivas', 'notas_trimestres', 'estudiante_seccion', 'secciones', 'inscripciones', 'materias'];

foreach ($tables as $t) {
    echo "=== TABLE: $t ===\\n";
    $res = $db->query("DESCRIBE $t");
    if ($res) {
        while ($row = $res->fetch_assoc()) {
            echo "  {$row['Field']} - {$row['Type']} - Null:{$row['Null']} - Default:{$row['Default']}\\n";
        }
    } else {
        echo "  Table $t not found or error: " . $db->error . "\\n";
    }
}
"""

with open(r'C:\xampp\htdocs\control_de_estudio\scratch\describe_tables.php', 'w') as f:
    f.write(php_code)

p = subprocess.run(['C:\\xampp\\php\\php.exe', r'C:\xampp\htdocs\control_de_estudio\scratch\describe_tables.php'], capture_output=True, text=True)
print(p.stdout)
