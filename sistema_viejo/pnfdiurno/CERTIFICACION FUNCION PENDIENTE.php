<?php
include "configuracion.php";


define( 'DB_HOST', $servidor ); // nombre del servidor
define( 'DB_USER', $usuario ); // nombre del usuario
define( 'DB_PASS', $clave ); // clave del servidor
define( 'DB_PASS', $base_datos );


class CRUD{


	private $conn;
	public $DB_NAME; 
	public $campos = array();
	public $tablas = array();
	public $objPHPExcel;
	public $index_tabla;        
	var $dat;

	public function __construct($DB_NAME)
	{ 
		global $connection;
		mb_internal_encoding( 'UTF-8' );
		mb_regex_encoding( 'UTF-8' );
		$this->conn = new mysqli( DB_HOST, DB_USER, DB_PASS, $DB_NAME );
		$this->DB_NAME=$DB_NAME;

		if( mysqli_connect_errno() )
		{
			$this->log_db_errors( "Connect failed: %s\n", mysqli_connect_error(), 'Fatal' );
			exit();
		}


	}

	public function __destruct()
	{
		$this->disconnect();
	}

	public function disconnect()
	{
		mysqli_close( $this->conn );
	}

	public function insert($tabla,$campos,$valores)
	{ 
		
		$sql = "INSERT INTO ".$tabla."(".$campos.")VALUES(".$valores.")";
		if ($this->conn->query($sql) == TRUE) {
			//echo "registro creado"."<br>";
		} else {
			//echo $this->conn->error;
		}
		
	}

	public function update($tabla,$campos_actualizar,$campo_buscar,$valor_buscar)
	{ 

		$sql = "UPDATE ".$tabla." SET ".$campos_actualizar." WHERE ".$campo_buscar." ='".$valor_buscar."'";		
		if ($this->conn->query($sql) == TRUE) {	
			//echo "registro actualizado"."<br>";
		} else {        	
			//echo "Error al actualizar el registro: ".$this->conn->error;
		}
		
	}




	public function select_nota($tabla,$where)
	{ 				
		$sql = "SELECT * FROM ".$tabla." WHERE ".$where;
		$result = $this->conn->query($sql);
		
		if ($result->num_rows > 0) {
			while($row = $result->fetch_assoc()) {
				$dat=$row['nota'];
				return $dat;
			} 
		}
	}


	
	public function sumar_materia($nota1,$nota2,$nota3)
	{ 				
		$dat=($nota1+$nota2+$nota3);
		
		return $dat;
	}



	public function select_lapso($tabla,$where)
	{ 				
		$sql = "SELECT * FROM ".$tabla." WHERE ".$where;
		$result = $this->conn->query($sql);
		
		if ($result->num_rows > 0) {
			while($row = $result->fetch_assoc()) {
				$dat=$row['lapso'];
				return $dat;
			} 
		}
	}

	public function select_tiplap($tabla,$where)
	{ 				
		$sql = "SELECT * FROM ".$tabla." WHERE ".$where;
		$result = $this->conn->query($sql);
		
		if ($result->num_rows > 0) {
			while($row = $result->fetch_assoc()) {
				$dat=$row['tiplap'];
				return $dat;
			} 
		}
	}


	public function select_seccion($tabla,$where)
	{ 				
		$sql = "SELECT * FROM ".$tabla." WHERE ".$where;
		$result = $this->conn->query($sql);
		
		if ($result->num_rows > 0) {
			while($row = $result->fetch_assoc()) {
				$dat=$row['seccion'];
				return $dat;
			} 
		}
	}


