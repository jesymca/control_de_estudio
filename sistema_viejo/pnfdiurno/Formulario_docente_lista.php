<?php
session_start();
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true && $_SESSION['docente']==1) {
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
<script charset="utf-8" src="Formulario_docente_data.js"></script>


    <style type="text/css">
        body
        {
            background-attachment: fixed;
            background-size:cover;
            background-repeat:no-repeat;
            background-position: center center;
            background:#272822;
        }

    #marco
    {
      width:2560px;
      min-width: 2560px;
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
                    <label id="titulo_formulario"><span class="glyphicon glyphicon-lock"></span> Formulario de docente</label>
                </div>
                <br />

<table>
        <tr>
        <td style="width: 30px;"></td>
        <td style="width: 120px;">
              <a href="Formulario_docente_form_agregar.php" class="btn btn-primary" style="width:120px"><span class="glyphicon glyphicon-plus"></span> Agregar</a></td>
        <td style="width: 10px;"></td>
        <td style="width: 120px;">

            <a href="principal.php" class="btn btn-primary" style="width:120px"><span class="glyphicon glyphicon-log-out"></span> Salir</a>
        </td>


        </tr>
        </table>



                <table class="datatable" id="table_companies">
                    <thead>
                      <tr>
                        <th width="1%">Cod Doc</th>
                        <th width="1%">Cedula</th>
                        <th width="1%">Nombre</th>
                        <th width="1%">Condicion</th>
                        <th width="1%">Depart</th>
                        <th width="1%">Sexo</th>
                        <th width="1%">Fechanac</th>
                        <th width="1%">Titulo C</th>
                        <th width="1%">Titulo L</th>
                        <th width="1%">Tipo</th>
                        <th width="1%">Ingreso</th>
                        <th width="1%">Categoria</th>
                        <th width="1%">Dedicacion</th>
                        <th width="1%">Telefono</th>
                        <th width="1%">Asignatura</th>
                        <th width="1%">Horas Ad</th>
                        <th width="1%">Horas Do</th>
                        <th width="1%">Observa</th>
                        <th width="1%">Actividad</th>
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


