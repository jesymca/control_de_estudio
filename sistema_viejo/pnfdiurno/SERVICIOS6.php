<?php
include 'db.php';
include 'menu.php';
?>
<div class="container" id="marco" style="width:600px;">
    <form class="form-horizontal" id="effect2" method="post" action="buscar6.php">
        <fieldset>
            <div class="form-group" id="titulo_formulario">
                <label id="titulo_formulario"><span class="glyphicon glyphicon-remove"></span> Eliminar Seccion</label>
            </div>
            
            <p>            

                <div class="form-group">
                    <div class="col-md-12" style="width:0%;margin-left: 20px;">
                        <label for="pensum">Pensum</label>
                        <input type="hidden" id="pensum" name="pensum">                     
                    </div>

                    <div class="selector-pensum" id="sp0">   
                        <select style="width:87%;height: 38px;margin-top:26px;margin-left: -15px;"></select>      
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-md-12" style="width:0%;margin-left: 20px;">
                        <label for="cod_doc">Docente</label>
                        <input type="hidden" id="cod_doc" name="cod_doc">                   
                    </div>

                    <div class="selector-docente" id="sp1">   
                        <select style="width:87%;height: 38px;margin-top:26px;margin-left: -15px;"></select> 
                    </div>
                </div>


                <div class="form-group">
                    <div class="col-md-12" style="width:0%;margin-left: 20px;">
                        <label for="cod_mat">Materia</label>  
                        <input type="hidden" id="cod_mat" name="cod_mat">                   
                    </div>

                    <div class="selector-cod_mat" id="sp5">   
                        <select style="width:87%;height: 38px;margin-top:26px;margin-left: -15px;"></select>   
                    </div>
                </div>


                <div class="form-group">
                    <div class="col-md-12" style="width:0%;margin-left: 20px;">
                        <label for="lapso">Lapso</label>  
                        <input type="hidden" id="lapso" name="lapso">                   
                    </div>

                    <div class="selector-lapso" id="sp2">   
                        <select style="width:87%;height: 38px;margin-top:26px;margin-left: -15px;"></select>  
                    </div>
                </div>


                <div class="form-group">
                    <div class="col-md-12" style="width:0%;margin-left: 20px;">
                        <label for="seccion">Seccion</label>
                        <input type="hidden" id="seccion" name="seccion">                   
                    </div>


                    <div class="selector-seccion" id="sp6">   
                        <select style="width:87%;height: 38px;margin-top:26px;margin-left: -15px;"></select>    
                    </div>
                </div>   
            </p>


            <div class="form-group" style="padding-left: 10px">
                <div class="col-md-12">

                    <input type="submit" class="btn btn-primary" name="submit" value="Eliminar" style="background: #0C4783;width:120px"/> 
                    <a href="principal.php" class="btn btn-primary" style="background: #0C4783;width:120px"><span class="glyphicon glyphicon-log-out"></span> Salir</a>
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
        $('#tiplap').val('');
        
        $.ajax({
            type: "POST",
            url: "getpensum.php",
            success: function(response)
            {
                $('.selector-pensum select').html(response).fadeIn();
            }
        });

        $('#sp0 select').click(function(){
            var v = $(this).val(); 
            $('#pensum').val(v);      
            buscardocente($(".selector-pensum select").val());
            buscaralumno($(".selector-pensum select").val());
            $('#cod_doc').val('');
            $(".selector-cod_doc select").empty();
            $('#cod_mat').val('');
            $(".selector-cod_mat select").empty();
            $('#lapso').val('');
            $(".selector-lapso select").empty();
            $('#seccion').val('');
            $(".selector-seccion select").empty();
            $('#alumno_1').val('');
            $(".selector-alumno_1 select").empty();
        }); 


        function buscardocente(val1){
            var parametros = {
                "pensum":val1
            }
            $.ajax({
                data:parametros,
                type: "POST",
                url: "getdocente_x.php",        
                success: function(response)
                {
                    $(".selector-docente select").html(response).fadeIn();
                }
            });  
        }

        $('#sp2 select').click(function(){
            var v = $(this).val();     
            $('#lapso').val(v); 
            buscarseccion($(".selector-docente select").val(),$(".selector-cod_mat select").val(),$(".selector-lapso select").val());

        }); 


        $(".selector-docente select").change(function() {
            var v = $(this).val();     
            $('#cod_doc').val(v);
            $('#cod_mat').val('');
            $(".selector-cod_mat select").empty();
            $('#lapso').val('');
            $(".selector-lapso select").empty();
            buscarmateria($(".selector-pensum select").val(),$(".selector-docente select").val());
        }); 


        function buscarmateria(val1,val2){
            var parametros = {
                "pensum":val1,
                "cod_doc":val2

            }
            $.ajax({
                data:parametros,
                type: "POST",
                url: "getseccion_x.php",        
                success: function(response)
                {
                    $('.selector-cod_mat select').html(response).fadeIn();
                }
            });
        }


        $(".selector-cod_mat select").change(function() {  
            var v = $(this).val();     
            $('#cod_mat').val(v);     
            buscarlapso($(".selector-pensum select").val(),$(".selector-docente select").val(),$(".selector-cod_mat select").val())

            $('#lapso').val('');
            $(".selector-lapso select").empty();
        });   

        function buscarlapso(val1,val2,val3){
            var parametros = {
                "pensum":val1,
                "cod_doc":val2,
                "cod_mat":val3,       
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
            buscartipolapso($(".selector-docente select").val(),$(".selector-cod_mat select").val(),$(".selector-lapso select").val());


            $('#seccion').val('');      
            $(".selector-seccion select").empty();
            buscarseccion($(".selector-docente select").val(),$(".selector-cod_mat select").val(),$(".selector-lapso select").val());
        });


        $('#sp6 select').click(function(){
            var v = $(this).val();     
            $('#seccion').val(v);         

        }); 

        function buscarseccion(val1,val2,val3){
            var parametros = {
                "cod_doc":val1,
                "cod_mat":val2,
                "lapso":val3
            }
            $.ajax({
                data:parametros,
                type: "POST",
                url: "getseccion2f.php",        
                success: function(response)
                {
                    $('.selector-seccion select').html(response).fadeIn();
                }
            });
        }

    });

</script>
