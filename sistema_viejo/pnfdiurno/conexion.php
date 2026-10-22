<?php

require ("configuracion.php");
$sql2 = new conectarMySQL('$servidor', '$usuario', '$clave', '$base_datos');

class conectarMySQL {
   var $servidor;
   var $usuario;
   var $password;
   var $bd;
   var $consulta;
   var $enlace;
   var $resultado;
   var $datos;

   function conectarMySQL($servidor,$usuario,$password,$bd) {
       $this->servidor=$servidor;
       $this->usuario=$usuario;
       $this->password=$password;
       $this->bd=$bd;
  }
  function conectar() {
     if($this->enlace=mysql_connect($this->servidor,$this->usuario,$this->password)) {
         if(mysql_select_db($this->bd,$this->enlace)) {
         } else {
             echo "No se ha podido seleccionar la bd";
         }
     } else {
         echo "No se ha podido conectar a la bd";
    }
 }
  function consultar($query) {
      $this->consulta=mysql_query($query,$this->enlace) or die (mysql_error());
  }
  function obtendatos() {
      $this->resultado=mysql_fetch_array($this->consulta);
      return $this->resultado;
  }
  function numerodedatos() {
      $this->datos=mysql_num_rows($this->consulta);
      return $this->datos;
  }
  function cerrarconexion() {
      mysql_close($this->enlace);
  }
  function limpiaconsulta() {
      mysql_free_result($this->consulta);
  }
}
?>
