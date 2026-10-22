<?php
include 'db.php';
$input = filter_input_array(INPUT_POST);
$cedula = mysqli_real_escape_string($conn, $input["cedula"]);
$nombre = mysqli_real_escape_string($conn, $input["nombre"]);
$nota = mysqli_real_escape_string($conn, $input["nota"]);
$acu = mysqli_real_escape_string($conn, $input["acu"]);
if($input["action"] === 'edit')
{
    $query = "UPDATE notas2 SET cedula = '".$cedula."',nombre = '".$nombre."',nota = '".$nota."',acu = '".$acu."' WHERE id = '".$input["id"]."'";
    mysqli_query($conn, $query);
}
if($input["action"] === 'delete')
{
    $query = "DELETE FROM notas2 WHERE id = '".$input["id"]."'";
    mysqli_query($conn, $query);
}
echo json_encode($input);
?>
