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

// echo "cedula: ".$cedula."<br>";
// echo "pensum: ".$pensum."<br>";

?>

<div class="container" id="marco" style="width:400px;margin-top:0px">
	<form class="form-horizontal" id="effect2" method="post" action="HISTORIAL ACADEMICO_TSU2.php">
		<fieldset>

			<div class="form-group" id="titulo_formulario">
				<label id="titulo_formulario"><span class="glyphicon glyphicon-education"></span> Grado y Carrera</label>
			</div>
			<br>		
<p>
			<div class="form-group">
				<div class="col-md-12" style="margin-left: 20px;margin-top:0px">
					<label for="grado">Grado</label>
					   
					<input id="cedula" name="cedula" type="hidden" class="form-control" value="<?php echo $cedula?>">
					<input id="grado" name="grado" type="hidden" class="form-control" required>
				</div>
				<div class="selector-grado">   
					<select style="width:200px;height: 38px;margin-left: 35px;"></select>      
				</div>
			</div> 

			<div class="form-group">
			   <div class="col-md-12" style="margin-left: 20px;margin-top:0px">
			    <label for="carrera">Carrera</label> 
			    <input id="pensum" name="pensum" type="hidden" value="<?php echo $pensum?>">    
			   
			    <div class="selector-carrera">   
			     <select style="width:200px;height: 38px;"></select>   
			   </div> 
			 </div>
			</div>    
</p>
			<div class="form-group">
				<div class="col-md-12" style="margin-left: 20px;;margin-top:20px">
					<input type="submit" id="submit" class="btn btn-primary" name="submit" id="submit" value="Buscar" style="background: #0C4783;width:120px"/> 
					<a href="principal.php" class="btn btn-primary" style="width: 100px;background: #0C4783;"><span class="glyphicon glyphicon-log-out"></span> Salir</a>
				</div>
			</div>
		</fieldset>
	</form>
</div>
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
			"pensum":$('#pensum').val(),
			"opcion":"getgrado"                   		                 
		}
		$.ajax({
			data:parametros,
			type: "POST",
			url: "getselect.php",
			success: function(response)
			{
				$('.selector-grado select').html(response).fadeIn();
			}
		}); 
        
        buscarcarrera($('#cedula').val());

		function buscarcarrera(val1){
		  var parametros = {
		    "cedula":val1        
		}
		$.ajax({
		    data:parametros,
		    type: "POST",
		    url: "getcarreras2.php",      
		    success: function(response)
		    {
		      $('.selector-carrera select').html(response).fadeIn();
		    }
		});
		}

		$('.selector-carrera select').change(function(){          
          var v = $(this).val();        
          $('#pensum').val(v+"XC");       
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


