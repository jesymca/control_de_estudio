
<?php
session_start();
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true && $_SESSION['historiales']==1) {
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
    body    

    input[type = "text"]
    {
      background:#658DB3;  
      font-weight:bold; 
      color:#000000; 
    }

  </style>
</head>
<body>
 <div class="container" id="marco" style="width:80%">
   <form class="form-horizontal" id="effect2" method="post" action="">
    <fieldset>
     <div class="form-group" id="titulo_formulario"> 
      <label id="titulo_formulario"><span class="glyphicon glyphicon-print"></span> Buscar alumno</label>
    </div>

    <center><table >
      <tr>
        <td>
          <div class="form-group">
            <div class="col-md-12" style="width: 100%;margin-left: 0%;margin-top:0px">
              <label for="buscar">Buscar</label>  
              <input id="buscar2" name="buscar2" type="text" placeholder="buscar" class="form-control">
            </div>
          </div>
        </td>
        <td style="width: 5px;"></td> 
        <td>
          <div class="form-group">
            <div class="col-md-12" style="margin-top: 27px;margin-left: 0">                                
              <a type="button" id="btnbuscar2" class="btn btn-primary" style="background: #0C4783;"><span class="glyphicon glyphicon-search"></span> Buscar</a>
                        
            </div>
          </div>
        </td>
      
   
      <td></td>

      <td width="100">
        <div class="form-group">
          <div class="col-md-12" style="width: 100%;margin-left:10%;margin-top:27px">
            <a href="principal.php" class="btn btn-primary" style="width: 100px;background: #0C4783;"><span class="glyphicon glyphicon-log-out"></span> Salir</a>
          </div>
        </div>
      </td>
    </tr>
  </table>


  <table id="editable_table" class="table table-bordered table-striped"></table></center>


</form>
</div>
</fieldset>
</form>

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


<script type="text/javascript">
  $(document).ready(function() {
     hide_loading_message();

    $("#buscar2").keydown(function(e) {        
      if (e.which == 13){
        e.preventDefault();
        enviar_datos();       
      }
    });


    $("#btnbuscar2").click(function() {
      enviar_datos();
    });

 
    function enviar_datos(){
      show_loading_message();
      var valor = $('#buscar2').val();
      $.ajax({
        type: "post",
        url: "buscar8.php",
        data:{buscar:valor},
        success: function(datos)
        {        
          $('#editable_table').html(datos);
          hide_loading_message();
        }
      });
    }   

      // Show loading message
  function show_loading_message(){
    $('#marco').hide();
    $('#loading_container').show();
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


        