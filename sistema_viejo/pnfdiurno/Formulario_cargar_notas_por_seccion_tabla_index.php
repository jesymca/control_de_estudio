<?php
include 'db.php';
include 'menu.php';
?>
<div class="container" id="marco">
    <form class="form-horizontal" id="effect2" method="post">
        <fieldset>
            <div class="form-group" id="titulo_formulario">
                <label id="titulo_formulario">Formulario cargar_notas_por_seccion</label>
            </div>
            <br />
            <br />
            <div class="form-group" style="padding-left: 10px">
             
                <div class="col-md-12" >
                        <a id="buscaralumnos" class="btn btn-primary" style="background: #0C4783;width:120px"><span class="glyphicon glyphicon-log-out"></span> Buscar</a>
						<a href="principal.php" class="btn btn-primary" style="background: #0C4783;width:120px"><span class="glyphicon glyphicon-log-out"></span> Salir</a>
					</div>
					<br>
					<br>

<p>					
							<div class="form-group" style="padding-left: 15px;padding-right:40px">
								<div class="col-md-12" style="width:50%;">
									<label for="cod_doc">Docente</label>  
									<input id="cod_doc" name="cod_doc" type="text" class="form-control" required>
								</div>



								<div class="selector-docente" id="sp1">   
									<select style="width:50%;height: 38px;margin-top:27px"></select>      
								</div>
							</div>

							<div class="form-group" style="padding-left: 15px;padding-right:40px">
								<div class="col-md-12" style="width: 50%;margin-left: 0%;margin-top:0px">
									<label for="SECCION">Seccion</label>  
									<input id="SECCION" name="SECCION" type="text" class="form-control" required>
								</div>


								<div class="selector-seccion" id="sp5">   
									<select style="width:50%;;height: 38px;margin-top:27px"></select>      
								</div>
							</div>

							<div class="form-group" style="padding-left: 15px;padding-right:40px">
								<div class="col-md-12" style="width: 50%;margin-left: 0%;margin-top:0px">
									<label for="LAPSO">Lapso</label>  
									<input id="LAPSO" name="LAPSO" type="text" class="form-control" required>
								</div>


								<div class="selector-lapso" id="sp2">   
									<select style="width:50%;;height: 38px;margin-top:28px"></select>      
								</div>
							</div>
						</p>
						<p>
							<div class="form-group" style="padding-left: 15px;padding-right:40px">
								<div class="col-md-12" style="width:50%;;margin-left: 0%;margin-top:0px">
									<label for="tiplap">Tipo de lapso</label>  
									<input id="tiplap" name="tiplap" type="text" class="form-control">
								</div>							

								<div class="selector-tipolapso" id="sp3">   
									<select style="width:50%;;height: 38px;margin-top:27px"></select>      
								</div>

							</div>
						</p>





            </div>

            <!--inicio de la Tabla-->
            <table id="editable_table" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th width="1%">Id</th>
                        <th width="3%">Seccion</th>
                        <th width="5%">Cedula</th>
                        <th width="20%">Nombre</th>
                        <th width="1%">Nota</th>
                        <th width="1%">Acum</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $query = "SELECT * FROM cargar_notas_por_seccion ORDER BY id ASC";
                    $result = mysqli_query($conn, $query);
                    while($row = mysqli_fetch_array($result))
                    {
                        echo '<tr>
                              <td>'.$row["id"].'</td>
                              <td>'.$row["seccion"].'</td>
                              <td>'.$row["cedula"].'</td>
                              <td>'.$row["nombre"].'</td>
                              <td>'.$row["nota"].'</td>
                              <td>'.$row["acum"].'</td>
                              </tr>';
                    }
                    ?>
                </tbody>
            </table>
            <!--Fin de la Tabla-->

        </div>
    </form>
</fieldset>
</div>


</body>
</html>
<script>
    $(document).ready(function(){
        $('#editable_table').Tabledit({
            url:'Formulario_cargar_notas_por_seccion_tabla_edit_delete.php',
            columns:{
                identifier:[0, "id"],
                editable:[[1,'seccion'], [2,'cedula'], [3,'nombre'], [4,'nota'], [5,'acum']]
            },
            restoreButton:false,
            onSuccess:function(data, textStatus, jqXHR)
            {
                if(data.action == 'delete')
                {
                    $('#'+data.id).remove();
                }
            }
        });


                $.ajax({
					type: "POST",
					url: "getdocente.php",
					success: function(response)
					{
						$('.selector-docente select').html(response).fadeIn();
					}
				});

				$('#sp1 select').click(function(){
					var v = $(this).val(); 
					$('#cod_doc').val(v); 			
				}); 

				$('#sp5 select').click(function(){
					var v = $(this).val();     
					$('#SECCION').val(v); 

				}); 


				$('#sp2 select').click(function(){
					var v = $(this).val();     
					$('#LAPSO').val(v); 

				}); 


				$('#sp3 select').click(function(){
					var v = $(this).val();     
					$('#tiplap').val(v); 
				}); 
				

				$(".selector-docente select").change(function() {

					$('#SECCION').val('');
					$(".selector-seccion select").empty();
					$('#LAPSO').val('');
					$(".selector-lapso select").empty();
					$('#tiplap').val('');			
					$(".selector-tipolapso select").empty();
					var form_data = {
						is_ajax: 1,
						docente: +$(".selector-docente select").val()
					};
					$.ajax({
						type: "POST",
						url: "getseccion.php",
						data: form_data,
						success: function(response)
						{
							$('.selector-seccion select').html(response).fadeIn();
						}
					});

				}); 



				$(".selector-seccion select").change(function() {				
					buscarlapso($(".selector-docente select").val(),$(".selector-seccion select").val());

					$('#LAPSO').val('');
					$(".selector-lapso select").empty();
					$('#tiplap').val('');			
					$(".selector-tipolapso select").empty();
				});

				function buscarlapso(val1,val2){
					var parametros = {
						"docente":val1,
						"seccion":val2				
					}
					$.ajax({
						data:parametros,
						type: "POST",
						url: "getlapso.php",				
						success: function(response)
						{
							$('.selector-lapso select').html(response).fadeIn();
						}
					});
				}


				$(".selector-lapso select").change(function() {				
					buscartipolapso($(".selector-docente select").val(),$(".selector-seccion select").val(),$(".selector-lapso select").val());

					$('#tiplap').val('');			
					$(".selector-tipolapso select").empty();

				});

				function buscartipolapso(val1,val2,val3){
					var parametros = {
						"docente":val1,
						"seccion":val2,
						"lapso":val3
					}
					$.ajax({
						data:parametros,
						type: "POST",
						url: "gettipolapso.php",				
						success: function(response)
						{

							$('.selector-tipolapso select').html(response).fadeIn();
						}
					});
				}



    });
</script>
