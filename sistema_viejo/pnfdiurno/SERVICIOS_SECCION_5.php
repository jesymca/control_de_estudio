
<?php
include 'menu.php';
$pensum=$_POST['pensum'];
$cod_doc=$_POST['cod_doc'];
$cod_mat=$_POST['cod_mat'];
$electiva=$_POST['electiva'];
$lapso=$_POST['lapso'];
?>
<html>
<head>
	
</head>
<body>
	<div class="container" id="marco" style="width:45%">
		<form class="form-horizontal" id="effect2" method="post" action="Formulario_cargar_materias_form.php">
			<fieldset>
				<div class="form-group" id="titulo_formulario"> 
					<label id="titulo_formulario"><span class="glyphicon glyphicon-tasks"></span> Seleccione la Seccion <span class="badge" style="font-weight:bold">05</span></label>
				</div>

				<center><table >
					<tr>
						<td>
							
							<div class="form-group">
								<div class="col-md-12" style="width: 200px;margin-left: 0px;margin-top:-20px">
									<label for="seccion">Seccion</label>
									<input type="hidden" id="pensum" name="pensum" value="<?php echo $pensum;?>"> 
					                <input type="hidden" id="cod_doc" name="cod_doc" value="<?php echo $cod_doc;?>">
					                <input type="hidden" id="cod_mat" name="cod_mat" value="<?php echo $cod_mat;?>">	
					                <input type="hidden" id="electiva" name="electiva" value="<?php echo $electiva;?>">					
								    <input type="hidden" id="lapso" name="lapso" value="<?php echo $lapso;?>">			
								    <input type="hidden" id="seccion" name="seccion">		
								</div>

						       <div class="selector-seccion">   
					                <select style="width:150px;height: 38px;margin-top:8px;margin-left: -185px;position: absolute;" required></select>   
				               </div>        
							</div>

						</td>
						<td style="width: 5px;"></td> 
						<td>
							<div class="form-group">
								<div class="col-md-12" style="margin-top: 45px;margin-left: 0px">                                
									<input type="submit" id="actualizar" class="btn btn-primary" name="submit" value="Siguiente" style="width:120px"/> 
								</div>
							</div>
						</td>


						<td></td> 

						<td width="100">
							<div class="form-group">
								<div class="col-md-12" style="margin-left:10px;margin-top:45px">
									<a href="principal.php" class="btn btn-primary" style="width: 100px;"><span class="glyphicon glyphicon-log-out"></span> Salir</a>
								</div>
							</div>
						</td>
					</tr>
				</table>


				<table id="editable_table" class="table table-bordered table-striped"></table></center>

			</fieldset>
		</form>
	</div>





	<script type="text/javascript">
		$(document).ready(function() {
			
		
        

                       buscarseccion($('#pensum').val());

                        function buscarseccion(val1){
                        	var parametros = {
                        	    "pensum":val1,                        		
                        		"opcion":"getnumseccion" 
                        	}
                        	$.ajax({
                        		data:parametros,
                        		type: "POST",
                        		url: "getselect.php",        
                        		success: function(response)
                        		{
                        			$('.selector-seccion select').html(response).fadeIn();
                        		}
                        	});
                        }
                        
                        $(".selector-seccion select").change(function() {       
                        	var v = $(this).val(); 
                        	$('#seccion').val(v);                         	
                        });

			
		});
	</script>

</body>
</html>


