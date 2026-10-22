<?php
// funciones/services/ProsecucionService.php
// Servicio OOP para la gestión de Prosecución Académica (TSU a Ingeniería/Licenciatura)

if (!class_exists('ProsecucionService')) {

class ProsecucionService {
    private $db;

    public function __construct($db_connection) {
        $this->db = $db_connection;
    }

    /**
     * Evalúa si un estudiante es apto para inscribirse en prosecución de estudios
     * @param int $id_usuario ID interno del usuario en la tabla users
     * @return array Información detallada de elegibilidad
     */
    public function verificarElegibilidad($id_usuario) {
        $id_usuario = (int)$id_usuario;
        if ($id_usuario <= 0) {
            return [
                'es_apto' => false,
                'motivo' => 'Usuario no válido.',
                'carrera_origen' => null,
                'titulo_obtenido' => null,
                'titulo_destino' => null,
                'ya_solicitado' => false,
                'solicitud' => null
            ];
        }

        // 1. Obtener datos del estudiante y carrera
        $queryUser = "SELECT u.id, u.idusuario, u.nombre, u.carrera, u.status, u.titulos, u.user_type,
                             c.id_carrera, c.nombre_carrera, c.titulo_otorga, c.otro_titulo
                      FROM users u
                      LEFT JOIN carreras c ON u.carrera = c.id_carrera
                      WHERE u.id = ? AND (u.user_type = 'estudiante' OR u.estudiante = 1)
                      LIMIT 1";
        
        $stmtUser = $this->db->prepare($queryUser);
        if (!$stmtUser) {
            return [
                'es_apto' => false,
                'motivo' => 'Error de base de datos al consultar estudiante.',
                'carrera_origen' => null,
                'titulo_obtenido' => null,
                'titulo_destino' => null,
                'ya_solicitado' => false,
                'solicitud' => null
            ];
        }

        $stmtUser->bind_param("i", $id_usuario);
        $stmtUser->execute();
        $resUser = $stmtUser->get_result();
        $user = $resUser ? $resUser->fetch_assoc() : null;
        $stmtUser->close();

        if (!$user) {
            return [
                'es_apto' => false,
                'motivo' => 'Estudiante no encontrado en el sistema.',
                'carrera_origen' => null,
                'titulo_obtenido' => null,
                'titulo_destino' => null,
                'ya_solicitado' => false,
                'solicitud' => null
            ];
        }

        $carrera_id = (int)($user['carrera'] ?? 0);
        $otro_titulo = trim($user['otro_titulo'] ?? '');
        $titulo_otorga = trim($user['titulo_otorga'] ?? '');

        // 2. Validar si la carrera cuenta con prosecución (segundo título: Ingeniería / Licenciatura)
        if (empty($otro_titulo)) {
            return [
                'es_apto' => false,
                'motivo' => 'El programa cursado (' . ($user['nombre_carrera'] ?? 'Carrera') . ') no cuenta con opción de prosecución a un segundo título.',
                'carrera_origen' => $user,
                'titulo_obtenido' => $titulo_otorga ?: 'T.S.U.',
                'titulo_destino' => null,
                'ya_solicitado' => false,
                'solicitud' => null
            ];
        }

        // 3. Evaluar condiciones académicas de culminación de TSU o avance a Trayecto 3
        $cumple_academico = false;
        $detalle_cumplimiento = [];

        // Check A: Registro en tabla de graduados
        $queryGrad = "SELECT id, estado FROM graduados WHERE id_usuario = ? AND estado IN ('cumple_requisitos', 'graduado', 'titulo_entregado') LIMIT 1";
        if ($stmtGrad = $this->db->prepare($queryGrad)) {
            $stmtGrad->bind_param("i", $id_usuario);
            $stmtGrad->execute();
            $resGrad = $stmtGrad->get_result();
            if ($resGrad && $resGrad->num_rows > 0) {
                $cumple_academico = true;
                $detalle_cumplimiento[] = 'Registrado como egresado/graduado en Control de Estudios';
            }
            $stmtGrad->close();
        }

        // Check B: Control de avance de trayecto (Trayecto >= 2 aprobado para avanzar)
        if (!$cumple_academico) {
            $queryAvance = "SELECT id, trayecto_actual FROM control_avance_trayecto 
                            WHERE id_usuario = ? AND trayecto_actual >= 2 AND puede_avanzar = 1 
                            ORDER BY trayecto_actual DESC LIMIT 1";
            if ($stmtAvance = $this->db->prepare($queryAvance)) {
                $stmtAvance->bind_param("i", $id_usuario);
                $stmtAvance->execute();
                $resAvance = $stmtAvance->get_result();
                if ($resAvance && $resAvance->num_rows > 0) {
                    $cumple_academico = true;
                    $detalle_cumplimiento[] = 'Aprobación de avance de Trayecto II registrada';
                }
                $stmtAvance->close();
            }
        }

        // Check C: Aprobaciones de avance con destino Trayecto 3 o 4
        if (!$cumple_academico) {
            $queryAprob = "SELECT id, trayecto_destino FROM aprobaciones_avance 
                           WHERE id_usuario = ? AND trayecto_destino >= 3 
                           LIMIT 1";
            if ($stmtAprob = $this->db->prepare($queryAprob)) {
                $stmtAprob->bind_param("i", $id_usuario);
                $stmtAprob->execute();
                $resAprob = $stmtAprob->get_result();
                if ($resAprob && $resAprob->num_rows > 0) {
                    $cumple_academico = true;
                    $detalle_cumplimiento[] = 'Autorización de avance a Trayecto III registrada';
                }
                $stmtAprob->close();
            }
        }

        // Check D: Uso de la función institucional puedeAvanzarTrayecto si está disponible
        if (!$cumple_academico && function_exists('puedeAvanzarTrayecto')) {
            $evalAvance = puedeAvanzarTrayecto($id_usuario, 2, $carrera_id);
            if (!empty($evalAvance['puede_avanzar'])) {
                $cumple_academico = true;
                $detalle_cumplimiento[] = 'Totalidad de unidades curriculares del Trayecto II aprobadas';
            }
        }

        // Check E: Cursando o cursó activamente Trayecto 3 o 4
        if (!$cumple_academico) {
            $querySec = "SELECT s.id_seccion, t.numero_trayecto 
                         FROM estudiante_seccion es
                         INNER JOIN secciones s ON es.id_seccion = s.id_seccion
                         INNER JOIN trayectos t ON s.id_trayecto = t.id_trayecto
                         WHERE es.id_usuario = ? AND t.numero_trayecto >= 3
                         LIMIT 1";
            if ($stmtSec = $this->db->prepare($querySec)) {
                $stmtSec->bind_param("i", $id_usuario);
                $stmtSec->execute();
                $resSec = $stmtSec->get_result();
                if ($resSec && $resSec->num_rows > 0) {
                    $cumple_academico = true;
                    $detalle_cumplimiento[] = 'Inscripción activa en asignaturas de ciclo de Ingeniería/Licenciatura';
                }
                $stmtSec->close();
            }
        }

        // Check F: Campo titulos de users contiene TSU o Tecnico
        if (!$cumple_academico && !empty($user['titulos'])) {
            $titulos_str = mb_strtoupper($user['titulos'], 'UTF-8');
            if (strpos($titulos_str, 'TSU') !== false || strpos($titulos_str, 'T.S.U') !== false || strpos($titulos_str, 'TECNICO') !== false) {
                $cumple_academico = true;
                $detalle_cumplimiento[] = 'Título de T.S.U. consignado en expediente';
            }
        }

        // 4. Verificar si ya tiene una solicitud previa en la tabla prosecuciones
        $solicitud_actual = $this->obtenerSolicitudPorEstudiante($id_usuario);
        $ya_solicitado = ($solicitud_actual !== null);

        if ($cumple_academico) {
            return [
                'es_apto' => true,
                'motivo' => !empty($detalle_cumplimiento) ? implode('. ', $detalle_cumplimiento) : 'Cumple con los requisitos académicos para prosecución de estudios.',
                'carrera_origen' => $user,
                'titulo_obtenido' => $titulo_otorga ?: 'T.S.U.',
                'titulo_destino' => $otro_titulo,
                'ya_solicitado' => $ya_solicitado,
                'solicitud' => $solicitud_actual
            ];
        } else {
            return [
                'es_apto' => false,
                'motivo' => 'Aún no ha culminado el ciclo de Técnico Superior Universitario (Trayecto II) o no posee aprobación formal de avance a Ingeniería/Licenciatura emitida por Control de Estudios.',
                'carrera_origen' => $user,
                'titulo_obtenido' => $titulo_otorga ?: 'T.S.U.',
                'titulo_destino' => $otro_titulo,
                'ya_solicitado' => $ya_solicitado,
                'solicitud' => $solicitud_actual
            ];
        }
    }

    /**
     * Obtiene los datos completos del estudiante para pre-llenar el formulario
     * @param int $id_usuario
     * @return array|null
     */
    public function obtenerDatosEstudiante($id_usuario) {
        $id_usuario = (int)$id_usuario;
        $query = "SELECT u.*, 
                         c.id_carrera, c.nombre_carrera, c.titulo_otorga, c.otro_titulo
                  FROM users u
                  LEFT JOIN carreras c ON u.carrera = c.id_carrera
                  WHERE u.id = ? AND (u.user_type = 'estudiante' OR u.estudiante = 1)
                  LIMIT 1";
        
        $stmt = $this->db->prepare($query);
        if (!$stmt) return null;

        $stmt->bind_param("i", $id_usuario);
        $stmt->execute();
        $res = $stmt->get_result();
        $estudiante = $res ? $res->fetch_assoc() : null;
        $stmt->close();

        return $estudiante;
    }

    /**
     * Registra una nueva solicitud de prosecución
     * @param int $id_usuario
     * @param array $datos
     * @return array
     */
    public function registrarSolicitud($id_usuario, $datos) {
        $id_usuario = (int)$id_usuario;
        $elegibilidad = $this->verificarElegibilidad($id_usuario);

        if (!$elegibilidad['es_apto']) {
            return [
                'success' => false,
                'message' => 'No es apto para prosecución: ' . $elegibilidad['motivo']
            ];
        }

        // Verificar si ya tiene una solicitud pendiente o aprobada
        if ($elegibilidad['ya_solicitado']) {
            $estado = $elegibilidad['solicitud']['estatus'] ?? 'pendiente';
            if ($estado === 'pendiente' || $estado === 'aprobada') {
                return [
                    'success' => false,
                    'message' => 'Ya posee una solicitud de prosecución en estado: ' . strtoupper($estado) . '. Puede descargar su planilla.',
                    'id' => $elegibilidad['solicitud']['id']
                ];
            }
        }

        $estudiante = $this->obtenerDatosEstudiante($id_usuario);
        if (!$estudiante) {
            return [
                'success' => false,
                'message' => 'No se encontró el registro del estudiante.'
            ];
        }

        $idusuario = $estudiante['idusuario'];
        $carrera_origen = (int)($estudiante['carrera'] ?? 0);
        $carrera_destino = $carrera_origen; // Mismo PNF en fase de Ingeniería / Licenciatura
        $titulo_obtenido = $elegibilidad['titulo_obtenido'] ?? 'T.S.U.';
        $titulo_solicitado = $elegibilidad['titulo_destino'] ?? 'Ingeniería';
        $turno = !empty($datos['turno']) ? trim($datos['turno']) : 'Diurno';
        $sede = !empty($datos['sede']) ? trim($datos['sede']) : ($estudiante['sede'] ?: 'Sede Principal');
        $telefono = !empty($datos['telefono_contacto']) ? trim($datos['telefono_contacto']) : ($estudiante['tlf'] ?: $estudiante['cel']);
        $email = !empty($datos['email_contacto']) ? trim($datos['email_contacto']) : $estudiante['email'];
        $direccion = !empty($datos['direccion_actual']) ? trim($datos['direccion_actual']) : $estudiante['direccion'];
        $observaciones = !empty($datos['observaciones']) ? trim($datos['observaciones']) : '';

        $queryInsert = "INSERT INTO prosecuciones (
                            id_usuario, idusuario, carrera_origen, carrera_destino,
                            titulo_obtenido, titulo_solicitado, turno, sede,
                            telefono_contacto, email_contacto, direccion_actual,
                            observaciones, estatus, fecha_solicitud
                        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pendiente', NOW())";

        $stmt = $this->db->prepare($queryInsert);
        if (!$stmt) {
            return [
                'success' => false,
                'message' => 'Error al preparar el registro de prosecución: ' . $this->db->error
            ];
        }

        $stmt->bind_param(
            "isiissssssss",
            $id_usuario,
            $idusuario,
            $carrera_origen,
            $carrera_destino,
            $titulo_obtenido,
            $titulo_solicitado,
            $turno,
            $sede,
            $telefono,
            $email,
            $direccion,
            $observaciones
        );

        if ($stmt->execute()) {
            $insert_id = $stmt->insert_id;
            $stmt->close();

            return [
                'success' => true,
                'id' => $insert_id,
                'message' => '¡Solicitud de prosecución registrada exitosamente! Descargue y conserve su planilla oficial.'
            ];
        } else {
            $error = $stmt->error;
            $stmt->close();
            return [
                'success' => false,
                'message' => 'Error al guardar la solicitud: ' . $error
            ];
        }
    }

    /**
     * Obtiene una solicitud de prosecución por su ID
     * @param int $id
     * @return array|null
     */
    public function obtenerSolicitudPorId($id) {
        $id = (int)$id;
        $query = "SELECT p.*, 
                         u.nombre as nombre_estudiante, u.idusuario as cedula_estudiante,
                         u.genero, u.edo_civil, u.fecha_nac, u.ciudad, u.estado, u.municipio, u.parroquia,
                         co.nombre_carrera as nombre_carrera_origen,
                         co.cod_carrera as cod_carrera_origen
                  FROM prosecuciones p
                  LEFT JOIN users u ON p.id_usuario = u.id
                  LEFT JOIN carreras co ON p.carrera_origen = co.id_carrera
                  WHERE p.id = ?
                  LIMIT 1";

        $stmt = $this->db->prepare($query);
        if (!$stmt) return null;

        $stmt->bind_param("i", $id);
        $stmt->execute();
        $res = $stmt->get_result();
        $solicitud = $res ? $res->fetch_assoc() : null;
        $stmt->close();

        return $solicitud;
    }

    /**
     * Obtiene la solicitud más reciente de un estudiante
     * @param int $id_usuario
     * @return array|null
     */
    public function obtenerSolicitudPorEstudiante($id_usuario) {
        $id_usuario = (int)$id_usuario;
        $query = "SELECT p.*, 
                         co.nombre_carrera as nombre_carrera_origen
                  FROM prosecuciones p
                  LEFT JOIN carreras co ON p.carrera_origen = co.id_carrera
                  WHERE p.id_usuario = ?
                  ORDER BY p.id DESC
                  LIMIT 1";

        $stmt = $this->db->prepare($query);
        if (!$stmt) return null;

        $stmt->bind_param("i", $id_usuario);
        $stmt->execute();
        $res = $stmt->get_result();
        $solicitud = $res ? $res->fetch_assoc() : null;
        $stmt->close();

        return $solicitud;
    }
}

}
?>
