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

    $query = "SELECT * FROM horarios ORDER BY cod_mat";
    $query = mysqli_query($db_connection, $query);
    if (!$query){
      $result  = 'error';
      $message = 'query error';
  } else {
      $result  = 'success';
      $message = 'query success';
      while ($row = mysqli_fetch_array($query)){
        $functions  = '<div class="function_buttons"><ul>';
        $functions .= '<li class="function_edit"><a href="Formulario_horarios_form_editar.php?action=editar&id='.$row['id'].'" data-id="'. $row['id'].'" data-name="' . $row['cod_mat'] . '"  style="background: #FF7D00"><span>Edit</span></a></li>';
        $functions .= '<li class="function_delete"><a data-id="'.$row['id'].'" data-name="'.$row['cod_mat'].'" style="background: #FF292B"><span>Delete</span></a></li>';
        $functions .= '</ul></div>';
        $mysql_data[] = array(
          "cod_mat"  => $row['cod_mat'],
          "electiva"  => $row['electiva'],
          "lapso"  => $row['lapso'],
          "seccion"  => $row['seccion'],
          "cod_doc"  => $row['cod_doc'],
          "aula"  => $row['aula'],
          "descrip"  => $row['descrip'],
          "hora_de_inicio"  => $row['hora_de_inicio'],
          "hora_final"  => $row['hora_final'],
          "cantidad"  => $row['cantidad'],
          "dia"  => $row['dia'],
          "functions"     => $functions
          );
    }
}

} elseif ($job == 'get_company'){


    if ($id == ''){
      $result  = 'error';
      $message = 'id missing';
  } else {
      $query = "SELECT * FROM horarios WHERE id = '" . mysqli_real_escape_string($db_connection, $id) ."'";
      $query = mysqli_query($db_connection, $query);
      if (!$query){
        $result  = 'error';
        $message = 'query error';
    } else {
        $result  = 'success';
        $message = 'query success';
        while ($row = mysqli_fetch_array($query)){
          $mysql_data[] = array(
            "cod_mat"  => $row['cod_mat'],
            "electiva"  => $row['electiva'],
            "lapso"  => $row['lapso'],
            "seccion"  => $row['seccion'],
            "cod_doc"  => $row['cod_doc'],
            "aula"  => $row['aula'],
            "descrip"  => $row['descrip'],
            "hora_de_inicio"  => $row['hora_de_inicio'],
            "hora_final"  => $row['hora_final'],
            "cantidad"  => $row['cantidad'],
            "dia"  => $row['dia']

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
