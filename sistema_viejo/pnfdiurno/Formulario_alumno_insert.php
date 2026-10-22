<?php
include('db.php');
include('function.php');
if(isset($_POST["operation"]))
{
  if($_POST["operation"] == "Add")
  {
    $image = '';
    if($_FILES["user_image"]["name"] != '')
    {
      $image = upload_image();
    }
    $statement = $connection->prepare("INSERT INTO alumno (codigo,cedula,nombre,carrera,mencion,plan,pensum,actividad,sexo,edocivil,lugar,municipio,estado,procedenci,fechanac,edad,direccion,telefonoh,telefonoc,telefonot,email,tipingreso,mencionbac,anogra_bac,tipinspro,codinspro,nivelsocio,ingreso,nuevo,semestre,num_titulo,promocion,fe_gr_alu,egreso,grupo,turno,trabajo,ireceptor,ihora,ifecha,ipensum,folio,tomo,cconst,rusnies,r_cupo,discapacid) VALUES (:codigo,:cedula,:nombre,:carrera,:mencion,:plan,:pensum,:actividad,:sexo,:edocivil,:lugar,:municipio,:estado,:procedenci,:fechanac,:edad,:direccion,:telefonoh,:telefonoc,:telefonot,:email,:tipingreso,:mencionbac,:anogra_bac,:tipinspro,:codinspro,:nivelsocio,:ingreso,:nuevo,:semestre,:num_titulo,:promocion,:fe_gr_alu,:egreso,:grupo,:turno,:trabajo,:ireceptor,:ihora,:ifecha,:ipensum,:folio,:tomo,:cconst,:rusnies,:r_cupo,:discapacid)");
    $result = $statement->execute(
      array(
        ':codigo' =>  $_POST["codigo"],
        ':cedula' =>  $_POST["cedula"],
        ':nombre' =>  $_POST["nombre"],
        ':carrera' =>  $_POST["carrera"],
        ':mencion' =>  $_POST["mencion"],
        ':plan' =>  $_POST["plan"],
        ':pensum' =>  $_POST["pensum"],
        ':actividad' =>  $_POST["actividad"],
        ':sexo' =>  $_POST["sexo"],
        ':edocivil' =>  $_POST["edocivil"],
        ':lugar' =>  $_POST["lugar"],
        ':municipio' =>  $_POST["municipio"],
        ':estado' =>  $_POST["estado"],
        ':procedenci' =>  $_POST["procedenci"],
        ':fechanac' =>  $_POST["fechanac"],
        ':edad' =>  $_POST["edad"],
        ':direccion' =>  $_POST["direccion"],
        ':telefonoh' =>  $_POST["telefonoh"],
        ':telefonoc' =>  $_POST["telefonoc"],
        ':telefonot' =>  $_POST["telefonot"],
        ':email' =>  $_POST["email"],
        ':tipingreso' =>  $_POST["tipingreso"],
        ':mencionbac' =>  $_POST["mencionbac"],
        ':anogra_bac' =>  $_POST["anogra_bac"],
        ':tipinspro' =>  $_POST["tipinspro"],
        ':codinspro' =>  $_POST["codinspro"],
        ':nivelsocio' =>  $_POST["nivelsocio"],
        ':ingreso' =>  $_POST["ingreso"],
        ':nuevo' =>  $_POST["nuevo"],
        ':semestre' =>  $_POST["semestre"],
        ':num_titulo' =>  $_POST["num_titulo"],
        ':promocion' =>  $_POST["promocion"],
        ':fe_gr_alu' =>  $_POST["fe_gr_alu"],
        ':egreso' =>  $_POST["egreso"],
        ':grupo' =>  $_POST["grupo"],
        ':turno' =>  $_POST["turno"],
        ':trabajo' =>  $_POST["trabajo"],
        ':ireceptor' =>  $_POST["ireceptor"],
        ':ihora' =>  $_POST["ihora"],
        ':ifecha' =>  $_POST["ifecha"],
        ':ipensum' =>  $_POST["ipensum"],
        ':folio' =>  $_POST["folio"],
        ':tomo' =>  $_POST["tomo"],
        ':cconst' =>  $_POST["cconst"],
        ':rusnies' =>  $_POST["rusnies"],
        ':r_cupo' =>  $_POST["r_cupo"],
        ':discapacid' =>  $_POST["discapacid"],
        ':image'    =>  $image
      )
    );
require ("aud.php");
auditar("alumno_A",$_POST["CODIGO"]);
    if(!empty($result))
    {
      echo 'Data Inserted';
    }
  }
  if($_POST["operation"] == "Edit")
  {
require ("aud.php");
auditar("alumno_B",$_POST["CODIGO"]);
    $image = '';
    if($_FILES["user_image"]["name"] != '')
    {
      $image = upload_image();
    }
    else
    {
      $image = $_POST["hidden_user_image"];
    }
    $statement = $connection->prepare("UPDATE alumno SET  codigo = :codigo,cedula = :cedula,nombre = :nombre,carrera = :carrera,mencion = :mencion,plan = :plan,pensum = :pensum,actividad = :actividad,sexo = :sexo,edocivil = :edocivil,lugar = :lugar,municipio = :municipio,estado = :estado,procedenci = :procedenci,fechanac = :fechanac,edad = :edad,direccion = :direccion,telefonoh = :telefonoh,telefonoc = :telefonoc,telefonot = :telefonot,email = :email,tipingreso = :tipingreso,mencionbac = :mencionbac,anogra_bac = :anogra_bac,tipinspro = :tipinspro,codinspro = :codinspro,nivelsocio = :nivelsocio,ingreso = :ingreso,nuevo = :nuevo,semestre = :semestre,num_titulo = :num_titulo,promocion = :promocion,fe_gr_alu = :fe_gr_alu,egreso = :egreso,grupo = :grupo,turno = :turno,trabajo = :trabajo,ireceptor = :ireceptor,ihora = :ihora,ifecha = :ifecha,ipensum = :ipensum,folio = :folio,tomo = :tomo,cconst = :cconst,rusnies = :rusnies,r_cupo = :r_cupo,discapacid = :discapacid WHERE id = :id");
    $result = $statement->execute(
      array(
        ':codigo' =>  $_POST["codigo"],
        ':cedula' =>  $_POST["cedula"],
        ':nombre' =>  $_POST["nombre"],
        ':carrera' =>  $_POST["carrera"],
        ':mencion' =>  $_POST["mencion"],
        ':plan' =>  $_POST["plan"],
        ':pensum' =>  $_POST["pensum"],
        ':actividad' =>  $_POST["actividad"],
        ':sexo' =>  $_POST["sexo"],
        ':edocivil' =>  $_POST["edocivil"],
        ':lugar' =>  $_POST["lugar"],
        ':municipio' =>  $_POST["municipio"],
        ':estado' =>  $_POST["estado"],
        ':procedenci' =>  $_POST["procedenci"],
        ':fechanac' =>  $_POST["fechanac"],
        ':edad' =>  $_POST["edad"],
        ':direccion' =>  $_POST["direccion"],
        ':telefonoh' =>  $_POST["telefonoh"],
        ':telefonoc' =>  $_POST["telefonoc"],
        ':telefonot' =>  $_POST["telefonot"],
        ':email' =>  $_POST["email"],
        ':tipingreso' =>  $_POST["tipingreso"],
        ':mencionbac' =>  $_POST["mencionbac"],
        ':anogra_bac' =>  $_POST["anogra_bac"],
        ':tipinspro' =>  $_POST["tipinspro"],
        ':codinspro' =>  $_POST["codinspro"],
        ':nivelsocio' =>  $_POST["nivelsocio"],
        ':ingreso' =>  $_POST["ingreso"],
        ':nuevo' =>  $_POST["nuevo"],
        ':semestre' =>  $_POST["semestre"],
        ':num_titulo' =>  $_POST["num_titulo"],
        ':promocion' =>  $_POST["promocion"],
        ':fe_gr_alu' =>  $_POST["fe_gr_alu"],
        ':egreso' =>  $_POST["egreso"],
        ':grupo' =>  $_POST["grupo"],
        ':turno' =>  $_POST["turno"],
        ':trabajo' =>  $_POST["trabajo"],
        ':ireceptor' =>  $_POST["ireceptor"],
        ':ihora' =>  $_POST["ihora"],
        ':ifecha' =>  $_POST["ifecha"],
        ':ipensum' =>  $_POST["ipensum"],
        ':folio' =>  $_POST["folio"],
        ':tomo' =>  $_POST["tomo"],
        ':cconst' =>  $_POST["cconst"],
        ':rusnies' =>  $_POST["rusnies"],
        ':r_cupo' =>  $_POST["r_cupo"],
        ':discapacid' =>  $_POST["discapacid"],
        ':image'    =>  $image,
        ':id'     =>  $_POST["user_id"]
      )
    );
    if(!empty($result))
    {
      echo 'Data Updated';
    }
  }
}
?>
