<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Agenda </title>
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto|Varela+Round">
<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<link rel="stylesheet" href="css/custom.css">
</head>
<body>

    <div class="container">
<?php

include('../../../funciones/functions.php');
//include('../../../funciones/operadora_sin_plan.php');
//include('../../../funciones/test.php');

          error_reporting(E_ALL);
          ini_set('display_errors', '1');

if (empty($id_usua)) {
echo '<div class="alert alert-danger" role="alert"><span class="glyphicon glyphicon-warning-sign" aria-hidden="true"></span> <b>Lo sentimos..!</b> Usted No posee autorizacion de ver el contenido de esta web.</div>';
    die();
}
          //echo 'El id Cliente es: '.$id_usua;
           ?>

        <div class="table-wrapper">
            <div class="table-title">
                <div class="row">

		<a href="#addProductModal" class="btn btn-success" data-toggle="modal"><i class="material-icons">&#xE147;</i>
		<span>Agregar nuevo Numero a Agenda</span>
		</a>
                </div>
            </div>
<div class='col-sm-4 pull-right'>
	<div id="custom-search-input">
    <div class="input-group col-md-12">
        <input type="text" class="form-control" placeholder="Buscar"  id="q" onkeyup="load(1);" />
        <span class="input-group-btn">
            <button class="btn btn-info" type="button" onclick="load(1);">
                <span class="glyphicon glyphicon-search"></span>
            </button>
        </span>
    </div>
</div>
</div>
	<div class='clearfix'></div>
	<hr>
	<div id="loader"></div><!-- Carga de datos ajax aqui -->
	<div id="resultados"></div><!-- Carga de datos ajax aqui -->
	<div class='outer_div'></div><!-- Carga de datos ajax aqui -->


</div>
</div>
	<!-- Edit Modal HTML -->
	<?php include("html/modal_add.php");?>
	<!-- Edit Modal HTML -->
	<?php include("html/modal_edit.php");?>
	<!-- Delete Modal HTML -->
	<?php include("html/modal_delete.php");?>
	<script src="js/script.js"></script>
</body>
</html>
