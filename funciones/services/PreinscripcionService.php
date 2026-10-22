<?php
// funciones/services/PreinscripcionService.php
// Servicio OOP para la gestión de Preinscripciones

if (!class_exists('PreinscripcionService')) {

class PreinscripcionService {
    private $db;

    public function __construct($db_connection) {
        $this->db = $db_connection;
    }

    public function estudianteExiste($idusuario) {
        $query = "SELECT id FROM users WHERE idusuario = ?";
        if ($stmt = $this->db->prepare($query)) {
            $stmt->bind_param("s", $idusuario);
            $stmt->execute();
            $result = $stmt->get_result();
            $exists = ($result->num_rows > 0);
            $stmt->close();
            return $exists;
        }
        return false;
    }

    public function preinscripcionPendienteExiste($idusuario) {
        $query = "SELECT id FROM preinscripciones WHERE idusuario = ? AND estatus = 'pendiente'";
        if ($stmt = $this->db->prepare($query)) {
            $stmt->bind_param("s", $idusuario);
            $stmt->execute();
            $result = $stmt->get_result();
            $exists = ($result->num_rows > 0);
            $stmt->close();
            return $exists;
        }
        return false;
    }

    public function obtenerPreinscripcionesPendientes($busqueda = null) {
        $query = "SELECT p.*, c.nombre_carrera 
                  FROM preinscripciones p 
                  LEFT JOIN carreras c ON p.carrera = c.id_carrera 
                  WHERE p.estatus = 'pendiente'";
        
        if ($busqueda) {
            $query .= " AND (p.nombre LIKE ? OR p.idusuario LIKE ? OR p.email LIKE ?)";
        }
        $query .= " ORDER BY p.fecha_creacion DESC";

        if ($stmt = $this->db->prepare($query)) {
            if ($busqueda) {
                $term = "%" . $busqueda . "%";
                $stmt->bind_param("sss", $term, $term, $term);
            }
            $stmt->execute();
            $result = $stmt->get_result();
            $items = [];
            while ($row = $result->fetch_assoc()) {
                $items[] = $row;
            }
            $stmt->close();
            return $items;
        }
        return [];
    }

    public function obtenerPreinscripcionPorId($id) {
        $query = "SELECT p.*, c.nombre_carrera 
                  FROM preinscripciones p 
                  LEFT JOIN carreras c ON p.carrera = c.id_carrera 
                  WHERE p.id = ?";
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
}

}
