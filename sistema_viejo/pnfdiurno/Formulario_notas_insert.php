<?php
include('db.php');
include('function.php');
if(isset($_POST["operation"]))
{
  if($_POST["operation"] == "Add")
  {
    
    $statement = $connection->prepare("INSERT INTO notas (codigo,cod_mat,nota,lapso,tiplap,cod_doc,acu) VALUES (:codigo,:cod_mat,:nota,:lapso,:tiplap,:cod_doc,:acu)");
    $result = $statement->execute(
      array(
        ':codigo' =>  $_POST["codigo"],
        ':cod_mat' =>  $_POST["cod_mat"],
        ':nota' =>  $_POST["nota"],
        ':lapso' =>  $_POST["lapso"],
        ':tiplap' =>  $_POST["tiplap"],
        ':cod_doc' =>  $_POST["cod_doc"],
        ':acu' =>  $_POST["acu"]        
      )
    );
     if(!empty($result))
    {
      echo 'Data Inserted';
    }
    
    $connection->close();

    
    
    





     //require ("aud.php");
     //auditar("notas_A",$_POST["CODIGO"]);
   



  }




}

//require ("configuracion.php");
    $servidor = "localhost";
    $usuario = "root";
    $clave = "SR19021601***";
    $base_datos = "pnfdiurno";

    date_default_timezone_set('America/Caracas');
    $hora = strftime("%I:%M:%S %p\n");
    $fecha = strftime("%x \n");

    $conn2 = new mysqli($servidor, $usuario, $clave, $base_datos);

    if ($conn2->connect_error) {
      die("Connection failed: " . $conn2->connect_error);
    }
    
   
    // $accion="Guardar";
    // $cod="1";
    // $usuario="WALTER";
    // $codigo=$_POST["codigo"];
    // $cod_mat=$_POST["cod_mat"];
    // $nota=$_POST["nota"];
    // $lapso=$_POST["lapso"];
    // $tiplap=$_POST["tiplap"];
    // $cod_doc=$_POST["cod_doc"];
    // $acu=$_POST["acu"]; 

    $accion="Guardar";
    $cod="1";
    $usuario="WALTER";
    $codigo="123";
    $cod_mat="M01AC30";
    $nota="20";
    $lapso="2018-3";
    $tiplap="T";
    $cod_doc="15";
    $acu="100";

    $sql2 = "INSERT INTO notas_auditoria (accion,cod,hora,fecha,usuario,codigo,cod_mat,nota,lapso,tiplap,cod_doc,acu) VALUES ('$accion','$cod','$hora','$fecha','$usuario','$codigo','$cod_mat','$nota','$lapso','$tiplap','$cod_doc','$acu');";
    $result2 = $conn2->query($sql2);
    $conn2->close();

?>
