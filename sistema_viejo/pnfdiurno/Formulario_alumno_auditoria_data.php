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


require ("configuracion.php");

$job = '';
$id  = '';
if (isset($_GET['job'])){
  $job = $_GET['job'];
  if ($job == 'get_companies' ||
      $job == 'get_company'){
    if (isset($_GET['id'])){
      $id = $_GET['id'];
      if (!is_numeric($id)){
        $id = '';
      }
    }
  } else {
    $job = '';
  }
}


$mysql_data = array();

if ($job != ''){  

  $db_connection = mysqli_connect($servidor, $usuario, $clave, $base_datos);
  if (mysqli_connect_errno()){
    $result  = 'error';
    $message = 'Failed to connect to database: ' . mysqli_connect_error();
    $job     = '';
}

if ($job == 'get_companies'){ 

    $query = "SELECT * FROM alumno_auditoria ORDER BY accion";
    $query = mysqli_query($db_connection, $query);
    if (!$query){
      $result  = 'error';
      $message = 'query error';
  } else {
      $result  = 'success';
      $message = 'query success';
      while ($row = mysqli_fetch_array($query)){
        $functions  = '<div class="function_buttons"><ul>';
        $functions .= '<li class="function_edit"></li>';
        $functions .= '<li class="function_delete"></li>';
        $functions .= '</ul></div>';
        $mysql_data[] = array(
          "accion"  => $row['accion'],
          "cod"  => $row['cod'],
          "usuario"  => $row['usuario'],
          "codigo"  => $row['codigo'],
          "cedula"  => $row['cedula'],
          "nombre"  => $row['nombre'],
          "carrera"  => $row['carrera'],
          "mencion"  => $row['mencion'],
          "plan"  => $row['plan'],
          "actividad"  => $row['actividad'],
          "sexo"  => $row['sexo'],
          "edocivil"  => $row['edocivil'],
          "lugar"  => $row['lugar'],
          "municipio"  => $row['municipio'],
          "estado"  => $row['estado'],
          "procedenci"  => $row['procedenci'],
          "fechanac"  => $row['fechanac'],
          "edad"  => $row['edad'],
          "direccion"  => $row['direccion'],
          "telefonoh"  => $row['telefonoh'],
          "telefonoc"  => $row['telefonoc'],
          "telefonot"  => $row['telefonot'],
          "email"  => $row['email'],
          "tipingreso"  => $row['tipingreso'],
          "ingreso"  => $row['ingreso'],
          "semestre"  => $row['semestre'],
          "egreso"  => $row['egreso'],
          "pasantia"  => $row['pasantia'],
          "turno"  => $row['turno'],
          "trabajo"  => $row['trabajo'],
          "beca"  => $row['beca'],
          "ireceptor"  => $row['ireceptor'],
          "folio"  => $row['folio'],
          "tomo"  => $row['tomo'],
          "rusnies"  => $row['rusnies'],
          "discapacid"  => $row['discapacid'],
          "pnf"  => $row['pnf'],
          "trayecto"  => $row['trayecto'],
          "hora"  => $row['hora'],
          "fecha"  => $row['fecha'],
          "functions"     => $functions
          );
    }
}

} elseif ($job == 'get_company'){


    if ($id == ''){
      $result  = 'error';
      $message = 'id missing';
  } else {
      $query = "SELECT * FROM alumno_auditoria WHERE id = '" . mysqli_real_escape_string($db_connection, $id) ."'";
      $query = mysqli_query($db_connection, $query);
      if (!$query){
        $result  = 'error';
        $message = 'query error';
    } else {
        $result  = 'success';
        $message = 'query success';
        while ($row = mysqli_fetch_array($query)){
          $mysql_data[] = array(
            "accion"  => $row['accion'],
            "cod"  => $row['cod'],
            "usuario"  => $row['usuario'],
            "codigo"  => $row['codigo'],
            "cedula"  => $row['cedula'],
            "nombre"  => $row['nombre'],
            "carrera"  => $row['carrera'],
            "mencion"  => $row['mencion'],
            "plan"  => $row['plan'],
            "actividad"  => $row['actividad'],
            "sexo"  => $row['sexo'],
            "edocivil"  => $row['edocivil'],
            "lugar"  => $row['lugar'],
            "municipio"  => $row['municipio'],
            "estado"  => $row['estado'],
            "procedenci"  => $row['procedenci'],
            "fechanac"  => $row['fechanac'],
            "edad"  => $row['edad'],
            "direccion"  => $row['direccion'],
            "telefonoh"  => $row['telefonoh'],
            "telefonoc"  => $row['telefonoc'],
            "telefonot"  => $row['telefonot'],
            "email"  => $row['email'],
            "tipingreso"  => $row['tipingreso'],
            "ingreso"  => $row['ingreso'],
            "semestre"  => $row['semestre'],
            "egreso"  => $row['egreso'],
            "pasantia"  => $row['pasantia'],
            "turno"  => $row['turno'],
            "trabajo"  => $row['trabajo'],
            "beca"  => $row['beca'],
            "ireceptor"  => $row['ireceptor'],
            "folio"  => $row['folio'],
            "tomo"  => $row['tomo'],
            "rusnies"  => $row['rusnies'],
            "discapacid"  => $row['discapacid'],
            "pnf"  => $row['pnf'],
            "trayecto"  => $row['trayecto'],
            "hora"  => $row['hora'],
            "fecha"  => $row['fecha']

            );
      }
  }
}

}

   mysqli_close($db_connection);

}

$data = array(
  "result"  => $result,
  "message" => $message,
  "data"    => $mysql_data
  );

$json_data = json_encode($data);
print $json_data;


?>
