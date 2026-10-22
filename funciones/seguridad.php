<?php
// funciones/seguridad.php - Módulo de ciberseguridad (CORREGIDO)
// NO iniciar sesión aquí, ya se inicia en el archivo que lo llama

// Evitar redeclaración si el archivo fue incluido más de una vez
if (!class_exists('Seguridad')) {

class Seguridad {
    private $db;
    private $ip;
    private $user_agent;
    
    public function __construct($db_connection) {
        $this->db = $db_connection;
        $this->ip = $this->getRealIP();
        $this->user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'desconocido';
    }
    
    // Obtener IP real (incluso detrás de proxies)
    private function getRealIP() {
        $headers = ['HTTP_CF_CONNECTING_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'REMOTE_ADDR'];
        foreach ($headers as $header) {
            if (!empty($_SERVER[$header])) {
                $ips = explode(',', $_SERVER[$header]);
                return trim($ips[0]);
            }
        }
        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }
    
    // ==============================================
    // 1. CONTROL DE ACCESO AL SISTEMA (cerrar/abrir)
    // ==============================================
    public function sistemaEstaActivo() {
        return $this->obtenerConfiguracion('sistema_activo', '1') == '1';
    }
    
    public function sistemaEnMantenimiento() {
        return $this->obtenerConfiguracion('modo_mantenimiento', '0') == '1';
    }
    
    // Verificar si el usuario ADMIN puede acceder al panel
    public function verificarAccesoAdmin() {
        if (!isset($_SESSION['user']['super_user']) || $_SESSION['user']['super_user'] != 1) {
            header('Location: login.php');
            exit;
        }
    }
    
    // ==============================================
    // 2. VALIDACIÓN DE TOKEN (detección de alteración)
    // ==============================================
    public function validarToken($token) {
        // Verificar que el token tenga formato hexadecimal (64 caracteres)
        if (!preg_match('/^[a-f0-9]{64}$/', $token)) {
            $this->registrarTokenInvalido($token);
            return false;
        }
        
        // Buscar token en BD
        $sql = "SELECT * FROM password_resets WHERE token = ? AND usado = 0 AND expira > NOW()";
        $stmt = mysqli_prepare($this->db, $sql);
        mysqli_stmt_bind_param($stmt, "s", $token);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if (mysqli_num_rows($result) == 0) {
            $this->registrarTokenInvalido($token);
            return false;
        }
        
        return mysqli_fetch_assoc($result);
    }
    
    private function registrarTokenInvalido($token) {
        $sql = "INSERT INTO seguridad_tokens_invalidos (token_recibido, ip, user_agent) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($this->db, $sql);
        mysqli_stmt_bind_param($stmt, "sss", $token, $this->ip, $this->user_agent);
        mysqli_stmt_execute($stmt);
    }
    
    // ==============================================
    // 3. CONTROL DE INTENTOS (bloqueos progresivos)
    // ==============================================
    public function verificarIntentos($email = null, $tipo = 'recuperar') {
        // Verificar bloqueo por IP
        $bloqueo_ip = $this->estaBloqueado($this->ip, null, $tipo);
        if ($bloqueo_ip) {
            return ['permitido' => false, 'mensaje' => "IP bloqueada hasta: " . $bloqueo_ip, 'bloqueado_hasta' => $bloqueo_ip];
        }
        
        // Verificar bloqueo por email (si se proporcionó)
        if ($email) {
            $bloqueo_email = $this->estaBloqueado(null, $email, $tipo);
            if ($bloqueo_email) {
                return ['permitido' => false, 'mensaje' => "Esta cuenta está bloqueada temporalmente", 'bloqueado_hasta' => $bloqueo_email];
            }
        }
        
        return ['permitido' => true];
    }
    
    private function estaBloqueado($ip = null, $email = null, $tipo = null) {
        $sql = "SELECT desbloqueo_en FROM seguridad_bloqueos WHERE activo = 1 AND desbloqueo_en > NOW()";
        if ($ip) {
            $sql .= " AND ip = '$ip'";
        }
        if ($email) {
            $sql .= " AND email = '$email'";
        }
        if ($tipo) {
            $sql .= " AND motivo LIKE '%$tipo%'";
        }
        $sql .= " ORDER BY desbloqueo_en DESC LIMIT 1";
        
        $result = mysqli_query($this->db, $sql);
        if ($row = mysqli_fetch_assoc($result)) {
            return $row['desbloqueo_en'];
        }
        return false;
    }
    
    public function registrarIntentoFallido($email = null, $tipo = 'recuperar') {
        // Registrar intento
        $sql = "INSERT INTO seguridad_intentos (ip, email, tipo, user_agent) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($this->db, $sql);
        mysqli_stmt_bind_param($stmt, "ssss", $this->ip, $email, $tipo, $this->user_agent);
        mysqli_stmt_execute($stmt);
        
        // Contar intentos en la última hora
        $limite = $this->obtenerConfiguracion('limite_recuperar_por_hora', 3);
        $intentos = $this->contarIntentosRecientes($email, $tipo);
        
        // Determinar tiempo de bloqueo
        $tiempo_bloqueo = $this->calcularTiempoBloqueo($email, $intentos);
        
        if ($intentos >= $limite && $tiempo_bloqueo) {
            $this->crearBloqueo($email, $tipo, $tiempo_bloqueo, $intentos);
            return $tiempo_bloqueo;
        }
        
        return false;
    }
    
    private function contarIntentosRecientes($email = null, $tipo = 'recuperar') {
        $sql = "SELECT COUNT(*) as total FROM seguridad_intentos 
                WHERE tipo = ? AND fecha > DATE_SUB(NOW(), INTERVAL 1 HOUR)";
        $params = [$tipo];
        $types = "s";
        
        if ($email) {
            $sql .= " AND email = ?";
            $params[] = $email;
            $types .= "s";
        } else {
            $sql .= " AND ip = ?";
            $params[] = $this->ip;
            $types .= "s";
        }
        
        $stmt = mysqli_prepare($this->db, $sql);
        mysqli_stmt_bind_param($stmt, $types, ...$params);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        
        return $row['total'];
    }
    
    private function calcularTiempoBloqueo($email, $intentos) {
        $primera_hora = $this->obtenerConfiguracion('limite_bloqueo_horas', 1);
        $segunda_hora = $this->obtenerConfiguracion('limite_bloqueo_incremento', 24);
        
        // Si es la primera vez que excede (3-5 intentos) -> 1 hora
        if ($intentos <= 5) {
            return $primera_hora;
        }
        // Si sigue insistiendo (6+ intentos) -> 24 horas
        else {
            return $segunda_hora;
        }
    }
    
    private function crearBloqueo($email, $tipo, $horas, $intentos) {
        $desbloqueo = date('Y-m-d H:i:s', strtotime("+$horas hours"));
        $motivo = $tipo . '_fallido';
        
        // Desactivar bloqueos anteriores
        $sql = "UPDATE seguridad_bloqueos SET activo = 0 WHERE email = ? AND motivo = ?";
        $stmt = mysqli_prepare($this->db, $sql);
        mysqli_stmt_bind_param($stmt, "ss", $email, $motivo);
        mysqli_stmt_execute($stmt);
        
        // Crear nuevo bloqueo
        $sql = "INSERT INTO seguridad_bloqueos (ip, email, motivo, desbloqueo_en, intentos) 
                VALUES (?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($this->db, $sql);
        mysqli_stmt_bind_param($stmt, "ssssi", $this->ip, $email, $motivo, $desbloqueo, $intentos);
        mysqli_stmt_execute($stmt);
    }
    
    // ==============================================
    // 4. CONTROL RPS (Requests Per Second)
    // ==============================================
    public function verificarRPS($endpoint) {
    global $db;
    
    // ==============================================
    // 1. ASEGURAR QUE LA TABLA EXISTA
    // ==============================================
    $sql = "SHOW TABLES LIKE 'seguridad_rps'";
    $result = mysqli_query($this->db, $sql);
    if (mysqli_num_rows($result) == 0) {
        $sql = "CREATE TABLE IF NOT EXISTS `seguridad_rps` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `ip` varchar(45) NOT NULL,
            `endpoint` varchar(100) NOT NULL,
            `fecha` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            KEY `idx_ip_fecha` (`ip`, `fecha`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
        mysqli_query($this->db, $sql);
    }
    
    // ==============================================
    // 2. LIMPIAR REGISTROS ANTIGUOS (más de 10 segundos)
    // ==============================================
    $sql = "DELETE FROM seguridad_rps WHERE fecha < DATE_SUB(NOW(), INTERVAL 10 SECOND)";
    mysqli_query($this->db, $sql);
    
    // ==============================================
    // 3. INSERTAR SIEMPRE LA PETICIÓN ACTUAL
    // ==============================================
    $sql = "INSERT INTO seguridad_rps (ip, endpoint) VALUES (?, ?)";
    $stmt = mysqli_prepare($this->db, $sql);
    mysqli_stmt_bind_param($stmt, "ss", $this->ip, $endpoint);
    $insertado = mysqli_stmt_execute($stmt);
    
    // Si falla la inserción, registrar error y permitir (para no bloquear)
    if (!$insertado) {
        error_log("Error al insertar en seguridad_rps: " . mysqli_error($this->db));
        return ['permitido' => true];
    }
    
    // ==============================================
    // 4. CONTAR PETICIONES DE ESTA IP (últimos 10 segundos)
    // ==============================================
    $sql = "SELECT COUNT(*) as total FROM seguridad_rps 
            WHERE ip = ? AND endpoint = ? 
            AND fecha > DATE_SUB(NOW(), INTERVAL 10 SECOND)";
    $stmt = mysqli_prepare($this->db, $sql);
    mysqli_stmt_bind_param($stmt, "ss", $this->ip, $endpoint);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    $total_ip = $row['total'] ?? 0;
    
    // Límite: 15 peticiones por IP en 10 segundos (más permisivo para pruebas)
    $limite_individual = 15;
    
    if ($total_ip >= $limite_individual) {
        return ['permitido' => false, 'mensaje' => "Demasiadas peticiones desde tu IP. Espera unos segundos."];
    }
    
    // ==============================================
    // 5. VERIFICACIÓN GLOBAL (10% de usuarios)
    // ==============================================
    $total_usuarios = $this->obtenerConfiguracion('total_usuarios', 0);
    
    // Si no hay usuarios, usar 128 como base
    if ($total_usuarios == 0 || $total_usuarios < 10) {
        $total_usuarios = 128;
    }
    
    $porcentaje = 10;
    $limite_global = ceil($total_usuarios * ($porcentaje / 100));
    
    // Mínimo 20 peticiones globales
    if ($limite_global < 20) {
        $limite_global = 20;
    }
    
    // Contar peticiones GLOBALES en los últimos 10 segundos
    $sql = "SELECT COUNT(*) as total FROM seguridad_rps 
            WHERE endpoint = ? 
            AND fecha > DATE_SUB(NOW(), INTERVAL 10 SECOND)";
    $stmt = mysqli_prepare($this->db, $sql);
    mysqli_stmt_bind_param($stmt, "s", $endpoint);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row_global = mysqli_fetch_assoc($result);
    $total_global = $row_global['total'] ?? 0;
    
    if ($total_global >= $limite_global) {
        return ['permitido' => false, 'mensaje' => "El sistema está congestionado. Intenta más tarde."];
    }
    
    return ['permitido' => true];
}
    
    // ==============================================
    // 5. FUNCIONES DE CONFIGURACIÓN (PÚBLICAS)
    // ==============================================
    public function obtenerConfiguracion($clave, $default = null) {
        $sql = "SELECT valor FROM seguridad_sistema WHERE clave = ?";
        $stmt = mysqli_prepare($this->db, $sql);
        mysqli_stmt_bind_param($stmt, "s", $clave);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if ($row = mysqli_fetch_assoc($result)) {
            return $row['valor'];
        }
        return $default;
    }
    
    public function actualizarConfiguracion($clave, $valor) {
        $sql = "UPDATE seguridad_sistema SET valor = ? WHERE clave = ?";
        $stmt = mysqli_prepare($this->db, $sql);
        mysqli_stmt_bind_param($stmt, "ss", $valor, $clave);
        return mysqli_stmt_execute($stmt);
    }
    
    // ==============================================
    // 6. ESTADÍSTICAS PARA EL PANEL
    // ==============================================
    public function obtenerEstadisticas() {
        $stats = [];
        
        // Intentos hoy
        $sql = "SELECT COUNT(*) as total FROM seguridad_intentos WHERE DATE(fecha) = CURDATE()";
        $result = mysqli_query($this->db, $sql);
        $stats['intentos_hoy'] = mysqli_fetch_assoc($result)['total'];
        
        // Bloqueos activos
        $sql = "SELECT COUNT(*) as total FROM seguridad_bloqueos WHERE activo = 1 AND desbloqueo_en > NOW()";
        $result = mysqli_query($this->db, $sql);
        $stats['bloqueos_activos'] = mysqli_fetch_assoc($result)['total'];
        
        // Tokens inválidos (posibles ataques)
        $sql = "SELECT COUNT(*) as total FROM seguridad_tokens_invalidos WHERE DATE(fecha) = CURDATE()";
        $result = mysqli_query($this->db, $sql);
        $stats['tokens_invalidos_hoy'] = mysqli_fetch_assoc($result)['total'];
        
        // RPS actual
        $sql = "SELECT COUNT(*) as total FROM seguridad_rps WHERE fecha > DATE_SUB(NOW(), INTERVAL 10 SECOND)";
        $result = mysqli_query($this->db, $sql);
        $stats['rps_actual'] = mysqli_fetch_assoc($result)['total'];
        
        // Intentos por IP (top 5)
        $sql = "SELECT ip, COUNT(*) as total FROM seguridad_intentos WHERE fecha > DATE_SUB(NOW(), INTERVAL 1 HOUR) GROUP BY ip ORDER BY total DESC LIMIT 5";
        $stats['top_ips'] = mysqli_query($this->db, $sql);
        
        return $stats;
    }


    // ==============================================
    // 7. CONTROL DE SISTEMA COMPLETO (Cierre total)
    // ==============================================
    public function sistemaCompletoActivo() {
        return $this->obtenerConfiguracion('sistema_completo_activo', '1') == '1';
    }
    
    public function cerrarSistemaCompleto($razon = '', $usuario = '') {
        $this->actualizarConfiguracion('sistema_completo_activo', '0');
        $this->actualizarConfiguracion('razon_cierre', $razon);
        $this->actualizarConfiguracion('ultimo_cierre_por', $usuario);
        $this->actualizarConfiguracion('fecha_cierre', date('Y-m-d H:i:s'));
    }
    
    public function abrirSistemaCompleto() {
        $this->actualizarConfiguracion('sistema_completo_activo', '1');
        $this->actualizarConfiguracion('razon_cierre', '');
    }
    
    public function verificarSistemaAbierto() {
        if (!$this->sistemaCompletoActivo()) {
            $razon = $this->obtenerConfiguracion('razon_cierre', 'Mantenimiento del sistema');
            $mensaje = <<<HTML
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset="UTF-8">
                <title>Sistema Temporalmente Cerrado</title>
                <meta name="viewport" content="width=device-width, initial-scale=1">
                <style>
                    body { background: #eaf4ff; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin:0; padding:20px; }
                    .uptpc-closed-wrap { display:flex; align-items:center; justify-content:center; min-height:100vh; }
                    .uptpc-card { background:#ffffff; border-radius:14px; max-width:760px; width:100%; padding:24px; box-shadow:0 12px 30px rgba(0,0,0,0.06); border:1px solid #dfeeff; }
                    .uptpc-logo { text-align:center; margin-bottom:12px; }
                    .uptpc-bar { background:#003d82; color:#ffffff; padding:12px 16px; border-radius:8px; margin-bottom:12px; }
                    .uptpc-bar h1 { margin:0; font-size:20px; }
                    .uptpc-desc { color:#27507a; background:#f2f9ff; border:1px solid #d7e9ff; padding:12px; border-radius:8px; }
                    .uptpc-reason { margin-top:12px; background:#f8fbff; border-left:4px solid #004b9e; padding:12px; border-radius:6px; color:#042a5b; }
                    .uptpc-actions { margin-top:16px; display:flex; justify-content:flex-end; gap:10px; }
                    .btn { padding:10px 16px; border-radius:8px; text-decoration:none; font-weight:600; }
                    .btn-primary { background:#003d82; color:#fff; }
                    .btn-secondary { background:#f0f4fb; color:#003d82; border:1px solid #dfeeff; }
                </style>
            </head>
            <body>
                <div class="uptpc-closed-wrap">
                    <div class="uptpc-card">
                        <div class="uptpc-logo">{{LOGO}}</div>
                        <div class="uptpc-bar"><h1>🔒 Sistema Temporalmente Cerrado</h1></div>
                        <div class="uptpc-desc">El Sistema de Control de Estudios se encuentra temporalmente cerrado por mantenimiento o razones administrativas. No se permitirá el acceso en este momento.</div>
                        <div class="uptpc-reason"><strong>📌 Motivo:</strong><div style="margin-top:6px">{{RAZON}}</div></div>
                        <div class="uptpc-actions"><a class="btn btn-secondary" href="login.php">Volver a Login</a><a class="btn btn-primary" href="javascript:location.reload()">Intentar más tarde</a></div>
                    </div>
                </div>
            </body>
            </html>
            HTML;
            // Inyectar logo y motivo de forma segura
            global $logo_uptpc, $logo_uptpcp, $logopertenencia;
            $logo_html = '';
            if (isset($logo_uptpc) && !empty($logo_uptpc)) {
                $logo_html = $logo_uptpc; // uptpc.png variable (ideal)
            } elseif (isset($logopertenencia) && !empty($logopertenencia)) {
                $logo_html = $logopertenencia; // fallback existing logo
            } else {
                $logo_html = '<img src="/images/uptpc.png" alt="UPTPC" style="max-width:320px;height:auto;">';
            }
            $mensaje = str_replace('{{LOGO}}', $logo_html, $mensaje);
            $mensaje = str_replace('{{RAZON}}', htmlspecialchars($razon), $mensaje);
            die($mensaje);
        }
        return true;
    }





}
}







// NO instanciar aquí, se instancia en el archivo que lo necesita
// La instancia se crea en recuperar_password.php y en otros archivos
?>