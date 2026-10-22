<?php


$db_server   = 'localhost';
$db_username = 'root';
$db_password = 'SR19021601***';
$db_name     = 'testing1';


$job = '';
$id  = '';
if (isset($_GET['job'])){
  $job = $_GET['job'];
  if ($job == 'get_companies' ||
      $job == 'get_company'   ||
      $job == 'add_company'   ||
      $job == 'edit_company'  ||
      $job == 'delete_company'){
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

  $db_connection = mysqli_connect($db_server, $db_username, $db_password, $db_name);
  if (mysqli_connect_errno()){
    $result  = 'error';
    $message = 'Failed to connect to database: ' . mysqli_connect_error();
    $job     = '';
}

if ($job == 'get_companies'){

    $query = "SELECT * FROM notas ORDER BY codigo";
    $query = mysqli_query($db_connection, $query);
    if (!$query){
      $result  = 'error';
      $message = 'query error';
  } else {
      $result  = 'success';
      $message = 'query success';
      while ($row = mysqli_fetch_array($query)){
        $functions  = '<div class="function_buttons"><ul>';
        $functions .= '<li class="function_edit"><a data-id="'   . $row['id'] . '" data-name="' . $row['nombre'] . '"><span>Edit</span></a></li>';
        $functions .= '<li class="function_delete"><a data-id="' . $row['id'] . '" data-name="' . $row['nombre'] . '"><span>Delete</span></a></li>';
        $functions .= '</ul></div>';
        $mysql_data[] = array(
          "codigo"  => $row['codigo'],
          "cod_mat"  => $row['cod_mat'],
          "nota"  => $row['nota'],
          "lapso"  => $row['lapso'],
          "tiplap"  => $row['tiplap'],
          "cod_doc"  => $row['cod_doc'],
          "acu"  => $row['acu'],
          "functions"     => $functions
          );
    }
}

} elseif ($job == 'get_company'){


    if ($id == ''){
      $result  = 'error';
      $message = 'id missing';
  } else {
      $query = "SELECT * FROM notas WHERE id = '" . mysqli_real_escape_string($db_connection, $id) ."'";
      $query = mysqli_query($db_connection, $query);
      if (!$query){
        $result  = 'error';
        $message = 'query error';
    } else {
        $result  = 'success';
        $message = 'query success';
        while ($row = mysqli_fetch_array($query)){
          $mysql_data[] = array(
            "codigo"  => $row['codigo'],
            "cod_mat"  => $row['cod_mat'],
            "nota"  => $row['nota'],
            "lapso"  => $row['lapso'],
            "tiplap"  => $row['tiplap'],
            "cod_doc"  => $row['cod_doc'],
            "acu"  => $row['acu']

            );
      }
  }
}

} elseif ($job == 'add_company'){

    $query = "INSERT INTO notas SET ";
    if (isset($_GET['codigo'])) { $query .= "codigo = '". mysqli_real_escape_string($db_connection, $_GET['codigo']) . "', "; }
    if (isset($_GET['cod_mat'])) { $query .= "cod_mat = '". mysqli_real_escape_string($db_connection, $_GET['cod_mat']) . "', "; }
    if (isset($_GET['nota'])) { $query .= "nota = '". mysqli_real_escape_string($db_connection, $_GET['nota']) . "', "; }
    if (isset($_GET['lapso'])) { $query .= "lapso = '". mysqli_real_escape_string($db_connection, $_GET['lapso']) . "', "; }
    if (isset($_GET['tiplap'])) { $query .= "tiplap = '". mysqli_real_escape_string($db_connection, $_GET['tiplap']) . "', "; }
    if (isset($_GET['cod_doc'])) { $query .= "cod_doc = '". mysqli_real_escape_string($db_connection, $_GET['cod_doc']) . "', "; }
    if (isset($_GET['acu'])) { $query .= "acu = '". mysqli_real_escape_string($db_connection, $_GET['acu']). "'";}

    $query = mysqli_query($db_connection, $query);
    if (!$query){
      $result  = 'error';
      $message = 'query error';
  } else {
      $result  = 'success';
      $message = 'query success';
  }

} elseif ($job == 'edit_company'){

    if ($id == ''){
      $result  = 'error';
      $message = 'id missing';
  } else {
      $query = "UPDATE notas SET ";
    if (isset($_GET['codigo'])) { $query .= "codigo = '". mysqli_real_escape_string($db_connection, $_GET['codigo']) . "', "; }
    if (isset($_GET['cod_mat'])) { $query .= "cod_mat = '". mysqli_real_escape_string($db_connection, $_GET['cod_mat']) . "', "; }
    if (isset($_GET['nota'])) { $query .= "nota = '". mysqli_real_escape_string($db_connection, $_GET['nota']) . "', "; }
    if (isset($_GET['lapso'])) { $query .= "lapso = '". mysqli_real_escape_string($db_connection, $_GET['lapso']) . "', "; }
    if (isset($_GET['tiplap'])) { $query .= "tiplap = '". mysqli_real_escape_string($db_connection, $_GET['tiplap']) . "', "; }
    if (isset($_GET['cod_doc'])) { $query .= "cod_doc = '". mysqli_real_escape_string($db_connection, $_GET['cod_doc']) . "', "; }
    if (isset($_GET['acu'])) { $query .= "acu = '". mysqli_real_escape_string($db_connection, $_GET['acu']) . "'"; }
      $query .= "WHERE id = '" . mysqli_real_escape_string($db_connection, $id). "'";}
      $query  = mysqli_query($db_connection, $query);
      if (!$query){
        $result  = 'error';
        $message = 'query error';
    } else {
        $result  = 'success';
        $message = 'query success';
    }
}

} elseif ($job == 'delete_notas'){

    if ($id == ''){
      $result  = 'error';
      $message = 'id missing';
  } else {
      $query = "DELETE FROM notas WHERE id = '" . mysqli_real_escape_string($db_connection, $id) . "'";
      $query = mysqli_query($db_connection, $query);
      if (!$query){
        $result  = 'error';
        $message = 'query error';
    } else {
        $result  = 'success';
        $message = 'query success';
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
