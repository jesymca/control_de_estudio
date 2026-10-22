<?php
include('db.php');
include('function.php');
$query = '';
$output = array();
$query .= "SELECT * FROM alumno ";
if(isset($_POST["search"]["value"]))
{ 
    $query .= 'WHERE codigo LIKE "%'.$_POST["search"]["value"].'%" ';
    $query .= 'OR cedula LIKE "%'.$_POST["search"]["value"].'%" ';
    $query .= 'OR nombre LIKE "%'.$_POST["search"]["value"].'%" ';

}
if(isset($_POST["order"]))
{
    $query .= 'ORDER BY '.$_POST['order']['0']['column'].' '.$_POST['order']['0']['dir'].' ';
}
else
{
    $query .= 'ORDER BY id DESC ';
}
if($_POST["length"] != -1)
{
    $query .= 'LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
}
$statement = $connection->prepare($query);
$statement->execute();
$result = $statement->fetchAll();
$data = array();
$filtered_rows = $statement->rowCount();
foreach($result as $row)
{
    $image = '';
    if($row["image"] != '')
    {
        $image = '<img src="upload/'.$row["image"].'" class="img-thumbnail" width="100" height="100" />';
    }
    else
    {
        $image = '';
    }
    $sub_array = array();
    $sub_array[] = $image;
    $sub_array[] = $row["codigo"];
    $sub_array[] = $row["cedula"];
    $sub_array[] = $row["nombre"];
    $sub_array[] = $row["carrera"];
    $sub_array[] = $row["mencion"];
    $sub_array[] = $row["plan"];
    $sub_array[] = $row["pensum"];
    $sub_array[] = $row["actividad"];
    $sub_array[] = $row["sexo"];
    $sub_array[] = $row["edocivil"];
    $sub_array[] = $row["lugar"];
    $sub_array[] = $row["municipio"];
    $sub_array[] = $row["estado"];
    $sub_array[] = $row["procedenci"];
    $sub_array[] = $row["fechanac"];
    $sub_array[] = $row["edad"];
    $sub_array[] = $row["direccion"];
    $sub_array[] = $row["telefonoh"];
    $sub_array[] = $row["telefonoc"];
    $sub_array[] = $row["telefonot"];
    $sub_array[] = $row["email"];
    $sub_array[] = $row["tipingreso"];
    $sub_array[] = $row["mencionbac"];
    $sub_array[] = $row["anogra_bac"];
    $sub_array[] = $row["tipinspro"];
    $sub_array[] = $row["codinspro"];
    $sub_array[] = $row["nivelsocio"];
    $sub_array[] = $row["ingreso"];
    $sub_array[] = $row["nuevo"];
    $sub_array[] = $row["semestre"];
    $sub_array[] = $row["num_titulo"];
    $sub_array[] = $row["promocion"];
    $sub_array[] = $row["fe_gr_alu"];
    $sub_array[] = $row["egreso"];
    $sub_array[] = $row["grupo"];
    $sub_array[] = $row["turno"];
    $sub_array[] = $row["trabajo"];
    $sub_array[] = $row["ireceptor"];
    $sub_array[] = $row["ihora"];
    $sub_array[] = $row["ifecha"];
    $sub_array[] = $row["ipensum"];
    $sub_array[] = $row["folio"];
    $sub_array[] = $row["tomo"];
    $sub_array[] = $row["cconst"];
    $sub_array[] = $row["rusnies"];
    $sub_array[] = $row["r_cupo"];
    $sub_array[] = $row["discapacid"];
    $sub_array[] = '<button type="button" name="update" id="'.$row["id"].'" class="btn btn-warning btn-xs update">Editar</button>';
    $sub_array[] = '<button type="button" name="delete" id="'.$row["id"].'" class="btn btn-danger btn-xs delete">Eliminar</button>';
    $data[] = $sub_array;
}
$output = array(
    "draw"              =>  intval($_POST["draw"]),
    "recordsTotal"      =>  $filtered_rows,
    "recordsFiltered"   =>  get_total_all_records(),
    "data"              =>  $data
);
echo json_encode($output);
?>
