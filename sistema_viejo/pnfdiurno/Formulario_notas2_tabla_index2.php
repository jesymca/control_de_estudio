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
	<script src="js/sweetalert-dev.js"></script>
	<style>
		{
			/*background-image: url("fondo.jpg");*/
			background-attachment: fixed;
			background-size:cover;
			background-repeat:no-repeat;
			background-position: center center;

		}

		
	</style>
</head>
<body>
	<?php
	include "db.php";
	include 'menu.php';

	if($_POST['cod_doc']<>"" and $_POST['cod_mat']<>"" and $_POST['seccion']<>"" and $_POST['lapso']<>""){


		$_SESSION['cod_doc_sec'] = $_POST['cod_doc'];		     
		$_SESSION['cod_mat_sec'] = $_POST['cod_mat']; 
		$_SESSION['seccion_sec'] = $_POST['seccion']; 
		$_SESSION['lapso_sec'] = $_POST['lapso']; 


		$cod_doc=$_SESSION['cod_doc_sec'];		
		$cod_mat=$_SESSION["cod_mat_sec"];
		$seccion=$_SESSION["seccion_sec"];
		$lapso=$_SESSION["lapso_sec"];

		if(substr($cod_mat, 1, 1)=="P" OR substr($cod_mat, 1, 1)=="T" OR substr($cod_mat, 1, 1)=="E"){
			$tiplap=substr($cod_mat, 1, 1);
		}else{
			$tiplap="";
		}
	}

	$cod_doc=$_SESSION['cod_doc_sec'];
	$cod_mat=$_SESSION["cod_mat_sec"];
	$seccion=$_SESSION["seccion_sec"];
	$lapso=$_SESSION["lapso_sec"];


	if(substr($cod_mat, 1, 1)=="P" OR substr($cod_mat, 1, 1)=="T" OR substr($cod_mat, 1, 1)=="E"){
		$tiplap=substr($cod_mat, 1, 1);
	}else{
		$tiplap="";
	}

	$pensum=substr($cod_mat, 0, 1)."X".substr($cod_mat, 4, 1);

	?>

	<style type="text/css">
		#marco
		{
			width:100%;
			/*min-width: 950px;*/
		}
	</style>

	<center><table>
		<tr>
			<td>
				<div class="container" id="marco">
					<form class="form-horizontal" id="effect2" name="a1" method="post" action="ACTA DE CALIFICACION FINAL.php">
						<fieldset>
							<div class="form-group" id="titulo_formulario">
								<label id="titulo_formulario">Cargar notas por seccion</label>
							</div>
							<br />
							<br />
							<div class="form-group" style="padding-left: 10px">
								<div class="col-md-12">

									<input type="hidden" id="cod_doc" name="cod_doc" value="<?php echo $cod_doc;?>">
									<input type="hidden" id="cod_mat" name="cod_mat" value="<?php echo $cod_mat;?>">
									<input type="hidden" id="seccion" name="seccion" value="<?php echo $seccion;?>">
									<input type="hidden" id="lapso" name="lapso" value="<?php echo $lapso;?>">
									<input type="hidden" id="tiplap" name="tiplap" value="<?php echo $tiplap;?>">
									<input type="hidden" id="pensum" name="pensum" value="<?php echo $pensum;?>">
									<input type="submit" class="btn btn-primary" name="submit" value="Acta de calificacion" style="width:180px;height: 34px;"/>  
									<a href="Formulario_notas2_tabla_index.php" class="btn btn-primary" style="width:120px;height: 34px;"><span class="glyphicon glyphicon-log-out"></span> Salir</a>
								</div>
							</div>
							<br>


							<!--inicio de la Tabla-->
							<table class="responstable">
								<thead>
									<tr>
										<th width="1%">Pensum</th>
										<th width="1%">Cod_mat</th>
										<th width="20%">Asignatura</th>
										<th width="1%">Seccion</th>
										<th width="6%">Lapso</th>
										<th width="1%">Tipo</th>
										<th width="1%">Cod_doc</th>
										<th width="7%">Cedula</th>
										<th width="20%">Nombre</th>


									</tr>
								</thead>
								<tbody>
									<?php

									$pensum=substr($cod_mat, 0, 1)."X".substr($cod_mat, 4, 1);

									include 'db.php';

									$sql = "SELECT * FROM lismat where cod_mat='".$cod_mat."'";
									$result = $conn->query($sql);

									if ($result->num_rows > 0) {
										while($fila = $result->fetch_assoc()) {							
											$descrip2=$fila["descrip2"];
										}
									}


									$sql = "SELECT * FROM docente where cod_doc='".$cod_doc."'";
									$result = $conn->query($sql);

									if ($result->num_rows > 0) {
										while($fila = $result->fetch_assoc()) { 

											echo '<tr>
											<td>'.$pensum.'</td>
											<td>'.$cod_mat.'</td>
											<td>'.$descrip2.'</td>							
											<td>'.$seccion.'</td>
											<td>'.$lapso.'</td>
											<td>'.$tiplap.'</td>						
											<td>'.$fila["cod_doc"].'</td>
											<td>'.$fila["cedula"].'</td>
											<td>'.$fila["nombre"].'</td>                                                      
										</tr>';
									}
								}

								?>
							</tbody>
						</table>
						<!--Fin de la Tabla-->
						<br />

						<!--inicio de la Tabla-->
						<div style="padding-left: 10px;padding-right:10px;">
							<table id="editable_table" class="table table-bordered table-striped">
								<thead>
									<tr>
										<th width="1%">#</th>
										<th width="1%">Materia</th>
										<th width="1%">Cedula</th>
										<th width="40%">Nombre</th>      
										<th width="10%">Lapso</th>
										<th width="1%">Tipo</th>
										<th width="1%">Docente</th>
										<th width="1%">Nota</th>
										<th width="1%">Acu</th>
										<th width="1%">Eliminar</th>
									</tr>
								</thead>
								<tbody>
									<?php

									$x=1;  
									$query = "SELECT alumno.cedula,alumno.nombre,notas.id,notas.nota,notas.acu,notas.lapso,notas.tiplap,notas.cod_doc,notas.cod_mat,notas.seccion FROM notas,alumno where notas.codigo=alumno.cedula and notas.seccion='".$seccion."' and notas.cod_doc='".$cod_doc."' and notas.cod_mat='".$cod_mat."' and notas.lapso='".$lapso."' ORDER BY alumno.nombre ASC";
									$result = mysqli_query($conn, $query);
									while($row = mysqli_fetch_array($result))
									{
										?>
										<tr><td contenteditable="false"><?php echo $x; ?></td>
											<td contenteditable="false" onBlur="saveToDatabase(this,'cod_mat','<?php echo $row['id']; ?>')" onClick="editRow(this);"><?php echo $row['cod_mat']; ?></td>	
											<td contenteditable="false" onBlur="saveToDatabase(this,'codigo','<?php echo $i; ?>')" onClick="editRow(this);"><?php echo $row['cedula']; ?></td>						
											<td contenteditable="false" onBlur="saveToDatabase(this,'nombre','<?php echo $i; ?>')" onClick="editRow(this);"><?php echo $row['nombre']; ?></td> 
											<td contenteditable="false" onBlur="saveToDatabase(this,'lapso','<?php echo $row['id']; ?>')" onClick="editRow(this);"><?php echo $row['lapso']; ?></td>
											<td contenteditable="false" onBlur="saveToDatabase(this,'tiplap','<?php echo $row['id']; ?>')" onClick="editRow(this);"><?php echo $row['tiplap']; ?></td>
											<td contenteditable="false" onBlur="saveToDatabase(this,'cod_doc','<?php echo $row['id']; ?>')" onClick="editRow(this);"><?php echo $row['cod_doc']; ?></td>				
											<td contenteditable="true" maxLength="2" onBlur="saveToDatabase(this,'nota','<?php echo $row['id']; ?>')" onClick="editRow(this);"><?php echo $row['nota']; ?></td>						
											<td contenteditable="false" onBlur="saveToDatabase(this,'acu',<?php echo $row['id']; ?>)" onClick="editRow(this);"><?php echo $row['acu']; ?></td>
											<td><a title="Eliminar" class="btn btn-sm btn-danger" onclick="deleteRecord(<?php echo $row['id']; ?>);"><span class="glyphicon glyphicon-trash"></span></a></td></tr>

											<?php
											$x++;
										}
										?>
									</tbody>
								</table>

							</div>
							<!--Fin de la Tabla-->
						</form>

					</fieldset>
				</div>
			</td>
			<td style="width: 10px;"></td>
			<td>
				<div class="form-group">
					<IMG SRC="pnf.jpg" WIDTH="200" HEIGHT="500">
					</div>
				</td>
			</tr>
		</table></center>

	</body>
	</html>

	<script type="text/javascript">

		function deleteRecord(id) {

			swal({   
				title: "Desea borrar la nota?",
				text: "",
				type: "success",
				showCancelButton: true,
				confirmButtonColor: '#FF292B',
				confirmButtonText: 'Si',
				cancelButtonText: 'No',
				closeOnConfirm: false },
				function(isConfirm){
					if (isConfirm){ 

						$.ajax({
							url: "delete.php",
							type: "POST",
							data:'id='+id,
							success: function(data){
 				//swal("Deleted!","", "success");
 				$("#table-row-"+id).remove(); 			     
 				window.location = 'Formulario_notas2_tabla_index2.php';
 			}
 		});	

					} else {

						window.location = 'Formulario_notas2_tabla_index2.php';

					}	
				});
		}


		function editRow(editableObj) {
     //$(editableObj).css("background","#FFF");

     // $.post("Formulario notas_insert_auditoria.php",{accion: "Guardar",codigo:codigo,cod_mat:cod_mat,lapso:lapso,tiplap:tiplap,cod_doc:cod_doc,seccion:seccion},function(res){               
     // }); 
 }

 function saveToDatabase(editableObj,column,id) {
 	
 	if($(editableObj).text()<21 || $(editableObj).text()=="IN"){
 		$(editableObj).css("background","#FFF url(loaderIcon.gif) no-repeat right");
 		$.ajax({
 			url: "edit.php",
 			type: "POST",
 			data:'column='+column+'&editval='+$(editableObj).text()+'&id='+id,
 			success: function(res){

 				$(editableObj).css("background","#FFF");

 				// swal({
 				// 	title: "Nota Guardada",
 				// 	text: "",
 				// 	timer: 1000,
 				// 	showConfirmButton: true,				
 				// 	imageUrl: "css/thumbs-up.jpg"
 				// });
 			}
 		}); 	


 	}else{

 		swal({
 			title: "Nota invalida",
 			text: "La escala es del 1 al 20",
 			timer: 1000, 			
 			showConfirmButton: true,
 			confirmButtonColor: "#CB171E"
 		});		
 	}
 }


 


</script>