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

    // $query2 = "SELECT notas.`cod_mat`,notas.`lapso`,notas.`seccion`, COUNT(*) Total FROM notas,planificacion where notas.cod_mat=planificacion.cod_mat and notas.lapso=planificacion.lapso and notas.seccion=planificacion.seccion GROUP BY `cod_mat`,`lapso`,`seccion` HAVING COUNT(*) > 0";
    // $query2 = mysqli_query($db_connection, $query2);
    //  while ($row2 = mysqli_fetch_array($query2)){
    //  $mysql_data2[] = array(
    //  	$cantidad=$row2['Total']
    //        );
    // }
   
    $query = " SELECT planificacion.pensum,planificacion.cod_mat,lismat.descrip2,lismat.creditos,planificacion.lapso,planificacion.seccion,planificacion.cod_doc,docente.nombre,planificacion.cupo,planificacion.tipo,planificacion.electiva,planificacion.turno,COUNT(*) Inscritos FROM planificacion,docente,lismat,notas where planificacion.pensum=lismat.pensum and planificacion.cod_mat=lismat.cod_mat and planificacion.cod_doc=docente.cod_doc and notas.cod_mat=planificacion.cod_mat and notas.lapso=planificacion.lapso and notas.seccion=planificacion.seccion";
    $query = mysqli_query($db_connection, $query);
    if (!$query){
      $result  = 'error';
      $message = 'query error';
  } else {
      $result  = 'success';
      $message = 'query success';
      while ($row = mysqli_fetch_array($query)){
        $functions  = '<div class="function_buttons"><ul>';
        $functions .= '<li class="function_edit"><a href="Formulario_planificacion_form_editar.php?action=editar&id='.$row['id'].'" data-id="'. $row['id'].'" data-name="' . $row['pensum'] . '"  style="background: #FF7D00"><span>Edit</span></a></li>';
        $functions .= '<li class="function_delete"><a data-id="'.$row['id'].'" data-name="'.$row['pensum'].'" style="background: #FF292B"><span>Delete</span></a></li>';
        $functions .= '</ul></div>';
        $mysql_data[] = array(
          "pensum"  => $row['pensum'],
          "cod_mat"  => $row['cod_mat'],
          "descrip2"  => $row['descrip2'],
          "creditos"  => $row['creditos'],
          "lapso"  => $row['lapso'],
          "seccion"  => $row['seccion'],
          "cod_doc"  => $row['cod_doc'],
          "nombre"  => $row['nombre'],
          "cupo"  => $row['cupo'],
          "inscritos"  => $row['Total'],
          "tipo"  => $row['tipo'],
          "electiva"  => $row['electiva'],
          "turno"  => $row['turno'],
          "functions"     => $functions
          );
    }
}

} elseif ($job == 'get_company'){


    if ($id == ''){
      $result  = 'error';
      $message = 'id missing';
  } else {
      $query = "SELECT planificacion.pensum,planificacion.cod_mat,lismat.descrip2,lismat.creditos,planificacion.lapso,planificacion.seccion,planificacion.cod_doc,docente.nombre,planificacion.cupo,planificacion.inscritos,planificacion.tipo,planificacion.electiva,planificacion.turno FROM planificacion,docente,lismat where planificacion.pensum=lismat.pensum and planificacion.cod_mat=lismat.cod_mat and planificacion.cod_doc=docente.cod_doc and planificacion.id = '" . mysqli_real_escape_string($db_connection, $id) ."'";
      $query = mysqli_query($db_connection, $query);
      if (!$query){
        $result  = 'error';
        $message = 'query error';
    } else {
        $result  = 'success';
        $message = 'query success';
        while ($row = mysqli_fetch_array($query)){
          $mysql_data[] = array(
          "pensum"  => $row['pensum'],
          "cod_mat"  => $row['cod_mat'],
          "descrip2"  => $row['descrip2'],
          "creditos"  => $row['creditos'],
          "lapso"  => $row['lapso'],
          "seccion"  => $row['seccion'],
          "cod_doc"  => $row['cod_doc'],
          "nombre"  => $row['nombre'],
          "cupo"  => $row['cupo'],
          "inscritos"  => $row['inscritos'],
          "tipo"  => $row['tipo'],
          "electiva"  => $row['electiva'],
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
