
<?php

include 'menu_web.php';

$cedula = $_POST['cedula'];


include "db.php";

$sql = "SELECT * FROM alumno WHERE cedula='".$cedula."'";
$resultado = $conn->query($sql); 
if ($resultado->num_rows > 0) {
	while($fila = $resultado->fetch_assoc()) { 		
		$CARRERA=$fila['carrera'];
		$MENCION=$fila['mencion'];
		$PLAN=$fila['plan'];
	}
}

$pensum=$CARRERA.$MENCION.$PLAN;
include 'menu2.php';

?>
<style>
	{		
		background:white;
		background-attachment: fixed;
		background-size:cover;
		background-repeat:no-repeat;
		background-position: center center;
	}

  #marco{   
    margin:10px auto;
    background:#FFFFFF;  
    border-radius:20px;      
    background: rgba(237, 237, 237,1);
    color:#000000;
    padding: 0;
    border-top-width: 4px;
    border-right-width: 4px;
    border-bottom-width: 4px;
    border-left-width: 4px;
    -webkit-box-shadow: 5px 5px 15px #575853;
    box-shadow: 5px 5px 15px #575853;
    border: 10px solid rgba(47, 164, 231,1);
    border-radius:20px; 
  }
    #titulo_formulario{
    width: 100% !important;
    background:#2FA4E7;
    text-align: center;
    font-size: 30px;
    border-top-left-radius:0px;
    border-top-right-radius:0px;
    color:#FFFFFF;
    margin:0px auto;    
  }

  #loading_container{
    width:40%;
    margin:50px auto;
    background:#FFFFFF;  
    border-radius:20px;      
    background: rgba(237, 237, 237,1);
    color:#000000;
    padding: 0;
    border-top-width: 4px;
    border-right-width: 4px;
    border-bottom-width: 4px;
    border-left-width: 4px;
    -webkit-box-shadow: 5px 5px 15px #575853;
    box-shadow: 5px 5px 15px #575853;
    border: 10px solid rgba(47, 164, 231,1);
    border-radius:20px; 
  }
 
</style>
<center><div class="container" id="marco" style="width:350px;margin-top:50px">
	<form class="form-horizontal" id="effect2" method="post" action="HISTORIAL ACADEMICO_TSU.php">
		<fieldset>

			<div class="form-group" id="titulo_formulario">
				<label id="titulo_formulario"><span class="glyphicon glyphicon-education"></span> Grado del alumno</label>
			</div>
			<br>		
<table>
<tr>
<td>
			<div class="form-group">
				<div class="col-md-12" style="width:100px;margin-left: 0px;">
					<label for="grado">Grado</label>
					<input id="pensum" name="pensum" type="hidden" value="<?php echo $pensum?>">   
					<input id="cedula" name="cedula" type="hidden" class="form-control" value="<?php echo $cedula?>">
					<input id="grado" name="grado" type="hidden" class="form-control" required>
				<div class="selector-grado">   
					<select style="width:150px;margin-top:0px;height: 38px;margin-left: 0px;"></select>      
				</div>
				</div>			
			</div>       
</td>
</tr>
<tr>
<td>
			<div class="form-group">
				<div class="col-md-12" style="margin-left: 0px;margin-top:0px">
					<input type="submit" id="submit" class="btn btn-primary" name="submit" id="submit" value="Buscar" style="background: #2FA4E7;width:120px"/> 
					<a href="principal.php" class="btn btn-primary" style="width: 100px;background: #2FA4E7;"><span class="glyphicon glyphicon-log-out"></span> Salir</a>
				</div>
			</div>
</td>
</tr>
</table>

		</fieldset>
	</form>
</div></center>
<noscript id="noscript_container">
	<div id="noscript" class="error">
		<p>JavaScript support is needed to use this page.</p>
	</div>
</noscript>

<div class="container" id="loading_container" style="width: 400px">
	<form class="form-horizontal">
		<fieldset>
			<div class="form-group" id="titulo_formulario"> 
				<label id="titulo_formulario"><span class="glyphicon glyphicon-time"></span> Informacion</label>
			</div>
			<div id="loading_container2">
				<center><h4>Cargando por favor espere...</h4></center>
			</div>
		</fieldset>
	</form>
</div>
<script>
	$(document).ready(function(){  

		hide_loading_message();
		$('#submit').click(function(){
			show_loading_message();    
		});

		$('#grado').val('');
		$('.selector-grado select').change(function(){
			var v = $(this).val(); 
			$('#grado').val(v);       
		});

		var parametros = {
			"pensum":$('#pensum').val()                
		}

		$.ajax({
			data:parametros,
			type: "POST",
			url: "getpensum4.php",
			success: function(response)
			{
				$('.selector-grado select').html(response).fadeIn();
			}
		}); 

		// Show loading message
		function show_loading_message(){
			$('#loading_container').show();
			$('#marco').hide();
		}
        // Hide loading message
        function hide_loading_message(){
        	$('#loading_container').hide();
        	$('#marco').show();            
        }

    });
</script>
</body>
</html>


