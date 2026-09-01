<?php
// recuperar_password.php - CORREGIDO (bloqueo ANTES que todo)
error_reporting(E_ALL);
ini_set('display_errors', '1');
$titulo = "Recuperar Contraseña - " . ($siglas_institucion ?? "UPTPC");
include('funciones/functions.php');
include('funciones/seguridad.php');

// Instanciar seguridad si no existe
if (!isset($seguridad)) {
    $seguridad = new Seguridad($db);
}

// ==============================================
// 1. VERIFICAR SISTEMA ACTIVO
// ==============================================
if (!$seguridad->sistemaEstaActivo()) {
    die("
    <div style='text-align: center; font-family: Arial; padding: 50px;'>
        <h2 style='color: #dc3545;'>🔒 Sistema Temporalmente Cerrado</h2>
        <p>El sistema de recuperación de contraseña está deshabilitado por razones de seguridad.</p>
        <a href='login.php' class='btn btn-primary'>Volver al login</a>
    </div>
    ");
}

if ($seguridad->sistemaEnMantenimiento()) {
    die("
    <div style='text-align: center; font-family: Arial; padding: 50px;'>
        <h2 style='color: #ffc107;'>🛠️ Sistema en Mantenimiento</h2>
        <p>El sistema está siendo actualizado. Por favor, intenta más tarde.</p>
        <a href='login.php' class='btn btn-primary'>Volver al login</a>
    </div>
    ");
}

$email = isset($_REQUEST['email']) ? trim($_REQUEST['email']) : "";

if (!empty($email)) {
    // ==============================================
    // 2. PRIMERO: VERIFICAR BLOQUEO (ANTES DE CUALQUIER COSA)
    // ==============================================
    $verificar = $seguridad->verificarIntentos($email, 'recuperar');
    if (!$verificar['permitido']) {
        $_SESSION['msg'] = '<i class="fa fa-lock"></i> ' . $verificar['mensaje'];
        header('location: login.php');
        exit;
    }
    
    // ==============================================
    // 3. SEGUNDO: VERIFICAR RPS
    // ==============================================
    $rps = $seguridad->verificarRPS('recuperar_password');
    if (!$rps['permitido']) {
        $_SESSION['msg'] = '<i class="fa fa-shield-alt"></i> ' . $rps['mensaje'];
        header('location: login.php');
        exit;
    }
    
    // ==============================================
    // 4. TERCERO: VERIFICAR SI EL EMAIL EXISTE
    // ==============================================
    global $db;
    
    $sql = "SELECT id, nombre, email, status, username FROM users WHERE email = ?";
    $stmt = mysqli_prepare($db, $sql);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $rows = mysqli_num_rows($result);
    
    if ($rows == 0) {
        // Registrar intento fallido
        $seguridad->registrarIntentoFallido($email, 'recuperar');
        array_push($errors, "El correo <b>$email</b> no existe en nuestra base de datos.");
        $_SESSION['msg_recuperar'] = display_error();
        header("location: recuperar_password.php");
        exit;
    }
    
    // ==============================================
    // 5. OBTENER DATOS DEL USUARIO
    // ==============================================
    $row = mysqli_fetch_assoc($result);
    $rowid = $row['id'];
    $nombre_usuario = $row['nombre'];
    $email_usuario = $row['email'];
    $status = $row['status'];
    
    if ($status == 0) {
        $_SESSION['msg'] = '<i class="fa fa-ban"></i> Usuario bloqueado. Contacta al administrador.';
        header('location: login.php');
        exit;
    }
    
    // ==============================================
    // 6. GENERAR TOKEN Y ENVIAR CORREO
    // ==============================================
    // Crear tabla password_resets si no existe
    $crear_tabla = "
    CREATE TABLE IF NOT EXISTS `password_resets` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `user_id` int(11) NOT NULL,
        `email` varchar(100) NOT NULL,
        `token` varchar(255) NOT NULL,
        `expira` datetime NOT NULL,
        `usado` tinyint(1) DEFAULT 0,
        `creado_en` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        KEY `token` (`token`),
        KEY `user_id` (`user_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ";
    mysqli_query($db, $crear_tabla);
    
    // Generar token único
    $token = bin2hex(random_bytes(32));
    $expira = date('Y-m-d H:i:s', strtotime('+1 hour'));
    
    // Insertar token
    $insert = "INSERT INTO password_resets (user_id, email, token, expira) VALUES (?, ?, ?, ?)";
    $stmt_insert = mysqli_prepare($db, $insert);
    mysqli_stmt_bind_param($stmt_insert, "isss", $rowid, $email_usuario, $token, $expira);
    mysqli_stmt_execute($stmt_insert);
    

    // Construir enlace completo, codificando el token para seguridad
    $enlace = $pag_web . '/nueva_password.php?token=' . urlencode($token);


    
    // Enviar correo conectado a variables centralizadas
    $asunto = "🔐 Recupera tu contraseña - " . ($nombre_sistema ?? "Sistema de Control de Estudios") . " " . ($siglas_institucion ?? "UPTPC");
    $cuerpo = "
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset='UTF-8'>
        <title>Recuperar Contraseña " . htmlspecialchars($siglas_institucion ?? "UPTPC") . "</title>
    </head>
    <body style='font-family: Arial, sans-serif; background-color: #f4f4f4; margin: 0; padding: 20px;'>
        <div style='max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 8px rgba(0,0,0,0.1);'>
            
            <div style='background: linear-gradient(135deg, #003366 0%, #00509e 100%); padding: 30px 20px; text-align: center;'>
                <h1 style='color: #ffffff; margin: 0; font-size: 28px;'>🏛️ " . htmlspecialchars($siglas_institucion ?? "UPTPC") . "</h1>
                <p style='color: #ffd700; margin: 5px 0 0; font-size: 14px;'>" . htmlspecialchars($nombre_empresa ?? "Universidad Politécnica Territorial de Puerto Cabello") . "</p>
                <p style='color: #cce5ff; margin: 5px 0 0; font-size: 12px;'>" . htmlspecialchars($nombre_sistema ?? "Sistema de Control de Estudios") . "</p>
            </div>
            
            <div style='padding: 30px 25px;'>
                <h2 style='color: #003366; margin-top: 0;'>Estimado(a), " . htmlspecialchars($nombre_usuario) . "</h2>
                
                <p style='color: #333; font-size: 16px; line-height: 1.5;'>Recibimos una solicitud para restablecer la contraseña de tu cuenta en el <strong>" . htmlspecialchars($nombre_sistema ?? "Sistema de Control de Estudios") . " de la " . htmlspecialchars($nombre_empresa ?? "Universidad Politécnica Territorial de Puerto Cabello") . " (" . htmlspecialchars($siglas_institucion ?? "UPTPC") . ")</strong>.</p>
                
                <p style='color: #333; font-size: 16px; line-height: 1.5;'>Para continuar y crear una nueva contraseña, haz clic en el siguiente botón:</p>
                
                <div style='text-align: center; margin: 35px 0;'>
                    <a href='{$enlace}' style='display: inline-block; background-color: #00509e; color: #ffffff; padding: 14px 32px; text-decoration: none; border-radius: 50px; font-weight: bold; font-size: 16px; box-shadow: 0 2px 5px rgba(0,0,0,0.2);'>
                        🔑 Restablecer mi contraseña
                    </a>
                </div>
                
                <div style='background-color: #f8f9fa; padding: 15px; border-radius: 8px; margin: 20px 0;'>
                    <p style='color: #555; font-size: 13px; margin: 0 0 5px;'>📎 <strong>¿El botón no funciona?</strong> Copia y pega este enlace en tu navegador:</p>
                    <p style='background-color: #e9ecef; padding: 10px; border-radius: 5px; word-break: break-all; font-size: 12px; color: #00509e; margin: 0;'>{$enlace}</p>
                </div>
                
                <div style='border-left: 4px solid #ffc107; background-color: #fff3cd; padding: 12px 15px; margin: 20px 0; border-radius: 5px;'>
                    <p style='color: #856404; font-size: 13px; margin: 0;'>
                        <strong>⏰ Importante:</strong> Este enlace expirará en <strong>1 hora</strong> por razones de seguridad.
                    </p>
                    <p style='color: #856404; font-size: 13px; margin: 5px 0 0;'>
                        <strong>🛡️ ¿No solicitaste este cambio?</strong> Ignora este mensaje, tu contraseña seguirá siendo la misma.
                    </p>
                </div>
            </div>
            
            <div style='background-color: #f8f9fa; padding: 15px; text-align: center; border-top: 1px solid #e9ecef; font-size: 11px; color: #6c757d;'>
                <p style='margin: 0;'>" . htmlspecialchars($nombre_empresa ?? "") . " - " . htmlspecialchars($siglas_institucion ?? "") . "</p>
                <p style='margin: 3px 0 0;'>" . htmlspecialchars($direccion_empresa ?? "") . "</p>
            </div>
        </div>
    </body>
    </html>";
    
    enviarEmail($email_usuario, $nombre_usuario, $asunto, $cuerpo);
    
    $_SESSION['msg'] = '<i class="fa fa-envelope"></i> ✅ Hemos enviado un correo a <strong>' . $email_usuario . '</strong> con las instrucciones para recuperar tu contraseña. Revisa tu bandeja de entrada o SPAM.';
    header('location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <?php echo $bootstrap_head; ?>
    <link rel="apple-touch-icon" href="images/favicon/apple-touch-icon.png" sizes="180x180">
    <link rel="icon" href="images/favicon/favicon-32x32.png" sizes="32x32" type="image/png">
    <link rel="icon" href="images/favicon/favicon-16x16.png" sizes="16x16" type="image/png">
    <link rel="icon" href="images/favicon/favicon.ico">
    <title><?php echo $titulo; ?></title>
</head>
<body>

<header class="py-2 bg-white border-bottom shadow-sm mb-3">
    <div class="container-fluid d-flex align-items-center justify-content-between px-3 px-md-4">
        <div class="header-logo-left">
            <?php echo $logopertenencia; ?>
        </div>
        <div class="header-logo-right">
           <?php echo $logo_mppeu; ?>
             
        </div>
    </div>
</header>
<hr>
<div class="container-fluid">
    <div class="row">
        <div class="d-none d-sm-block col-md-6">
            <?php echo $image_responsive; ?>
        </div>
        <div class="col-sm-12 col-md-6">
            <h3 class="text-center text-uppercase" style="color: #003366;">🏛️ Recuperar Contraseña</h3>
            <h5 class="text-center text-muted"><?php echo $nombre_empresa; ?></h5>
            <h6 class="text-center text-muted mb-4"><?php echo $nombre_sistema; ?></h6>
            
            <form class="was-validated" method="post" action="recuperar_password.php" autocomplete="off">
                
                <?php if (isset($_SESSION['msg_recuperar'])) : ?>
                    <div class="alert alert-danger" role="alert">
                        <?php
                            echo $_SESSION['msg_recuperar'];
                            unset($_SESSION['msg_recuperar']);
                        ?>
                    </div>
                <?php endif ?>
                
                <?php echo display_error(); ?>
                
                <div class="form-group">
                    <label for="email">📧 Correo electrónico institucional</label>
                    <input type="email" class="form-control" id="email" placeholder="tucorreo@uptpc.edu.ve" name="email" required>
                    <div class="invalid-feedback">Indica tu correo electrónico registrado en el sistema</div>
                    <small class="form-text text-muted">
                        <i class="fa fa-info-circle"></i> Te enviaremos un enlace seguro para restablecer tu contraseña
                    </small>
                </div>
                
                <button type="submit" class="btn btn-primary btn-block" style="background-color: #00509e; border-color: #003366;">
                    <i class="fa fa-key"></i> Enviar enlace de recuperación
                </button>
                
                <a href="login.php" class="btn btn-secondary btn-block mt-2">
                    <i class="fa fa-arrow-left"></i> Volver al inicio de sesión
                </a>
            </form>
            
            <hr>
            <div class="text-center">
                <small class="text-muted">
                    <i class="fa fa-university"></i> <?php echo $siglas_institucion; ?> - <?php echo $nombre_sistema; ?><br><?php echo $nombre_empresa; ?>
                </small>
            </div>
        </div>
    </div>
</div>

<?php echo $footer_institucional; ?>
</body>
</html>