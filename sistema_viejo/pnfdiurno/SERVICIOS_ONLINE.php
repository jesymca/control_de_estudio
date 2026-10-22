
<?php

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

  <link rel="stylesheet" href="css/bootstrap.css" media="screen">
  <link rel="stylesheet" href="css/bootstrap.css" media="screen">
  <link rel="stylesheet" href="css/sweetalert.css">
  <link rel="stylesheet" href="css/dataTables.bootstrap.min.css" />
  <link rel="stylesheet" href="css/dataTables.bootstrap.css"/>        
  <link type="text/css" href="css/theme.css" rel="stylesheet">        
  <link rel="stylesheet" href="css/style.css" media="screen">
  <link type="text/css" rel="stylesheet" href="css/font-awesome.min.css"/>

  <script src="js/jquery-3.1.1.min3.js"></script>

</head>
<body>
 <div class="container" id="marco" style="width:80%">
   <form class="form-horizontal" id="effect2" method="post" action="HISTORIAL_ONLINE.php">
    <fieldset>
     <div class="form-group" id="titulo_formulario"> 
      <label id="titulo_formulario"><span class="glyphicon glyphicon-hand-right"></span> Datos del alumno</label>
    </div>
<br>
    <center><table >
      <tr>
        <td>
          <div class="form-group">
            <div class="col-md-12" style="width: 100%;margin-left: 0%;margin-top:0px">
              <label for="buscar">Buscar</label>  
              <input id="cedula" name="cedula" type="text" placeholder="V24123456" class="form-control">
              <input id="grado" name="grado" type="hidden" class="form-control" required>
            </div>
          </div>
        </td>
        <td style="width: 5px;"></td> 
        
        <td>        
        <div class="selector-grado">   
          <select style="width:150px;margin-top:8px;height: 38px;margin-left: 0px;">
           
            <option value="">Seleccionar</option>
            <option value="T">Tsu</option>
            <option value="I">Ingenieria</option>
            <option value="L">Licenciado</option>

          </select>      
        </div>
        </td>
         <td style="width: 5px;"></td> 

        <td>
          <div class="form-group">
            <div class="col-md-12" style="margin-top: 22px;margin-left: 0">                                
              <!-- <a type="button" id="btnbuscar2" class="btn btn-primary" style="background: #0C4783;"><span class="glyphicon glyphicon-search"></span> Buscar</a> -->
              <input type="submit" id="btnbuscar2" class="btn btn-primary" name="submit" value="Buscar" style="background: #0C4783;width:120px"/>           
            </div>
          </div>
        </td>     
    </tr>
  </table>




</form>
</div>
</fieldset>
</form>


<script type="text/javascript">
  $(document).ready(function() {

$('#grado').val('');
    $('.selector-grado select').click(function(){
      var v = $(this).val(); 
      $('#grado').val(v);       
    }); 

  });
</script>

</body>
</html>