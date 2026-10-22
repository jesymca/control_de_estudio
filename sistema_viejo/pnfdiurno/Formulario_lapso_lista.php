<?php
session_start();
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true && $_SESSION['lapso']==1) {
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
<script charset="utf-8" src="Formulario_lapso_data.js"></script>


    <style type="text/css">


    #marco
    {
      width:690px;
      min-width: 690px;
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
                    <label id="titulo_formulario"><span class="glyphicon glyphicon-calendar"></span> Formulario de lapso</label>
                </div>
                <br />

<table>
        <tr>
        <td style="width: 30px;"></td>
        <td style="width: 120px;">
              <a href="Formulario_lapso_form_agregar.php" class="btn btn-primary" style="width:120px"><span class="glyphicon glyphicon-plus"></span> Agregar</a></td>
        <td style="width: 10px;"></td>
        <td style="width: 120px;">

            <a href="principal.php" class="btn btn-primary" style="width:120px"><span class="glyphicon glyphicon-log-out"></span> Salir</a>
        </td>


        </tr>
        </table>



                <table class="datatable" id="table_companies">
                    <thead>
                      <tr>
                        <th width="1%">Id</th>
                        <th width="1%">Lapso</th>
                        <th width="1%">Descrip</th>
                        <th width="1%">Carrera</th>
                        <th width="1%">Funciones</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
          </fieldset>
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


