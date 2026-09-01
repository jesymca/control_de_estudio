<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');


$titulo ="Ingreso al Sistema";
require_once('funciones/functions.php');

$mostrarPreinscripcion = obtenerConfiguracionSecretaria('mostrar_preinscripcion', '1');
$mostrarProsecucion = obtenerConfiguracionSecretaria('mostrar_prosecucion', '1');

?>

<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Ingreso al Sistema</title>
<?php echo $bootstrap_head; ?>



</head>
<body class="page-login">

<header class="py-2 bg-white border-bottom shadow-sm mb-3">
    <div class="container-fluid d-flex align-items-center justify-content-between px-3 px-md-4">
        <div class="header-logo-left">
            <?php echo $logopertenencia; ?>
        </div>
        <div class="header-logo-right">
            <?php echo $logo_mppeu; ?>
        </div>
    </div>
</header>


<hr>

<nav class="nav nav-pills justify-content-end"> 
        <div class="btn-group-horizontal" >
            <!-- Botón de Preinscripción -->
            <?php if ($mostrarPreinscripcion !== '0'): ?>
            <span class="d-inline-block" data-toggle="popover" data-content="Complete el formulario de preinscripción para iniciar su proceso de admisión.">
                <a type="link" class="btn btn-outline-success" href="preinscripcion.php">
                    <i class="fa fa-edit"></i> Preinscripción
                </a>
            </span>
            <?php endif; ?>
            
            <!-- Botón de Prosecución -->
            <?php if ($mostrarProsecucion !== '0'): ?>
            <span class="d-inline-block" data-toggle="popover" data-content="Continúe con su proceso de prosecución académica.">
                <a type="link" class="btn btn-outline-info" href="prosecucion.php">
                    <i class="fa fa-graduation-cap"></i> Prosecución
                </a>
            </span>
            <?php endif; ?>
            
            <!-- Botones de Recuperar Contraseña -->
            <span class="d-inline-block" data-toggle="popover" data-content="...">
                <a type="link" class="btn btn-outline-danger" href="recuperar_password.php">
                    <i class="fa fa-unlock-alt"></i> Recuperar Contraseña
                </a>
                <a type="link" class="btn btn-outline-danger" href="recuperar_password_provicional.php">
                    <i class="fa fa-unlock-alt"></i> Recuperar Contraseña provicional
                </a>
            </span>
            
        </div>
    </nav>

<hr>

<div id="main" class="container-fluid">
<div class="row">
<div class="d-none d-sm-block col-md-6 mx-auto">

 <div class="d-flex justify-content-center align-items-center">
      <?php echo $image_responsive; ?>
    </div>

</div>
<div class="col-sm-12 col-md-6">
<h3 class="text-center text-uppercase"> ACCEDA AL SISTEMA</h3>

<form class="was-validated" method="post" action="login.php" autocomplete="on">
<!-- notification message -->
<?php if (isset($_SESSION['msg'])) : ?>
<div class="alert alert-danger" role="alert" >
<h3>
<?php
echo $_SESSION['msg'];
unset($_SESSION['msg']);
?>
</h3>
</div>
<hr>
<?php endif ?>

<?php echo display_error(); ?>

<div class="form-group">
<label for="exampleInputUser">Usuario</label>
<input type="text" class="form-control no-uppercase" id="exampleInputUser" aria-describedby="UserlHelp" placeholder="Usuario o Correo Electronico" name="username" required>
<div class="invalid-feedback">Ingrese su numero de Usuario o Correo Electronico.</div>
</div>
<div class="form-group">
<label for="exampleInputPassword1">Clave:</label>
<input type="password" class="form-control no-uppercase" id="exampleInputPassword1" placeholder="Su Clave de Acceso" name="password" required>
</div>

<div class="btn-group-horizontal" >
<span class="d-inline-block" data-toggle="popover" data-content="Aca podra acceder al sistema las 24 horas del dia, los 365 dias del año.">
<button type="submit" class="btn btn-primary" name="login_btn"> <span  class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span> <i class="fa fa-sign-in-alt"></i> Acceder <span  class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span> </button>
</span>
</div> <!-- CIERRE DE GRUPO DE BOTONES 1 -->
</form>
<br>

</div>
</div>

<?php echo $footer_institucional; ?>
</body>
</html>