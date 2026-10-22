<?php
// funciones/services/CarreraService.php
// Servicio OOP para la gestión de Carreras y Mallas

if (!class_exists('CarreraService')) {

class CarreraService {
    private $db;

    public function __construct($db_connection) {
        $this->db = $db_connection;
    }

    public function obtenerNombreCarrera($carrera_id) {
        $sql = "SELECT nombre_carrera FROM carreras WHERE id_carrera = ? AND activa = 1";
        if ($stmt = mysqli_prepare($this->db, $sql)) {
            mysqli_stmt_bind_param($stmt, "i", $carrera_id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            if ($row = mysqli_fetch_assoc($result)) {
                mysqli_stmt_close($stmt);
                return $row['nombre_carrera'];
            }
            mysqli_stmt_close($stmt);
        }
        return 'No especificada';
    }

    public function obtenerCarreras($format = 'array') {
        $carreras = [];
        $query = "SELECT id_carrera, nombre_carrera FROM carreras WHERE activa = 1 ORDER BY nombre_carrera ASC";
        $result = $this->db->query($query);
        
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                if ($format === 'options') {
                    $carreras[$row['id_carrera']] = $row['nombre_carrera'];
                } else {
                    $carreras[] = $row;
                }
            }
        }
        return $carreras;
    }

    public function obtenerTipoPeriodoPorCarrera($id_carrera) {
        $query = "SELECT nombre_carrera FROM carreras WHERE id_carrera = ?";
        if ($stmt = $this->db->prepare($query)) {
            $stmt->bind_param("i", $id_carrera);
            if ($stmt->execute()) {
                $result = $stmt->get_result();
                if ($result->num_rows > 0) {
                    $carrera = $result->fetch_assoc();
                    $nombre_carrera = strtolower(trim($carrera['nombre_carrera']));
                    $stmt->close();

                    $carreras_trimestre = ['informatica', 'materiales industriales', 'mantenimiento', 'mecanica'];
                    foreach ($carreras_trimestre as $carrera_trim) {
                        if (strpos($nombre_carrera, $carrera_trim) !== false) {
                            return 'trimestre';
                        }
                    }

                    $carreras_semestre = ['turismo', 'logistica y distribucion', 'mecanica termica', 'mecanica automotriz'];
                    foreach ($carreras_semestre as $carrera_sem) {
                        if (strpos($nombre_carrera, $carrera_sem) !== false) {
                            return 'semestre';
                        }
                    }
                } else {
                    $stmt->close();
                }
            } else {
                $stmt->close();
            }
        }
        return 'semestre';
    }
}

}