	public function calcular_ira($cedula)
	{ 				
		
		

		include "db.php";

		$sql = "SELECT * FROM alumno WHERE cedula='".$cedula."'";
		$resultado = $conn->query($sql);
		if ($resultado->num_rows > 0) {
			while($fila = $resultado->fetch_assoc()) {  


				$nombre  = utf8_decode($fila['nombre']);		

				$CODIGO=$fila['codigo'];			
				$CARRERA=$fila['carrera'];
				$MENCION=$fila['mencion'];
				$PLAN=$fila['plan'];

				$iras = $fila['iras'];	
				$irapromo = $fila['irapromo'];	
				$num_est = $fila['num_est'];	
				$ubicacion = $fila['ubicacion'];
				$fe_gr_alu = $fila['fe_gr_alu'];			
			}  
		}		

		$pensum=$CARRERA.$MENCION.$PLAN;

		$sql = "SELECT * FROM lismat WHERE pensum='".$pensum."'";
		$resultado = $conn->query($sql);

		$x1=0;
		

		if ($resultado->num_rows > 0) {
			while($fila = $resultado->fetch_assoc()) {  
				if($fila['nota']=="R" AND $fila['trayecto']<5){
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

				} 

			}
		}


		$nota1= 0; 
		$nota2= 0;
		$nota3= 0;

		$X3=1;
		$t==0;
		$IRA_0_nota=0;
		$IRA_1_nota=0;
		$IRA_2_nota=0;

		$IRA_0=0;
		$IRA_1=0;
		$IRA_2=0;

		for ($i=0; $i <count($lismat_cod_mat) ; $i++) { 

			$nota = $this->select_nota("notas","codigo='".$cedula."' and cod_mat='".$lismat_cod_mat[$i]."'");
			$lapso = $this->select_lapso("notas","codigo='".$cedula."' and cod_mat='".$lismat_cod_mat[$i]."'");
			$tiplap = $this->select_tiplap("notas","codigo='".$cedula."' and cod_mat='".$lismat_cod_mat[$i]."'");
			$seccion = $this->select_seccion("notas","codigo='".$cedula."' and cod_mat='".$lismat_cod_mat[$i]."'");


			$materia=substr($lismat_cod_mat[$i], 2, 2);
			$mate=substr($lismat_cod_mat[$i], 1, 1);
			$carrera=substr($lismat_cod_mat[$i], 0, 1);
			$plan=substr($lismat_cod_mat[$i], 4, 1);

			$materia0=$carrera."0".$materia.$plan;
			$materia1=$carrera."1".$materia.$plan;
			$materia2=$carrera."2".$materia.$plan;
			$materia3=$carrera."3".$materia.$plan;
			$materia4=$carrera."R".$materia.$plan;

			$nota0 = $this->select_nota("notas","codigo='".$cedula."' and cod_mat='".$materia0."'");
			$nota1 = $this->select_nota("notas","codigo='".$cedula."' and cod_mat='".$materia1."'");
			$nota2 = $this->select_nota("notas","codigo='".$cedula."' and cod_mat='".$materia2."'");
			$nota3 = $this->select_nota("notas","codigo='".$cedula."' and cod_mat='".$materia3."'");
			$divicion = $this->select_divicion("lismat","pensum='".$pensum."' and cod_mat='".$materia4."'");

			if($divicion==1 && $mate=="0"){
				$nota_R= $nota0;		   		   
			}elseif($divicion==1 && $mate=="1"){  
				$nota_R= $nota1;
			}elseif($divicion==1 && $mate=="2"){  
				$nota_R= $nota2;
			}elseif($divicion==1 && $mate=="3"){ 
				$nota_R= $nota3;
			}else{
				$nota_R= $this->sumar_materia($nota1,$nota2,$nota3);              

				if($nota_R>10.4){
					$nota_R=round($nota_R/$divicion,2); 
					$nota_R=ceil($nota_R);

				}else{
					$nota_R=round($nota_R/$divicion,2);		   	 
				} 
				if($nota_R<10){
					$nota_R= sprintf( "%2d".$nota_R, "" ); 
				} 

			}


			if($nota<>""){


			}else{


				if ($lismat_cod_mat[$i]==$materia4) {

					$lapso0 = $this->select_lapso("notas","codigo='".$cedula."' and cod_mat='".$materia0."'");
					$lapso1 = $this->select_lapso("notas","codigo='".$cedula."' and cod_mat='".$materia1."'");
					$lapso2 = $this->select_lapso("notas","codigo='".$cedula."' and cod_mat='".$materia2."'");
					$lapso3 = $this->select_lapso("notas","codigo='".$cedula."' and cod_mat='".$materia3."'");

					$tiplap0 = $this->select_tiplap("notas","codigo='".$cedula."' and cod_mat='".$materia0."'");
					$tiplap1 = $this->select_tiplap("notas","codigo='".$cedula."' and cod_mat='".$materia1."'");
					$tiplap2 = $this->select_tiplap("notas","codigo='".$cedula."' and cod_mat='".$materia2."'");
					$tiplap3 = $this->select_tiplap("notas","codigo='".$cedula."' and cod_mat='".$materia3."'");

					$nota0 = $this->select_nota("notas","codigo='".$cedula."' and cod_mat='".$materia0."'");
					$nota1 = $this->select_nota("notas","codigo='".$cedula."' and cod_mat='".$materia1."'");
					$nota2 = $this->select_nota("notas","codigo='".$cedula."' and cod_mat='".$materia2."'");
					$nota3 = $this->select_nota("notas","codigo='".$cedula."' and cod_mat='".$materia3."'");


					if($lapso3<>""){
						$lapso=$lapso3;
						$tiplap=$tiplap3;
						$nota=$nota3;
					}elseif ($lapso2<>"") {
						$lapso=$lapso2;
						$tiplap=$tiplap2;
						$nota=$nota2;
					}elseif ($lapso1<>"") {
						$lapso=$lapso1;
						$tiplap=$tiplap1;
						$nota=$nota1;
					}else{
						$lapso=$lapso0;
						$tiplap=$tiplap0;
						$nota=$nota0;
					}




					if($trayecto[$i]==3){
                	//if($nota>=$aprobatori[$i]){
						$A1++;			       
						$IRA_1=$IRA_1+$nota;
						$IRA_1B=$IRA_1/$A1;

				   // }        
					}

					if($trayecto[$i]==4){
                	//if($nota>=$aprobatori[$i]){
						$A2++;			      
						$IRA_2=$IRA_2+$nota;
						$IRA_2B=$IRA_2/$A2;

						$A3=$A1+$A2;
						$IRA_3=round(($IRA_1+$IRA_2)/$A3, 3);				         
					}

				}else{

				}
			}
		}



		echo $IRA_3;



	}










