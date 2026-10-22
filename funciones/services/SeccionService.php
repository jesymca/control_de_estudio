<?php
// funciones/services/SeccionService.php
// Servicio OOP para la gestión de Secciones, Cupos y Horarios

if (!class_exists('SeccionService')) {

class SeccionService {
    private $db;

    public function __construct($db_connection) {
        $this->db = $db_connection;
    }

    public function obtenerCodigosSecciones() {
        $sql = "SELECT cs.*, c.nombre_carrera 
                FROM codigos_secciones cs 
                INNER JOIN carreras c ON cs.id_carrera = c.id_carrera 
                ORDER BY c.nombre_carrera, cs.codigo_inicio";
        $result = $this->db->query($sql);
        $codigos = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $codigos[] = $row;
            }
        }
        return $codigos;
    }

    public function insertarCodigoSeccion($id_carrera, $codigo_inicio, $codigo_fin, $descripcion) {
        $sql_check = "SELECT COUNT(*) as total FROM codigos_secciones 
                      WHERE id_carrera = ? AND 
                      ((? BETWEEN codigo_inicio AND codigo_fin) OR 
                       (? BETWEEN codigo_inicio AND codigo_fin) OR 
                       (codigo_inicio BETWEEN ? AND ?) OR 
                       (codigo_fin BETWEEN ? AND ?))";
        
        $stmt_check = $this->db->prepare($sql_check);
        $stmt_check->bind_param("iiiiiii", $id_carrera, $codigo_inicio, $codigo_fin, $codigo_inicio, $codigo_fin, $codigo_inicio, $codigo_fin);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();
        $row_check = $result_check->fetch_assoc();
        $stmt_check->close();

        if ($row_check['total'] > 0) {
            return ['success' => false, 'message' => 'El rango de códigos se solapa con uno existente para esta carrera.'];
        }

        $sql = "INSERT INTO codigos_secciones (id_carrera, codigo_inicio, codigo_fin, descripcion) VALUES (?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("iiis", $id_carrera, $codigo_inicio, $codigo_fin, $descripcion);
        if ($stmt->execute()) {
            $stmt->close();
            return ['success' => true, 'message' => 'Código de sección creado correctamente.'];
        } else {
            $error = $stmt->error;
            $stmt->close();
            return ['success' => false, 'message' => 'Error al crear el código de sección: ' . $error];
        }
    }

    public function actualizarCodigoSeccion($id, $id_carrera, $codigo_inicio, $codigo_fin, $descripcion) {
        $sql_check = "SELECT COUNT(*) as total FROM codigos_secciones 
                      WHERE id != ? AND id_carrera = ? AND 
                      ((? BETWEEN codigo_inicio AND codigo_fin) OR 
                       (? BETWEEN codigo_inicio AND codigo_fin) OR 
                       (codigo_inicio BETWEEN ? AND ?) OR 
                       (codigo_fin BETWEEN ? AND ?))";
        
        $stmt_check = $this->db->prepare($sql_check);
        $stmt_check->bind_param("iiiiiiii", $id, $id_carrera, $codigo_inicio, $codigo_fin, $codigo_inicio, $codigo_fin, $codigo_inicio, $codigo_fin);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();
        $row_check = $result_check->fetch_assoc();
        $stmt_check->close();

        if ($row_check['total'] > 0) {
            return ['success' => false, 'message' => 'El rango de códigos se solapa con uno existente para esta carrera.'];
        }

        $sql = "UPDATE codigos_secciones SET id_carrera = ?, codigo_inicio = ?, codigo_fin = ?, descripcion = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("iiisi", $id_carrera, $codigo_inicio, $codigo_fin, $descripcion, $id);
        if ($stmt->execute()) {
            $stmt->close();
            return ['success' => true, 'message' => 'Código de sección actualizado correctamente.'];
        } else {
            $error = $stmt->error;
            $stmt->close();
            return ['success' => false, 'message' => 'Error al actualizar el código de sección: ' . $error];
        }
    }

    public function eliminarCodigoSeccion($id) {
        $sql = "DELETE FROM codigos_secciones WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            $stmt->close();
            return ['success' => true, 'message' => 'Código de sección eliminado correctamente.'];
        } else {
            $error = $stmt->error;
            $stmt->close();
            return ['success' => false, 'message' => 'Error al eliminar el código de sección: ' . $error];
        }
    }

    public function generarCodigoSeccion($id_carrera, $turno) {
        $sql = "SELECT codigo_inicio, codigo_fin FROM codigos_secciones WHERE id_carrera = ? ORDER BY codigo_inicio";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id_carrera);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows == 0) {
            $stmt->close();
            return null;
        }

        $rangos = [];
        while ($row = $result->fetch_assoc()) {
            $rangos[] = $row;
        }
        $stmt->close();

        $sql_usados = "SELECT DISTINCT CAST(codigo_seccion AS UNSIGNED) as codigo_num 
                       FROM secciones 
                       WHERE id_carrera = ? AND turno = ? AND codigo_seccion REGEXP '^[0-9]+$'";
        $stmt_usados = $this->db->prepare($sql_usados);
        $stmt_usados->bind_param("is", $id_carrera, $turno);
        $stmt_usados->execute();
        $result_usados = $stmt_usados->get_result();

        $usados = [];
        while ($row = $result_usados->fetch_assoc()) {
            $usados[] = (int)$row['codigo_num'];
        }
        $stmt_usados->close();

        foreach ($rangos as $rango) {
            for ($codigo = $rango['codigo_inicio']; $codigo <= $rango['codigo_fin']; $codigo++) {
                if (!in_array($codigo, $usados, true)) {
                    return (string)$codigo;
                }
            }
        }
        return null;
    }
}

}
