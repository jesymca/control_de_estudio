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
  <style>
      #marco
    {      
      max-width: 400px;
      min-width: 315px;
    }

  </style>
<div class="well bs-component" class="container" id="marco">
	<form  id="effect2" method="post" action="ACTA DE CALIFICACION FINAL.php">
		<fieldset>
			<div class="form-group" id="titulo_formulario">
				<label id="titulo_formulario"><span class="glyphicon glyphicon-book"></span> Acta de calificacion</label>
			</div>           

			<p> 

				
				<div class="col-md-12">
						<label for="pensum">Pensum</label>
						<input type="hidden" id="pensum" name="pensum">                     
					<div class="selector-pensum">   
						<select style="width:100%;height: 38px;" required></select>      
					</div>
				</div>


				
					<div class="col-md-12">
						<label for="cod_doc">Docente</label>
						<input type="hidden" id="cod_doc" name="cod_doc">                   
					

					<div class="selector-cod_doc">   
						<select style="width:100%;height: 38px;" required></select>      
					</div>
				</div>
               

			
					<div class="col-md-12">
						<label for="cod_mat">Materia</label>  
						<input type="hidden" id="cod_mat" name="cod_mat">                   
					

					<div class="selector-cod_mat">   
						<select style="width:100%;height: 38px;" required></select>      
					</div>
				</div>
              

			
					<div class="col-md-12">
						<label for="lapso">Lapso</label>  
						<input type="hidden" id="lapso" name="lapso">                   
					

					<div class="selector-lapso">   
						<select style="width:100%;height: 38px;" required></select>      
					</div>
				</div>
              

			
					<div class="col-md-12">
						<label for="seccion">Seccion</label>
						<input type="hidden" id="seccion" name="seccion">                   
		            <div class="selector-seccion">   
						<select style="width:100%;height: 38px;" required></select>          
					</div>
				</div> 
				

                
                <div class="col-md-12" style="margin-top:20px">
                <p>
                    <input type="submit" class="btn btn-primary" name="submit" value="Imprimir" style="width:120px"/> 
                    <a href="principal.php" class="btn btn-primary" style="width:120px;"><span class="glyphicon glyphicon-log-out"></span> Salir</a>
                </p>
                </div>
                 
			</p>  
		     
	

	</fieldset>
	</form>

	

<div class="form-group" style="padding-left:20px;padding-right:20px;">

	<form class="form-horizontal" method="post" action="ACTA DE CALIFICACION FINAL ESCEL.php">
		<input type="hidden" id="cod_doc1" name="cod_doc1">
		<input type="hidden" id="cod_mat1" name="cod_mat1">
		<input type="hidden" id="lapso1" name="lapso1">
		<input type="hidden" id="seccion1" name="seccion1">
		


		<p>					
			<div class="form-group" style="margin-top:20px">
				<div class="col-md-12" style="width:98%;margin-top: -20px">
					<label for="url">Nombre de Archivo</label>  
					<input id="url" name="url" type="text" class="form-control" required value="Acta de calificacion" required>
				</div>							
			</div>	

			
			<div class="form-group">
				<div class="col-md-12">

					<input type="submit" class="btn btn-primary" name="submit" value="Exportar a Excel" style="width:180px"/> 

                </div>
            </div> 


				</p>
               </form>
			</div>
		</div>

</body>
</html>

<script>
	$(document).ready(function(){        

		$('#pensum').val('');
        $('#cod_doc').val('');
        $('#cod_mat').val(''); 
        $('#lapso').val(''); 
        $('#seccion').val('');
       
        buscarpensum();
        function buscarpensum(){
            var parametros = {
            "opcion":"pensum"
        }
        $.ajax({
          data:parametros,
          type: "POST",
          url: "getselect.php",
          success: function(response)
          {
            $('.selector-pensum select').html(response).fadeIn();
          }
        });
        }


        $('.selector-pensum select').change(function(){
            var v = $(this).val(); 
            $('#pensum').val(v);              
            $('#cod_doc').val('');
            $(".selector-cod_doc select").empty();
            $('#cod_mat').val('');
            $(".selector-cod_mat select").empty();
            $('#lapso').val('');
            $(".selector-lapso select").empty();
            $('#seccion').val('');
            $(".selector-seccion select").empty(); 
            buscardocente($(".selector-pensum select").val());           
        }); 

            function buscardocente(val1){
            var parametros = {
                "pensum":val1,            
                "opcion":"getdocente",
                "campos":"pd"
            }
            $.ajax({
                data:parametros,
                type: "POST",
                url: "getselect.php",        
                success: function(response)
                {
                    $(".selector-cod_doc select").html(response).fadeIn();
                }
            });  
            }

        $('.selector-lapso select').change(function(){
            var v = $(this).val();     
            $('#lapso').val(v); 
            $('#lapso1').val(v); 
            buscarseccion($(".selector-cod_doc select").val(),$(".selector-cod_mat select").val(),$(".selector-lapso select").val());
        }); 

        $(".selector-cod_doc select").change(function() {
            var v = $(this).val();     
            $('#cod_doc').val(v);
            $('#cod_doc1').val(v);
            $('#cod_mat').val('');
            $(".selector-cod_mat select").empty();
            $('#lapso').val('');
            $(".selector-lapso select").empty();
            buscarmateria($(".selector-pensum select").val(),$(".selector-cod_doc select").val());
        }); 


        function buscarmateria(val1,val2){
            var parametros = {
                "pensum":val1,
                "cod_doc":val2,
                "opcion":"getmateria2"
            }
            $.ajax({
                data:parametros,
                type: "POST",
                url: "getselect.php",        
                success: function(response)
                {
                    $('.selector-cod_mat select').html(response).fadeIn();
                }
            });
        }


        $(".selector-cod_mat select").change(function() {  
            var v = $(this).val();     
            $('#cod_mat').val(v); 
            $('#cod_mat1').val(v);    
            buscarlapso($(".selector-cod_doc select").val(),$(".selector-cod_mat select").val())

            $('#lapso').val('');
            $(".selector-lapso select").empty();
        });   

        function buscarlapso(val1,val2){
            var parametros = {
                "cod_doc":val1,
                "cod_mat":val2,
                "opcion":"getlapso", 
                "campos":"dm"      
            }
            $.ajax({
                data:parametros,
                type: "POST",
                url: "getselect.php",        
                success: function(response)
                {             
                    $('.selector-lapso select').html(response).fadeIn();
                }
            });
        }

        $(".selector-lapso select").change(function() {       
            buscartipolapso($(".selector-cod_doc select").val(),$(".selector-cod_mat select").val(),$(".selector-lapso select").val());
            $('#seccion').val('');      
            $(".selector-seccion select").empty();
            buscarseccion($(".selector-cod_doc select").val(),$(".selector-cod_mat select").val(),$(".selector-lapso select").val());
        });

        $('.selector-seccion select').change(function(){
            var v = $(this).val();     
            $('#seccion').val(v);         
            $('#seccion1').val(v); 
        }); 

        function buscarseccion(val1,val2,val3){
            var parametros = {
                "cod_doc":val1,
                "cod_mat":val2,
                "lapso":val3,
                "campos":"dml",
                "opcion":"getnumseccion3" 
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

	});
</script>
