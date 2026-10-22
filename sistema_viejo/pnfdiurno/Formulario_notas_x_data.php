<?php


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

    $query = "SELECT notas.cod_mat,notas.cod_doc,notas.seccion,notas.lapso,notas.seccion,notas.tiplap,lismat.descrip2, COUNT(*) cantidad FROM notas,lismat where notas.cod_mat=lismat.cod_mat GROUP BY notas.cod_mat,notas.seccion,notas.lapso,notas.seccion,lismat.descrip2 HAVING COUNT(*) > 1";
    $query = mysqli_query($db_connection, $query);
    if (!$query){
      $result  = 'error';
      $message = 'query error';
  } else {
      $result  = 'success';
      $message = 'query success';
      while ($row = mysqli_fetch_array($query)){
        $functions  = '<div class="function_buttons"><ul>';
        $functions .= '</ul></div>';
        $mysql_data[] = array(
          "cod_mat"  => $row['cod_mat'],
          "descrip2"  => $row['descrip2'], 
          "cod_doc"  => $row['cod_doc'],         
          "lapso"  => $row['lapso'],
          "tiplap"  => $row['tiplap'],         
          "seccion"  => $row['seccion'],
          "cantidad"  => $row['cantidad'],         
          
          );
    }
}

} elseif ($job == 'get_company'){


    if ($id == ''){
      $result  = 'error';
      $message = 'id missing';
  } else {

      $query = "SELECT notas.cod_mat,notas.cod_doc,notas.seccion,notas.lapso,notas.seccion,notas.tiplap,lismat.descrip2, COUNT(*) cantidad FROM notas,lismat WHERE notas.cod_mat=lismat.cod_mat id = '" . mysqli_real_escape_string($db_connection, $id) ."' GROUP BY notas.cod_mat,notas.seccion,notas.lapso,notas.seccion HAVING COUNT(*) > 1";

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
            "descrip2"  => $row['descrip2'],
            "cod_doc"  => $row['cod_doc'],    
            "lapso"  => $row['lapso'],
            "tiplap"  => $row['tiplap'],                   
            "seccion"  => $row['seccion'], 
            "cantidad"  => $row['cantidad']          
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
