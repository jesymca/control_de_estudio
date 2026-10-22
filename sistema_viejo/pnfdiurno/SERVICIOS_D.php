<?php

$id = intval($_GET['id']);
if ($id=="") {
	$id=$_SESSION['id'];
 }



include "db.php";

$sql = "SELECT * FROM alumno WHERE id='".$id."'";
$resultado = $conn->query($sql); 
if ($resultado->num_rows > 0) {
while($fila = $resultado->fetch_assoc()) {                 

$cedula=$fila["cedula"];
$CARRERA=$fila['carrera'];
$MENCION=$fila['mencion'];
$PLAN=$fila['plan'];

}
}

$pensum=$CARRERA.$MENCION.$PLAN;

include 'menu.php';

?>

<div class="container" id="marco" style="width:400px;margin-top:0px">
	<form class="form-horizontal" id="effect2" method="post" action="HISTORIAL ACADEMICO_TSU_DESGLOSADO.php">
		<fieldset>

			<div class="form-group" id="titulo_formulario">
				<label id="titulo_formulario"><span class="glyphicon glyphicon-education"></span> Grado del alumno</label>
			</div>
            <br>		

            <div class="form-group">
				<div class="col-md-12" style="width:150px;margin-left: 20px;">
					<label for="grado">Grado</label>
					<input id="pensum" name="pensum" type="hidden" value="<?php echo $pensum?>">   
					<input id="cedula" name="cedula" type="hidden" class="form-control" value="<?php echo $cedula?>">
					<input id="grado" name="grado" type="hidden" class="form-control" required>
				</div>

				<div class="selector-grado">   
					<select style="width:150px;margin-top:26px;height: 38px;margin-left: -135px;">
						<option value="">Seleccionar</option>
						<option value="T">Tsu</option>
                        <option value="I">Ingenieria</option>
                        <option value="L">Licenciado</option>
					</select>      
				</div>
			</div>       

			<div class="form-group">
				<div class="col-md-12" style="margin-left: 20px;;margin-top:20px">
					<input type="submit" id="actualizar" class="btn btn-primary" name="submit" value="Buscar" style="background: #0C4783;width:120px"/> 
					<a href="principal.php" class="btn btn-primary" style="width: 100px;background: #0C4783;"><span class="glyphicon glyphicon-log-out"></span> Salir</a>
				</div>
			</div>
		</fieldset>
	</form>
</div>

<script>
	$(document).ready(function(){    
    $('#grado').val('');
		$('.selector-grado select').click(function(){
			var v = $(this).val(); 
			$('#grado').val(v);       
		});       
	});
</script>
</body>
</html>


