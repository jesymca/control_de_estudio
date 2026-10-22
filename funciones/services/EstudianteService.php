<?php
// funciones/services/EstudianteService.php
// Servicio OOP para la gestión de Estudiantes

if (!class_exists('EstudianteService')) {

class EstudianteService {
    private $db;

    public function __construct($db_connection) {
        $this->db = $db_connection;
    }

    public function obtenerEstudiantes() {
        $estudiantes = [];
        $query = "SELECT 
                    u.id,
                    u.idusuario AS cedula,
                    u.nombre,
                    c.nombre_carrera AS carrera,
                    u.genero,
                    u.tlf AS num_telf,
                    u.email AS correo,
                    u.fecha_ingreso,
                    u.fecha_nac,
                    u.embarazada,
                    u.status
                  FROM users u
                  LEFT JOIN carreras c ON u.carrera = c.id_carrera
                  WHERE u.user_type = ?
                  ORDER BY u.fecha_ingreso DESC";
        
        if ($stmt = $this->db->prepare($query)) {
            $tipoUsuario = 'estudiante';
            $stmt->bind_param("s", $tipoUsuario);
            
            if ($stmt->execute()) {
                $result = $stmt->get_result();
                while ($row = $result->fetch_assoc()) {
                    $estudiantes[] = $row;
                }
                $stmt->close();
                return $estudiantes;
            } else {
                $error = $stmt->error;
                $stmt->close();
                return ['error' => "Error al ejecutar la consulta: " . $error];
            }
        } else {
            return ['error' => "Error al preparar la consulta: " . $this->db->error];
        }
    }

    public function obtenerDetalleEstudiante($id) {
        $query = "SELECT 
                    e.*,
                    c.nombre_carrera AS carrera_nombre
                  FROM estudiantes e
                  LEFT JOIN carreras c ON e.carrera = c.id_carrera
                  WHERE e.id = ?";
        
        if ($stmt = $this->db->prepare($query)) {
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) {
                $result = $stmt->get_result();
                $estudiante = $result->fetch_assoc();
                $stmt->close();
                
                if ($estudiante) {
                    $estudiante['carrera'] = $estudiante['carrera_nombre'];
                    return $estudiante;
                }
                return ['error' => "Estudiante no encontrado"];
            } else {
                $error = $stmt->error;
                $stmt->close();
                return ['error' => "Error al obtener detalle del estudiante: " . $error];
            }
        }
        return ['error' => "Error al preparar consulta: " . $this->db->error];
    }

    public function obtenerEstudiantePorId($id) {
        $query = "SELECT * FROM users WHERE id = ? AND user_type = 'estudiante'";
        if ($stmt = $this->db->prepare($query)) {
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $data = $result->fetch_assoc();
            $stmt->close();
            return $data;
        }
        return null;
    }

    public function mostrarEstadoEstudiante($status) {
        switch ($status) {
            case 'activo':
                return '<span class="badge badge-success">Activo</span>';
            case 'inactivo':
                return '<span class="badge badge-secondary">Inactivo</span>';
            case 'graduado':
                return '<span class="badge badge-info">Graduado</span>';
            case 'suspendido':
                return '<span class="badge badge-danger">Suspendido</span>';
            default:
                return '<span class="badge badge-dark">Desconocido</span>';
        }
    }
}

}
