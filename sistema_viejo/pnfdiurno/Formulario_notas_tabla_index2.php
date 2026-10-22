
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
include "db.php";
include 'menu.php';


$id = $_POST['id'];
$cod_mat = $_POST['cod_mat'];

if ($id=="") {
  $id=$_SESSION['id']; 
}
if ($cod_mat=="") { 
  $cod_mat=$_SESSION['cod_mat_sec'];
}else{
  $_SESSION['cod_mat_sec']=$cod_mat;
}

$query = "SELECT * FROM alumno WHERE id='".$id."'";
$result = mysqli_query($conn, $query);
while($row = mysqli_fetch_array($result))
{                
 $cedula=$row["cedula"];
 $carrera=$row["carrera"];
}


include('/Classes/class_api.php');
$x=new PDF();
$pensum = $x->pensum($carrera);



$sql = "SELECT * FROM lismat WHERE pensum='".$pensum."' GROUP BY id ASC";
$resultado = $conn->query($sql);

$x1=0;
if ($resultado->num_rows > 0) {
  while($fila = $resultado->fetch_assoc()) { 
    $lismat_cod_mat[$x1]=$fila['cod_mat'];      
    $descrip2[$x1]=$fila['descrip2'];
    $creditos[$x1]=$fila['creditos'];
    $semestre[$x1]=$fila['semestre'];     
    $nota_cat[$x1]=$fila['nota'];
    $divicion[$x1]=$fila['divicion'];
    $trayecto[$x1]=$fila['trayecto'];
    $aprobatori[$x1]=$fila['aprobatori'];
    $electiva[$x1]=$fila['electiva'];
    $x1=$x1+1;  
  }
}


for ($i=0; $i <count($lismat_cod_mat) ; $i++) { 
  if($lismat_cod_mat[$i]==$carrera."R".$cod_mat."C"){                                                                                                                                                         
   $resumida=$x->calcular_resumida($pensum,$X1B,$lismat_cod_mat[$i],$cedula,$X1B,$descrip2[$i],$semestre[$i],$creditos[$i],$trayecto[$i],$aprobatori[$i],$divicion[$i],$cod_mat_libro_rector[$i],$electiva[$i],"false","",$cant,$apro);
   list($nota_resumida, $lapso_resumida,$tipo_resumida) = split('[|]', $resumida);
 }  
}

if($resumida<>"IN"){
	$resumida=round($resumida, 0, PHP_ROUND_HALF_UP);
}

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

  <div class="container" id="marco">
   <form class="form-horizontal" id="effect2" method="post" action="Formulario notas_form_agregar.php">

    <fieldset>
     <div class="form-group" id="titulo_formulario">
      <label id="titulo_formulario">Expendiente del alumno</label>
    </div>

    <br />

    <div class="form-group" align="right" style="margin-top:0px;">                                          
      <div class="col-md-12">
       <input type="hidden" id="pensum" name="pensum" value="<?php echo $pensum?>">
       <input type="hidden" id="cedula" name="cedula" value="<?php echo $cedula;?>">
       <input type="hidden" id="cod_mat" name="cod_mat" value="<?php echo $cod_mat;?>">
       <input type="submit" class="btn btn-primary" name="submit" value="Agregar" style="width: 120px;background: #0C4783;right: 10px"/> 
       <a href="SERVICIOS2D.php" class="btn btn-primary" style="background: #0C4783;right: 10px"><span class="glyphicon glyphicon-log-out"></span> Salir</a>
       <input name="codigo" type="text"  class="form-control" value="<?php echo $cedula?>" style="display: none;">

     </div>
   </div>
   

   <table class="responstable">
    <thead>
      <tr>
        <th width="10%">Codigo</th>
        <th width="10%">Cedula</th>
        <th width="30%">Nombre</th>
        <th width="1%">Carrera</th>                        
        <th width="1%">Mencion</th>
        <th width="1%">Plan</th>                  
        <th width="1%">Actividad</th>
        <th width="1%">Turno</th>

      </tr>
    </thead>
    <tbody>
      <?php
      $query = "SELECT * FROM alumno WHERE id='".$id."'";
      $result = mysqli_query($conn, $query);
      while($row = mysqli_fetch_array($result))
      {
       $_SESSION['id']=$row["id"];
       $_SESSION['pensum']=$row["carrera"].$row["mencion"].$row["plan"];
       $cedula=$row["cedula"];
       $carrera= $row["carrera"];

       echo '<tr>
       <td >'.$row["id"].'</td>
       <td>'.$row["cedula"].'</td>
       <td>'.$row["nombre"].'</td>
       <td>'.$row["carrera"].'</td>                             
       <td>'.$row["mencion"].'</td>
       <td>'.$row["plan"].'</td>                              
       <td>'.$row["actividad"].'</td>
       <td>'.$row["turno"].'</td>                              
     </tr>';
   }
   if($carrera!="G"){
    $Semestre="Trimestre";
  }else{
    $Semestre="Semestre";
  }
  ?>
