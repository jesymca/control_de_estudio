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
  if ($job == 'get_alumnos' ||
      $job == 'get_alumno'){
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

if ($job == 'get_alumnos'){ 

    $query = "SELECT `id`,`cedula`, `nombre`, `carrera`, `mencion`, `plan`, `actividad`, `sexo`, `edocivil`, `fechanac`, `edad`, `direccion`, `telefonoh`, `telefonoc`, `telefonot`, `email`, `ingreso`, `turno` FROM alumno ORDER BY cedula";
    $query = mysqli_query($db_connection, $query);
    if (!$query){
      $result  = 'error';
      $message = 'query error';
  } else {
      $result  = 'success';
      $message = 'query success';
      while ($row = mysqli_fetch_array($query)){
        $functions  = '<div class="function_buttons"><ul>';
        $functions .= '<li class="function_edit"><a href="Formulario_alumno_form_editar.php?action=editar&id='.$row['id'].'" data-id="'. $row['id'].'" data-name="' . $row['cedula'] . '"  style="background: #FF7D00"><span>Edit</span></a></li>';
        $functions .= '<li class="function_delete"><a data-id="'.$row['id'].'" data-name="'.$row['cedula'].'" style="background: #FF292B"><span>Delete</span></a></li>';
        $functions .= '</ul></div>';
        $mysql_data[] = array(
          "cedula"  => $row['cedula'],
          "nombre"  => $row['nombre'],
          "carrera"  => $row['carrera'],
          "mencion"  => $row['mencion'],
          "plan"  => $row['plan'],
          "actividad"  => $row['actividad'],
          "sexo"  => $row['sexo'],
          "edocivil"  => $row['edocivil'],
          "fechanac"  => $row['fechanac'],
          "edad"  => $row['edad'],
          "direccion"  => $row['direccion'],
          "telefonoh"  => $row['telefonoh'],
          "telefonoc"  => $row['telefonoc'],
          "telefonot"  => $row['telefonot'],
          "email"  => $row['email'],
          "ingreso"  => $row['ingreso'],
          "turno"  => $row['turno'],
          "functions"     => $functions
          );
    }
}

} elseif ($job == 'get_alumno'){


    if ($id == ''){
      $result  = 'error';
      $message = 'id missing';
  } else {
      $query = "SELECT `id`,`cedula`, `nombre`, `carrera`, `mencion`, `plan`, `actividad`, `sexo`, `edocivil`, `fechanac`, `edad`, `direccion`, `telefonoh`, `telefonoc`, `telefonot`, `email`, `ingreso`, `turno` FROM alumno WHERE id = '" . mysqli_real_escape_string($db_connection, $id) ."'";
      $query = mysqli_query($db_connection, $query);
      if (!$query){
        $result  = 'error';
        $message = 'query error';
    } else {
        $result  = 'success';
        $message = 'query success';
        while ($row = mysqli_fetch_array($query)){
          $mysql_data[] = array(
            "cedula"  => $row['cedula'],
            "nombre"  => $row['nombre'],
            "carrera"  => $row['carrera'],
            "mencion"  => $row['mencion'],
            "plan"  => $row['plan'],
            "actividad"  => $row['actividad'],
            "sexo"  => $row['sexo'],
            "edocivil"  => $row['edocivil'],
            "fechanac"  => $row['fechanac'],
            "edad"  => $row['edad'],
            "direccion"  => $row['direccion'],
            "telefonoh"  => $row['telefonoh'],
            "telefonoc"  => $row['telefonoc'],
            "telefonot"  => $row['telefonot'],
            "email"  => $row['email'],
            "ingreso"  => $row['ingreso'],
            "turno"  => $row['turno']

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
