<?php
include 'db.php';
$input = filter_input_array(INPUT_POST);
$alumno = mysqli_real_escape_string($conn, $input["alumno"]);
$seccion = mysqli_real_escape_string($conn, $input["seccion"]);
if($input["action"] === 'edit')
{
    $query = "UPDATE inscribir_materia SET alumno = '".$alumno."',seccion = '".$seccion."' WHERE id = '".$input["id"]."'";
    mysqli_query($conn, $query);
}
if($input["action"] === 'delete')
{
    $query = "DELETE FROM inscribir_materia WHERE id = '".$input["id"]."'";
    mysqli_query($conn, $query);
}
echo json_encode($input);
?>
