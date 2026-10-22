<?php

include 'menu.php';

isset($_POST["cod_doc"] and $_POST["cod_doc"] and $_POST["cod_mat"] and $_POST["seccion"] and $_POST["lapso"] and $_POST["tiplap"]){


$cod_doc=$_POST["cod_doc"];
$cod_mat=$_POST["cod_mat"];
$seccion=$_POST["seccion"];
$lapso=$_POST["lapso"];
$tiplap=$_POST["tiplap"];

$pensum=substr($cod_mat, 0, 1)."X".substr($cod_mat, 4, 1);
}
?>
<style type="text/css">
	input {
		height: 10px;
		line-height: normal;
	}

</style>

<div class="container" id="marco">
	<form class="form-horizontal" id="effect2" method="post">
		<fieldset>
			<div class="form-group" id="titulo_formulario">
				<label id="titulo_formulario">Cargar notas por seccion</label>
			</div>
			



			<!--inicio de la Tabla-->
			<table class="responstable">
				<thead>
					<tr>
						<th width="1%">Pensum</th>
						<th width="1%">Materia</th>
						<th width="1%">Seccion</th>
						<th width="5%">Lapso</th>
						<th width="1%">Tipo</th>
						<th width="1%">Codigo</th>
						<th width="5%">Cedula</th>
						<th width="30%">Nombre</th>


					</tr>
				</thead>
				<tbody>
					<?php


					include 'db.php';
					
					$sql = "SELECT * FROM docente where cod_doc='".$cod_doc."'";
					$result = $conn->query($sql);
					
					if ($result->num_rows > 0) {
						while($fila = $result->fetch_assoc()) { 

							echo '<tr>
							<td>'.$pensum.'</td>
							<td>'.$cod_mat.'</td>
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


		<table class="table table-bordered table-striped">
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
					
				</tr>
			</thead>
			<tbody id="table-body">
				<?php



				$sql = "SELECT alumno.cedula,alumno.nombre,notas.id,notas.nota,notas.acu,notas.lapso,notas.tiplap,notas.cod_doc,notas.cod_mat,notas.seccion FROM notas,alumno where notas.codigo=alumno.cedula and notas.seccion='".$seccion."' and notas.cod_doc='".$cod_doc."' and notas.cod_mat='".$cod_mat."' and notas.lapso='".$lapso."'";
				$result = $conn->query($sql);
				$x=1;
				if ($result->num_rows > 0) {
					while($row = $result->fetch_assoc()) {

						?>
						<tr><td contenteditable="false"><?php echo $x; ?></td>
							<td contenteditable="false" onBlur="saveToDatabase(this,'cod_mat','<?php echo $row['id']; ?>')" onClick="editRow(this);"><?php echo $row['cod_mat']; ?></td>	
							<td contenteditable="false" onBlur="saveToDatabase(this,'codigo','<?php echo $i; ?>')" onClick="editRow(this);"><?php echo $row['cedula']; ?></td>						
							<td contenteditable="false" onBlur="saveToDatabase(this,'nombre','<?php echo $i; ?>')" onClick="editRow(this);"><?php echo $row['nombre']; ?></td> 
							<td contenteditable="false" onBlur="saveToDatabase(this,'lapso','<?php echo $row['id']; ?>')" onClick="editRow(this);"><?php echo $row['lapso']; ?></td>
							<td contenteditable="false" onBlur="saveToDatabase(this,'tiplap','<?php echo $row['id']; ?>')" onClick="editRow(this);"><?php echo $row['tiplap']; ?></td>
							<td contenteditable="false" onBlur="saveToDatabase(this,'cod_doc','<?php echo $row['id']; ?>')" onClick="editRow(this);"><?php echo $row['cod_doc']; ?></td>				
							<td contenteditable="true" maxLength="2" onBlur="saveToDatabase(this,'nota','<?php echo $row['id']; ?>')" onClick="editRow(this);"><?php echo $row['nota']; ?></td>						
							<td contenteditable="false" onBlur="saveToDatabase(this,'acu',<?php echo $row['id']; ?>)" onClick="editRow(this);"><?php echo $row['acu']; ?></td></tr>
							<?php
							$x++;

						}
					} 


                 
                    
					?>
					
					
				</tbody>
			</table>

			<div class="form-group" style="padding-left: 10px">
				<div class="col-md-12">  
					
					<a href="Formulario_notas2_tabla_index.php" class="btn btn-primary" style="background: #0C4783;width:120px;height: 34px;"><span class="glyphicon glyphicon-log-out"></span> Salir</a>
				</div>
			</div>

		</div>

</form>
</fieldset>
</div>




<script src="jquery-3.1.1.min3.js" type="text/javascript"></script>
<script>


	function editRow(editableObj) {
     //$(editableObj).css("background","#FFF");

     $.post("Formulario notas_insert_auditoria.php",{accion: "Guardar",codigo:codigo,cod_mat:cod_mat,lapso:lapso,tiplap:tiplap,cod_doc:cod_doc,seccion:seccion},function(res){               
     }); 
 }

 function saveToDatabase(editableObj,column,id) {
 	
 	$(editableObj).css("background","#FFF url(loaderIcon.gif) no-repeat right");
 	$.ajax({
 		url: "edit.php",
 		type: "POST",
 		data:'column='+column+'&editval='+$(editableObj).text()+'&id='+id,
 		success: function(data){
 			$(editableObj).css("background","#FFF");
 		}
 	});
 	

 	$(editableObj).css("background","#FFF url(loaderIcon.gif) no-repeat right");
 	$.ajax({
 		url: "Formulario notas_insert_auditoria2.php",
 		type: "POST",
 		data:'column='+column+'&editval='+$(editableObj).text()+'&id='+id,
 		success: function(data){
 			$(editableObj).css("background","#FFF");
 		}
 	});



 }

 function deleteRecord(id) {
 	if(confirm("Are you sure you want to delete this row?")) {
 		$.ajax({
 			url: "delete.php",
 			type: "POST",
 			data:'id='+id,
 			success: function(data){
 				$("#table-row-"+id).remove();
 			}
 		});
 	}
 }
 


 	// function imprimiracta(val1,val2,val3,val4,val5){
 	// 	var parametros = {
 	// 		"cod_doc":val1,
 	// 		"cod_mat":val2,
 	// 		"lapso":val3,
 	// 		"tiplap":val4,
 	// 		"seccion":val5
 	// 	}
 	// 	$.ajax({
 	// 		data:parametros,
 	// 		type: "POST",
 	// 		url: "ACTA DE CALIFICACION FINAL.php",				
 	// 		success: function(response)
 	// 		{
 				
 	// 		}
 	// 	});
 	// } 



	

</script>