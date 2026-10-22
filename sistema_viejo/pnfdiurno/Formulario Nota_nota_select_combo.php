
<?php
include('db.php'); 


$sql = "SELECT * FROM nota";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
	echo '<option value="" disabled>Selecciona</option>';
    while($row = $result->fetch_assoc()) {
        echo '<option>'.$row["nota"].'</option>';
    }
} 
$conn->close();
?>
