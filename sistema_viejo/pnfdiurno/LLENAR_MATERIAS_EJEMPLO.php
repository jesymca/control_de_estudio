  <?php
  include "db.php";
	
	$pensum="IXC";
    

	$sql = "SELECT * FROM lismat WHERE pensum='".$pensum."'";
	$resultado = $conn->query($sql);




	if ($resultado->num_rows > 0) {
		while($fila = $resultado->fetch_assoc()) {  
			//if($fila['nota']<>"R"){

				$lismat_libro_rector[$x1]=$fila['cod_mat_libro_rector'];	
				$lismat_cod_mat[$x1]=$fila['cod_mat'];			
				$descrip2[$x1]=$fila['descrip2'];
				$creditos[$x1]=$fila['creditos'];
				$semestre[$x1]=$fila['semestre'];			
				$nota_cat[$x1]=$fila['nota'];
				$divicion[$x1]=$fila['divicion'];
				$trayecto[$x1]=$fila['trayecto'];
				$aprobatori[$x1]=$fila['aprobatori'];
				$x1=$x1+1; 
			//} 
		}
	}

$codigo="V11095251";
$carrera="I";
$nota="20";
$lapso="2018-1";
$tiplap="";
$cod_doc="2";
$cod_usu="WALTER";
$acu="100";
$seccion="70";


	for ($i=0; $i <count($lismat_cod_mat) ; $i++) {

$cod_mat=$lismat_cod_mat[$i];
                                                                                                                                                                                                                                                 
$sql = "INSERT INTO notas (`codigo`, `cod_mat`, `carrera`, `nota`, `lapso`, `tiplap`, `cod_doc`, `cod_usu`, `acu`, `seccion`) VALUES ('$codigo', '$cod_mat', '$carrera', '$nota', '$lapso', '$tiplap', '$cod_doc', '$cod_usu', '$acu', '$seccion');";

if ($conn->multi_query($sql) === TRUE) {
    echo $cod_mat."<br>";
} else {
    echo "No se pudieron crear los registros: " . $sql . "<br>" . $conn->error."<br>";
}

	 }

	 $conn->close();