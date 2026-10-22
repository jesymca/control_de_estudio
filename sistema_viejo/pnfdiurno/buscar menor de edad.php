<?php

include('db.php');

$sql = "SELECT `cedula`,`nombre`,`carrera`,`fechanac` FROM `alumno` WHERE `fechanac` LIKE '%2002%' OR `fechanac` LIKE '%2003%' OR `fechanac` LIKE '%2004%' OR `fechanac` LIKE '%2005%' OR `fechanac` LIKE '%2006%' OR `fechanac` LIKE '%2007%' OR `fechanac` LIKE '%2008%' OR `fechanac` LIKE '%2009%' OR `fechanac` LIKE '%2010%' OR `fechanac` LIKE '%2011%' OR `fechanac` LIKE '%2012%'";

$resultado = $conn->query($sql); 


if ($resultado->num_rows > 0) {

     while($fila = $resultado->fetch_assoc()) { 
       
       switch ($fila["carrera"])  {
                        case "M":
                        $Carrera_a1="P.N.F. MECANICA";
                        $Carrera_a2="MECANICA";
                        break;
                        case "T":
                        $Carrera_a1="P.N.F. MANTENIMIENTO";
                        $Carrera_a2="MANTENIMIENTO";
                        break;
                        case "E":
                        $Carrera_a1="P.N.F. MATERIALES INDUSTRIALES";
                        $Carrera_a2="MATERIALES";
                        break;
                        case "I":
                        $Carrera_a1="P.N.F. INFORMATICA";
                        $Carrera_a2="INFORMATICA";
                        break;
                        case "G":
                        $Carrera_a1="P.N.F. TURISMO";
                        $Carrera_a2="TURISMO";
                        break;   
                        case "O":
                        $Carrera_a1="P.I.F. MECANICA TERMICA";
                        $Carrera_a2="TERMICA";
                        break;
                        case "A":
                        $Carrera_a1="P.N.F. MEC. AUTOMOTRIZ";
                        $Carrera_a2="AUTOMOTRIZ";
                        break;
                }
        
        echo $fila["cedula"]." | ".$fila["nombre"]." | ".$Carrera_a2." | ".$fila["fechanac"]."<BR>";
      

}

}

?>