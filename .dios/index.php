<?php
// .dios/index.php - CENTRO DE COMANDO SOBERANO (MODO DIOS)
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/config.php';

// Verificar autenticación en la sesión aislada DIOS_SESSION
if (!isset($_SESSION['dios_autenticado']) || $_SESSION['dios_autenticado'] !== true) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../funciones/functions.php';
require_once __DIR__ . '/../funciones/seguridad.php';

if (!isset($db) || !$db) {
    die("Error crítico: No se pudo conectar a la base de datos institucional.");
}

$seguridad = new Seguridad($db);

$permisos_sistema = [
    'editar_user', 'editar_nota', 'editar_acceso', 'editar_valores', 'editar_estudiante',
    'agregar_estudiante', 'agregar_docente', 'editar_docente', 'agregar_carrera', 'agregar_materia',
    'editar_materia', 'pagos', 'auditoria', 'secciones', 'rela_materia_carrera',
    'periodos_academicos', 'asig_secciones', 'asig_cursos', 'horarios', 'gestion_director_carrera',
    'notas_cargadas', 'consultar_notas', 'consultar_notas_pasadas', 'tipos_pago', 'tipos_horario',
    'horario_personal', 'respaldo_bd', 'gestionar_carrera', 'gestion_periodo_academico', 'gestion_asig_cursos',
    'gestion_horario', 'titulos_re_materia', 'grado', 'gestion_grado', 'vocero',
    'visita', 'constancias', 'preinscripciones', 'inscripcion_materias', 'aprobar_secciones',
    'aulas', 'actas_calificacion', 'secretaria', 'ver_estudiantes', 'ver_docentes', 'mensajeria'
];

/**
 * Función soberana para inyectar sesión en el sistema principal (PHPSESSID)
 * manteniendo intacta la sesión aislada DIOS_SESSION y garantizando el Modo Fantasma 100% anónimo.
 */
