<?php
include 'db.php';
$input = filter_input_array(INPUT_POST);
$seccion = mysqli_real_escape_string($conn, $input["seccion"]);
$cedula = mysqli_real_escape_string($conn, $input["cedula"]);
$nombre = mysqli_real_escape_string($conn, $input["nombre"]);
$nota = mysqli_real_escape_string($conn, $input["nota"]);
$acum = mysqli_real_escape_string($conn, $input["acum"]);
if($input["action"] === 'edit')
{
    $query = "UPDATE cargar_notas_por_seccion SET seccion = '".$seccion."',cedula = '".$cedula."',nombre = '".$nombre."',nota = '".$nota."',acum = '".$acum."' WHERE id = '".$input["id"]."'";
    mysqli_query($conn, $query);
}
if($input["action"] === 'delete')
{
    $query = "DELETE FROM cargar_notas_por_seccion WHERE id = '".$input["id"]."'";
    mysqli_query($conn, $query);
}
echo json_encode($input);
?>
