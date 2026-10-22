
<?php
include('db.php');

$carrera=substr($_POST["pensum"], 0, 1);
$fe_gr_alu=$_POST["fe_gr_alu"];
$marca=$_POST["marca"];

$sql = "SELECT DISTINCT marca FROM alumno where fe_gr_alu='".$fe_gr_alu."' and carrera='".$carrera."' ORDER BY marca ASC";//and fe_gr_alu<>''  carrera='".$carrera."' and fe_gr_alu='".$fe_gr_alu."' and 
$result = $conn->query($sql);
if ($result->num_rows > 0) {

	echo '<option value="0" disabled selected>Selecciona la marca</option>';
    while($row = $result->fetch_assoc()) {
        echo '<option value='.$row["marca"].'>'.$row["marca"].'</option>';
    }
} 
$conn->close();
?>
