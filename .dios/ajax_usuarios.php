<?php
// .dios/ajax_usuarios.php - Búsqueda y Filtrado Omnipotente de Usuarios en Tiempo Real
require_once __DIR__ . '/config.php';

$token_recibido = $_POST['token'] ?? $_GET['token'] ?? '';
$autenticado = (isset($_SESSION['dios_autenticado']) && $_SESSION['dios_autenticado'] === true) ||
               (!empty($_SESSION['dios_ajax_token']) && hash_equals($_SESSION['dios_ajax_token'], (string)$token_recibido));

if (!$autenticado) {
    http_response_code(403);
    exit('<div class="alert alert-danger m-3">No autorizado. Recargue el panel MODO DIOS.</div>');
}

require_once __DIR__ . '/../funciones/functions.php';

if (!isset($db) || !$db) {
    http_response_code(500);
    exit('<div class="alert alert-danger m-3">Error de conexión a la base de datos.</div>');
}

$search = trim($_POST['search'] ?? $_GET['search'] ?? '');
$rol_filter = trim($_POST['rol_filter'] ?? $_GET['rol_filter'] ?? 'todos');
$csrf_token = $_SESSION['dios_csrf_token'] ?? '';

$permisos_cols = [
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

$where = ["1=1"];
$params = [];
$types = "";

if ($search !== '') {
    $where[] = "(u.id LIKE ? OR u.idusuario LIKE ? OR u.nombre LIKE ? OR u.username LIKE ? OR u.email LIKE ?)";
    $term = "%{$search}%";
    array_push($params, $term, $term, $term, $term, $term);
    $types .= "sssss";
}

switch ($rol_filter) {
    case 'super_user':
        $where[] = "u.super_user = 1";
        break;
    case 'admin':
        $where[] = "u.admin = 1";
        break;
    case 'director':
        $where[] = "(u.carrera_di IS NOT NULL AND u.carrera_di != '' AND u.carrera_di != '0')";
        break;
    case 'docente':
        $where[] = "u.docente = 1";
        break;
    case 'estudiante':
        $where[] = "u.estudiante = 1";
        break;
    case 'bloqueados':
        $where[] = "u.status = 0";
        break;
}

$where_sql = implode(" AND ", $where);
$limit = ($search !== '' || $rol_filter !== 'todos') ? 50 : 25;

$sql = "SELECT u.*, 
               c.nombre_carrera AS nombre_carrera_est,
               cd.nombre_carrera AS nombre_carrera_dir
        FROM users u
        LEFT JOIN carreras c ON u.carrera = c.id_carrera
        LEFT JOIN carreras cd ON u.carrera_di = cd.id_carrera
        WHERE {$where_sql}
        ORDER BY u.super_user DESC, u.admin DESC, u.id DESC
        LIMIT {$limit}";

$stmt = mysqli_prepare($db, $sql);
if ($stmt && !empty($params)) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}
if ($stmt) {
    mysqli_stmt_execute($stmt);
    $usuarios = mysqli_stmt_get_result($stmt);
} else {
    $usuarios = false;
}

if (!$usuarios || mysqli_num_rows($usuarios) === 0): ?>
    <div class="text-center py-5" style="color: #94a3b8;">
        <i class="fas fa-user-slash fa-3x mb-3" style="color: rgba(251,191,36,0.35);"></i>
        <h6 class="text-white mb-1">No se encontraron usuarios con ese criterio</h6>
        <small>Intenta buscar por Cédula, Nombre, Usuario, Correo o cambia el filtro de rol.</small>
    </div>
