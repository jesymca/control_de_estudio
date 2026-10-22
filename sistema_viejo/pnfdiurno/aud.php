<?php
function auditar($n,$cod)
{


	session_start();
	if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true) {
	} else {
		header("Location: index.html");
		exit;
	}
	$now = time();
	if($now > $_SESSION['expire']) {
		session_destroy();
		echo "Su sesion a terminado,<a href='index.html'>Necesita Hacer Login</a>";
		exit;
	}	

	date_default_timezone_set('America/Caracas');
	$cod_usuario = $_SESSION['username'];
	$ventana =$n;

	switch ($ventana) {




		case 'ACTAS_FINAL':
		$cod_mat=$cod;
		$hora=strftime("%I:%M:%S %p\n"); 
		$fecha = strftime("%x \n"); 
		$accion = "IMPRIMIR";
		$ventana="Nominas";
		$cod_usuario = $_SESSION['username'];  
		$hora=strftime("%I:%M:%S %p\n"); 
		$fecha = strftime("%x \n");		
		include('db.php');
		$sql = "INSERT INTO auditor (cod_usuario,ventana,accion,cod_mat,hora,fecha,cod_alumno,carrera,nota_pre,nota_pos)
		VALUES ('$cod_usuario','$ventana','$accion','$cod_mat','$hora','$fecha','$cod_alumno','$carrera','$nota_pre','$nota_pos')";
		if ($conn->query($sql) === TRUE) {

		} else {
			echo $conn->error;
		}

		$conn->close();

		break;








		case '0A':
		$hora=strftime("%I:%M:%S %p\n"); 
		$fecha = strftime("%x \n"); 
		$accion = "INICIO";
		$ventana="LOGIN";
		$cod_usuario = $_SESSION['username'];  
		$hora=strftime("%I:%M:%S %p\n"); 
		$fecha = strftime("%x \n");		
		include('db.php');
		$sql = "INSERT INTO auditor (cod_usuario,ventana,accion,cod_mat,hora,fecha,cod_alumno,carrera,nota_pre,nota_pos)
		VALUES ('$cod_usuario','$ventana','$accion','$cod_mat','$hora','$fecha','$cod_alumno','$carrera','$nota_pre','$nota_pos')";
		if ($conn->query($sql) === TRUE) {

		} else {
			echo $conn->error;
		}

		$conn->close();

		break;

		case '0B':	
		$hora=strftime("%I:%M:%S %p\n"); 
		$fecha = strftime("%x \n"); 
		$accion = "SALIR";
		$ventana="MENU";
		$cod_usuario = $_SESSION['username'];  
		$hora=strftime("%I:%M:%S %p\n"); 
		$fecha = strftime("%x \n");		
		include('db.php');
		$sql = "INSERT INTO auditor (cod_usuario,ventana,accion,cod_mat,hora,fecha,cod_alumno,carrera,nota_pre,nota_pos)
		VALUES ('$cod_usuario','$ventana','$accion','$cod_mat','$hora','$fecha','$cod_alumno','$carrera','$nota_pre','$nota_pos')";
		if ($conn->query($sql) === TRUE) {

		} else {
			echo $conn->error;
		}

		$conn->close();


		break;

		case 'notas_A':
		include('configuracion.php');
		$id=$cod;
		$conn = new mysqli($servidor, $usuario, $clave, $base_datos);
		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		} 
		$sql = "SELECT * FROM notas WHERE id=".$id;
		$result = $conn->query($sql);
		if ($result->num_rows > 0) {
			while($row = $result->fetch_assoc()) {
				$cod_alumno  = $_SESSION['codigo'];  
				$carrera  = $row['CARRERA'];    
			}
		}

		$hora=strftime("%I:%M:%S %p\n"); 
		$fecha = strftime("%x \n"); 
		$accion = "GUARDAR";
		$ventana="NOTAS";
		$cod_usuario = $_SESSION['username'];
		  
		$hora=strftime("%I:%M:%S %p\n"); 
		$fecha = strftime("%x \n");		
		include('db.php');
		$sql = "INSERT INTO auditor (cod_usuario,ventana,accion,cod_mat,hora,fecha,cod_alumno,carrera,nota_pre,nota_pos)
		VALUES ('$cod_usuario','$ventana','$accion','$cod_mat','$hora','$fecha','$cod_alumno','$carrera','$nota_pre','$nota_pos')";
		if ($conn->query($sql) === TRUE) {

		} else {
			echo $conn->error;
		}

		$conn->close();
		break;

		case 'notas_B':
		include('configuracion.php');
		$id=$cod;
		$conn = new mysqli($servidor, $usuario, $clave, $base_datos);
		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		} 
		$sql = "SELECT * FROM notas WHERE id=".$id;
		$result = $conn->query($sql);
		if ($result->num_rows > 0) {
			while($row = $result->fetch_assoc()) {
				$cod_alumno  = $_SESSION['codigo'];  
				$carrera  = $row['CARRERA'];    
			}
		}

		$hora=strftime("%I:%M:%S %p\n"); 
		$fecha = strftime("%x \n"); 
		$accion = "MODIFICAR";
		$ventana="NOTAS";
		$cod_usuario = $_SESSION['username'];  
		$hora=strftime("%I:%M:%S %p\n"); 
		$fecha = strftime("%x \n");		
		include('db.php');
		$sql = "INSERT INTO auditor (cod_usuario,ventana,accion,cod_mat,hora,fecha,cod_alumno,carrera,nota_pre,nota_pos)
		VALUES ('$cod_usuario','$ventana','$accion','$cod_mat','$hora','$fecha','$cod_alumno','$carrera','$nota_pre','$nota_pos')";
		if ($conn->query($sql) === TRUE) {

		} else {
			echo $conn->error;
		}

		$conn->close();
		break;

		case 'notas_C':
		include('configuracion.php');
		$id=$cod;
		$conn = new mysqli($servidor, $usuario, $clave, $base_datos);
		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		} 
		$sql = "SELECT * FROM notas WHERE id=".$id;
		$result = $conn->query($sql);
		if ($result->num_rows > 0) {
			while($row = $result->fetch_assoc()) {
				$cod_alumno  = $_SESSION['codigo'];  
				$carrera  = $row['CARRERA'];    
			}
		}

		$hora=strftime("%I:%M:%S %p\n"); 
		$fecha = strftime("%x \n"); 
		$accion = "BORRAR";
		$ventana="NOTAS";
		$cod_usuario = $_SESSION['username'];  
		$hora=strftime("%I:%M:%S %p\n"); 
		$fecha = strftime("%x \n");		
		include('db.php');
		$sql = "INSERT INTO auditor (cod_usuario,ventana,accion,cod_mat,hora,fecha,cod_alumno,carrera,nota_pre,nota_pos)
		VALUES ('$cod_usuario','$ventana','$accion','$cod_mat','$hora','$fecha','$cod_alumno','$carrera','$nota_pre','$nota_pos')";
		if ($conn->query($sql) === TRUE) {

		} else {
			echo $conn->error;
		}

		$conn->close();


		break;

		case 'alumno_A':	
		include('configuracion.php');
		$id=$cod;
		$conn = new mysqli($servidor, $usuario, $clave, $base_datos);
		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		} 
		$sql = "SELECT * FROM alumno WHERE CODIGO=".$id;
		$result = $conn->query($sql);
		if ($result->num_rows > 0) {
			while($row = $result->fetch_assoc()) {
				$cod_alumno  = $row['CODIGO'];  
				$carrera  = $row['CARRERA'];    
			}
		}

		$hora=strftime("%I:%M:%S %p\n"); 
		$fecha = strftime("%x \n"); 
		$accion = "GUARDAR";
		$ventana="ALUMNO";

		$cod_usuario = $_SESSION['username'];  
		$hora=strftime("%I:%M:%S %p\n"); 
		$fecha = strftime("%x \n");		
		include('db.php');
		$sql = "INSERT INTO auditor (cod_usuario,ventana,accion,cod_mat,hora,fecha,cod_alumno,carrera,nota_pre,nota_pos)
		VALUES ('$cod_usuario','$ventana','$accion','$cod_mat','$hora','$fecha','$cod_alumno','$carrera','$nota_pre','$nota_pos')";
		if ($conn->query($sql) === TRUE) {

		} else {
			echo $conn->error;
		}

		$conn->close();
		break;

		case 'alumno_B':		
		include('configuracion.php');
		$id=$cod;
		$conn = new mysqli($servidor, $usuario, $clave, $base_datos);
		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		} 
		$sql = "SELECT * FROM alumno WHERE CODIGO=".$id;
		$result = $conn->query($sql);
		if ($result->num_rows > 0) {
			while($row = $result->fetch_assoc()) {
				$cod_alumno  = $row['CODIGO'];  
				$carrera  = $row['CARRERA'];    
			}
		}

		$hora=strftime("%I:%M:%S %p\n"); 
		$fecha = strftime("%x \n"); 
		$accion = "MODIFICAR";
		$ventana="ALUMNO";

		$cod_usuario = $_SESSION['username'];  
		$hora=strftime("%I:%M:%S %p\n"); 
		$fecha = strftime("%x \n");		
		include('db.php');
		$sql = "INSERT INTO auditor (cod_usuario,ventana,accion,cod_mat,hora,fecha,cod_alumno,carrera,nota_pre,nota_pos)
		VALUES ('$cod_usuario','$ventana','$accion','$cod_mat','$hora','$fecha','$cod_alumno','$carrera','$nota_pre','$nota_pos')";
		if ($conn->query($sql) === TRUE) {

		} else {
			echo $conn->error;
		}

		$conn->close();
		break;

		case 'alumno_C':		
		include('configuracion.php');
		$id=$cod;
		$conn = new mysqli($servidor, $usuario, $clave, $base_datos);
		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		} 
		$sql = "SELECT * FROM alumno WHERE id=".$id;
		$result = $conn->query($sql);
		if ($result->num_rows > 0) {
			while($row = $result->fetch_assoc()) {
				$cod_alumno  = $row['CODIGO'];  
				$carrera  = $row['CARRERA'];    
			}
		}

		$hora=strftime("%I:%M:%S %p\n"); 
		$fecha = strftime("%x \n"); 
		$accion = "BORRAR";
		$ventana="ALUMNO";
		$cod_usuario = $_SESSION['username'];  
		$hora=strftime("%I:%M:%S %p\n"); 
		$fecha = strftime("%x \n");		
		include('db.php');
		$sql = "INSERT INTO auditor (cod_usuario,ventana,accion,cod_mat,hora,fecha,cod_alumno,carrera,nota_pre,nota_pos)
		VALUES ('$cod_usuario','$ventana','$accion','$cod_mat','$hora','$fecha','$cod_alumno','$carrera','$nota_pre','$nota_pos')";
		if ($conn->query($sql) === TRUE) {

		} else {
			echo $conn->error;
		}

		$conn->close();
		break;
	}






}
?>