
<?php
include('db.php');



$sql = "SELECT DISTINCT fe_gr_alu FROM alumno WHERE `fe_gr_alu` != ''";
$result = $conn->query($sql);
if ($result->num_rows > 0) {

	echo '<option value="0" disabled selected>Selecciona una fecha</option>';
    while($row = $result->fetch_assoc()) {
        echo '<option value='.$row["fe_gr_alu"].'>'.$row["fe_gr_alu"].'</option>';
    }
} 
$conn->close();
?>
