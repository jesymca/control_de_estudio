<?php
session_start();
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true && $_SESSION['auditoria']==1) {
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
?>

<?php include 'menu.php';?>

<link rel="stylesheet" href="css/dataTables.bootstrap.min.css" /> 
<link rel="stylesheet" href="css/font-awesome.min.css">
<link rel="stylesheet" href="css/layout.css">

<script charset="utf-8" src="js/dataTables.bootstrap.min.js"></script>
<script charset="utf-8" src="js/jquery.dataTables.min.js"></script>
<script charset="utf-8" src="js/jquery.validate.min.js"></script>
<script charset="utf-8" src="Formulario_lapso_auditoria_data.js"></script>


    <style type="text/css">
        body
        {
            /*background-image: url("fondo.jpg");*/
            background-attachment: fixed;
            background-size:cover;
            background-repeat:no-repeat;
            background-position: center center;
            background:#272822;
        }

    #marco
    {
      width:1130px;
      min-width: 1130px;
    }

    </style>
</head>
<body>

<?php include 'nav1.php';?>



</head>
<body>




<div class="container" id="marco">

            <fieldset>
                <div class="form-group" id="titulo_formulario">
                    <label id="titulo_formulario">Formulario de Lapso Auditoria</label>
                </div>
                <br />

<table>
        <tr>
        <td style="width: 30px;"></td>
        <td style="width: 120px;">

              <a id="add_company" name="add_company" class="btn btn-primary" style="background: #0C4783;width:120px">Agregar</a>
              </td>
        <td style="width: 10px;"></td>
        <td style="width: 120px;">

            <a href="auditorias.php" class="btn btn-primary" style="background: #0C4783;width:120px"><span class="glyphicon glyphicon-log-out"></span> Salir</a>
        </td>


        </tr>
        </table>



                <table class="datatable" id="table_companies">
                    <thead>
                      <tr>
                        <th width="1%">Accion</th>
                        <th width="1%">Cod</th>
                        <th width="1%">Usuario</th>
                        <th width="1%">Lapso</th>
                        <th width="1%">Descrip</th>
                        <th width="1%">Hora</th>
                        <th width="1%">Fecha</th>
                        <th width="1%">Funciones</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>

        </div>
</fieldset>
</form>
</div>

        <div class="lightbox_bg"></div>

        <div class="lightbox_container" style="width: 70%;height:80%;">
          <div class="lightbox_close"></div>
          <div class="lightbox_content">


           <h2>Agregar Lapso Auditoria</h2>
           <form class="form add" id="form_lapso_auditoria" data-id="">
              <div class="input_container">
                <label for="accion">Accion:</label>
                <div class="field_container">
                  <input type="text" class="text" name="accion" id="accion" tabindex="1" required>
              </div>
          </div>
 
 
              <div class="input_container">
                <label for="cod">Cod:</label>
                <div class="field_container">
                  <input type="text" class="text" name="cod" id="cod" tabindex="2" required>
              </div>
          </div>
 
 
              <div class="input_container">
                <label for="usuario">Usuario:</label>
                <div class="field_container">
                  <input type="text" class="text" name="usuario" id="usuario" tabindex="3" required>
              </div>
          </div>
 
 
              <div class="input_container">
                <label for="lapso">Lapso:</label>
                <div class="field_container">
                  <input type="text" class="text" name="lapso" id="lapso" tabindex="4" required>
              </div>
          </div>
 
 
              <div class="input_container">
                <label for="descrip">Descrip:</label>
                <div class="field_container">
                  <input type="text" class="text" name="descrip" id="descrip" tabindex="5" required>
              </div>
          </div>
 
 
              <div class="input_container">
                <label for="hora">Hora:</label>
                <div class="field_container">
                  <input type="text" class="text" name="hora" id="hora" tabindex="6" required>
              </div>
          </div>
 
 
              <div class="input_container">
                <label for="fecha">Fecha:</label>
                <div class="field_container">
                  <input type="text" class="text" name="fecha" id="fecha" tabindex="7" required>
              </div>
          </div>
 
 


      <div class="button_container">
        <button type="submit">Agregar</button>
    </div>
</form>

</div>
</div>

<noscript id="noscript_container">
  <div id="noscript" class="error">
    <p>JavaScript support is needed to use this page.</p>
</div>
</noscript>

<div id="message_container">
  <div id="message" class="success">
    <p>This is a success message.</p>
</div>
</div>

<div id="loading_container">
  <div id="loading_container2">
    <div id="loading_container3">
      <div id="loading_container4">
        Cargando por favor espere...
    </div>
</div>
</div>
</div>


</body>
</html>


