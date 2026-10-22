
<?php
session_start();
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true && $_SESSION['actas']==1) {
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
	
	.col-md-12 {
		padding-left:30px; 
		padding-right:30px;
		min-width: 205px;
	}
	.selector-docente,.selector-cod_mat,.selector-lapso,.selector-tipolapso{
		padding-left:30px; 
		padding-right:30px;
		min-width: 350px;
	}
	.panel {
		overflow-y: hidden;
		overflow-x: hidden;
		-ms-overflow-style: hidden;
	}

</style>

<link rel="stylesheet" type="text/css" href="css/themes/default/easyui.css">
<link rel="stylesheet" type="text/css" href="css/themes/demo.css">
<link rel="stylesheet" type="text/css" href="css/themes/icon.css">
<script type="text/javascript" src="js/jquery.min.js"></script>
<script type="text/javascript" src="js/jquery.easyui.min.js"></script>



</head>
<body>
<div class="form-group" id="marco" style="width:50%">
	<form class="form-horizontal" id="effect2" method="post" action="LISTADO DE ALUMNOS SECCION PDF.php">
			<div id="titulo_formulario"> 
				<label id="titulo_formulario"><span class="fa-table fa"></span> Listado de Alumnos por Seccion</label>
			</div>
			<div class="form-group">
				<div class="col-md-12" style="margin-left: 10px;margin-top:20px;">
					
					<input type="submit" class="btn btn-primary" name="submit" value="Imprimir" style="width:120px"/> 
					<a href="principal.php" class="btn btn-primary" style="width:120px"><span class="glyphicon glyphicon-log-out"></span> Salir</a><a id="cantidad_label" style="margin-left: 20px;color: black;font-size: 15px;"></a>
				    
				</div>
							
			</div>			

			<div class="form-group">
				<div class="col-md-12" style="width: 100%;margin-left: 10px;margin-top:0px">
					<label for="pensum">Pensum</label>    
					<input type="hidden" id="cantidad" name="cantidad">
					<input type="hidden" id="pensum" name="pensum">
					<div class="selector-pensum">   
						<select style="width:85%;height: 38px;margin-top:0px;margin-left: 0px;position: absolute;" required></select>      
					</div> 
				</div>		
			</div>
            <br>
		
			<div class="form-group" style="margin-top:0px">
				<div class="col-md-12" style="width: 100%;margin-left: 10px;margin-top:0px">
					<label for="seccion">Seccion</label>      
					<input type="hidden" id="seccion" name="seccion"> 
					<div class="selector-seccion">   
						<select style="width:85%;height: 38px;margin-top:0px;margin-left: 0px;position: absolute;" required></select>    
					</div> 
				</div>
			</div>

			<br>

			<div class="form-group">
				<div class="col-md-12" style="width: 100%;margin-left: 10px;margin-top:0px">
					<label for="lapso">Lapso</label>     
					<input type="hidden" id="lapso" name="lapso"> 
					<div class="selector-lapso">   
						<select style="width:85%;height: 38px;margin-top:0px;margin-left: -30px;position: absolute;" required></select>   
					</div> 
				</div>
			</div>
           <br>	
		
			<br>		
			
</form>


</div>			

<script>
	$(document).ready(function(){     

	$('#carrera').val(''); 
	$('#seccion').val('');
	$('#lapso').val('');

	$.ajax({
		type: "POST",
		url: "getpensum6.php",
		success: function(response)
		{
			$('.selector-pensum select').html(response).fadeIn();
		}
	});

	$('.selector-pensum select').change(function(){
		var v = $(this).val();					
		$('#pensum').val(v);		
		buscarseccion($('#pensum').val());
	}); 

	function buscarseccion(val1){
		var parametros = {				
			"pensum":val1
		}
		$.ajax({
			data:parametros,
			type: "POST",
			url: "getnumseccion_1.php",				
			success: function(response)
			{
				$('.selector-seccion select').html(response).fadeIn();
			}
		});
	}

	$('.selector-seccion select').change(function(){
		var v = $(this).val(); 
		$('#seccion').val(v); 		
		buscarlapso($('#pensum').val(),$('#seccion').val());                    
	}); 

	function buscarlapso(val1,val2){
		var parametros = {				
			"pensum":val1,
			"seccion":val2
		}
		$.ajax({
			data:parametros,
			type: "POST",
			url: "getlapso_6.php",				
			success: function(response)
			{
				$('.selector-lapso select').html(response).fadeIn();
			}
		});
	}

	$('.selector-lapso select').change(function(){
		var v = $(this).val(); 
		$('#lapso').val(v);	
		$('#cantidad').html('');                    
        cantidad($('#pensum').val(),$('#lapso').val(),$('#seccion').val()); 	 
	}); 

	function cantidad(val1,val2,val3){
            var parametros = {
            "pensum":val1,
            "lapso":val2,
            "seccion":val3,            
            "opcion":"getcantidad2"
        }
        $.ajax({
          data:parametros,
          type: "POST",
          url: "getselect.php",
          success: function(response)
          {     
            $('#cantidad').val(response);  
            $('#cantidad_label').html("Cantidad en la Seccion: " + response);            
          }
        });
        }

	});
</script>

</body>
</html>
