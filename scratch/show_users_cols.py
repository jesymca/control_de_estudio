import subprocess

php_test = """<?php
require_once 'funciones/functions.php';
global $db;
$res = $db->query("SHOW COLUMNS FROM users");
while ($r = $res->fetch_assoc()) {
    echo $r['Field'] . " (" . $r['Type'] . ")\\n";
}
"""

with open(r'C:\xampp\htdocs\control_de_estudio\scratch\show_users_cols.php', 'w') as f:
    f.write(php_test)

p = subprocess.run(['C:\\xampp\\php\\php.exe', r'C:\xampp\htdocs\control_de_estudio\scratch\show_users_cols.php'], capture_output=True)
print(p.stdout.decode('utf-8', errors='ignore'))
