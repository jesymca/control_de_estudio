<?php
require_once 'funciones/functions.php';
global $db;
$res = $db->query("SHOW COLUMNS FROM users");
while ($r = $res->fetch_assoc()) {
    echo $r['Field'] . " (" . $r['Type'] . ")\n";
}
