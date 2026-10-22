<?php

require('configuracion.php');

define('servidor', $servidor);
define('usuario', $usuario);
define('clave', $clave);
define('base_datos', $base_datos);

class carreras{

    private $conn;

    public function __construct()
	{ 
	    
        $this->conn = new mysqli(servidor, usuario, clave, base_datos);

        if ($this->conn->connect_error) {
          die("Conexión Fallida: ".$this->conn->connect_error);
        } 
	}

   
	public function carrera_larga($carrera)
	{
		

		$pensum=substr($carrera, 0, 1)."XC";

		$sql = "SELECT DISTINCT * FROM pensum where pensum='".$pensum."'";
		$resultado = $this->conn->query($sql);


		if ($resultado->num_rows > 0) {		
			while($fila = $resultado->fetch_assoc()) { 
				$descripcion=$fila["descripcion"];
			}
		}  
	    
	    
		return $descripcion;

	}


	public function pensum($carrera){		

		$pensum=substr($carrera, 0, 1)."XC";

		$sql = "SELECT DISTINCT * FROM pensum where pensum='".$pensum."'";
		$resultado = $this->conn->query($sql);

		if ($resultado->num_rows > 0) {		
			while($fila = $resultado->fetch_assoc()) { 
				$dat=$fila["pensum"];
			}
		}  
	    
	   
		return $dat;
	}


	public function carrera_corta($carrera){		

		$pensum=$carrera."XC";

		$sql = "SELECT DISTINCT * FROM pensum where pensum='".$pensum."'";
		$resultado = $this->conn->query($sql);

		if ($resultado->num_rows > 0) {		
			while($fila = $resultado->fetch_assoc()) { 
				$descripcion2=$fila["descripcion2"];
			}
		}     
	    
	    
		return $descripcion2;
	}





}




// include('getpensum3_clase.php');
// $con = new carreras();
// $carrera_a1 = $con->carrera_larga($carrera);
// $carrera_a2 = $con->carrera_corta($carrera);


// include('getpensum3_clase.php');
// $con = new carreras();
// $pensum= $con->pensum($carrera);