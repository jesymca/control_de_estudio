<?php
 include('db.php');
 include('function.php');
  if(isset($_POST["user_id"]))
   {
    $output = array();
    $statement = $connection->prepare("SELECT * FROM notas WHERE id = '".$_POST["user_id"]."' LIMIT 1");
    $statement->execute();
    $result = $statement->fetchAll();
    foreach($result as $row)
    {
      $output["codigo"] = $row["codigo"];
      $output["cod_mat"] = $row["cod_mat"];
      $output["nota"] = $row["nota"];
      $output["lapso"] = $row["lapso"];
      $output["tiplap"] = $row["tiplap"];
      $output["cod_doc"] = $row["cod_doc"];
      $output["acu"] = $row["acu"];
      if($row["image"] != '')
      {
        $output['user_image'] = '<img src="upload/'.$row["image"].'" class="img-thumbnail" width="10" height="10" /><input type="hidden" name="hidden_user_image" value="'.$row["image"].'" />';
      }
      else
      {
        $output['user_image'] = '<input type="hidden" name="hidden_user_image" value="" />';
      }
    }
      echo json_encode($output);
   }
?>
