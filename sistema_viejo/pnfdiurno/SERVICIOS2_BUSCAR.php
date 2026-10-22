<?php
include 'db.php';
include 'menu.php';
?>
<html>
  <head>
    <style>
    </style>
  </head>
  <body>
<div class="container" id="marco">
   <form class="form-horizontal" id="effect2" method="post" action="Formulario_notas_tabla_index.php">
      <fieldset>
       <div class="form-group" id="titulo_formulario"> 
        <label id="titulo_formulario">Expediente del alumno</label>
    </div>

            <br />
            <br />
            
            <table>
                <tr>
                    <td>
                        <div class="form-group">
                            <div class="col-md-12" style="width: 100%;margin-left: 0%;margin-top:0px">
                                <label for="buscar">Buscar</label>  
                                <input id="buscar2" name="buscar2" type="text" placeholder="buscar2" class="form-control">
                            </div>
                        </div>
                    </td>
                    <td style="width: 5px;"></td> 
                    <td>
                        <div class="form-group">
                            <div class="col-md-12" style="margin-top: 27px;margin-left: 0">
                                <!--<input type="submit" class="btn btn-default" name="submit" value="Buscar"/>--> 
                                <input type="button" id="btnbuscar2" class="btn btn-default" name="submit" value="Buscar" style="width: 100px;"/>                     
                                <a href="principal.php" class="btn btn-default" style="width: 100px;">Salir</a>
                            </div>
                        </div>
                    </td>
                </tr>
            </table>

            
            <table id="editable_table" class="table table-bordered table-striped">
                <thead>
                    
                </thead>
                
            </table>
            

        </div>
    </form>
</fieldset>
</div>


</body>
</html>
<script type="text/javascript">
$(document).ready(function() {

$("#btnbuscar2").click(function() {
enviar_datos();
});

$("#buscar2").change(function() {
enviar_datos();
});


function enviar_datos(){
var valor = $('#buscar2').val();
    $.ajax({
    type: "post",
    url: "buscar.php",
    data:{buscar:valor},
    success: function(datos)
    {        
        $('#editable_table').html(datos);
    }
});
}

        // $('#editable_table').Tabledit({
        //     url:'',
        //     columns:{
        //         identifier:[0, "id"],                
        //     },
        //     restoreButton:false,
        //     onSuccess:function(data, textStatus, jqXHR)
        //     {
        //         if(data.action == 'ver')
        //         {
        //             $('#'+data.id).ver();
        //         }
        //     }
        // });

});
</script>
