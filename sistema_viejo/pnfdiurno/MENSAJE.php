<?php

include 'menu.php';
?>

<html>
<head>

  <style>
     #marco
    {
      width:400px;
      min-width: 400px;
    }

  </style>
</head>
<body>
 <div class="container" id="marco">
   <form class="form-horizontal" id="effect2" method="post" action="SERVICIOS2.php">
    <fieldset>
    
     <div class="form-group" id="titulo_formulario"> 
      <label id="titulo_formulario"><span class="glyphicon glyphicon-remove"></span> Materia ya cargada</label>

    </div>

    <center><table>
     <tr>

      <td width="100">
        <div class="form-group">
          <div class="col-md-12" style="width: 150px;margin-left: 0;margin-top:23px">
            <input type="submit" class="btn btn-primary" name="submit" value="Volver" style="background: #0C4783;width:120px"/> 
          </div>
        </div>

      </td>
      <td width="10"></td>
      <td width="100">
        <div class="form-group">
          <div class="col-md-12" style="width: 150px;margin-left:0;margin-top:23px">
            <a href="principal.php" class="btn btn-primary" style="background: #0C4783;width:120px"><span class="glyphicon glyphicon-log-out"></span> Salir</a>  
          </div>
        </div>
      </td>
      <tr width="100">       
        <div class="progress progress-striped active">
          <div class="progress-bar" style="width: 90%"></div>
        </div>
      </tr>

    </tr>
  </table></center>

  </fieldset>
</form>
</div>


</form>
</body>
</html>