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
<script charset="utf-8" src="Formulario_docente_auditoria_data.js"></script>


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
      width:3110px;
      min-width: 3110px;
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
                    <label id="titulo_formulario">Formulario de docente_auditoria</label>
                </div>
                <br />

<table>
        <tr>
        <td style="width: 30px;"></td>
        <td style="width: 120px;">

            <a href="principal.php" class="btn btn-primary" style="background: #0C4783;width:120px"><span class="glyphicon glyphicon-log-out"></span> Salir</a>
        </td>


        </tr>
        </table>



                <table class="datatable" id="table_companies">
                    <thead>
                      <tr>
                        <th width="1%">Accion</th>
                        <th width="1%">Cod</th>
                        <th width="1%">Usuario</th>
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
                        <th width="1%">Hora</th>
                        <th width="1%">Fecha</th>
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


