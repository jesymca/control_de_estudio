<?php
session_start();
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true && $_SESSION['alumno']==1) {
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
<script charset="utf-8" src="Formulario_alumno_data.js"></script>


    <style type="text/css">
        
    #marco
    {
      width:2230px;
      min-width: 2230px;
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
                    <label id="titulo_formulario"><span class="glyphicon glyphicon-book"></span> Expediente del Alumno</label>
                </div>
                <br />

<table>
        <tr>
        <td style="width: 30px;"></td>
        <td style="width: 120px;">
              <a href="Formulario_alumno_form_agregar.php" class="btn btn-primary" style="width:120px"><span class="glyphicon glyphicon-plus"></span> Agregar</a></td>
        <td style="width: 10px;"></td>
        <td style="width: 120px;">

            <a href="principal.php" class="btn btn-primary" style="width:120px"><span class="glyphicon glyphicon-log-out"></span> Salir</a>
        </td>


        </tr>
        </table>



                <table class="datatable" id="table_companies">
                    <thead>
                      <tr>
                        <th width="1%">Cedula</th>
                        <th width="15%">Nombre</th>
                        <th width="1%">Carrera</th>
                        <th width="1%">Mencion</th>
                        <th width="1%">Plan</th>
                        <th width="1%">Actividad</th>
                        <th width="1%">Sexo</th>
                        <th width="1%">Edocivil</th>
                        <th width="1%">Fechanac</th>
                        <th width="1%">Edad</th>
                        <th width="1%">Direccion</th>
                        <th width="1%">Telefonoh</th>
                        <th width="1%">Telefonoc</th>
                        <th width="1%">Telefonot</th>
                        <th width="1%">Email</th>
                        <th width="1%">Ingreso</th>
                        <th width="1%">Turno</th>
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


