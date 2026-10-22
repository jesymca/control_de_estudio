<?php


//auditar("GUARDAR","9999","NOTAS","WALTER","I","M06AB30","20");

function auditar($accion,$cod_alumno,$ventana,$cod_usuario,$carrera,$cod_mat,$nota)
{

    date_default_timezone_set('America/Caracas');

    switch ($accion) {

        case $accion:

        $hora=strftime("%I:%M:%S %p\n");
        $fecha = strftime("%x \n");               
        $hora=strftime("%I:%M:%S %p\n");
        $fecha = strftime("%x \n");

        include('db.php');
        $sql = "INSERT INTO auditor (cod_usuario,ventana,accion,cod_mat,hora,fecha,cod_alumno,carrera,nota_pre,nota_pos,nota)
        VALUES ('$cod_usuario','$ventana','$accion','$cod_mat','$hora','$fecha','$cod_alumno','$carrera','$nota_pre','$nota_pos','$nota')";
        if ($conn->query($sql) === TRUE) {

        } else {
            echo $conn->error;
        }
        $conn->close();
        break;        
    }
}

?>