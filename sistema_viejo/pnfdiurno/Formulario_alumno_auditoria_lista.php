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
<script charset="utf-8" src="Formulario_alumno_auditoria_data.js"></script>


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
      width:4760px;
      min-width: 4760px;
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
                    <label id="titulo_formulario">Formulario de alumno_auditoria</label>
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
                        <th width="1%">Codigo</th>
                        <th width="1%">Cedula</th>
                        <th width="1%">Nombre</th>
                        <th width="1%">Carrera</th>
                        <th width="1%">Mencion</th>
                        <th width="1%">Plan</th>
                        <th width="1%">Actividad</th>
                        <th width="1%">Sexo</th>
                        <th width="1%">Edocivil</th>
                        <th width="1%">Lugar</th>
                        <th width="1%">Municipio</th>
                        <th width="1%">Estado</th>
                        <th width="1%">Procedenci</th>
                        <th width="1%">Fechanac</th>
                        <th width="1%">Edad</th>
                        <th width="1%">Direccion</th>
                        <th width="1%">Telefonoh</th>
                        <th width="1%">Telefonoc</th>
                        <th width="1%">Telefonot</th>
                        <th width="1%">Email</th>
                        <th width="1%">Tipingreso</th>
                        <th width="1%">Ingreso</th>
                        <th width="1%">Semestre</th>
                        <th width="1%">Egreso</th>
                        <th width="1%">Pasantia</th>
                        <th width="1%">Turno</th>
                        <th width="1%">Trabajo</th>
                        <th width="1%">Beca</th>
                        <th width="1%">Ireceptor</th>
                        <th width="1%">Folio</th>
                        <th width="1%">Tomo</th>
                        <th width="1%">Rusnies</th>
                        <th width="1%">Discapacid</th>
                        <th width="1%">Pnf</th>
                        <th width="1%">Trayecto</th>
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


