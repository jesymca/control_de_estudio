<?php
include 'db.php';
include 'menu.php';
?>
<div class="container" id="marco" style="width:600px;">
    <form class="form-horizontal" id="effect2" method="post" action="LISTADO DE ALUMNOS POR MATERIA.php">
        <fieldset>
            <div class="form-group" id="titulo_formulario">
                <label id="titulo_formulario"><span class="glyphicon glyphicon-stats"></span> Listado de alumnos por Materia</label>
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

                        <div class="selector-cod_doc">   
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

                        <div class="selector-seccion">   
                            <select style="width:87%;height: 38px;margin-top:26px;margin-left: -15px;"></select>    
                        </div>
                    </div>   
            </p>

            <div class="form-group" style="padding-left: 20px">
                <div class="col-md-12">
                    <input type="submit" class="btn btn-primary" name="submit" value="Imprimir" style="background: #0C4783;width:120px"/> 
                    <a href="principal.php" class="btn btn-primary" style="background: #0C4783;width:120px"><span class="glyphicon glyphicon-log-out"></span> Salir</a>
                </div>
            </div>               
         </form>


         <div class="form-group" style="padding-left:40px;padding-right:15px;">
                                                                                         
                    <form class="form-horizontal" method="post" action="LISTADO DE ALUMNOS MATERIA2.php">        
                        <input type="hidden" id="pensum2" name="pensum2">
                        <input type="hidden" id="cod_mat2" name="cod_mat2">
                        <input type="hidden" id="seccion2" name="seccion2">
                        <input type="hidden" id="lapso2" name="lapso2"> 
                        <input type="hidden" id="cod_doc2" name="cod_doc2">

                        <div class="form-group">
                            <div class="col-md-12" style="width:50%;">
                                <label for="url">Nombre de Archivo</label>  
                                <input id="url" name="url" type="text" class="form-control" required value="Listado de alumnos">
                            </div>                          
                        </div>  

                        <div class="form-group">
                            <div class="col-md-12"> 
                                <input type="submit" class="btn btn-primary" name="submit" value="Exportar a Excel" style="background: #0C4783;width:180px"/> 

                            </div>
                        </div>
                    </form>
                    </div> 

        </div>
    
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


        var valor1 = {
            "opcion":"pensum"
        }
        $.ajax({
            data:valor1,
            type: "POST",
            url: "getselect.php",
            success: function(response)
            {
                $('.selector-pensum select').html(response).fadeIn();
            }
        });

        $('.selector-pensum select').change(function(){
            var v = $(this).val(); 
            $('#pensum').val(v); 
            $('#pensum2').val(v);             
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
            $('#lapso2').val(v); 
            buscarseccion($(".selector-cod_doc select").val(),$(".selector-cod_mat select").val(),$(".selector-lapso select").val());
        }); 

        $(".selector-cod_doc select").change(function() {
            var v = $(this).val();     
            $('#cod_doc').val(v);
            $('#cod_doc2').val(v);
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
            $('#cod_mat2').val(v);     
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
            $('#seccion2').val(v);         

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
