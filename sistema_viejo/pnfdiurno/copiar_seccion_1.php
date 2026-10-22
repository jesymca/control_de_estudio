<?php
include 'db.php';
include 'menu.php';
?>
<div class="container" id="marco" style="width:600px;">
    <form class="form-horizontal" id="effect2" method="post" action="copiar_seccion_2.php">
        <fieldset>
            <div class="form-group" id="titulo_formulario">
                <label id="titulo_formulario"><span class="glyphicon glyphicon-copy"></span> Seccion de origen</label>
            </div>        

            <p>                   
                <div class="form-group">
                    <div class="col-md-12" style="width:0%;margin-left: 20px;">
                        <label for="pensum">Pensum</label>
                        <input type="hidden" id="cantidad" name="cantidad">
                        <input type="hidden" id="pensum" name="pensum">                     
                    </div>

                    <div class="selector-pensum">   
                        <select style="width:87%;height: 38px;margin-top:26px;margin-left: -15px;" required></select>      
                    </div>
                </div>



                <div class="form-group">
                    <div class="col-md-12" style="width:0%;margin-left: 20px;">
                        <label for="cod_doc">Docente</label>
                        <input type="hidden" id="cod_doc" name="cod_doc">                   
                    </div>

                    <div class="selector-cod_doc">   
                        <select style="width:87%;height: 38px;margin-top:26px;margin-left: -15px;" required></select> 
                    </div>
                </div>


                <div class="form-group">
                    <div class="col-md-12" style="width:0%;margin-left: 20px;">
                        <label for="cod_mat">Materia</label>  
                        <input type="hidden" id="cod_mat" name="cod_mat">                   
                    </div>

                    <div class="selector-cod_mat">   
                        <select style="width:87%;height: 38px;margin-top:26px;margin-left: -15px;" required></select>   
                    </div>
                </div>


                <div class="form-group">
                    <div class="col-md-12" style="width:0%;margin-left: 20px;">
                        <label for="lapso">Lapso</label>  
                        <input type="hidden" id="lapso" name="lapso">                   
                    </div>

                    <div class="selector-lapso">   
                        <select style="width:87%;height: 38px;margin-top:26px;margin-left: -15px;" required></select>  
                    </div>
                </div>


                <div class="form-group">
                    <div class="col-md-12" style="width:0%;margin-left: 20px;">
                        <label for="seccion">Seccion</label>
                        <input type="hidden" id="seccion" name="seccion">                   
                    </div>


                    <div class="selector-seccion">   
                        <select style="width:87%;height: 38px;margin-top:26px;margin-left: -15px;" required></select>    
                    </div>
                </div>   
            </p>



            <div class="form-group" style="padding-left: 20px">
                <div class="col-md-12">

                    <input type="submit" class="btn btn-primary" name="submit" value="Siguiente" style="width:120px"/> 
                    <a href="principal.php" class="btn btn-primary" style="width:120px"><span class="glyphicon glyphicon-log-out"></span> Salir</a><a id="cantidad_label" style="margin-left: 20px;color: black;font-size: 15px;"></a>

                </div>
            </div>          

            <!--inicio de la Tabla-->
            <table id="editable_table" class="table table-bordered table-striped">

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
            buscarseccion($(".selector-cod_doc select").val(),$(".selector-cod_mat select").val(),$(".selector-lapso select").val());
        }); 

        $(".selector-cod_doc select").change(function() {
            var v = $(this).val();     
            $('#cod_doc').val(v);
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
            $('#cantidad').html('');                    
            cantidad($('#cod_doc').val(),$('#cod_mat').val(),$('#lapso').val(),$('#seccion').val()); 
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

        function cantidad(val1,val2,val3,val4){
            var parametros = {
            "cod_doc":val1,
            "cod_mat":val2,
            "lapso":val3,
            "seccion":val4,            
            "opcion":"getcantidad"
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
