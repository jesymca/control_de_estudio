<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
$titulo = "Crear Contraseña - " . ($siglas_institucion ?? "UPTPC");
include('funciones/functions.php');

$idUser =   $_REQUEST['id'] = isset($_REQUEST['id']) ? $_REQUEST['id'] : "";
$control =  $_REQUEST['control'] = isset($_REQUEST['control']) ? $_REQUEST['control'] : "";



if (empty($idUser)) {
    //echo "LO SENTIMOS";
    array_push($errors, '<i class="fa fa-exclamation-triangle"></i> Lo sentimos algo ha ocurrido, si usted desea crear la contraseña de su usuario te recomendamos accedas al correo que recibistes e intentes acceder nuevamente al link que alli te suministramos o utiliza el boton de recuperar contraseña.<hr><a type="link" class="btn btn-danger btn-sm mx-auto" href="recuperar_password.php"><i class="fa fa-key"></i> Recuperar Contraseña</a>');
    $_SESSION['msg_crear']  = 'Si posees Usuario y Clave puede acceder e iniciar sesion <a href="login.php" class="link">AQUI</a><br>';
    //header("location: login.php");


} else if (empty($control)) {
    //echo "LO SENTIMOS";
    array_push($errors, '<i class="fa fa-exclamation-triangle"></i> Lo sentimos algo ha ocurrido, si usted desea crear la contraseña de su usuario te recomendamos accedas al correo que recibistes e intentes acceder nuevamente al link que alli te suministramos o utiliza el boton de recuperar contraseña.<hr><a type="link" class="btn btn-danger btn-sm mx-auto" href="recuperar_password.php"><i class="fa fa-key"></i> Recuperar Contraseña</a>');
    $_SESSION['msg_crear']  = 'Si posees Usuario y Clave puede acceder e iniciar sesion <a href="login.php" class="link">AQUI</a><br>';
    //header("location: login.php");
   // exit();


}
else {
    global $db;

$sql = "SELECT * FROM users
WHERE id = '$idUser' AND control = '$control' ";
   $result = mysqli_query($db, $sql);
   $row = mysqli_fetch_assoc($result);


    $rowControl = $row['control'];



if ($control !== $rowControl){

    array_push($errors, '<i class="fa fa-exclamation-triangle"></i> Lo sentimos algo ha ocurrido.<hr><a type="link" class="btn btn-danger btn-sm mx-auto" href="recuperar_password.php"><i class="fa fa-key"></i> Recuperar Contraseña</a>');
   // $_SESSION['msg_crear']  = '<div class="mx-auto"><a type="link" class="btn btn-danger btn-sm" href="recuperar_password.php"><i class="fa fa-key"></i> Recuperar Contraseña</a></div>';

} else {
  $status = $row['status'];
  $motivo = $row['motivo_bloqueo'];


  if ($status == '0') {
    array_push($errors, '<i class="fa fa-exclamation-triangle"></i> Lo sentimos su usuario ha sido bloqueado por el siguiente motivo: '.$motivo.' De tal manera lamentablemente no podras hacer uso de la plataforma.<hr>');
  } else {
    $rowEmail = $row['email'];
    $rowPassword = $row['password'];
    $rowNombre = $row['nombre'];
    $username = $row['username'];
  }

}
}

?>
<!DOCTYPE html>
<html>
<head>
  <?php echo $bootstrap_head; ?>
  <!-- FAVICON EN LOGIN -->


  <link rel="apple-touch-icon" href="images/favicon/apple-touch-icon.png" sizes="180x180">
  <link rel="icon" href="images/favicon/favicon-32x32.png" sizes="32x32" type="image/png">
  <link rel="icon" href="images/favicon/favicon-16x16.png" sizes="16x16" type="image/png">

  	<link rel="icon" href="images/favicon/favicon.ico">
</head>
<body>

<div class="container text-center">

        <div class="d-flex align-items-center justify-content-between px-3"><?php echo $logopertenencia; ?><?php echo $logo_mppeu; ?></div>
       