function inyectarSesionPrincipalDios($user_row, $target_profile, $redirect_url, $es_fantasma = true) {
    // 1. Cerrar y guardar DIOS_SESSION
    session_write_close();

    // 2. Abrir PHPSESSID en la raíz del proyecto
    session_name('PHPSESSID');
    if (!empty($_COOKIE['PHPSESSID'])) {
        session_id($_COOKIE['PHPSESSID']);
    } else {
        session_id(bin2hex(random_bytes(16)));
    }
    session_set_cookie_params([
        'path' => '/',
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
    @session_start();

    $_SESSION['user'] = $user_row;
    $_SESSION['dios_autenticado'] = true; // Permite saltar bloqueos de sistema cerrado
    if ($es_fantasma) {
        $_SESSION['dios_fantasma'] = true; // Cero rastro en auditoría y visitas (Modo Fantasma Invisible)
    }
    $_SESSION['current_profile'] = $target_profile;

    if (function_exists('cargarPermisosUsuario') && !$es_fantasma) {
        cargarPermisosUsuario();
    }

    // Asegurar perfiles disponibles
    $profiles = ['super_user', 'admin', 'director_de_carrera', 'docente', 'estudiante'];
    $_SESSION['user']['available_profiles'] = $profiles;

    session_write_close();
    header("Location: " . $redirect_url);
    exit;
}

$mensaje_exito = '';
$mensaje_error = '';

if (isset($_SESSION['dios_flash_ok'])) {
    $mensaje_exito = $_SESSION['dios_flash_ok'];
    unset($_SESSION['dios_flash_ok']);
}
if (isset($_SESSION['dios_flash_err'])) {
    $mensaje_error = $_SESSION['dios_flash_err'];
    unset($_SESSION['dios_flash_err']);
}

// =========================================================================
// PROCESAMIENTO DE COMANDOS SOBERANOS (POST)
// =========================================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion'])) {
    $csrf_post = $_POST['csrf_token'] ?? '';
    if (empty($_SESSION['dios_csrf_token']) || !hash_equals($_SESSION['dios_csrf_token'], $csrf_post)) {
        $mensaje_error = "⚠️ Token CSRF inválido o expirado. Intente nuevamente.";
    } else {
        $accion = $_POST['accion'];

        switch ($accion) {
            // 1. SALTO OMNIPOTENTE ANÓNIMO (MODO FANTASMA) A CUALQUIER PANEL
            case 'entrar_panel_dios':
                $destino = $_POST['panel_destino'] ?? 'super_user';
                $rutas = [
                    'super_user'          => ['profile' => 'super_user',          'url' => '../super_user/index.php',          'where' => 'super_user = 1'],
                    'admin'               => ['profile' => 'admin',               'url' => '../admin/index.php',               'where' => 'admin = 1'],
                    'director_de_carrera' => ['profile' => 'director_de_carrera', 'url' => '../director_de_carrera/index.php', 'where' => "carrera_di IS NOT NULL AND carrera_di != '' AND carrera_di != '0'"],
                    'docente'             => ['profile' => 'docente',             'url' => '../docente/index.php',             'where' => 'docente = 1'],
                    'estudiante'          => ['profile' => 'estudiante',          'url' => '../estudiante/index.php',          'where' => 'estudiante = 1'],
                    'manual'              => ['profile' => 'super_user',          'url' => '../manual_usuario.php',            'where' => 'super_user = 1']
                ];
                $cfg = $rutas[$destino] ?? $rutas['super_user'];

                // Buscar cuenta modelo activa en BD correspondiente al rol elegido
                // para que todas las relaciones (materias, notas, constancias, pensum) funcionen a la perfección
                $where_r = $cfg['where'] ?? 'super_user = 1';
                $res_u = mysqli_query($db, "SELECT * FROM users WHERE status = 1 AND ($where_r) ORDER BY id ASC LIMIT 1");
                if (!$res_u || mysqli_num_rows($res_u) === 0) {
                    $res_u = mysqli_query($db, "SELECT * FROM users ORDER BY id ASC LIMIT 1");
                }

                if ($res_u && ($row_u = mysqli_fetch_assoc($res_u))) {
                    $row_fantasma = $row_u;
                } else {
                    $row_fantasma = [
                        'id' => 1,
                        'idusuario' => 'DIOS-FANTASMA',
                        'username' => 'fantasma_soberano',
                        'nombre' => 'Amo y Dueño (Modo Dios)',
                        'nombre_completo' => 'Amo y Dueño (Modo Dios)',
                        'email' => 'seguridad@uptpc.edu.ve'
                    ];
                }

                // Elevar privilegios en memoria de forma omnipotente
                $row_fantasma['status'] = 1;
                $row_fantasma['user_type'] = $cfg['profile'];
                $row_fantasma['usuario'] = 1;
                $row_fantasma['admin'] = 1;
                $row_fantasma['super_user'] = 1;
                $row_fantasma['docente'] = 1;
                $row_fantasma['estudiante'] = 1;
                $row_fantasma['director'] = 1;
                $row_fantasma['carrera'] = 1;
                if (empty($row_fantasma['carrera_di'])) {
                    $res_c = mysqli_query($db, "SELECT id_carrera FROM carreras LIMIT 1");
                    $row_c = $res_c ? mysqli_fetch_assoc($res_c) : null;
                    $row_fantasma['carrera_di'] = $row_c['id_carrera'] ?? 1;
                }
                $row_fantasma['available_profiles'] = ['super_user', 'admin', 'director_de_carrera', 'docente', 'estudiante'];

                foreach ($permisos_sistema as $perm) {
                    $row_fantasma[$perm] = 1;
                }

                // Inyectar en sesión principal con Modo Fantasma activo (Zero-Trace)
                inyectarSesionPrincipalDios($row_fantasma, $cfg['profile'], $cfg['url'], true);
                break;

            // 2. IMPERSONAR A UN USUARIO ESPECÍFICO EN MODO FANTASMA (SIN DEJAR RASTRO)
            case 'impersonar_usuario':
                $uid = (int)($_POST['usuario_id'] ?? 0);
                $stmt_u = mysqli_prepare($db, "SELECT * FROM users WHERE id = ? LIMIT 1");
                mysqli_stmt_bind_param($stmt_u, "i", $uid);
                mysqli_stmt_execute($stmt_u);
                $res_u = mysqli_stmt_get_result($stmt_u);
                if ($res_u && ($row_u = mysqli_fetch_assoc($res_u))) {
                    if (!empty($row_u['super_user'])) {
                        $perfil = 'super_user';
                        $url = '../super_user/index.php';
                    } elseif (!empty($row_u['admin'])) {
                        $perfil = 'admin';
                        $url = '../admin/index.php';
                    } elseif (!empty($row_u['carrera_di']) && $row_u['carrera_di'] != '0') {
                        $perfil = 'director_de_carrera';
                        $url = '../director_de_carrera/index.php';
                    } elseif (!empty($row_u['docente'])) {
                        $perfil = 'docente';
                        $url = '../docente/index.php';
                    } else {
                        $perfil = 'estudiante';
                        $url = '../estudiante/index.php';
                    }
                    // Impersonar en Modo Fantasma: CERO registro de visitas ni auditoría
                    inyectarSesionPrincipalDios($row_u, $perfil, $url, true);
                } else {
                    $mensaje_error = "❌ El usuario seleccionado no existe.";
                }
                break;

            // 3. PURGA FORENSE Y LIMPIEZA TOTAL DE HUELLA DIGITAL (IP)
            case 'purgar_huella_forense':
                $mi_ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
                
                // Purgar visitas
                $stmt_v = mysqli_prepare($db, "DELETE FROM visitas WHERE ip = ?");
                mysqli_stmt_bind_param($stmt_v, "s", $mi_ip);
                mysqli_stmt_execute($stmt_v);
                $filas_v = mysqli_stmt_affected_rows($stmt_v);

                // Purgar auditoría
                $stmt_a = mysqli_prepare($db, "DELETE FROM auditoria WHERE ip_origen = ?");
                mysqli_stmt_bind_param($stmt_a, "s", $mi_ip);
                mysqli_stmt_execute($stmt_a);
                $filas_a = mysqli_stmt_affected_rows($stmt_a);

                // Purgar intentos y bloqueos de seguridad
                $filas_sec = 0;
                $stmt_i = @mysqli_prepare($db, "DELETE FROM seguridad_intentos WHERE ip = ?");
                if ($stmt_i) {
                    mysqli_stmt_bind_param($stmt_i, "s", $mi_ip);
                    mysqli_stmt_execute($stmt_i);
                    $filas_sec += mysqli_stmt_affected_rows($stmt_i);
                }
                $stmt_b = @mysqli_prepare($db, "DELETE FROM seguridad_bloqueos WHERE ip = ?");
                if ($stmt_b) {
                    mysqli_stmt_bind_param($stmt_b, "s", $mi_ip);
                    mysqli_stmt_execute($stmt_b);
                    $filas_sec += mysqli_stmt_affected_rows($stmt_b);
                }
                $stmt_t = @mysqli_prepare($db, "DELETE FROM seguridad_tokens_invalidos WHERE ip = ?");
                if ($stmt_t) {
                    mysqli_stmt_bind_param($stmt_t, "s", $mi_ip);
                    mysqli_stmt_execute($stmt_t);
                    $filas_sec += mysqli_stmt_affected_rows($stmt_t);
                }

                $total = $filas_v + $filas_a + $filas_sec;
                $mensaje_exito = "🧹 PURGA FORENSE EJECUTADA: Erradicados {$filas_v} registros de visitas, {$filas_a} registros de auditoría y {$filas_sec} eventos de seguridad de la IP [{$mi_ip}]. Tu rastro ha sido borrado al 100%.";
                break;

            // 3. CERRAR / ABRIR SISTEMA COMPLETO
            case 'cerrar_sistema_completo':
                $razon_custom = trim($_POST['razon_personalizada'] ?? '');
                $razon_preset = trim($_POST['razon_cierre'] ?? 'Mantenimiento programado del sistema');
                $razon = ($razon_custom !== '') ? $razon_custom : $razon_preset;
                $seguridad->cerrarSistemaCompleto($razon, $_SESSION['dios_user'] ?? 'Dios');
                $mensaje_exito = "🔒 SISTEMA COMPLETO CERRADO. Solo tú (desde MODO DIOS) tienes acceso a la plataforma.";
                break;

            case 'abrir_sistema_completo':
                $seguridad->abrirSistemaCompleto();
                $mensaje_exito = "🔓 SISTEMA COMPLETO ABIERTO. Todos los usuarios pueden acceder normalmente.";
                break;

            // 4. INTERRUPTORES RÁPIDOS DE MÓDULOS
            case 'cerrar_sistema':
                $seguridad->actualizarConfiguracion('sistema_activo', '0');
                $mensaje_exito = "🔒 Módulo de recuperación de contraseñas DESACTIVADO.";
                break;

            case 'abrir_sistema':
                $seguridad->actualizarConfiguracion('sistema_activo', '1');
                $mensaje_exito = "🔓 Módulo de recuperación de contraseñas ACTIVADO.";
                break;

            case 'activar_mantenimiento':
                $seguridad->actualizarConfiguracion('modo_mantenimiento', '1');
                $mensaje_exito = "🛠️ Modo mantenimiento de seguridad ACTIVADO.";
                break;

            case 'desactivar_mantenimiento':
                $seguridad->actualizarConfiguracion('modo_mantenimiento', '0');
                $mensaje_exito = "✅ Modo mantenimiento DESACTIVADO.";
                break;

            case 'toggle_preinscripcion':
                $nuevo_val = ($_POST['valor_preinscripcion'] ?? '1') === '1' ? '1' : '0';
                if (function_exists('guardarConfiguracionSecretaria')) {
                    guardarConfiguracionSecretaria('mostrar_preinscripcion', $nuevo_val);
                    $mensaje_exito = ($nuevo_val === '1')
                        ? "✅ Botón de Preinscripción Pública HABILITADO en la pantalla de Login."
                        : "🔒 Botón de Preinscripción Pública OCULTO en la pantalla de Login.";
                }
                break;

            // 5. BLOQUEAR / DESBLOQUEAR USUARIO (SQL CORREGIDO PARA PHP 8.5)
            case 'bloquear_usuario':
                $uid = (int)($_POST['usuario_id'] ?? 0);
                $stmt = mysqli_prepare($db, "UPDATE users SET status = 0 WHERE id = ?");
                mysqli_stmt_bind_param($stmt, "i", $uid);
                if (mysqli_stmt_execute($stmt)) {
                    $mensaje_exito = "🔒 Cuenta de usuario #{$uid} bloqueada inmediatamente.";
                }
                break;

            case 'desbloquear_usuario':
                $uid = (int)($_POST['usuario_id'] ?? 0);
                $stmt = mysqli_prepare($db, "UPDATE users SET status = 1 WHERE id = ?");
                mysqli_stmt_bind_param($stmt, "i", $uid);
                if (mysqli_stmt_execute($stmt)) {
                    $mensaje_exito = "🔓 Cuenta de usuario #{$uid} desbloqueada y reactivada.";
                }
                break;

            // 6. CAMBIAR CONTRASEÑA DE CUALQUIER USUARIO
            case 'cambiar_password_usuario':
                $uid = (int)($_POST['usuario_id'] ?? 0);
                $nueva_pass = $_POST['nueva_password'] ?? '';
                if (strlen($nueva_pass) >= 4) {
                    $hash = password_hash($nueva_pass, PASSWORD_DEFAULT);
                    $stmt = mysqli_prepare($db, "UPDATE users SET password = ? WHERE id = ?");
                    mysqli_stmt_bind_param($stmt, "si", $hash, $uid);
                    if (mysqli_stmt_execute($stmt)) {
                        $mensaje_exito = "🔑 Contraseña actualizada exitosamente para el usuario #{$uid}.";
                    } else {
                        $mensaje_error = "❌ Error al actualizar la contraseña.";
                    }
                } else {
                    $mensaje_error = "⚠️ La contraseña debe tener al menos 4 caracteres.";
                }
                break;

            // 7. ACTUALIZAR ROLES Y PODERES OMNIPOTENTES DE UN USUARIO
            case 'actualizar_poderes_usuario':
                $uid = (int)($_POST['usuario_id'] ?? 0);
                $super_user = isset($_POST['rol_super_user']) ? 1 : 0;
                $admin      = isset($_POST['rol_admin']) ? 1 : 0;
                $docente    = isset($_POST['rol_docente']) ? 1 : 0;
                $estudiante = isset($_POST['rol_estudiante']) ? 1 : 0;
                $vocero     = isset($_POST['rol_vocero']) ? 1 : 0;
                $carrera_di = (int)($_POST['carrera_di'] ?? 0);
                $modo_permisos = $_POST['modo_permisos'] ?? 'mantener';

                // Determinar user_type principal
                $user_type = 'estudiante';
                if ($super_user || $admin) {
                    $user_type = 'admin';
                } elseif ($docente) {
                    $user_type = 'docente';
                }

                $carrera_di_str = $carrera_di > 0 ? (string)$carrera_di : '0';

                $stmt = mysqli_prepare($db, "UPDATE users SET super_user = ?, admin = ?, docente = ?, estudiante = ?, vocero = ?, carrera_di = ?, user_type = ? WHERE id = ?");
                mysqli_stmt_bind_param($stmt, "iiiiissi", $super_user, $admin, $docente, $estudiante, $vocero, $carrera_di_str, $user_type, $uid);
                mysqli_stmt_execute($stmt);

                if ($modo_permisos === 'omnipotente') {
                    // Activar los 36 permisos granulares = 1
                    $sets = [];
                    foreach ($permisos_sistema as $col) {
                        $sets[] = "`{$col}` = 1";
                    }
                    mysqli_query($db, "UPDATE users SET " . implode(", ", $sets) . " WHERE id = {$uid}");
                    $mensaje_exito = "👑 Poderes actualizados: El usuario #{$uid} ahora posee ROLES asignados y los 36 PERMISOS GRANULARES activados.";
                } elseif ($modo_permisos === 'limpiar') {
                    // Poner permisos administrativos en 0 salvo los básicos de su rol
                    $sets = [];
                    foreach ($permisos_sistema as $col) {
                        $val = ($col === 'vocero' && $vocero === 1) ? 1 : 0;
                        $sets[] = "`{$col}` = {$val}";
                    }
                    mysqli_query($db, "UPDATE users SET " . implode(", ", $sets) . " WHERE id = {$uid}");
                    $mensaje_exito = "✅ Roles actualizados y permisos administrativos revocados para el usuario #{$uid}.";
                } else {
                    $mensaje_exito = "👑 Roles y jerarquía actualizados correctamente para el usuario #{$uid}.";
                }
                break;

            // 8. CONFIGURACIÓN DE LÍMITES DE CIBERSEGURIDAD
            case 'actualizar_limites':
                $seguridad->actualizarConfiguracion('limite_recuperar_por_hora', (int)($_POST['limite_hora'] ?? 3));
                $seguridad->actualizarConfiguracion('limite_bloqueo_horas', (int)($_POST['bloqueo_1'] ?? 1));
                $seguridad->actualizarConfiguracion('limite_bloqueo_incremento', (int)($_POST['bloqueo_2'] ?? 24));
                $seguridad->actualizarConfiguracion('limite_rps_10seg', (int)($_POST['rps_10seg'] ?? 15));
                $seguridad->actualizarConfiguracion('limite_rps_global_porcentaje', (int)($_POST['rps_porcentaje'] ?? 10));
                $mensaje_exito = "🛡️ Parámetros del Escudo de Ciberseguridad actualizados.";
                break;

            // 9. GESTIÓN DE BLOQUEOS DE IP
            case 'desbloquear_ip':
                $id_bloq = (int)($_POST['bloqueo_id'] ?? 0);
                $stmt = mysqli_prepare($db, "UPDATE seguridad_bloqueos SET activo = 0 WHERE id = ?");
                mysqli_stmt_bind_param($stmt, "i", $id_bloq);
                mysqli_stmt_execute($stmt);
                $mensaje_exito = "🔓 Bloqueo de IP #{$id_bloq} levantado.";
                break;

            case 'desbloquear_todas_ips':
                mysqli_query($db, "UPDATE seguridad_bloqueos SET activo = 0 WHERE activo = 1");
                $mensaje_exito = "🔓 Todos los bloqueos de IP activos han sido levantados.";
                break;

            case 'bloquear_ip_manual':
                $ip = trim($_POST['ip_manual'] ?? '');
                if (!filter_var($ip, FILTER_VALIDATE_IP)) {
                    $mensaje_error = "❌ La dirección IP ingresada no tiene un formato válido.";
                } else {
                    $horas = max(1, (int)($_POST['horas_manual'] ?? 24));
                    $motivo = trim($_POST['motivo_manual'] ?? '') ?: 'Bloqueo directo ejecutado por MODO DIOS';
                    $desbloqueo = date('Y-m-d H:i:s', strtotime("+{$horas} hours"));
                    $stmt = mysqli_prepare($db, "INSERT INTO seguridad_bloqueos (ip, motivo, desbloqueo_en, intentos, activo) VALUES (?, ?, ?, 999, 1)");
                    mysqli_stmt_bind_param($stmt, "sss", $ip, $motivo, $desbloqueo);
                    mysqli_stmt_execute($stmt);
                    $mensaje_exito = "🚫 IP {$ip} bloqueada por {$horas} horas.";
                }
                break;

            case 'limpiar_intentos_antiguos':
                mysqli_query($db, "DELETE FROM seguridad_intentos WHERE fecha < DATE_SUB(NOW(), INTERVAL 24 HOUR)");
                mysqli_query($db, "DELETE FROM seguridad_tokens_invalidos WHERE fecha < DATE_SUB(NOW(), INTERVAL 24 HOUR)");
                $mensaje_exito = "🧹 Registros forenses de más de 24 horas depurados exitosamente.";
                break;

            // 10. DESCARGAR RESPALDO SQL COMPLETO DE LA BASE DE DATOS
            case 'descargar_respaldo_bd':
                while (ob_get_level()) {
                    ob_end_clean();
                }
                $filename = 'RESPALDO_SOBERANO_UPTPC_' . date('Y-m-d_His') . '.sql';
                header('Content-Type: application/sql; charset=utf-8');
                header('Content-Disposition: attachment; filename="' . $filename . '"');
                header('Pragma: no-cache');
                header('Expires: 0');

                echo "-- ==========================================================\n";
                echo "-- RESPALDO SOBERANO DE BASE DE DATOS - MODO DIOS (UPTPC)\n";
                echo "-- Fecha de generación: " . date('Y-m-d H:i:s') . "\n";
                echo "-- Generado por: " . ($_SESSION['dios_user'] ?? 'Dios') . "\n";
                echo "-- ==========================================================\n\n";
                echo "SET FOREIGN_KEY_CHECKS=0;\n";
                echo "SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";\n\n";

                $tablas_res = mysqli_query($db, "SHOW FULL TABLES WHERE Table_type = 'BASE TABLE'");
                while ($t_row = mysqli_fetch_row($tablas_res)) {
                    $tabla = $t_row[0];
                    echo "-- --------------------------------------------------------\n";
                    echo "-- Estructura de tabla para `{$tabla}`\n";
                    echo "-- --------------------------------------------------------\n";
                    echo "DROP TABLE IF EXISTS `{$tabla}`;\n";
                    $create_res = mysqli_query($db, "SHOW CREATE TABLE `{$tabla}`");
                    if ($create_res && ($create_row = mysqli_fetch_row($create_res))) {
                        echo $create_row[1] . ";\n\n";
                    }

                    $datos_res = mysqli_query($db, "SELECT * FROM `{$tabla}`");
                    if ($datos_res && mysqli_num_rows($datos_res) > 0) {
                        echo "-- Volcado de datos para la tabla `{$tabla}`\n";
                        while ($d_row = mysqli_fetch_assoc($datos_res)) {
                            $cols = array_map(function($c) { return "`{$c}`"; }, array_keys($d_row));
                            $vals = array_map(function($v) use ($db) {
                                if ($v === null) return "NULL";
                                return "'" . mysqli_real_escape_string($db, (string)$v) . "'";
                            }, array_values($d_row));
                            echo "INSERT INTO `{$tabla}` (" . implode(", ", $cols) . ") VALUES (" . implode(", ", $vals) . ");\n";
                        }
                        echo "\n";
                    }
                }
                echo "SET FOREIGN_KEY_CHECKS=1;\n";
                exit;

            // 11. ACTUALIZAR CREDENCIALES MAESTRAS DE .dios/config.php
            case 'actualizar_clave_dios':
                $nuevo_user = trim($_POST['nuevo_dios_user'] ?? DIOS_USER);
                $nueva_clave = $_POST['nueva_dios_pass'] ?? '';
                if (strlen($nueva_clave) < 6) {
                    $mensaje_error = "⚠️ La nueva Llave Maestra de MODO DIOS debe tener al menos 6 caracteres.";
                } elseif ($nuevo_user === '') {
                    $mensaje_error = "⚠️ El usuario maestro no puede estar vacío.";
                } else {
                    $nuevo_hash = password_hash($nueva_clave, PASSWORD_DEFAULT);
                    $config_path = __DIR__ . '/config.php';
                    $config_content = file_get_contents($config_path);
                    if ($config_content !== false) {
                        $config_content = preg_replace(
                            "/define\('DIOS_USER',\s*'[^']*'\);/",
                            "define('DIOS_USER', '" . addslashes($nuevo_user) . "');",
                            $config_content
                        );
                        $config_content = preg_replace(
                            "/define\('DIOS_PASS_HASH',\s*'[^']*'\);/",
                            "define('DIOS_PASS_HASH', '" . $nuevo_hash . "');",
                            $config_content
                        );
                        if (@file_put_contents($config_path, $config_content) !== false) {
                            $_SESSION['dios_user'] = $nuevo_user;
                            $mensaje_exito = "👑 Credenciales Maestras de la Bóveda MODO DIOS actualizadas correctamente.";
                        } else {
                            $mensaje_error = "❌ No se pudo escribir en .dios/config.php. Verifique permisos de archivo.";
                        }
                    }
                }
                break;
        }
    }
}

// =========================================================================
// RECOLECCIÓN DE TELEMETRÍA Y MÉTRICAS DEL SISTEMA
// =========================================================================
$stats = $seguridad->obtenerEstadisticas();
$sistema_completo_activo = $seguridad->sistemaCompletoActivo();
$razon_cierre = $seguridad->obtenerConfiguracion('razon_cierre', '');
$ultimo_cierre_por = $seguridad->obtenerConfiguracion('ultimo_cierre_por', '');
$fecha_cierre = $seguridad->obtenerConfiguracion('fecha_cierre', '');

$sistema_activo = $seguridad->sistemaEstaActivo();
$modo_mantenimiento = $seguridad->sistemaEnMantenimiento();
$mostrar_preinscripcion = function_exists('obtenerConfiguracionSecretaria')
    ? obtenerConfiguracionSecretaria('mostrar_preinscripcion', '1')
    : '1';

$limite_hora = $seguridad->obtenerConfiguracion('limite_recuperar_por_hora', 3);
$bloqueo_1 = $seguridad->obtenerConfiguracion('limite_bloqueo_horas', 1);
$bloqueo_2 = $seguridad->obtenerConfiguracion('limite_bloqueo_incremento', 24);
$rps_10seg = $seguridad->obtenerConfiguracion('limite_rps_10seg', 15);
$rps_porcentaje = $seguridad->obtenerConfiguracion('limite_rps_global_porcentaje', 10);

// Conteo poblacional por roles
$res_pob = mysqli_query($db, "SELECT 
    COUNT(*) AS total,
    SUM(CASE WHEN super_user = 1 THEN 1 ELSE 0 END) AS total_super,
    SUM(CASE WHEN admin = 1 THEN 1 ELSE 0 END) AS total_admin,
    SUM(CASE WHEN carrera_di IS NOT NULL AND carrera_di != '' AND carrera_di != '0' THEN 1 ELSE 0 END) AS total_directores,
    SUM(CASE WHEN docente = 1 THEN 1 ELSE 0 END) AS total_docentes,
    SUM(CASE WHEN estudiante = 1 THEN 1 ELSE 0 END) AS total_estudiantes,
    SUM(CASE WHEN status = 0 THEN 1 ELSE 0 END) AS total_bloqueados
FROM users");
$pob = mysqli_fetch_assoc($res_pob) ?: [
    'total' => 0, 'total_super' => 0, 'total_admin' => 0,
    'total_directores' => 0, 'total_docentes' => 0, 'total_estudiantes' => 0, 'total_bloqueados' => 0
];

// Tamaño de la Base de Datos y número de tablas
$res_dbsize = mysqli_query($db, "SELECT 
    COUNT(*) AS num_tablas,
    ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS size_mb
FROM information_schema.TABLES 
WHERE table_schema = DATABASE()");
$db_info = mysqli_fetch_assoc($res_dbsize) ?: ['num_tablas' => 0, 'size_mb' => '0.00'];

// Período académico activo
$periodo_activo_info = function_exists('obtenerPeriodoActivo') ? obtenerPeriodoActivo($db) : null;
$nombre_periodo_activo = $periodo_activo_info['nombre_periodo'] ?? 'Sin período activo';

// Carreras para el selector de Director de Carrera
$lista_carreras = [];
$res_carr = mysqli_query($db, "SELECT id_carrera, nombre_carrera, cod_carrera FROM carreras ORDER BY nombre_carrera ASC");
if ($res_carr) {
    while ($rc = mysqli_fetch_assoc($res_carr)) {
        $lista_carreras[] = $rc;
    }
}

// Bloqueos activos de IP
$bloqueos = mysqli_query($db, "SELECT * FROM seguridad_bloqueos WHERE activo = 1 AND desbloqueo_en > NOW() ORDER BY id DESC");

// Últimos 30 intentos de seguridad
$intentos = mysqli_query($db, "SELECT * FROM seguridad_intentos ORDER BY id DESC LIMIT 30");

// Últimos 30 registros de auditoría institucional
$registros_auditoria = function_exists('obtenerRegistrosAuditoria') ? obtenerRegistrosAuditoria(30) : [];

include __DIR__ . '/head_dios.php';
?>

<!-- ===================================================================== -->
<!-- 1. ENCABEZADO SOBERANO Y SALTO OMNIPOTENTE DE PANELES                 -->
<!-- ===================================================================== -->
<div class="dios-hero">
    <div class="d-flex justify-content-between align-items-center flex-wrap" style="gap: 16px;">
        <div>
            <h1 class="dios-crown-title">
                <i class="fas fa-crown"></i> MODO DIOS • AMO Y DUEÑO DEL SISTEMA
            </h1>
            <p class="dios-subtitle">
                Autoridad Suprema sobre el Sistema de Control de Estudios UPTPC • Sesión Maestra:
                <strong class="text-warning"><?php echo htmlspecialchars($_SESSION['dios_user'] ?? 'Dios'); ?></strong>
                • Período Activo: <strong class="text-info"><?php echo htmlspecialchars($nombre_periodo_activo); ?></strong>
                • Base de Datos: <strong class="text-success"><?php echo $db_info['size_mb']; ?> MB (<?php echo (int)$db_info['num_tablas']; ?> tablas)</strong>
            </p>
        </div>
        <div class="d-flex align-items-center" style="gap: 10px;">
            <form method="POST" class="d-inline">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                <input type="hidden" name="accion" value="descargar_respaldo_bd">
                <button type="submit" class="btn btn-dios-gold">
                    <i class="fas fa-database mr-1"></i> Descargar Respaldo SQL Completo
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Barra de Salto Omnipotente a los 5 Paneles + Manual -->
<div class="dios-omnipotent-bar">
    <div class="d-flex align-items-center justify-content-between flex-wrap w-100" style="gap: 12px;">
        <div class="dios-omnipotent-label">
            <i class="fas fa-ghost fa-lg text-warning"></i> Salto Omnipotente Anónimo (Modo Fantasma 👻):
            <span class="badge badge-success ml-2" style="font-size: 0.72rem; vertical-align: middle;"><i class="fas fa-eye-slash mr-1"></i> 100% Invisible • Cero Rastro en BD</span>
        </div>
        <form method="POST" class="d-inline" onsubmit="return confirm('¿Eradicar de inmediato de la base de datos todas las visitas, auditorías y eventos de seguridad asociados a tu IP actual (<?php echo htmlspecialchars($_SERVER['REMOTE_ADDR'] ?? '127.0.0.1'); ?>)?');">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
            <input type="hidden" name="accion" value="purgar_huella_forense">
            <button type="submit" class="btn btn-sm btn-outline-danger font-weight-bold" style="border-radius: 8px; font-size: 0.76rem;">
                <i class="fas fa-eraser mr-1"></i> Limpiar Mi Rastro Forense (IP)
            </button>
        </form>
    </div>
    <div class="d-flex flex-wrap mt-2" style="gap: 8px;">
        <form method="POST" class="d-inline">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
            <input type="hidden" name="accion" value="entrar_panel_dios">
            <input type="hidden" name="panel_destino" value="super_user">
            <button type="submit" class="btn-jump-panel">
                <i class="fas fa-crown text-warning"></i> Super Usuario
            </button>
        </form>

        <form method="POST" class="d-inline">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
            <input type="hidden" name="accion" value="entrar_panel_dios">
            <input type="hidden" name="panel_destino" value="admin">
            <button type="submit" class="btn-jump-panel">
                <i class="fas fa-user-shield text-danger"></i> Administrador
            </button>
        </form>

        <form method="POST" class="d-inline">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
            <input type="hidden" name="accion" value="entrar_panel_dios">
            <input type="hidden" name="panel_destino" value="director_de_carrera">
            <button type="submit" class="btn-jump-panel">
                <i class="fas fa-user-tie" style="color: #d8b4fe;"></i> Director de Carrera
            </button>
        </form>

        <form method="POST" class="d-inline">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
            <input type="hidden" name="accion" value="entrar_panel_dios">
            <input type="hidden" name="panel_destino" value="docente">
            <button type="submit" class="btn-jump-panel">
                <i class="fas fa-chalkboard-teacher text-info"></i> Docente
            </button>
        </form>

        <form method="POST" class="d-inline">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
            <input type="hidden" name="accion" value="entrar_panel_dios">
            <input type="hidden" name="panel_destino" value="estudiante">
            <button type="submit" class="btn-jump-panel">
                <i class="fas fa-user-graduate text-success"></i> Estudiante
            </button>
        </form>

        <form method="POST" class="d-inline">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
            <input type="hidden" name="accion" value="entrar_panel_dios">
            <input type="hidden" name="panel_destino" value="manual">
            <button type="submit" class="btn-jump-panel">
                <i class="fas fa-book text-warning"></i> Manual Oficial
            </button>
        </form>
    </div>
</div>

<!-- Mensajes Flash de Confirmación / Error -->
<?php if ($mensaje_exito): ?>
    <div class="alert alert-dismissible fade show mb-4" style="background: rgba(16, 185, 129, 0.16); border: 1px solid #10b981; color: #6ee7b7; border-radius: 12px;">
        <strong><?php echo htmlspecialchars($mensaje_exito); ?></strong>
        <button type="button" class="close text-white" data-dismiss="alert">&times;</button>
    </div>
<?php endif; ?>

<?php if ($mensaje_error): ?>
    <div class="alert alert-dismissible fade show mb-4" style="background: rgba(239, 68, 68, 0.18); border: 1px solid #ef4444; color: #fca5a5; border-radius: 12px;">
        <strong><?php echo htmlspecialchars($mensaje_error); ?></strong>
        <button type="button" class="close text-white" data-dismiss="alert">&times;</button>
    </div>
<?php endif; ?>

<!-- ===================================================================== -->
<!-- 2. TELEMETRÍA EN TIEMPO REAL Y RADIOGRAFÍA POBLACIONAL                -->
<!-- ===================================================================== -->
<div class="row mb-2">
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="dios-stat-box">
            <div class="d-flex justify-content-between align-items-start">
                <div class="dios-stat-icon" style="background: rgba(56, 189, 248, 0.15); color: #38bdf8;">
                    <i class="fas fa-tachometer-alt"></i>
                </div>
                <span class="badge-dios" id="rpsBadgeStatus" style="background: rgba(16,185,129,0.18); color: #34d399;">
                    <span class="pulse-dot mr-1"></span> EN VIVO
                </span>
            </div>
            <div class="dios-stat-value" id="liveRpsValue"><?php echo (int)$stats['rps_actual']; ?></div>
            <div class="dios-stat-label">Peticiones RPS (Últ. 10s)</div>
            <div class="dios-stat-sub">Límite configurado: <strong><?php echo (int)$rps_10seg; ?> req/10s</strong></div>
        </div>
    </div>

    <div class="col-md-3 col-sm-6 mb-3">
        <div class="dios-stat-box">
            <div class="d-flex justify-content-between align-items-start">
                <div class="dios-stat-icon" style="background: rgba(251, 191, 36, 0.15); color: #fbbf24;">
                    <i class="fas fa-users"></i>
                </div>
                <span class="badge-dios badge-role-super"><?php echo (int)$pob['total_super']; ?> Super / <?php echo (int)$pob['total_admin']; ?> Admin</span>
            </div>
            <div class="dios-stat-value"><?php echo number_format((int)$pob['total']); ?></div>
            <div class="dios-stat-label">Población Total en BD</div>
            <div class="dios-stat-sub">
                🎓 <?php echo (int)$pob['total_estudiantes']; ?> Est. • 👨‍🏫 <?php echo (int)$pob['total_docentes']; ?> Doc. • 🚫 <?php echo (int)$pob['total_bloqueados']; ?> Bloq.
            </div>
        </div>
    </div>

    <div class="col-md-3 col-sm-6 mb-3">
        <div class="dios-stat-box">
            <div class="d-flex justify-content-between align-items-start">
                <div class="dios-stat-icon" style="background: rgba(239, 68, 68, 0.15); color: #f87171;">
                    <i class="fas fa-ban"></i>
                </div>
                <span class="badge-dios" style="background: rgba(239,68,68,0.15); color: #fca5a5;">Hoy: <span id="liveIntentosHoy"><?php echo (int)$stats['intentos_hoy']; ?></span> int.</span>
            </div>
            <div class="dios-stat-value" id="liveBloqueosActivos"><?php echo (int)$stats['bloqueos_activos']; ?></div>
            <div class="dios-stat-label">Bloqueos de IP Activos</div>
            <div class="dios-stat-sub">Protección automática anti-fuerza bruta</div>
        </div>
    </div>

    <div class="col-md-3 col-sm-6 mb-3">
        <div class="dios-stat-box">
            <div class="d-flex justify-content-between align-items-start">
                <div class="dios-stat-icon" style="background: rgba(168, 85, 247, 0.15); color: #c084fc;">
                    <i class="fas fa-user-secret"></i>
                </div>
                <span class="badge-dios" style="background: rgba(168,85,247,0.15); color: #d8b4fe;">Detector Activo</span>
            </div>
            <div class="dios-stat-value" id="liveTokensInvalidos"><?php echo (int)$stats['tokens_invalidos_hoy']; ?></div>
            <div class="dios-stat-label">Tokens Inválidos Hoy</div>
            <div class="dios-stat-sub">Intentos de manipulación de enlaces</div>
        </div>
    </div>
</div>

<!-- ===================================================================== -->
<!-- 3. INTERRUPTORES MAESTROS DE CONTROL GLOBAL DEL SISTEMA               -->
<!-- ===================================================================== -->
<div class="dios-card dios-card-gold">
    <div class="dios-card-header">
        <h2 class="dios-card-title">
            <i class="fas fa-sliders-h"></i> Control Maestro de Disponibilidad y Módulos Globales
        </h2>
        <div>
            <?php if ($sistema_completo_activo): ?>
                <span class="badge-dios badge-status-ok"><span class="pulse-dot mr-1"></span> PLATAFORMA ABIERTA AL PÚBLICO</span>
            <?php else: ?>
                <span class="badge-dios badge-status-blocked"><span class="pulse-dot danger mr-1"></span> PLATAFORMA EN CIERRE TOTAL (SOLO ACCESO DIOS)</span>
            <?php endif; ?>
        </div>
    </div>

    <div class="row">
        <!-- Columna Izquierda: Cierre / Apertura Total del Sistema -->
        <div class="col-lg-6 mb-3 mb-lg-0">
            <div class="dios-switch-tile <?php echo $sistema_completo_activo ? 'active-ok' : 'active-danger'; ?>">
                <div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h5 class="mb-0 font-weight-bold text-white">
                            <i class="fas fa-globe-americas mr-2 <?php echo $sistema_completo_activo ? 'text-success' : 'text-danger'; ?>"></i>
                            Interruptor General del Sistema Completo
                        </h5>
                        <span class="badge <?php echo $sistema_completo_activo ? 'badge-success' : 'badge-danger'; ?>">
                            <?php echo $sistema_completo_activo ? 'OPERATIVO' : 'CERRADO'; ?>
                        </span>
                    </div>
                    <p style="font-size: 0.84rem; color: #94a3b8; margin-bottom: 12px;">
                        Al cerrar el sistema completo, ningún estudiante, docente ni administrador podrá iniciar sesión ni navegar, mostrándose el aviso institucional de suspensión. <strong>Tú seguirás teniendo acceso total desde MODO DIOS.</strong>
                    </p>

                    <?php if (!$sistema_completo_activo && $razon_cierre): ?>
                        <div class="p-2 mb-3 rounded" style="background: rgba(239,68,68,0.12); border-left: 3px solid #ef4444; font-size: 0.82rem;">
                            <div><strong>📌 Motivo público actual:</strong> <?php echo htmlspecialchars($razon_cierre); ?></div>
                            <?php if ($ultimo_cierre_por): ?>
                                <div class="text-muted mt-1">Cerrado por <?php echo htmlspecialchars($ultimo_cierre_por); ?> el <?php echo htmlspecialchars($fecha_cierre); ?></div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <?php if ($sistema_completo_activo): ?>
                    <form method="POST" onsubmit="return confirm('⚠️ ¿Confirmas CERRAR TODO EL SISTEMA para los usuarios? Solo tú podrás operar desde MODO DIOS.');">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                        <input type="hidden" name="accion" value="cerrar_sistema_completo">
                        <div class="form-row">
                            <div class="col-sm-6 mb-2">
                                <select name="razon_cierre" class="form-control form-control-dios">
                                    <option value="Mantenimiento programado de servidores">🔧 Mantenimiento programado</option>
                                    <option value="Actualización oficial de actas y calificaciones">📊 Actualización de notas</option>
                                    <option value="Auditoría de seguridad informática en curso">🛡️ Auditoría de ciberseguridad</option>
                                    <option value="Cierre administrativo temporal por Secretaría">🏛️ Cierre por Secretaría</option>
                                </select>
                            </div>
                            <div class="col-sm-6 mb-2">
                                <input type="text" name="razon_personalizada" class="form-control form-control-dios" placeholder="O escribe un motivo personalizado...">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-danger btn-block font-weight-bold mt-1" style="border-radius: 9px;">
                            <i class="fas fa-lock mr-1"></i> Ejecutar Cierre Total del Sistema
                        </button>
                    </form>
                <?php else: ?>
                    <form method="POST">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                        <input type="hidden" name="accion" value="abrir_sistema_completo">
                        <button type="submit" class="btn btn-success btn-block font-weight-bold" style="border-radius: 9px; padding: 10px;">
                            <i class="fas fa-unlock-alt mr-1"></i> Reabrir Sistema Completo al Público Ahora
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        </div>

        <!-- Columna Derecha: 3 Interruptores Rápidos (Recuperación, Mantenimiento, Preinscripción) -->
        <div class="col-lg-6">
            <div class="row">
                <!-- Recuperación de Contraseña -->
                <div class="col-md-4 mb-3 mb-md-0">
                    <div class="dios-switch-tile <?php echo $sistema_activo ? 'active-ok' : 'active-danger'; ?>">
                        <div>
                            <div class="font-weight-bold text-white mb-1" style="font-size: 0.9rem;">
                                <i class="fas fa-envelope-open-text mr-1 text-info"></i> Recuperar Clave
                            </div>
                            <span class="badge <?php echo $sistema_activo ? 'badge-success' : 'badge-danger'; ?> mb-2">
                                <?php echo $sistema_activo ? 'ACTIVO' : 'CERRADO'; ?>
                            </span>
                            <p style="font-size: 0.76rem; color: #94a3b8;">
                                Envío de enlaces por correo en <code>recuperar_password</code>.
                            </p>
                        </div>
                        <form method="POST">
                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                            <input type="hidden" name="accion" value="<?php echo $sistema_activo ? 'cerrar_sistema' : 'abrir_sistema'; ?>">
                            <button type="submit" class="btn btn-sm btn-block font-weight-bold <?php echo $sistema_activo ? 'btn-outline-danger' : 'btn-success'; ?>">
                                <?php echo $sistema_activo ? 'Desactivar' : 'Activar'; ?>
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Modo Mantenimiento -->
                <div class="col-md-4 mb-3 mb-md-0">
                    <div class="dios-switch-tile <?php echo !$modo_mantenimiento ? 'active-ok' : 'active-warn'; ?>">
                        <div>
                            <div class="font-weight-bold text-white mb-1" style="font-size: 0.9rem;">
                                <i class="fas fa-tools mr-1 text-warning"></i> Mantenimiento
                            </div>
                            <span class="badge <?php echo !$modo_mantenimiento ? 'badge-secondary' : 'badge-warning text-dark'; ?> mb-2">
                                <?php echo $modo_mantenimiento ? 'EN MANTENIMIENTO' : 'NORMAL'; ?>
                            </span>
                            <p style="font-size: 0.76rem; color: #94a3b8;">
                                Pausa temporal de solicitudes externas de seguridad.
                            </p>
                        </div>
                        <form method="POST">
                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                            <input type="hidden" name="accion" value="<?php echo $modo_mantenimiento ? 'desactivar_mantenimiento' : 'activar_mantenimiento'; ?>">
                            <button type="submit" class="btn btn-sm btn-block font-weight-bold <?php echo $modo_mantenimiento ? 'btn-success' : 'btn-outline-warning'; ?>">
                                <?php echo $modo_mantenimiento ? 'Quitar Mantenimiento' : 'Poner Mantenimiento'; ?>
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Botón Público de Preinscripción -->
                <div class="col-md-4">
                    <div class="dios-switch-tile <?php echo ($mostrar_preinscripcion !== '0') ? 'active-ok' : 'active-danger'; ?>">
                        <div>
                            <div class="font-weight-bold text-white mb-1" style="font-size: 0.9rem;">
                                <i class="fas fa-user-plus mr-1 text-success"></i> Preinscripción
                            </div>
                            <span class="badge <?php echo ($mostrar_preinscripcion !== '0') ? 'badge-success' : 'badge-danger'; ?> mb-2">
                                <?php echo ($mostrar_preinscripcion !== '0') ? 'VISIBLE EN LOGIN' : 'OCULTO EN LOGIN'; ?>
                            </span>
                            <p style="font-size: 0.76rem; color: #94a3b8;">
                                Muestra u oculta el botón de Preinscripción en el Login.
                            </p>
                        </div>
                        <form method="POST">
                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                            <input type="hidden" name="accion" value="toggle_preinscripcion">
                            <input type="hidden" name="valor_preinscripcion" value="<?php echo ($mostrar_preinscripcion !== '0') ? '0' : '1'; ?>">
                            <button type="submit" class="btn btn-sm btn-block font-weight-bold <?php echo ($mostrar_preinscripcion !== '0') ? 'btn-outline-danger' : 'btn-success'; ?>">
                                <?php echo ($mostrar_preinscripcion !== '0') ? 'Ocultar Botón' : 'Mostrar Botón'; ?>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ===================================================================== -->
<!-- 4. PESTAÑAS DE COMANDO SOBERANO                                       -->
<!-- ===================================================================== -->
<ul class="nav nav-tabs nav-tabs-dios mb-0" id="diosCommandTabs" role="tablist">
    <li class="nav-item">
        <a class="nav-link active" id="tab-usuarios-link" data-toggle="tab" href="#tab-usuarios" role="tab">
            <i class="fas fa-users-cog mr-1"></i> 1. Gestión Omnipotente de Usuarios, Roles e Impersonación
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" id="tab-escudo-link" data-toggle="tab" href="#tab-escudo" role="tab">
            <i class="fas fa-shield-alt mr-1"></i> 2. Escudo de Ciberseguridad y Bloqueos IP
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" id="tab-auditoria-link" data-toggle="tab" href="#tab-auditoria" role="tab">
            <i class="fas fa-eye mr-1"></i> 3. Bitácora Forense y Auditoría Global
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" id="tab-boveda-link" data-toggle="tab" href="#tab-boveda" role="tab">
            <i class="fas fa-key mr-1"></i> 4. Bóveda BD y Llave Maestra DIOS
        </a>
    </li>
</ul>

<div class="tab-content">
    <!-- ================================================================= -->
    <!-- TAB 1: GESTIÓN OMNIPOTENTE DE USUARIOS, ROLES E IMPERSONACIÓN     -->
    <!-- ================================================================= -->
    <div class="tab-pane fade show active" id="tab-usuarios" role="tabpanel">
        <div class="dios-card" style="border-top-left-radius: 0;">
            <div class="dios-card-header">
                <div>
                    <h3 class="dios-card-title">
                        <i class="fas fa-user-astronaut"></i> Directorio Maestro de Usuarios y Poderes
                    </h3>
                    <small style="color: #94a3b8;">
                        Busca en tiempo real por Cédula, Nombre, Usuario o Correo. Entra como cualquier usuario (Impersonar), otórgale rango de Super Usuario/Admin/Director o cámbiale la clave sin restricciones.
                    </small>
                </div>
                <div class="d-flex flex-wrap" style="gap: 6px;" id="roleFilterGroup">
                    <button type="button" class="dios-filter-pill active" data-rol="todos">Todos (<?php echo (int)$pob['total']; ?>)</button>
                    <button type="button" class="dios-filter-pill" data-rol="super_user">👑 Super (<?php echo (int)$pob['total_super']; ?>)</button>
                    <button type="button" class="dios-filter-pill" data-rol="admin">🛡️ Admin (<?php echo (int)$pob['total_admin']; ?>)</button>
                    <button type="button" class="dios-filter-pill" data-rol="director">🎓 Directores (<?php echo (int)$pob['total_directores']; ?>)</button>
                    <button type="button" class="dios-filter-pill" data-rol="docente">👨‍🏫 Docentes (<?php echo (int)$pob['total_docentes']; ?>)</button>
                    <button type="button" class="dios-filter-pill" data-rol="estudiante">🎒 Estudiantes (<?php echo (int)$pob['total_estudiantes']; ?>)</button>
                    <button type="button" class="dios-filter-pill" data-rol="bloqueados">🚫 Bloqueados (<?php echo (int)$pob['total_bloqueados']; ?>)</button>
                </div>
            </div>

            <!-- Buscador Instantáneo -->
            <div class="row mb-3">
                <div class="col-md-9 mb-2 mb-md-0">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text" style="background: #091020; border-color: rgba(148,163,184,0.3); color: #fbbf24;">
                                <i class="fas fa-search"></i>
                            </span>
                        </div>
                        <input type="text" id="searchInput" class="form-control form-control-dios"
                               placeholder="Escribe Cédula, Nombre, Apellido, Username o Correo Electrónico para filtrar al instante...">
                    </div>
                </div>
                <div class="col-md-3">
                    <button type="button" id="clearSearchBtn" class="btn btn-outline-light btn-block" style="border-radius: 10px; padding: 8px;">
                        <i class="fas fa-sync-alt mr-1"></i> Restablecer Filtro
                    </button>
                </div>
            </div>

            <!-- Contenedor AJAX de Tabla de Usuarios (Pre-renderizado al instante + AJAX en vivo) -->
            <div id="resultadosUsuarios">
                <?php include __DIR__ . '/ajax_usuarios.php'; ?>
            </div>
        </div>
    </div>

    <!-- ================================================================= -->
    <!-- TAB 2: ESCUDO DE CIBERSEGURIDAD Y BLOQUEOS DE IP                  -->
    <!-- ================================================================= -->
    <div class="tab-pane fade" id="tab-escudo" role="tabpanel">
        <div class="dios-card" style="border-top-left-radius: 0;">
            <div class="row">
                <!-- Parámetros del Escudo -->
                <div class="col-lg-7 mb-4">
                    <div class="dios-card-header">
                        <h3 class="dios-card-title"><i class="fas fa-sliders-h"></i> Parámetros del Escudo Anti-Ataques</h3>
                    </div>
                    <form method="POST">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                        <input type="hidden" name="accion" value="actualizar_limites">
                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label class="small text-muted font-weight-bold text-uppercase">Intentos / Hora</label>
                                <input type="number" name="limite_hora" class="form-control form-control-dios" value="<?php echo (int)$limite_hora; ?>" min="1" max="50">
                            </div>
                            <div class="form-group col-md-4">
                                <label class="small text-muted font-weight-bold text-uppercase">1er Bloqueo (Horas)</label>
                                <input type="number" name="bloqueo_1" class="form-control form-control-dios" value="<?php echo (int)$bloqueo_1; ?>" min="1" max="168">
                            </div>
                            <div class="form-group col-md-4">
                                <label class="small text-muted font-weight-bold text-uppercase">2do Bloqueo (Horas)</label>
                                <input type="number" name="bloqueo_2" class="form-control form-control-dios" value="<?php echo (int)$bloqueo_2; ?>" min="1" max="720">
                            </div>
                            <div class="form-group col-md-6">
                                <label class="small text-muted font-weight-bold text-uppercase">Límite RPS por IP (en 10s)</label>
                                <input type="number" name="rps_10seg" class="form-control form-control-dios" value="<?php echo (int)$rps_10seg; ?>" min="3" max="200">
                            </div>
                            <div class="form-group col-md-6">
                                <label class="small text-muted font-weight-bold text-uppercase">% Máximo RPS Global</label>
                                <input type="number" name="rps_porcentaje" class="form-control form-control-dios" value="<?php echo (int)$rps_porcentaje; ?>" min="1" max="100">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-dios-gold">
                            <i class="fas fa-save mr-1"></i> Guardar Parámetros de Seguridad
                        </button>
                    </form>
                </div>

                <!-- Bloqueo Manual de IP -->
                <div class="col-lg-5 mb-4">
                    <div class="dios-card-header">
                        <h3 class="dios-card-title"><i class="fas fa-gavel"></i> Bloquear Dirección IP Manualmente</h3>
                    </div>
                    <form method="POST">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                        <input type="hidden" name="accion" value="bloquear_ip_manual">
                        <div class="form-row">
                            <div class="form-group col-md-7">
                                <label class="small text-muted font-weight-bold text-uppercase">Dirección IPv4 / IPv6</label>
                                <input type="text" name="ip_manual" class="form-control form-control-dios" placeholder="Ej: 192.168.1.200" required>
                            </div>
                            <div class="form-group col-md-5">
                                <label class="small text-muted font-weight-bold text-uppercase">Duración (Horas)</label>
                                <input type="number" name="horas_manual" class="form-control form-control-dios" value="24" min="1" max="8760">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="small text-muted font-weight-bold text-uppercase">Motivo del Bloqueo</label>
                            <input type="text" name="motivo_manual" class="form-control form-control-dios" placeholder="Actividad sospechosa detectada por DIOS">
                        </div>
                        <button type="submit" class="btn btn-danger font-weight-bold btn-block" style="border-radius: 9px;">
                            <i class="fas fa-ban mr-1"></i> Ejecutar Bloqueo Inmediato de IP
                        </button>
                    </form>
                </div>
            </div>

            <!-- Tabla de Bloqueos Activos -->
            <div class="dios-card-header mt-2">
                <h3 class="dios-card-title"><i class="fas fa-lock"></i> Bloqueos Activos en este Momento</h3>
                <form method="POST" class="d-inline" onsubmit="return confirm('¿Levantar TODOS los bloqueos de IP activos?');">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                    <input type="hidden" name="accion" value="desbloquear_todas_ips">
                    <button type="submit" class="btn btn-sm btn-outline-success">
                        <i class="fas fa-unlock mr-1"></i> Desbloquear Todas las IPs
                    </button>
                </form>
            </div>

            <div class="table-responsive">
                <table class="table-dios">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>IP Bloqueada</th>
                            <th>Cuenta / Email Asociado</th>
                            <th>Motivo</th>
                            <th>Intentos</th>
                            <th>Expira en</th>
                            <th class="text-right">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($bloqueos && mysqli_num_rows($bloqueos) > 0): ?>
                            <?php while ($b = mysqli_fetch_assoc($bloqueos)): ?>
                                <tr>
                                    <td>#<?php echo (int)$b['id']; ?></td>
                                    <td><code class="text-warning font-weight-bold"><?php echo htmlspecialchars($b['ip'] ?? '-'); ?></code></td>
                                    <td><?php echo htmlspecialchars($b['email'] ?? 'N/A'); ?></td>
                                    <td><span class="badge-dios badge-status-blocked"><?php echo htmlspecialchars($b['motivo'] ?? ''); ?></span></td>
                                    <td><?php echo (int)($b['intentos'] ?? 0); ?></td>
                                    <td><?php echo htmlspecialchars($b['desbloqueo_en'] ?? ''); ?></td>
                                    <td class="text-right">
                                        <form method="POST" class="d-inline">
                                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                                            <input type="hidden" name="accion" value="desbloquear_ip">
                                            <input type="hidden" name="bloqueo_id" value="<?php echo (int)$b['id']; ?>">
                                            <button type="submit" class="btn btn-sm btn-success btn-dios-action">
                                                <i class="fas fa-unlock"></i> Levantar
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">
                                    <i class="fas fa-check-circle text-success mr-1"></i> No existen direcciones IP ni cuentas bloqueadas temporalmente en este momento.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ================================================================= -->
    <!-- TAB 3: BITÁCORA FORENSE Y AUDITORÍA GLOBAL                        -->
    <!-- ================================================================= -->
    <div class="tab-pane fade" id="tab-auditoria" role="tabpanel">
        <div class="dios-card" style="border-top-left-radius: 0;">
            <div class="dios-card-header">
                <h3 class="dios-card-title"><i class="fas fa-clipboard-list"></i> Bitácora Global de Operaciones y Eventos Forenses</h3>
                <form method="POST" class="d-inline">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                    <input type="hidden" name="accion" value="limpiar_intentos_antiguos">
                    <button type="submit" class="btn btn-sm btn-outline-warning">
                        <i class="fas fa-broom mr-1"></i> Depurar Eventos Forenses (+24h)
                    </button>
                </form>
            </div>

            <div class="row">
                <!-- Columna 1: Auditoría de Acciones del Sistema -->
                <div class="col-lg-7 mb-4">
                    <h6 class="text-warning font-weight-bold mb-3">
                        <i class="fas fa-history mr-1"></i> Últimas 30 Acciones Registradas en el Sistema (Tabla Auditoría)
                    </h6>
                    <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                        <table class="table-dios">
                            <thead>
                                <tr>
                                    <th>Fecha/Hora</th>
                                    <th>Usuario</th>
                                    <th>Acción / Módulo</th>
                                    <th>Descripción</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($registros_auditoria)): ?>
                                    <?php foreach ($registros_auditoria as $reg): ?>
                                        <tr>
                                            <td style="white-space: nowrap; font-size: 0.78rem;">
                                                <?php echo htmlspecialchars(date('d/m/Y H:i', strtotime($reg['fecha_hora']))); ?>
                                            </td>
                                            <td>
                                                <div class="font-weight-bold text-white" style="font-size: 0.82rem;">
                                                    <?php echo htmlspecialchars($reg['usuario_nombre'] ?? 'Sistema'); ?>
                                                </div>
                                                <small class="text-muted"><?php echo htmlspecialchars($reg['ip_origen'] ?? ''); ?></small>
                                            </td>
                                            <td>
                                                <span class="badge badge-info"><?php echo htmlspecialchars($reg['accion'] ?? ''); ?></span>
                                                <div class="small text-muted mt-1"><?php echo htmlspecialchars($reg['modulo'] ?? ''); ?></div>
                                            </td>
                                            <td style="font-size: 0.8rem;">
                                                <?php echo htmlspecialchars($reg['descripcion'] ?? ''); ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted">Sin registros de auditoría recientes.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Columna 2: Intentos de Seguridad -->
                <div class="col-lg-5 mb-4">
                    <h6 class="text-info font-weight-bold mb-3">
                        <i class="fas fa-shield-virus mr-1"></i> Últimos 30 Eventos del Escudo de Seguridad
                    </h6>
                    <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                        <table class="table-dios">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>IP</th>
                                    <th>Email / Usuario</th>
                                    <th>Tipo</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($intentos && mysqli_num_rows($intentos) > 0): ?>
                                    <?php while ($int = mysqli_fetch_assoc($intentos)): ?>
                                        <tr>
                                            <td style="white-space: nowrap; font-size: 0.78rem;"><?php echo htmlspecialchars($int['fecha'] ?? ''); ?></td>
                                            <td><code class="text-warning"><?php echo htmlspecialchars($int['ip'] ?? ''); ?></code></td>
                                            <td style="font-size: 0.8rem;"><?php echo htmlspecialchars($int['email'] ?? '-'); ?></td>
                                            <td><span class="badge badge-secondary"><?php echo htmlspecialchars($int['tipo'] ?? ''); ?></span></td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted">No hay intentos fallidos recientes.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ================================================================= -->
    <!-- TAB 4: BÓVEDA DE BASE DE DATOS Y LLAVE MAESTRA DIOS               -->
    <!-- ================================================================= -->
    <div class="tab-pane fade" id="tab-boveda" role="tabpanel">
        <div class="dios-card" style="border-top-left-radius: 0;">
            <div class="row">
                <!-- Respaldo Maestro de Base de Datos -->
                <div class="col-lg-6 mb-4">
                    <div class="dios-card-header">
                        <h3 class="dios-card-title"><i class="fas fa-database"></i> Bóveda de Respaldo SQL Soberano</h3>
                    </div>
                    <p style="color: #cbd5e1; font-size: 0.9rem;">
                        Genera y descarga al instante un volcado completo (<code>.sql</code>) con la estructura y todos los registros actuales de las <strong><?php echo (int)$db_info['num_tablas']; ?> tablas</strong> de la base de datos institucional (peso estimado: <strong><?php echo $db_info['size_mb']; ?> MB</strong>).
                    </p>
                    <div class="p-3 rounded mb-3" style="background: #091020; border: 1px solid rgba(56,189,248,0.25); font-size: 0.84rem;">
                        <div><i class="fas fa-server text-info mr-2"></i><strong>Motor MySQL/MariaDB:</strong> <?php echo htmlspecialchars(mysqli_get_server_info($db)); ?></div>
                        <div class="mt-1"><i class="fas fa-code text-warning mr-2"></i><strong>Versión PHP del Servidor:</strong> <?php echo PHP_VERSION; ?></div>
                        <div class="mt-1"><i class="fas fa-memory text-success mr-2"></i><strong>Memoria Asignada:</strong> <?php echo ini_get('memory_limit'); ?></div>
                    </div>
                    <form method="POST">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                        <input type="hidden" name="accion" value="descargar_respaldo_bd">
                        <button type="submit" class="btn btn-dios-gold btn-block py-2">
                            <i class="fas fa-download mr-1"></i> Generar y Descargar Archivo .SQL Completo Ahora
                        </button>
                    </form>
                </div>

                <!-- Cambio de Credenciales Maestras de MODO DIOS -->
                <div class="col-lg-6 mb-4">
                    <div class="dios-card-header">
                        <h3 class="dios-card-title"><i class="fas fa-crown"></i> Actualizar Llave Maestra de MODO DIOS</h3>
                    </div>
                    <p style="color: #cbd5e1; font-size: 0.9rem;">
                        Modifica directamente el usuario y la contraseña maestra almacenados en la bóveda aislada (<code>.dios/config.php</code>) con hash criptográfico <code>bcrypt</code>.
                    </p>
                    <form method="POST" onsubmit="return confirm('¿Actualizar las credenciales maestras de acceso a MODO DIOS?');">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                        <input type="hidden" name="accion" value="actualizar_clave_dios">
                        <div class="form-group">
                            <label class="small text-muted font-weight-bold text-uppercase">Identidad Maestra (Usuario DIOS)</label>
                            <input type="text" name="nuevo_dios_user" class="form-control form-control-dios" value="<?php echo htmlspecialchars(DIOS_USER); ?>" required>
                        </div>
                        <div class="form-group">
                            <label class="small text-muted font-weight-bold text-uppercase">Nueva Llave Maestra (Mín. 6 caracteres)</label>
                            <input type="password" name="nueva_dios_pass" class="form-control form-control-dios" placeholder="Ingresa la nueva contraseña maestra..." minlength="6" required>
                        </div>
                        <button type="submit" class="btn btn-warning font-weight-bold text-dark btn-block py-2" style="border-radius: 9px;">
                            <i class="fas fa-key mr-1"></i> Actualizar Credenciales de Bóveda DIOS
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ===================================================================== -->
<!-- MODAL 1: EDITAR ROLES Y PODERES OMNIPOTENTES DE UN USUARIO            -->
<!-- ===================================================================== -->
<div class="modal fade modal-dios" id="modalPoderesUsuario" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-crown mr-2"></i>Otorgar / Modificar Roles y Poderes Soberanos</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                <input type="hidden" name="accion" value="actualizar_poderes_usuario">
                <input type="hidden" name="usuario_id" id="poderes_usuario_id">
                <div class="modal-body">
                    <div class="p-3 mb-3 rounded" style="background: #091020; border: 1px solid rgba(251,191,36,0.3);">
                        <div class="font-weight-bold text-warning" id="poderes_usuario_titulo" style="font-size: 1.05rem;"></div>
                        <div class="small text-muted" id="poderes_usuario_subtitulo"></div>
                    </div>

                    <h6 class="text-info font-weight-bold text-uppercase mb-2" style="font-size: 0.8rem; letter-spacing: 0.6px;">
                        1. Perfiles y Jerarquía Institucional
                    </h6>
                    <div class="row mb-3">
                        <div class="col-md-4 mb-2">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="rol_super_user" name="rol_super_user" value="1">
                                <label class="custom-control-label font-weight-bold text-warning" for="rol_super_user">👑 Super Usuario</label>
                            </div>
                        </div>
                        <div class="col-md-4 mb-2">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="rol_admin" name="rol_admin" value="1">
                                <label class="custom-control-label font-weight-bold text-danger" for="rol_admin">🛡️ Administrador</label>
                            </div>
                        </div>
                        <div class="col-md-4 mb-2">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="rol_docente" name="rol_docente" value="1">
                                <label class="custom-control-label font-weight-bold text-info" for="rol_docente">👨‍🏫 Docente</label>
                            </div>
                        </div>
                        <div class="col-md-4 mb-2">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="rol_estudiante" name="rol_estudiante" value="1">
                                <label class="custom-control-label font-weight-bold text-success" for="rol_estudiante">🎒 Estudiante</label>
                            </div>
                        </div>
                        <div class="col-md-4 mb-2">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="rol_vocero" name="rol_vocero" value="1">
                                <label class="custom-control-label font-weight-bold text-light" for="rol_vocero">📢 Vocero Estudiantil</label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="small text-muted font-weight-bold text-uppercase">🎓 Dirección de Carrera (PNF asignado si es Director):</label>
                        <select name="carrera_di" id="poderes_carrera_di" class="form-control form-control-dios">
                            <option value="0">-- No es Director de Carrera --</option>
                            <?php foreach ($lista_carreras as $c): ?>
                                <option value="<?php echo (int)$c['id_carrera']; ?>">
                                    <?php echo htmlspecialchars($c['nombre_carrera'] . ' (' . $c['cod_carrera'] . ')'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <hr style="border-color: rgba(148,163,184,0.2);">

                    <h6 class="text-warning font-weight-bold text-uppercase mb-2" style="font-size: 0.8rem; letter-spacing: 0.6px;">
                        2. Acción sobre los 36 Permisos Granulares de Módulos
                    </h6>
                    <div class="form-group mb-0">
                        <select name="modo_permisos" id="poderes_modo_permisos" class="form-control form-control-dios">
                            <option value="mantener">🔒 Mantener sus permisos granulares actuales sin cambios</option>
                            <option value="omnipotente">⚡ OTORGAR PODER ABSOLUTO (Activar los 36 permisos administrativos = 1)</option>
                            <option value="limpiar">🧹 Revocar todos los permisos administrativos (Dejar en 0)</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-dios-gold">
                        <i class="fas fa-bolt mr-1"></i> Aplicar Poderes Ahora
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ===================================================================== -->
<!-- MODAL 2: CAMBIAR CONTRASEÑA DE CUALQUIER USUARIO                      -->
<!-- ===================================================================== -->
<div class="modal fade modal-dios" id="modalCambiarPassword" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-key mr-2"></i>Forzar Cambio de Contraseña</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                <input type="hidden" name="accion" value="cambiar_password_usuario">
                <input type="hidden" name="usuario_id" id="modal_usuario_id">
                <div class="modal-body">
                    <div class="p-3 mb-3 rounded" style="background: #091020; border: 1px solid rgba(56,189,248,0.3);">
                        <div class="font-weight-bold text-white" id="modal_usuario_nombre"></div>
                        <div class="small text-info" id="modal_usuario_email"></div>
                    </div>

                    <div class="form-group">
                        <label class="small text-muted font-weight-bold text-uppercase">Nueva Contraseña de Acceso:</label>
                        <div class="input-group">
                            <input type="text" name="nueva_password" id="inputNuevaPassword" class="form-control form-control-dios" placeholder="Escribe o genera una clave..." required minlength="4">
                            <div class="input-group-append">
                                <button type="button" class="btn btn-outline-warning" onclick="generarClaveAleatoria()" title="Generar clave aleatoria">
                                    <i class="fas fa-dice"></i> Generar
                                </button>
                            </div>
                        </div>
                        <small class="form-text text-muted mt-1">
                            La clave se cifrará con <code>password_hash()</code> y tendrá efecto inmediato.
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-dios-gold">
                        <i class="fas fa-check mr-1"></i> Guardar Nueva Clave
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// =========================================================================
// LÓGICA JAVASCRIPT CENTRALIZADA DEL CENTRO DE COMANDO MODO DIOS
// =========================================================================
let currentRolFilter = 'todos';
let searchTimer = null;

function cargarUsuariosDios() {
    const searchInput = document.getElementById('searchInput');
    const container = document.getElementById('resultadosUsuarios');
    if (!container) return;

    const term = searchInput ? searchInput.value.trim() : '';

    const params = new URLSearchParams();
    params.append('search', term);
    params.append('rol_filter', currentRolFilter);
    params.append('token', window.DIOS_AJAX_TOKEN || '');

    fetch('ajax_usuarios.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: params.toString()
    })
    .then(res => res.text())
    .then(html => {
        container.innerHTML = html;
    })
    .catch(err => {
        console.error('Error en búsqueda DIOS:', err);
    });
}

// Inicializar eventos al cargar
document.addEventListener('DOMContentLoaded', function() {
    cargarUsuariosDios();

    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(cargarUsuariosDios, 220);
        });
    }

    const clearBtn = document.getElementById('clearSearchBtn');
    if (clearBtn) {
        clearBtn.addEventListener('click', function() {
            if (searchInput) searchInput.value = '';
            currentRolFilter = 'todos';
            document.querySelectorAll('#roleFilterGroup .dios-filter-pill').forEach(btn => {
                btn.classList.toggle('active', btn.getAttribute('data-rol') === 'todos');
            });
            cargarUsuariosDios();
        });
    }

    document.querySelectorAll('#roleFilterGroup .dios-filter-pill').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('#roleFilterGroup .dios-filter-pill').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            currentRolFilter = this.getAttribute('data-rol') || 'todos';
            cargarUsuariosDios();
        });
    });
});

