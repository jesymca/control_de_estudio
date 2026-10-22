<?php
 include('db.php');
 include('function.php');
  if(isset($_POST["user_id"]))
   {
    $output = array();
    $statement = $connection->prepare("SELECT * FROM alumno WHERE id = '".$_POST["user_id"]."' LIMIT 1");
    $statement->execute();
    $result = $statement->fetchAll();
    foreach($result as $row)
    {
      $output["codigo"] = $row["codigo"];
      $output["cedula"] = $row["cedula"];
      $output["nombre"] = $row["nombre"];
      $output["carrera"] = $row["carrera"];
      $output["mencion"] = $row["mencion"];
      $output["plan"] = $row["plan"];
      $output["pensum"] = $row["pensum"];
      $output["actividad"] = $row["actividad"];
      $output["sexo"] = $row["sexo"];
      $output["edocivil"] = $row["edocivil"];
      $output["lugar"] = $row["lugar"];
      $output["municipio"] = $row["municipio"];
      $output["estado"] = $row["estado"];
      $output["procedenci"] = $row["procedenci"];
      $output["fechanac"] = $row["fechanac"];
      $output["edad"] = $row["edad"];
      $output["direccion"] = $row["direccion"];
      $output["telefonoh"] = $row["telefonoh"];
      $output["telefonoc"] = $row["telefonoc"];
      $output["telefonot"] = $row["telefonot"];
      $output["email"] = $row["email"];
      $output["tipingreso"] = $row["tipingreso"];
      $output["mencionbac"] = $row["mencionbac"];
      $output["anogra_bac"] = $row["anogra_bac"];
      $output["tipinspro"] = $row["tipinspro"];
      $output["codinspro"] = $row["codinspro"];
      $output["nivelsocio"] = $row["nivelsocio"];
      $output["ingreso"] = $row["ingreso"];
      $output["nuevo"] = $row["nuevo"];
      $output["semestre"] = $row["semestre"];
      $output["num_titulo"] = $row["num_titulo"];
      $output["promocion"] = $row["promocion"];
      $output["fe_gr_alu"] = $row["fe_gr_alu"];
      $output["egreso"] = $row["egreso"];
      $output["grupo"] = $row["grupo"];
      $output["turno"] = $row["turno"];
      $output["trabajo"] = $row["trabajo"];
      $output["ireceptor"] = $row["ireceptor"];
      $output["ihora"] = $row["ihora"];
      $output["ifecha"] = $row["ifecha"];
      $output["ipensum"] = $row["ipensum"];
      $output["folio"] = $row["folio"];
      $output["tomo"] = $row["tomo"];
      $output["cconst"] = $row["cconst"];
      $output["rusnies"] = $row["rusnies"];
      $output["r_cupo"] = $row["r_cupo"];
      $output["discapacid"] = $row["discapacid"];
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