</div>
<hr>


<div class="container-fluid">
  <div class="row">
    <div class="d-none d-sm-block col-md-6">

    <!-- <img src="./images/responsive.png" class="img-fluid ${3|rounded-top,rounded-right,rounded-bottom,rounded-left,rounded-circle,|}" alt=""> -->
    <?php
    echo $image_responsive;
    ?>
    </div>
    <div class="col-sm-12 col-md-6">
    <h3 class="text-center text-uppercase"> Crear su Password o Clave de Acceso</h3>

    <form class="was-validated" method="post" action="crear_password.php?id=<?php echo $idUser; ?>&control=<?php echo $control; ?>" autocomplete="off" name="crear_password">
  <!-- notification message -->
  <?php if (isset($_SESSION['msg_crear'])) : ?>
      <div class="alert alert-danger" role="alert">
        <h3>
          <?php
            echo $_SESSION['msg_crear'];
            unset($_SESSION['msg_crear']);
          ?>
        </h3>
      </div>
  <?php endif ?>

  <?php echo display_error();
  echo "<br>";
  ?>

  <?php if ((count($errors) == 0) || (count($error) >0)) :
  echo display_error2(); ?>

  <div class="form-group">
    <label for="password_1">Password o Contraseña</label>
    <input pattern="[a-zA-Z0-9.+_-]{6,10}" title="Debe utilizar combiaciones de Letras, Numeros y Puede utilizar los caracteres especiales: . + _ - Puede usar un minimo de 6 caracteres y un maximo de 10"
  type="password" class="form-control" id="password_1" placeholder="Password" name="password_1" required>
    <div class="invalid-feedback">Ingrese su Password o Contraseña. Por su seguridad Recomendamos que Utilice una contraseña conformada por combiaciones de Letras Pueden ser Mayusculas o Minusculas y Numeros. Su contraseña debe tener minimo 6 caracteres y un maximo de 10 caracteres. Puede utilizar los caracteres especiales: . + _ - </div>
  </div>

  <div class="form-group" >
    <label for="password_2">Repita Password o Contraseña:</label>
    <input name="password_2" onkeyup="comprobar_password();" pattern="[a-zA-Z0-9.+_-]{6,10}" title="Recomendamos que Utilice una contraseña conformada por combiaciones de Letras y Numeros y use un minimo de 6 caracteres y un maximo de 10"
  type="password" class="form-control" id="password_2" placeholder="Repita Password"  required>
  <div class="invalid-feedback">Repita su Password o Contraseña.</div>
  </div>

  <input type="hidden" name="idusuario" value="<?php echo $idUser; ?>">
  <input type="hidden" name="email" value="<?php echo $rowEmail;  ?>">
  <input type="hidden" name="control" value="<?php echo $control;  ?>">
  <input type="hidden" name="nombre" value="<?php echo $rowNombre;  ?>">
  <input type="hidden" name="username" value="<?php echo $username;  ?>">

  <button type="submit" class="btn btn-danger btn-sm" name="crear_password_btn" disabled="disabled"><i class="fa fa-key"></i> Crear Password</button>
  <div id="mensaje2"></div>
  <?php endif ?>
  </form>

    </div>
  </div>


    <script type="text/javascript">

      function comprobar_password() {
      var p1 = document.crear_password.password_1.value;
      var p2 = document.crear_password.password_2.value;

      if (p1 != p2) {
      document.getElementById("mensaje2").innerHTML = '<div class="alert alert-warning" role="alert">Ambas contraseñas deben ser iguales</div>';
      password_2.setCustomValidity("Las contraseñas no coinciden");
      $('.btn').prop('disabled', true);
      } else {
      document.getElementById("mensaje2").innerHTML = "";
      password_2.setCustomValidity('');
      $('.btn').prop('disabled', false);
      }
      }

      password_1.onchange = comprobar_password;
      password_2.onkeyup = comprobar_password;


    $('.carousel').carousel({
  interval: 8000
})
    </script>






<?php include("usuario/includes/footer.php"); ?>
