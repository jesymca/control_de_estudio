<?php


session_start();
unset ($SESSION['username']);


  require ("aud.php");
    auditar("0B");


session_destroy();
header('location:index.html');


?>
