<?php
// .dios/login.php - Bóveda de Máxima Seguridad y Acceso Maestro (MODO DIOS)
require_once __DIR__ . '/config.php';

// Si ya está autenticado en Modo Dios, redirigir al panel
if (isset($_SESSION['dios_autenticado']) && $_SESSION['dios_autenticado'] === true) {
    header('Location: index.php');
    exit;
}

$error = '';
$motivo = $_GET['motivo'] ?? '';
if ($motivo === 'expirado') {
    $error = '⏳ La sesión expiró por 20 minutos de inactividad. Ingrese nuevamente sus credenciales.';
} elseif ($motivo === 'secuestro') {
    $error = '🚨 ALERTA DE SEGURIDAD: Se detectó un cambio sospechoso en la red o navegador. Sesión revocada por seguridad.';
}

$bloqueado_hasta = $_SESSION['dios_login_lock_until'] ?? 0;
$intentos_actuales = $_SESSION['dios_login_attempts'] ?? 0;

// Generar desafío matemático si no existe
if (!isset($_SESSION['dios_math_a']) || !isset($_SESSION['dios_math_b']) || !isset($_SESSION['dios_math_res'])) {
    $a = rand(4, 15);
    $b = rand(3, 12);
    $_SESSION['dios_math_a'] = $a;
    $_SESSION['dios_math_b'] = $b;
    $_SESSION['dios_math_res'] = $a + $b;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Verificación de bloqueo temporal por fuerza bruta
    if ($bloqueado_hasta > time()) {
        $minutos_restantes = ceil(($bloqueado_hasta - time()) / 60);
        $error = "🔒 BÓVEDA SELLADA TEMPORALMENTE. Demasiados intentos fallidos. Espere {$minutos_restantes} minuto(s) antes de reintentar.";
    } else {
        // 2. Honeypot anti-bots (trampa oculta)
        if (!empty($_POST['boveda_trap'])) {
            // Silencioso descarte de bot
            sleep(2);
            $error = "⛔ Acceso denegado por filtro de seguridad perimetral.";
        } else {
            // 3. Verificación de token CSRF
            $csrf_post = $_POST['csrf_token'] ?? '';
            if (empty($_SESSION['dios_csrf_token']) || !hash_equals($_SESSION['dios_csrf_token'], $csrf_post)) {
                $error = '⚠️ Token de seguridad expirado o alterado. Recargue la página e intente de nuevo.';
            } else {
                // 4. Verificación de desafío matemático (Captcha dinámico)
                $math_input = intval($_POST['math_challenge'] ?? -999);
                $math_expected = intval($_SESSION['dios_math_res'] ?? -1);

                if ($math_input !== $math_expected) {
                    $_SESSION['dios_login_attempts'] = ($_SESSION['dios_login_attempts'] ?? 0) + 1;
                    $error = '❌ Desafío de seguridad matemático incorrecto.';
                } else {
                    // 5. Verificación de credenciales maestras (Usuario + Contraseña + PIN de Bóveda)
                    $user = trim($_POST['usuario'] ?? '');
                    $pass = $_POST['password'] ?? '';
                    $pin  = trim($_POST['pin_maestro'] ?? '');

                    $user_ok = hash_equals(DIOS_USER, $user);
                    $pass_ok = password_verify($pass, DIOS_PASS_HASH);
                    $pin_ok  = password_verify($pin, DIOS_PIN_HASH);

                    if ($user_ok && $pass_ok && $pin_ok) {
                        // ÉXITO MAESTRO: Regenerar ID y autenticar
                        session_regenerate_id(true);
                        unset($_SESSION['dios_login_attempts'], $_SESSION['dios_login_lock_until']);
                        unset($_SESSION['dios_math_a'], $_SESSION['dios_math_b'], $_SESSION['dios_math_res']);

                        $client_ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
                        $client_ua = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';

                        $_SESSION['dios_autenticado'] = true;
                        $_SESSION['dios_user'] = $user;
                        $_SESSION['dios_ip'] = $client_ip;
                        $_SESSION['dios_hora'] = time();
                        $_SESSION['dios_last_activity'] = time();
                        $_SESSION['dios_fingerprint'] = hash('sha256', $client_ip . '|' . $client_ua);

                        header('Location: index.php');
                        exit;
                    } else {
                        // Intento fallido
                        $_SESSION['dios_login_attempts'] = ($_SESSION['dios_login_attempts'] ?? 0) + 1;
                        $intentos = $_SESSION['dios_login_attempts'];

                        if ($intentos >= DIOS_MAX_ATTEMPTS) {
                            $_SESSION['dios_login_lock_until'] = time() + DIOS_LOCKOUT_TIME;
                            $error = '🚨 BÓVEDA BLOQUEADA: 3 intentos fallidos consecutivos. El acceso ha sido sellado por 20 minutos.';
                        } else {
                            $restantes = DIOS_MAX_ATTEMPTS - $intentos;
                            $error = "❌ Autenticación rechazada. Credenciales o PIN de Bóveda inválidos. Intentos restantes: {$restantes}.";
                        }
                    }
                }
            }
        }
    }

    // Renovar desafío matemático para la próxima petición
    $a = rand(4, 15);
    $b = rand(3, 12);
    $_SESSION['dios_math_a'] = $a;
    $_SESSION['dios_math_b'] = $b;
    $_SESSION['dios_math_res'] = $a + $b;
}

