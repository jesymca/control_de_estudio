<?php

$id = intval($_GET['id']);
if ($id=="") {
	$id=$_SESSION['id'];
}

include "db.php";
$pensum = $_POST['pensum'];
$fe_gr_alu = $_POST['fe_gr_alu'];
$grado = $_POST['grado'];
$marca = $_POST['marca'];

include 'menu.php';


?>

<div class="container" id="marco" style="width:400px;margin-top:0px">
	<form class="form-horizontal" id="effect2" method="post" action="LISTA DE GRADUADOS.php">
		<fieldset>

			<div class="form-group" id="titulo_formulario">
				<label id="titulo_formulario"><span class="glyphicon glyphicon-education"></span> Grado del alumno</label>
			</div>
            <br>		

            <div class="form-group">
				<div class="col-md-12" style="width:150px;margin-left: 20px;">
					<label for="grado">Grado</label>
					<input id="fe_gr_alu" name="fe_gr_alu" type="hidden" value="<?php echo $fe_gr_alu?>">
					<input id="pensum" name="pensum" type="hidden" value="<?php echo $pensum?>">
					<input id="marca" name="marca" type="hidden" value="<?php echo $marca?>">
					<input id="grado" name="grado" type="hidden">   
				</div>

				<div class="selector-grado">   
					<select style="width:150px;margin-top:26px;height: 38px;margin-left: -135px;" required>
						<option value="">Seleccionar</option>
						<option value="T">Tsu</option>
                        <option value="I">Ingenieria</option>
                        <option value="L">Licenciado</option>
					</select>      
				</div>
			</div>       

			<div class="form-group">
				<div class="col-md-12" style="margin-left: 20px;;margin-top:20px">
					<input type="submit" id="actualizar" class="btn btn-primary" name="submit" value="Imprimir" style="background: #0C4783;width:120px"/> 
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


