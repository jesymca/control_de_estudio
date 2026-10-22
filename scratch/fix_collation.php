<?php
require_once 'funciones/functions.php';
global $db;

$db->query("ALTER TABLE pagos MODIFY COLUMN tipo_pago VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL");
$db->query("ALTER TABLE pagos MODIFY COLUMN otro_concepto VARCHAR(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL");
$db->query("ALTER TABLE pagos MODIFY COLUMN metodo_pago VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Transferencia'");
$db->query("ALTER TABLE pagos MODIFY COLUMN banco_origen VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL");
$db->query("ALTER TABLE pagos MODIFY COLUMN referencia VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL");
$db->query("ALTER TABLE pagos MODIFY COLUMN comprobante VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL");
$db->query("ALTER TABLE pagos MODIFY COLUMN status_pago VARCHAR(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'aprobado'");
$db->query("ALTER TABLE pagos MODIFY COLUMN motivo_rechazo TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL");
$db->query("ALTER TABLE pagos MODIFY COLUMN observaciones TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL");

$db->query("ALTER TABLE tipo_pago MODIFY COLUMN tipopago VARCHAR(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL");
$db->query("ALTER TABLE bancos CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci");

echo "Columnas y tablas ajustadas a utf8mb4_general_ci exitosamente.\n";