	public function select_divicion($tabla,$where)
	{ 				
		$sql = "SELECT * FROM ".$tabla." WHERE ".$where;
		$result = $this->conn->query($sql);
		
		if ($result->num_rows > 0) {
			while($row = $result->fetch_assoc()) {
				$dat=$row['divicion'];
				return $dat;
			} 
		}
	}

	public function sumar_fila($tabla,$campo,$where)
	{ 
		
		$sql = "SELECT SUM(`".$campo."`) AS TOTAL FROM '".$tabla."'";		
		$result = $this->conn->query($sql);       

		if ($result->num_rows > 0) {
			while($row = mysqli_fetch_row($result)) {
				echo $row[0]. "<br>";			    
			} 
		}		
	}
}

$con = new CRUD($base_datos);
$con->calcular_ira("V17025831");

include "db.php";
        $x2=0;
		$sql = "SELECT cedula FROM alumno WHERE fe_gr_alu='".$fe_gr_alu."' and marca='".$marca."'";
		$resultado = $conn->query($sql);
		if ($resultado->num_rows > 0) {
			while($fila = $resultado->fetch_assoc()) {
				$cedula[$x2]=$fila['cedula'];			
				$x2=$x2+1; 		
			}  
		}	

       
for ($i=0; $i <count($cedula) ; $i++) { 
	
   $ira=$con->calcular_ira($cedula[$i]);
   $num_est=count($cedula);

   $sql = "UPDATE `alumno` SET `iras`='".$ira."',num_est='".$num_est."' WHERE fe_gr_alu='".$fe_gr_alu."' and marca='".$marca."'";
   $resultado = $conn->query($sql);


}

       



?>