<?php else: ?>
    <div class="table-responsive">
        <table class="table-dios">
            <thead>
                <tr>
                    <th>ID / Cédula</th>
                    <th>Identidad y Contacto</th>
                    <th>Roles y Poderes en el Sistema</th>
                    <th>Estado</th>
                    <th class="text-right">Comandos Soberanos</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($u = mysqli_fetch_assoc($usuarios)):
                    $uid = (int)$u['id'];
                    $cedula = htmlspecialchars($u['idusuario'] ?? 'S/C');
                    $nombre = htmlspecialchars($u['nombre'] ?? 'Sin Nombre');
                    $username = htmlspecialchars($u['username'] ?? '');
                    $email = htmlspecialchars($u['email'] ?? 'Sin correo');
                    $status = (int)($u['status'] ?? 1);
                    $carrera_est = $u['nombre_carrera_est'] ?? '';
                    $carrera_dir = $u['nombre_carrera_dir'] ?? '';
                    $carrera_di_val = (int)($u['carrera_di'] ?? 0);

                    // Contar permisos activos
                    $permisos_activos = 0;
                    foreach ($permisos_cols as $col) {
                        if (!empty($u[$col]) && (int)$u[$col] === 1) {
                            $permisos_activos++;
                        }
                    }

                    $js_user_data = htmlspecialchars(json_encode([
                        'id' => $uid,
                        'cedula' => $u['idusuario'] ?? '',
                        'nombre' => $u['nombre'] ?? '',
                        'username' => $u['username'] ?? '',
                        'email' => $u['email'] ?? '',
                        'super_user' => (int)($u['super_user'] ?? 0),
                        'admin' => (int)($u['admin'] ?? 0),
                        'docente' => (int)($u['docente'] ?? 0),
                        'estudiante' => (int)($u['estudiante'] ?? 0),
                        'vocero' => (int)($u['vocero'] ?? 0),
                        'carrera_di' => $carrera_di_val,
                        'permisos_activos' => $permisos_activos
                    ], JSON_HEX_APOS | JSON_HEX_QUOT), ENT_QUOTES, 'UTF-8');
                ?>
                <tr>
                    <td>
                        <div class="font-weight-bold" style="color: var(--dios-gold);">#<?php echo $uid; ?></div>
                        <span class="badge badge-dark border border-secondary mt-1" style="font-size: 0.78rem;">
                            <i class="fas fa-id-card text-info mr-1"></i><?php echo $cedula; ?>
                        </span>
                    </td>
                    <td>
                        <div class="font-weight-bold text-white" style="font-size: 0.94rem;"><?php echo $nombre; ?></div>
                        <div style="font-size: 0.8rem; color: #94a3b8;">
                            <i class="fas fa-user-tag mr-1"></i><strong><?php echo $username; ?></strong>
                            <span class="mx-1">•</span>
                            <i class="fas fa-envelope mr-1"></i><?php echo $email; ?>
                        </div>
                        <?php if ($carrera_est): ?>
                            <div style="font-size: 0.76rem; color: #6ee7b7; margin-top: 2px;">
                                <i class="fas fa-graduation-cap mr-1"></i>PNF: <?php echo htmlspecialchars($carrera_est); ?>
                            </div>
                        <?php endif; ?>
                        <?php if ($carrera_dir): ?>
                            <div style="font-size: 0.76rem; color: #d8b4fe; margin-top: 2px;">
                                <i class="fas fa-sitemap mr-1"></i>Dirige: <?php echo htmlspecialchars($carrera_dir); ?>
                            </div>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div class="d-flex flex-wrap" style="gap: 5px;">
                            <?php if (!empty($u['super_user'])): ?>
                                <span class="badge-dios badge-role-super"><i class="fas fa-crown"></i> SUPER USUARIO</span>
                            <?php endif; ?>
                            <?php if (!empty($u['admin'])): ?>
                                <span class="badge-dios badge-role-admin"><i class="fas fa-user-shield"></i> ADMIN</span>
                            <?php endif; ?>
                            <?php if ($carrera_di_val > 0): ?>
                                <span class="badge-dios badge-role-director"><i class="fas fa-user-tie"></i> DIRECTOR</span>
                            <?php endif; ?>
                            <?php if (!empty($u['docente'])): ?>
                                <span class="badge-dios badge-role-docente"><i class="fas fa-chalkboard-teacher"></i> DOCENTE</span>
                            <?php endif; ?>
                            <?php if (!empty($u['estudiante'])): ?>
                                <span class="badge-dios badge-role-estudiante"><i class="fas fa-user-graduate"></i> ESTUDIANTE</span>
                            <?php endif; ?>
                            <?php if (!empty($u['vocero'])): ?>
                                <span class="badge-dios" style="background: rgba(245,158,11,0.18); color: #fcd34d; border: 1px solid rgba(245,158,11,0.4);">
                                    <i class="fas fa-bullhorn"></i> VOCERO
                                </span>
                            <?php endif; ?>
                        </div>
                        <div class="mt-1">
                            <small style="color: #94a3b8; font-size: 0.74rem;">
                                <i class="fas fa-key text-warning mr-1"></i>Permisos granulares activos: <strong><?php echo $permisos_activos; ?> / <?php echo count($permisos_cols); ?></strong>
                            </small>
                        </div>
                    </td>
                    <td>
                        <?php if ($status === 1): ?>
                            <span class="badge-dios badge-status-ok"><i class="fas fa-check-circle"></i> ACTIVO</span>
                        <?php else: ?>
                            <span class="badge-dios badge-status-blocked"><i class="fas fa-ban"></i> BLOQUEADO</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-right" style="white-space: nowrap;">
                        <!-- 1. Impersonar / Entrar como este usuario -->
                        <form method="POST" action="index.php" class="d-inline" title="Entrar al sistema directamente con la sesión de este usuario">
                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                            <input type="hidden" name="accion" value="impersonar_usuario">
                            <input type="hidden" name="usuario_id" value="<?php echo $uid; ?>">
                            <button type="submit" class="btn btn-sm btn-info btn-dios-action" style="background: #0284c7; border-color: #0369a1;">
                                <i class="fas fa-mask"></i> Impersonar
                            </button>
                        </form>

                        <!-- 2. Editar Roles y Poderes -->
                        <button type="button" class="btn btn-sm btn-warning btn-dios-action text-dark font-weight-bold"
                                onclick='abrirModalPoderes(<?php echo $js_user_data; ?>)'
                                title="Modificar roles y permisos omnipotentes">
                            <i class="fas fa-crown"></i> Poderes
                        </button>

                        <!-- 3. Cambiar Contraseña -->
                        <button type="button" class="btn btn-sm btn-primary btn-dios-action"
                                onclick='abrirModalPassword(<?php echo $uid; ?>, <?php echo json_encode($u["nombre"] ?? ""); ?>, <?php echo json_encode($u["email"] ?? ""); ?>)'
                                title="Forzar nueva contraseña sin validación previa">
                            <i class="fas fa-key"></i> Clave
                        </button>

                        <!-- 4. Bloquear / Desbloquear -->
                        <?php if ($status === 1): ?>
                            <form method="POST" action="index.php" class="d-inline" onsubmit="return confirm('¿Bloquear inmediatamente el acceso a <?php echo addslashes($nombre); ?>?');">
                                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                                <input type="hidden" name="accion" value="bloquear_usuario">
                                <input type="hidden" name="usuario_id" value="<?php echo $uid; ?>">
                                <button type="submit" class="btn btn-sm btn-danger btn-dios-action" title="Bloquear cuenta">
                                    <i class="fas fa-lock"></i>
                                </button>
                            </form>
                        <?php else: ?>
                            <form method="POST" action="index.php" class="d-inline">
                                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                                <input type="hidden" name="accion" value="desbloquear_usuario">
                                <input type="hidden" name="usuario_id" value="<?php echo $uid; ?>">
                                <button type="submit" class="btn btn-sm btn-success btn-dios-action" title="Desbloquear cuenta">
                                    <i class="fas fa-unlock"></i>
                                </button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>