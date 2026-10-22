
<?php
session_start();
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true && $_SESSION['auditoria']==1) {
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
include 'menu.php';
?>
<html>
<head>

	<style>
	
		input[type = "text"]
		{
			background:#658DB3;  
			font-weight:bold; 
			color:#000000; 
		}

		#marco
		{
			max-width:540px; 
			min-width: 180px;
		}

	</style>
</head>
<body>
	<div class="container" id="marco">
		<form class="form-horizontal" id="effect2" method="post">
			<fieldset>
				<div class="form-group" id="titulo_formulario"> 
					<label id="titulo_formulario"><span class="glyphicon glyphicon-signal"></span> Auditoria</label>
				</div>

				<br>


				<center><div class="row">
					<div class="col-md-12">
						
						<p class="bs-component">							
							<a href="Formulario_alumno_auditoria_lista.php" class="btn btn-primary"><span class="glyphicon glyphicon-th-list"></span> Alumno</a>
							<a href="Formulario_docente_auditoria_lista.php" class="btn btn-primary"><span class="glyphicon glyphicon-th-list"></span> Docente</a>
							<a href="Formulario_notas_auditoria_lista.php" class="btn btn-primary"><span class="glyphicon glyphicon-th-list"></span> Notas</a>
							<a href="Formulario_lismat_auditoria_lista.php" class="btn btn-primary"><span class="glyphicon glyphicon-th-list"></span> Pensum</a>
						</p>
						
						<p class="bs-component">
							<a href="Formulario_lapso_auditoria_lista.php" class="btn btn-primary"><span class="glyphicon glyphicon-th-list"></span> Lapso</a>
							<a href="Formulario_tipos_lapso_auditoria_lista.php" class="btn btn-primary"><span class="glyphicon glyphicon-th-list"></span> Tipos Lapso</a>
							<a href="Formulario_nota_auditoria_lista.php" class="btn btn-primary"><span class="glyphicon glyphicon-th-list"></span> Num de Nota</a>
							<a href="Formulario_seccion_auditoria_lista.php" class="btn btn-primary"><span class="glyphicon glyphicon-th-list"></span> Num de Sec</a>
						</p>
						
						<p class="bs-component">
							<a href="Formulario_user_auditoria_lista.php" class="btn btn-primary"><span class="glyphicon glyphicon-th-list"></span> Usuarios</a>
							<a href="Formulario_agregarseccion_auditoria_lista.php" class="btn btn-primary"><span class="glyphicon glyphicon-th-list"></span> Secciones</a>
							<a href="principal.php" class="btn btn-primary"><span class="glyphicon glyphicon-log-out"></span> Salir</a>
						</p>

					</div>
				</div>
			</form>
		</div>
	</fieldset>
</form>
</body>
</html>
