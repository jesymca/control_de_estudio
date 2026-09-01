<?php
// nueva_password.php - Sistema de Control de Estudios UPT-PC
session_start();
error_reporting(E_ALL);
ini_set('display_errors', '1');
$titulo = "Nueva Contraseña - " . ($siglas_institucion ?? "UPTPC");
include('funciones/functions.php');

$token = $_GET['token'] ?? '';

if (empty($token)) {
    die("
    <div style='text-align: center; font-family: Arial; padding: 50px;'>
        <h2 style='color: #dc3545;'>❌ Enlace inválido</h2>
        <p>El enlace que has usado no es válido.</p>
        <a href='recuperar_password.php' class='btn btn-primary'>Solicitar nuevo enlace</a>
    </div>
    ");
}

global $db;

// Verificar que la tabla exista
$verificar_tabla = "SHOW TABLES LIKE 'password_resets'";
$tabla_existe = mysqli_query($db, $verificar_tabla);

if (mysqli_num_rows($tabla_existe) == 0) {
    die("
    <div style='text-align: center; font-family: Arial; padding: 50px;'>
        <h2 style='color: #dc3545;'>❌ Sistema no configurado</h2>
        <p>Por favor, solicita un nuevo enlace de recuperación.</p>
        <a href='recuperar_password.php' class='btn btn-primary'>Ir a recuperar contraseña</a>
    </div>
    ");
}

// Buscar token válido
$sql = "SELECT pr.*, u.nombre, u.email 
        FROM password_resets pr 
        JOIN users u ON pr.user_id = u.id 
        WHERE pr.token = ? AND pr.usado = 0 AND pr.expira > NOW()";
$stmt = mysqli_prepare($db, $sql);
mysqli_stmt_bind_param($stmt, "s", $token);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) == 0) {
    die("
    <div style='text-align: center; font-family: Arial; padding: 50px;'>
        <h2 style='color: #dc3545;'>⏰ Enlace expirado o inválido</h2>
        <p>El enlace de recuperación ha expirado o ya ha sido utilizado.</p>
        <p>Por favor, solicita un nuevo enlace.</p>
        <a href='recuperar_password.php' class='btn btn-primary'>Solicitar nuevo enlace</a>
    </div>
    ");
}

$reset = mysqli_fetch_assoc($result);
$user_id = $reset['user_id'];
$user_email = $reset['email'];
$user_nombre = $reset['nombre'];

// Procesar el formulario cuando se envía
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $password = $_POST['password'];
    $confirmar = $_POST['confirmar_password'];
    
    $errors = array();
    
    if (strlen($password) < 6) {
        $errors[] = "La contraseña debe tener al menos 6 caracteres.";
    }
    
    if ($password !== $confirmar) {
        $errors[] = "Las contraseñas no coinciden.";
    }
    
    if (empty($errors)) {
        // Hashear nueva contraseña
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        
        // Actualizar contraseña en users
        $update = "UPDATE users SET password = ? WHERE id = ?";
        $stmt_update = mysqli_prepare($db, $update);
        mysqli_stmt_bind_param($stmt_update, "si", $password_hash, $user_id);
        $actualizado = mysqli_stmt_execute($stmt_update);
        
        if ($actualizado) {
            // Marcar token como usado
            $marcar = "UPDATE password_resets SET usado = 1 WHERE token = ?";
            $stmt_marcar = mysqli_prepare($db, $marcar);
            mysqli_stmt_bind_param($stmt_marcar, "s", $token);
            mysqli_stmt_execute($stmt_marcar);
            
            // Enviar correo de confirmación conectado a variables centralizadas
            $asunto = "✅ Tu contraseña ha sido cambiada - " . ($siglas_institucion ?? "UPTPC");
            $cuerpo = "
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset='UTF-8'>
                <title>Contraseña Actualizada - " . htmlspecialchars($siglas_institucion ?? "UPTPC") . "</title>
            </head>
            <body style='font-family: Arial, sans-serif; background-color: #f4f4f4; margin: 0; padding: 20px;'>
                <div style='max-width: 600px; margin: 0 auto; background: white; border-radius: 10px; overflow: hidden;'>
                    <div style='background: #00509e; padding: 20px; text-align: center;'>
                        <h2 style='color: white; margin: 0;'>🏛️ " . htmlspecialchars($siglas_institucion ?? "UPTPC") . "</h2>
                        <p style='color: #cce5ff; margin: 0;'>" . htmlspecialchars($nombre_empresa ?? "Universidad Politécnica Territorial de Puerto Cabello") . "</p>
                        <p style='color: #cce5ff; margin: 3px 0 0; font-size: 12px;'>" . htmlspecialchars($nombre_sistema ?? "Sistema de Control de Estudios") . "</p>
                    </div>
                    <div style='padding: 25px;'>
                        <h2 style='color: #003366;'>Estimado(a), " . htmlspecialchars($user_nombre) . "</h2>
                        <p>Te confirmamos que tu contraseña ha sido <strong style='color: green;'>cambiada exitosamente</strong> en el " . htmlspecialchars($nombre_sistema ?? "Sistema de Control de Estudios") . " de la " . htmlspecialchars($nombre_empresa ?? "UPTPC") . " (" . htmlspecialchars($siglas_institucion ?? "UPTPC") . ").</p>
                        <p>Si no realizaste este cambio, contacta al administrador del sistema inmediatamente.</p>
                        <hr>
                        <p style='color: #999; font-size: 12px;'>Este es un mensaje automático, por favor no responder.</p>
                        <p style='color: #999; font-size: 12px;'>© " . date('Y') . " - " . htmlspecialchars($nombre_empresa ?? "UPTPC") . "</p>
                    </div>
                </div>
            </body>
            </html>";
            
            enviarEmail($user_email, $user_nombre, $asunto, $cuerpo);
            
            $_SESSION['msg'] = "✅ ¡Contraseña cambiada exitosamente! Ahora puedes iniciar sesión con tu nueva contraseña.";
            header('Location: login.php');
            exit;
        } else {
            $error_msg = "❌ Error al cambiar la contraseña. Por favor, intenta de nuevo.";
        }
    } else {
        $error_msg = implode("<br>", $errors);
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <?php echo $bootstrap_head; ?>
    <title><?php echo $titulo; ?></title>
</head>
<body>
<div class="container text-center">
    <?php echo $logopertenencia; ?>
</div>
<hr>
<div class="container-fluid">
    <div class="row">
        <div class="col-sm-12 col-md-6 mx-auto">
            <div class="card shadow">
                <div class="card-header" style="background-color: #00509e; color: white; text-align: center;">
                    <h3 class="mb-0">🔑 Crear nueva contraseña</h3>
                    <small>Universidad Politécnica Territorial de Puerto Cabello</small>
                </div>
                <div class="card-body">
                    <p class="text-center text-muted">
                        <i class="fa fa-user-circle"></i> <strong><?php echo $user_nombre; ?></strong><br>
                        <small><?php echo $user_email; ?></small>
                    </p>
                    
                    <?php if (isset($error_msg)): ?>
                        <div class="alert alert-danger"><?php echo $error_msg; ?></div>
                    <?php endif; ?>
                    
                    <form method="post" action="">
                        <div class="form-group">
                            <label for="password">🔒 Nueva contraseña</label>
                            <input type="password" class="form-control" id="password" name="password" placeholder="Mínimo 6 caracteres" required>
                            <small class="form-text text-muted">La contraseña debe tener al menos 6 caracteres</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="confirmar_password">✅ Confirmar contraseña</label>
                            <input type="password" class="form-control" id="confirmar_password" name="confirmar_password" placeholder="Repite tu nueva contraseña" required>
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-block" style="background-color: #00509e; border-color: #003366;">
                            <i class="fa fa-save"></i> Guardar nueva contraseña
                        </button>
                        
                        <a href="login.php" class="btn btn-secondary btn-block mt-2">
                            <i class="fa fa-arrow-left"></i> Volver al login
                        </a>
                    </form>
                </div>
                <div class="card-footer text-center text-muted">
                    <small>UPT-PC - Sistema de Control de Estudios</small><br>
                    <small>Universidad Politécnica Territorial de Puerto Cabello</small>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>