<?php


function encriptar($cadena)
{
    $key='2019';  // ya no tiene utilidad debido a que la cadena (SALT), se genera aleatoriamente

    // SE SUSTITUYE EL ALGORITMO DE ENCRIPTACION DE LA LIBRERIA MCRYPT POR BLOWFISH (Password_Hash)


  //   $encrypted = password_hash($cadena, PASSWORD_DEFAULT, array("cost"=>12));
     
     // Esto se elimina para evitar la dependencia de las librerias de terceros, que no
     // son compatibles con las mas actuales verisones de PHP
     // 
      $encrypted = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($key), $cadena, 
                  MCRYPT_MODE_CBC, md5(md5($key))));

    return $encrypted;

} // fin Function encriptar



function desencriptar($cadena){
  $key='2019';

// SE SUSTITUYE EL ALGORITMO DE ENCRIPTACION DE LA LIBRERIA MCRYPT POR BLOWFISH (Password_Hash)



// $decrypted = password_verify($cadena, $encrypted);

 $decrypted = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($key), base64_decode($cadena), 
             MCRYPT_MODE_CBC, md5(md5($key))), "\0");


return $decrypted;



}


?>