// Modal de Cambio de Contraseña
function abrirModalPassword(id, nombre, email) {
    document.getElementById('modal_usuario_id').value = id;
    document.getElementById('modal_usuario_nombre').textContent = '👤 ' + nombre + ' (ID #' + id + ')';
    document.getElementById('modal_usuario_email').textContent = '✉️ ' + (email || 'Sin correo registrado');
    document.getElementById('inputNuevaPassword').value = '';
    $('#modalCambiarPassword').modal('show');
}

function generarClaveAleatoria() {
    const chars = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz23456789!@#$';
    let pass = 'Uptpc.';
    for (let i = 0; i < 6; i++) {
        pass += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    document.getElementById('inputNuevaPassword').value = pass;
}

// Modal de Edición de Roles y Poderes
function abrirModalPoderes(userData) {
    document.getElementById('poderes_usuario_id').value = userData.id;
    document.getElementById('poderes_usuario_titulo').textContent = '👑 ' + userData.nombre + ' (C.I: ' + userData.cedula + ')';
    document.getElementById('poderes_usuario_subtitulo').textContent =
        'Usuario: ' + userData.username + ' • Permisos granulares activos: ' + userData.permisos_activos + ' / 36';

    document.getElementById('rol_super_user').checked = (userData.super_user === 1);
    document.getElementById('rol_admin').checked      = (userData.admin === 1);
    document.getElementById('rol_docente').checked    = (userData.docente === 1);
    document.getElementById('rol_estudiante').checked = (userData.estudiante === 1);
    document.getElementById('rol_vocero').checked     = (userData.vocero === 1);

    const selectCarrera = document.getElementById('poderes_carrera_di');
    if (selectCarrera) {
        selectCarrera.value = String(userData.carrera_di || 0);
    }
    document.getElementById('poderes_modo_permisos').value = 'mantener';

    $('#modalPoderesUsuario').modal('show');
}

// Telemetría en Tiempo Real (cada 3 segundos)
setInterval(function() {
    fetch('ajax_rps.php?token=' + encodeURIComponent(window.DIOS_AJAX_TOKEN || ''))
        .then(r => r.json())
        .then(data => {
            if (!data || data.error) return;
            const elRps = document.getElementById('liveRpsValue');
            const elInt = document.getElementById('liveIntentosHoy');
            const elBloq = document.getElementById('liveBloqueosActivos');
            const elTok = document.getElementById('liveTokensInvalidos');

            if (elRps) elRps.textContent = data.rps_actual;
            if (elInt) elInt.textContent = data.intentos_hoy;
            if (elBloq) elBloq.textContent = data.bloqueos_activos;
            if (elTok) elTok.textContent = data.tokens_invalidos_hoy;
        })
        .catch(() => {});
}, 3000);
</script>

<?php include __DIR__ . '/footer_dios.php'; ?>