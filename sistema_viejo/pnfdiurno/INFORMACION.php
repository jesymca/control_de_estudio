
<?php
session_start();
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true) {
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

    #marco
    {
      width:450px;
      min-width: 450px;
    }

  </style>
</head>
<body>
 <div class="container" id="marco">
   <form class="form-horizontal" id="effect2" method="post">
    <fieldset>
     <div class="form-group" id="titulo_formulario"> 
      <label id="titulo_formulario"><span class="glyphicon glyphicon-info-sign"></span> Selección de Información</label>
    </div>

<br>



<table>
      <tr>
<td width="10"></td>
        <td width="100">
          <div class="form-group">
            <div class="col-md-12">
              <a href="Formulario_alumno_lista.php" class="btn btn-primary" style="background: #0C4783;"><span class="glyphicon glyphicon-th-list"></span> Información del Alumno</a>

            </div>
          </div>
        </td>

        <td width="10"></td>

        <td width="100">
          <div class="form-group">
            <div class="col-md-12">
              <a href="PENSA.php" class="btn btn-primary" style="background: #0C4783;"><span class="glyphicon glyphicon-th-list"></span> Pensa de Estudios</a>
            </div>
          </div>
        </td>
</tr>
</table>
      
<table>
      <tr>

        <td width="10"></td>


        <td width="100">
          <div class="form-group">
            <div class="col-md-12">
              <a href="Formulario_docente_lista.php" class="btn btn-primary" style="background: #0C4783;"><span class="glyphicon glyphicon-th-list"></span> Información del Docente</a>
            </div>
          </div>
        </td>


        <td width="10"></td>


        <td width="100">
          <div class="form-group">
            <div class="col-md-12">
              <a href="principal.php" class="btn btn-primary" style="background: #0C4783;width:120px"><span class="glyphicon glyphicon-log-out"></span> Salir</a>
            </div>
          </div>
        </td>



      </tr>
    </table>



           </form>

    </fieldset>
      </div>

</body>
</html>
