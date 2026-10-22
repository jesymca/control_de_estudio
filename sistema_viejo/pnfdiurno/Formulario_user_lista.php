<?php
session_start();
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true && $_SESSION['user']==1) {
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
<script charset="utf-8" src="Formulario_user_data.js"></script>


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
      width:4300px;
      min-width: 4300px;
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
                    <label id="titulo_formulario"><span class=" glyphicon glyphicon-user"></span>Formulario de user</label>
                </div>
                <br />

<table>
        <tr>
        <td style="width: 30px;"></td>
        <td style="width: 120px;">
              <a href="Formulario_user_form_agregar.php" class="btn btn-primary" style="width:120px"><span class="glyphicon glyphicon-plus"></span> Agregar</a></td>
        <td style="width: 10px;"></td>
        <td style="width: 120px;">

            <a href="principal.php" class="btn btn-primary" style="width:120px"><span class="glyphicon glyphicon-log-out"></span> Salir</a>
        </td>


        </tr>
        </table>



                <table class="datatable" id="table_companies">
                    <thead>
                      <tr>
                        <th width="1%">Nombre</th>
                        <th width="1%">Login</th>
                        <th width="1%">Clave</th>
                        <th width="1%">Alumno</th>
                        <th width="1%">Docente</th>
                        <th width="1%">Notas</th>
                        <th width="1%">Notas Guardar</th>
                        <th width="1%">Notas Modificar</th>
                        <th width="1%">Notas Borrar</th>
                        <th width="1%">Lapso</th>
                        <th width="1%">Lismat</th>
                        <th width="1%">Seccion</th>
                        <th width="1%">Tipos Lapso</th>
                        <th width="1%">Nota</th>
                        <th width="1%">User</th>
                        <th width="1%">User Clave</th>
                        <th width="1%">Auditoria</th>
                        <th width="1%">Actas</th>
                        <th width="1%">Historiales</th>
                        <th width="1%">Agregar Seccion</th>
                        <th width="1%">Inscribir Materia</th>
                        <th width="1%">Copiar Seccion</th>
                        <th width="1%">Eliminar Seccion</th>
                        <th width="1%">Horas</th>
                        <th width="1%">Aula</th>
                        <th width="1%">Electivas</th>
                        <th width="1%">Cambiar Docente</th>
                        <th width="1%">Cambiar Lapso</th>
                        <th width="1%">Cambiar Seccion</th>
                        <th width="1%">Cambiar Materia</th>
                        <th width="1%">Desactivar Alumnos</th>
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


