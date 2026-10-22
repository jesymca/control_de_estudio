<?php
session_start();
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true && $_SESSION['notas']==1) {
} else {
  header("Location: index.html");
  exit;
}
$now = time();
if($now > $_SESSION['expire']) {
  session_destroy();
  echo "Su sesion a terminado,<a href='index.html'>Necesita Hacer Login</a>";
  exit;
}

include 'menu.php';
include('/Classes/class_api.php');
$con=new PDF();

include "db.php";
$id = intval($_GET['id']);
if ($id=="") {
  $id=$_SESSION['id'];
}

$sql = "SELECT * FROM alumno WHERE id='".$id."'";
$resultado = $conn->query($sql);

while($fila = $resultado->fetch_assoc()) {                            
  $cedula=$fila["cedula"];
  $carrera=$fila["carrera"];      
} 
$pensum = $con->pensum($carrera);

?>

<html>
<head>
  <style>
    #marco{
      width:90%;
      min-width: 700px;           
    }
  </style>
</head>
<body>

  <?php
  $con->marco_superior_formulario("SERVICIOS_B10.php","Expendiente del alumno");
  ?>

  <br/>

  <div class="form-group" align="left" style="margin-top:25px;">                                          
    <div class="col-md-12">
      <input type="hidden" id="id" name="id" value="<?php echo $id?>">
      <input type="hidden" id="pensum" name="pensum" value="<?php echo $pensum?>">
      <input type="hidden" id="cedula" name="cedula" value="<?php echo $cedula;?>">
      <input name="codigo" type="text"  class="form-control" value="<?php echo $cedula?>" style="display: none;">
      <a href="SERVICIOS_B.php?id=<?php echo $id?>pensum=<?php echo $pensum?>" class="btn btn-primary" style="width:115px;margin-left: 10px"><span class="glyphicon glyphicon-print"></span> Historial</a>
      <a href="Formulario cambiar_seccion_alumno.php?id=<?php echo $id?>" class="btn btn-primary" style="width: 180px;margin-left: 10px"><span class="glyphicon glyphicon-list"></span> Cambiar Seccion</a>
      <input type="submit" class="btn btn-primary" name="submit" value="Agregar" style="width: 100px;margin-left: 10px"/> 
	  

	  <a href="SERVICIOS_REVICION.php?id=<?php echo $id?>" class="btn btn-primary" style="width: 240px;margin-left: 10px"><span class="glyphicon glyphicon-list"></span> Constancia de Inscripción </a>
      
      <a href="SERVICIOS2.php" class="btn btn-primary" style="width: 100px;margin-left: 10px"><span class="glyphicon glyphicon-log-out"></span> Salir</a>
    
	
	</div>
  </div>

  <div class="form-group">
   <div class="col-md-4" style="margin-left: 10px;margin-top:0px">
    <label for="lapso">Filtrar por Lapso</label>     
    <input type="hidden" id="lapso" name="lapso"> 
    <div class="selector-lapso">   
     <select style="width:100px;height: 38px;margin-top:0px;margin-left: 0px;"></select>   
   </div> 
 </div>
</div>

<div class="form-group">
   <div class="col-md-4" style="margin-left: 140px;margin-top:-81px">
    <label for="carrera">Filtrar por Carrera</label>     
    <input type="hidden" id="carrera" name="carrera"> 
    <div class="selector-carrera">   
     <select style="width:200px;height: 38px;"></select>   
   </div> 
 </div>
</div>

<div class="form-group">
   <div class="col-md-4" style="margin-left: 370px;margin-top:-96px">
    <label for="cod_mat">Filtrar por Materia</label>     
    <input type="hidden" id="cod_mat" name="cod_mat"> 
    <div class="selector-cod_mat">   
     <select style="width:300px;height: 38px;"></select>   
   </div> 
 </div>
</div>

</form>

<?php
$con->titulo_tabla_notas($id);
$con->tabla_contenido_notas($pensum,$cedula,$lapso);
?>

</fieldset>
</div> 

<script src="Formulario Lismat_cod_mat_combo_buscar.js"></script>

<script type="text/javascript">
  $(document).ready(function() {

      buscarlapso($('#cedula').val(),$('#carrera').val());
    $('.selector-lapso select').change(function(){      
      var v = $(this).val();
      $('#lapso').val(v);     
      mostrar_seccion_tabla($("#cedula").val(),$("#lapso").val(),$("#pensum").val());  
    }); 

      buscarcarrera($('#cedula').val());
    $('.selector-carrera select').change(function(){          
      var v = $(this).val();
      $('#carrera').val(v);
      $('#pensum').val(v+"XC");     
      mostrar_carrera_tabla($("#cedula").val(),$("#carrera").val(),$("#pensum").val());  
      buscarmateria($('#cedula').val(),$('#carrera').val());
    }); 

      
    $('.selector-cod_mat select').change(function(){          
      var v = $(this).val();
      $('#cod_mat').val(v);     
      mostrar_materia_tabla($("#cedula").val(),$("#cod_mat").val(),$("#pensum").val());  
    }); 


  });

</script>
</body>
</html>