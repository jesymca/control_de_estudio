<?php
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

?>
<!DOCTYPE html>
<html lang="es">
<head>
	
	<title>Control de Estudio</title>
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0" />
	<!--<meta name="viewport" content="width=device-width, initial-scale=1">-->
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta charset="utf-8">

	<link rel="stylesheet" href="css/bootstrap.css" media="screen">
	<link rel="stylesheet" href="css/sweetalert.css">
	<link rel="stylesheet" href="css/dataTables.bootstrap.css"/>        
	<!-- <link type="text/css" href="css/theme.css" rel="stylesheet">  -->       
	<link rel="stylesheet" href="css/style.css" media="screen">
	<link type="text/css" rel="stylesheet" href="css/font-awesome.min.css"/>


	<script src="js/jquery-3.1.1.min3.js"></script>
	<script src="js/jquery-1.9.1.min.js"></script>
	<script src="js/jquery-1.10.2.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
	<script src="js/jquery.dataTables.min.js"></script>
	<script src="js/dataTables.bootstrap.min.js"></script>
	<script src="js/jquery.tabledit.js"></script>  
	<script src="js/jquery-ui-1.10.1.custom.min.js" type="text/javascript"></script>   
	<script src="js/jquery.dataTables.js"></script>
	<script src="js/dataTables.bootstrap.js"></script>
	<script src="js/sweetalert-dev.js"></script> 

	
</head>
<body>

	<?php include 'nav1.php';?>
	<br>
	<br>
	<br>