</tbody>
</table>


<table class="responstable">
  <thead>
    <tr>
      <th width="10%">Nota resumida</th>
      <th width="10%">Lapso resumida</th>
      <th width="30%">Tipo resumida</th>  
    </tr>
  </thead>
  <tbody>
    <?php
    echo '<tr>       
    <td>'.$nota_resumida.'</td>
    <td>'.$lapso_resumida.'</td>
    <td>'.$tipo_resumida.'</td>                                      
  </tr>';    
  ?>
</tbody>
</table>

<table id="editable_table" class="table table-bordered table-striped">
  <thead>
    <tr>
      <th width="1%" style="display: none;">Id</th>
      <th width="1%">Codigo</th>
      <th width="20%">Asignatura</th>      
      <th width="1%">Nota</th>
      <th width="1%">Acu</th>
      <th width="1%">Lapso</th>
      <th width="1%">Tipo</th>
      <th width="1%">Docente</th>
      <th width="1%"><?php echo $Semestre;?></th>
      <th width="1%">Trayecto</th>
      <th width="1%">Seccion</th>
      <th width="1%">Editar</th>
      <th width="1%">Eliminar</th>

    </tr>
  </thead>
  <tbody>

    <?php
    $cod_mat = $_POST['cod_mat'];                              

    $query = "SELECT lismat.semestre,lismat.trayecto,lismat.descrip2,notas.id,notas.codigo,notas.nota,notas.lapso,notas.tiplap,notas.cod_mat,notas.seccion,notas.acu,notas.cod_doc FROM notas,lismat WHERE notas.cod_mat = lismat.cod_mat and notas.codigo='".$cedula."' and SUBSTRING(notas.cod_mat,3,2)='".$_SESSION['cod_mat_sec']."' ORDER BY notas.lapso ASC";
    $result = mysqli_query($conn, $query);
    while($row = mysqli_fetch_array($result))
    {
     echo '<tr>                     
     <td style="display: none;">'.$row["id"].'</td>     
     <td>'.$row["cod_mat"].'</td>
     <td>'.$row["descrip2"].'</td>     
     <td>'.$row["nota"].'</td>
     <td>'.$row["acu"].'</td>
     <td>'.$row["lapso"].'</td>                             
     <td>'.$row["tiplap"].'</td>
     <td>'.$row["cod_doc"].'</td>  
     <td>'.$row["semestre"].'</td> 
     <td>'.$row["trayecto"].'</td> 
     <td>'.$row["seccion"].'</td>
     <td><a href="Formulario notas_form_editarb.php?action=editar&id='.$row['id'].'"  data-toggle="tooltip" title="Editar datos" class="btn btn-sm btn-info" style="background: #FF9800;width:45px"><span class="glyphicon glyphicon-pencil"></span></a></td>
     <td><a data-toggle="tooltip" title="Eliminar" class="btn btn-sm btn-danger" onClick="Borrar('.$row['id'].')"><span class="glyphicon glyphicon-trash" style="background: #E51C23;width:25px"></span></a></td></tr>';
   }
   $conn->close();    
   ?>
 </tbody>
</table>

</fieldset>
</form>
<br />
<br />
</div> 

<script src="Formulario Lismat_cod_mat_combo_buscar2.js"></script>

<script type="text/javascript">
  $(document).ready(function() {

   var parametros = {
    "cedula":$('#cedula').val()                
  }

  $.ajax({
    data:parametros,
    type: "POST",
    url: "getcod_mat.php",
    success: function(response)
    {
      $('.selector-cod_mat select').html(response).fadeIn();
    }
  }); 

});

</script>
</body>
</html>