$is_locked = ($bloqueado_hasta > time());
$segundos_restantes = max(0, $bloqueado_hasta - time());
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>👑 Bóveda Soberana | MODO DIOS - UPTPC</title>
    <link rel="icon" type="image/png" href="../images/uptpc.png">
    <link rel="stylesheet" href="../funciones/bootstrap-4.6.2-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../funciones/fontawesome-free-5.15.4-web/css/all.min.css">
    <style>
        body {
            background-color: #040813;
            background-image:
                radial-gradient(circle at 15% 15%, rgba(251, 191, 36, 0.12) 0%, transparent 45%),
                radial-gradient(circle at 85% 85%, rgba(56, 189, 248, 0.10) 0%, transparent 45%),
                radial-gradient(circle at 50% 50%, rgba(16, 185, 129, 0.05) 0%, transparent 55%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            color: #f8fafc;
            margin: 0;
            padding: 20px;
        }

        .vault-card {
            background: linear-gradient(160deg, #0d172e 0%, #060b17 100%);
            border: 1px solid rgba(251, 191, 36, 0.45);
            border-top: 4px solid #fbbf24;
            border-radius: 22px;
            width: 100%;
            max-width: 480px;
            padding: 32px 30px;
            box-shadow: 0 30px 80px rgba(0, 0, 0, 0.85), 0 0 35px rgba(251, 191, 36, 0.12);
            position: relative;
        }

        .vault-logos {
            background: rgba(255, 255, 255, 0.96);
            border-radius: 12px;
            padding: 8px 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 22px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.35);
        }

        .vault-logos img {
            max-height: 40px;
            width: auto;
            object-fit: contain;
        }

        .vault-crown {
            width: 66px;
            height: 66px;
            margin: 0 auto 12px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(251, 191, 36, 0.28) 0%, rgba(251, 191, 36, 0.04) 70%);
            border: 2px solid #fbbf24;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.85rem;
            color: #fbbf24;
            box-shadow: 0 0 25px rgba(251, 191, 36, 0.35);
        }

        .vault-title {
            text-align: center;
            font-size: 1.48rem;
            font-weight: 800;
            color: #fbbf24;
            letter-spacing: 1.5px;
            margin-bottom: 3px;
        }

        .vault-subtitle {
            text-align: center;
            font-size: 0.82rem;
            color: #94a3b8;
            margin-bottom: 20px;
            text-transform: uppercase;
            letter-spacing: 0.8px;
        }

        .form-label-vault {
            font-size: 0.76rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #cbd5e1;
            margin-bottom: 6px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .input-vault-group {
            position: relative;
        }

        .input-vault {
            width: 100%;
            background: #050a14;
            border: 1px solid rgba(148, 163, 184, 0.3);
            border-radius: 10px;
            padding: 11px 42px 11px 14px;
            color: #ffffff;
            font-size: 0.95rem;
            transition: all 0.2s ease;
        }

        .input-vault:focus {
            border-color: #fbbf24;
            box-shadow: 0 0 0 3px rgba(251, 191, 36, 0.22);
            outline: none;
            background: #081020;
        }

        .toggle-pass-btn {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #94a3b8;
            cursor: pointer;
            padding: 4px;
        }

        .toggle-pass-btn:hover {
            color: #fbbf24;
        }

        .math-badge {
            background: rgba(56, 189, 248, 0.15);
            border: 1px solid rgba(56, 189, 248, 0.35);
            color: #38bdf8;
            padding: 2px 8px;
            border-radius: 6px;
            font-weight: 700;
            font-size: 0.82rem;
        }

        .btn-vault-enter {
            width: 100%;
            background: linear-gradient(135deg, #fbbf24 0%, #d97706 100%);
            color: #040813;
            font-weight: 800;
            font-size: 0.96rem;
            text-transform: uppercase;
            letter-spacing: 0.9px;
            border: none;
            border-radius: 10px;
            padding: 13px;
            margin-top: 12px;
            cursor: pointer;
            transition: all 0.2s ease;
            box-shadow: 0 8px 22px rgba(251, 191, 36, 0.28);
        }

        .btn-vault-enter:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 12px 28px rgba(251, 191, 36, 0.42);
            filter: brightness(1.08);
        }

        .btn-vault-enter:disabled {
            opacity: 0.55;
            cursor: not-allowed;
            transform: none;
        }

        .vault-telemetry {
            margin-top: 20px;
            padding-top: 14px;
            border-top: 1px solid rgba(148, 163, 184, 0.15);
            display: flex;
            justify-content: space-between;
            font-size: 0.74rem;
            color: #64748b;
        }
    </style>
</head>
<body>
    <div class="vault-card">
        <div class="vault-logos">
            <img src="../images/uptpc.png" alt="Logo UPTPC" onerror="this.style.display='none'">
            <img src="../images/mppeu_bb.png" alt="Logo Ciencia y Tecnología" onerror="this.style.display='none'">
        </div>

        <div class="vault-crown">
            <i class="fas fa-crown"></i>
        </div>
        <div class="vault-title">BÓVEDA SOBERANA</div>
        <div class="vault-subtitle">MODO DIOS • Amo y Dueño Absoluto</div>

        <?php if ($error): ?>
            <div class="alert alert-danger py-2 px-3 mb-3" style="background: rgba(239,68,68,0.18); border: 1px solid rgba(239,68,68,0.45); color: #fca5a5; border-radius: 10px; font-size: 0.84rem; line-height: 1.4;">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <?php if ($is_locked): ?>
            <div class="p-3 mb-3 text-center" style="background: rgba(239,68,68,0.1); border: 1px dashed rgba(239,68,68,0.5); border-radius: 10px;">
                <i class="fas fa-user-lock text-danger fa-2x mb-2"></i>
                <div class="text-danger font-weight-bold" style="font-size: 0.9rem;">Acceso Temporalmente Inhabilitado</div>
                <div class="text-muted small mt-1">Tiempo de espera para desbloqueo:</div>
                <div id="countdown" class="badge badge-danger px-3 py-2 mt-2" style="font-size: 1rem;">
                    <?php echo gmdate("i:s", $segundos_restantes); ?>
                </div>
            </div>
        <?php endif; ?>

        <form method="POST" autocomplete="off">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['dios_csrf_token'] ?? ''); ?>">

            <!-- Honeypot Invisible Anti-Bot -->
            <input type="text" name="boveda_trap" value="" style="display:none !important; visibility:hidden; position:absolute; left:-9999px;" tabindex="-1" autocomplete="off">

            <!-- Factor 1: Identidad Maestra -->
            <div class="form-group mb-2">
                <label class="form-label-vault">
                    <span><i class="fas fa-user-astronaut mr-1 text-warning"></i> 1. Identidad Soberana</span>
                </label>
                <div class="input-vault-group">
                    <input type="text" name="usuario" class="input-vault" placeholder="Usuario Maestro" <?php echo $is_locked ? 'disabled' : 'required autofocus'; ?>>
                </div>
            </div>

            <!-- Factor 2: Llave Maestra (Password) -->
            <div class="form-group mb-2">
                <label class="form-label-vault">
                    <span><i class="fas fa-key mr-1 text-warning"></i> 2. Llave Maestra</span>
                </label>
                <div class="input-vault-group">
                    <input type="password" name="password" id="vaultPassword" class="input-vault" placeholder="Contraseña Maestra" <?php echo $is_locked ? 'disabled' : 'required'; ?>>
                    <button type="button" class="toggle-pass-btn" onclick="toggleField('vaultPassword', 'eyePass')" title="Mostrar/Ocultar" tabindex="-1">
                        <i class="fas fa-eye" id="eyePass"></i>
                    </button>
                </div>
            </div>

            <!-- Factor 3: PIN Maestro de Bóveda (2FA) -->
            <div class="form-group mb-2">
                <label class="form-label-vault">
                    <span><i class="fas fa-shield-alt mr-1 text-warning"></i> 3. PIN de Bóveda (2FA)</span>
                    <span class="badge badge-warning text-dark font-weight-bold" style="font-size: 0.68rem;">4 Dígitos</span>
                </label>
                <div class="input-vault-group">
                    <input type="password" name="pin_maestro" id="vaultPin" maxlength="8" class="input-vault" placeholder="••••" <?php echo $is_locked ? 'disabled' : 'required'; ?>>
                    <button type="button" class="toggle-pass-btn" onclick="toggleField('vaultPin', 'eyePin')" title="Mostrar/Ocultar PIN" tabindex="-1">
                        <i class="fas fa-eye" id="eyePin"></i>
                    </button>
                </div>
            </div>

            <!-- Factor 4: Desafío de Cálculo Dinámico (Anti-Script) -->
            <div class="form-group mb-3">
                <label class="form-label-vault">
                    <span><i class="fas fa-calculator mr-1 text-info"></i> 4. Verificación Perimetral</span>
                    <span class="math-badge">¿Cuánto es <?php echo $_SESSION['dios_math_a']; ?> + <?php echo $_SESSION['dios_math_b']; ?>?</span>
                </label>
                <div class="input-vault-group">
                    <input type="number" name="math_challenge" class="input-vault" placeholder="Resultado numérico" <?php echo $is_locked ? 'disabled' : 'required'; ?>>
                </div>
            </div>

            <button type="submit" class="btn-vault-enter" <?php echo $is_locked ? 'disabled' : ''; ?>>
                <i class="fas fa-bolt mr-1"></i> Desbloquear Núcleo Soberano
            </button>
        </form>

        <div class="text-center mt-3">
            <a href="../login.php" style="color: #94a3b8; font-size: 0.8rem; text-decoration: none;">
                <i class="fas fa-arrow-left mr-1"></i> Volver al Portal Institucional
            </a>
        </div>

        <div class="vault-telemetry">
            <span><i class="fas fa-shield-virus text-success mr-1"></i> Blindaje 3FA Activo</span>
            <span><i class="fas fa-network-wired text-info mr-1"></i> IP: <?php echo htmlspecialchars($_SERVER['REMOTE_ADDR'] ?? '127.0.0.1'); ?></span>
            <span><i class="fas fa-ghost text-warning mr-1"></i> Zero-Trace</span>
        </div>
    </div>

    <script>
        function toggleField(fieldId, eyeId) {
            const input = document.getElementById(fieldId);
            const eye = document.getElementById(eyeId);
            if (input.type === 'password') {
                input.type = 'text';
                eye.classList.remove('fa-eye');
                eye.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                eye.classList.remove('fa-eye-slash');
                eye.classList.add('fa-eye');
            }
        }

        <?php if ($is_locked && $segundos_restantes > 0): ?>
        let remaining = <?php echo $segundos_restantes; ?>;
        const countdownEl = document.getElementById('countdown');
        const interval = setInterval(() => {
            remaining--;
            if (remaining <= 0) {
                clearInterval(interval);
                location.reload();
            } else {
                const mins = String(Math.floor(remaining / 60)).padStart(2, '0');
                const secs = String(remaining % 60).padStart(2, '0');
                if (countdownEl) countdownEl.innerText = `${mins}:${secs}`;
            }
        }, 1000);
        <?php endif; ?>
    </script>
</body>
</html>