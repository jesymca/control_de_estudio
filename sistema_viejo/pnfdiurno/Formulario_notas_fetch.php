<?php
include('db.php');
include('function.php');
$query = '';
$output = array();

$query = "SELECT notas.codigo,notas.cod_mat,notas.nota,notas.lapso,notas.tiplap,notas.cod_doc,notas.acu,alumno.cedula,alumno.nombre FROM notas,alumno WHERE notas.codigo='".$_POST["search"]["value"]."' AND alumno.codigo='".$_POST["search"]["value"]."' ORDER BY notas.cod_mat ASC";

$statement = $connection->prepare($query);
$statement->execute();
$result = $statement->fetchAll();
$data = array();
$filtered_rows = $statement->rowCount();
foreach($result as $row)
{
    
    $sub_array = array();    
    $sub_array[] = $row["codigo"];
    $sub_array[] = $row["cod_mat"];    
    $sub_array[] = $row["lapso"];
    $sub_array[] = $row["tiplap"];   
    $sub_array[] = $row["nota"];
    $sub_array[] = $row["acu"];
    $sub_array[] = $row["cedula"];
    $sub_array[] = $row["nombre"];
     $sub_array[] = $row["cod_doc"];

    $sub_array[] = '<button type="button" name="update" id="'.$row["id"].'" class="btn btn-warning btn-xs update">Editar</button>';
    $sub_array[] = '<button type="button" name="delete" id="'.$row["id"].'" class="btn btn-danger btn-xs delete">Eliminar</button>';
    $data[] = $sub_array;
}
$output = array(
    "draw"              =>  intval($_POST["draw"]),
    "recordsTotal"      =>  1,
    "recordsFiltered"   =>  1,
    "data"              =>  $data
);
echo json_encode($output);
?>
