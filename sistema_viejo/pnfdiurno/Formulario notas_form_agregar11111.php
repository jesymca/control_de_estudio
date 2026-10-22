<?php
session_start();
if(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true && $_SESSION['notas_guardar'] == 1)
{
} //isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true && $_SESSION['notas_guardar'] == 1
else
{
header("Location: index.html");
exit;
}
$now = time();
if($now > $_SESSION['expire'])
{
session_destroy();
echo "Su sesion a terminado,<a href='index.html'>Necesita Hacer Login</a>";
exit;
} //$now > $_SESSION['expire']
include 'menu.php';
if($_POST['cedula'] == "")
{
$cedula = $_SESSION['alumno_sec'];
} //$_POST['cedula'] == ""
else
{
$_SESSION['alumno_sec'] = $_POST['cedula'];
$cedula = $_SESSION['alumno_sec'];
}
$grado = $_POST["grado"];
$cedula = $_POST["cedula"];
$pensum = $_POST["pensum"];

// echo $grado . "<br>";
// echo $cedula . "<br>";
// echo $pensum . "<br>";

require("db.php");
$query = "SELECT * FROM alumno WHERE cedula='" . $cedula . "'";
$result = mysqli_query($conn, $query);
while($row = mysqli_fetch_array($result))
{
$carrera = $row["carrera"];
} //$row = mysqli_fetch_array($result)
include('/Classes/class_api.php');
$con = new PDF();
$pensum = $con->pensum($carrera);
?>

<style type="text/css">
#marco{
    width:800px;
    min-width: 800px;               
    max-width: 800px;               
}
#titulo_formulario{
    width:100%;
}
</style>
<title></title>
</head>
<body>
<center>
<table>
<tr>
<td><!--Formulario-->
<div class="container" id="marco">
<form class="form-horizontal" name="informacion" id="effect2" method="post" action="Formulario%20notas_insertc.php">
<fieldset>
<div class="form-group" id="titulo_formulario">
<label id="titulo_formulario">Agregar nota</label>
<input type="hidden" id="codigo1" name="codigo1" value="<?php echo $cedula; ?>" /> 
<input id="pensum" name="pensum" type="hidden" value="<?php echo $pensum; ?>" />
</div>
<br />
<table>
<tr>
<td style="width: 20px;"></td>
<td style="width: 250px;">
<div class="form-group">
<div class="col-md-12">
<input type="submit" class="btn btn-primary" name="submit" id="Guardar" value="Guardar" style="background: #0C4783;width:120px" /> 
<a href="Formulario_notas_tabla_index.php" class="btn btn-primary" style="background: #0C4783;width:120px">Salir</a>
</div>
</div>
</td>
</tr>
</table>
<table>
<tr>
<td style="width: 20px;"></td>
<td style="width: 70px;">
<div class="form-group">
<div class="col-md-12" style="width: 150px;margin-top:0px"><label for="codigo">Cedula</label> <input type="hidden" id="pensum" name="pensum" value="<?php echo $pensum; ?>" /> <input type="hidden" id="codigo" name="codigo" value="<?php echo $cedula; ?>" /> <input id="codigo1" name="codigo1" type="text" placeholder="Codigo" value="<?php echo $cedula; ?>" class="form-control" style="background-color:#F0F0F0" readonly="readonly" /></div>
</div>
</td>
<td style="width: 25px;"></td>
<td style="width: 180px;">
<div class="form-group">
<div class="col-md-12" style="width:0%;margin-top:0px;margin-left: -15px"><label for="cod_doc">Docente</label> <input type="hidden" id="cod_doc" name="cod_doc" /></div>
<div class="selector-cod_doc"></div>
</div>
</td>
<td style="width: 40px;"></td>
<td style="width: 350px;">
<div class="form-group">
<div class="col-md-12" style="width:0%;margin-top:0px;margin-left: -15px"><label for="cod_mat">Materia</label> <input type="hidden" id="cod_mat" name="cod_mat" /></div>
<div class="selector-cod_mat"></div>
</div>
</td>
</tr>
</table>
<table>
<tr>
<td style="width: 35px;"></td>
<td style="width: 70px;">
<div class="form-group">
<div class="col-md-12" style="width:0%;margin-top:0px;margin-left: -15px"><label for="lapso">Lapso</label> <input type="hidden" id="lapso" name="lapso" /></div>
<div class="selector-lapso"></div>
</div>
</td>
<td style="width: 40px;"></td>
<td style="width: 100px;">
<div class="form-group">
<div class="col-md-12" style="width:0%;margin-top:0px;margin-left: -15px"><label for="seccion">Seccion</label> <input type="hidden" id="seccion" name="seccion" /></div>
<div class="selector-seccion"></div>
</div>
</td>
<td style="width: 13px;"></td>
<td style="width: 250px;">
<div class="form-group">
<div class="col-md-12" style="width: 100%;margin-top:-33px"><label for="electiva">Electiva</label> <input type="hidden" id="electiva" name="electiva" />
<div class="selector-electiva"></div>
</div>
</div>
</td>
<td style="width: 20px;"></td>
<td style="width: 100px;">
<div class="form-group">
<div class="col-md-12" style="width:0%;margin-top:0px;margin-left: -15px"><label for="nota">Nota</label> <input type="hidden" id="nota" name="nota" /></div>
<div class="selector-nota"></div>
</div>
</td>
<td style="width: 13px;"></td>
<td style="width: 80px;">
<div class="form-group">
<div class="col-md-12" style="width: 120px;margin-top:0px"><label for="acu">Acu</label> <input type="hidden" id="acu" name="acu" /> <input id="acu1" name="acu1" type="text" placeholder="Acu" class="form-control" style="background-color:#F0F0F0;" readonly="readonly" /></div>
</div>
</td>
</tr>
</table>
</fieldset>
</form>
</div>
</td>
</tr>
</table>
</center>
<center>
<table>
<tr>
<td>
<div class="form-group" id="marco2"><img src="pnf.jpg" width="200" height="500" /></div>
</td>
</tr>
</table>
</center>
<noscript id="noscript_container">
<div id="noscript" class="error">
<p>JavaScript support is needed to use this page.</p>
</div>
</noscript>
<div class="container" id="loading_container" style="width: 400px">
<form class="form-horizontal">
<fieldset>
<div class="form-group" id="titulo_formulario"><label id="titulo_formulario">Informacion</label></div>
<div id="loading_container2">
<center>
<h4>Cargando por favor espere...</h4>
</center>
</div>
</fieldset>
</form>
</div>
<script type="text/javascript">
//<![CDATA[
$(document).ready(function() {
        hide_loading_message();
        
        buscardocente();

        function buscardocente(){
                var parametros = {                                                 
                "opcion":"getdocente",
                "campos":"docente"
                }                               
                $.ajax({
                data:parametros,
                type: "POST",
                url: "getselect.php",        
                success: function(response)
                {
                    $(".selector-cod_doc select").html(response).fadeIn();
                }
                });  
        }

        $(".selector-cod_doc select").change(function() {
                var v = $(this).val();     
                $('#cod_doc').val(v);                           
        }); 

        buscarmateria($('#pensum').val(),$('#codigo1').val(),$('#grado').val());

        function buscarmateria(val1,val2,val3){
                var parametros = {
                "pensum":val1, 
                "cedula":val2,
                "grado":val3,                                                                   
                "opcion":"getmateria_no_vista"
                }
                $.ajax({
                data:parametros,
                type: "POST",
                url: "getselect.php",        
                success: function(response)
                {
                    $('.selector-cod_mat select').html(response).fadeIn();
                }
                });
        }

        $('.selector-cod_mat select').change(function(){
                var v = $(this).val();     
                $('#cod_mat').val(v);
                $('#electiva').val('');      
                $('.selector-electiva select').empty();
                buscarelectiva($('#pensum').val(),$(".selector-cod_mat select").val());                                             
        }); 

        function buscarelectiva(val1,val2){                                                                                             
                var parametros = {
                "pensum":val1,
                "cod_mat":val2,                                 
                "opcion":"getelectivas" 
                }
                $.ajax({
                data:parametros,
                type: "POST",
                url: "getselect.php",        
                success: function(response)
                {
                    $('.selector-electiva select').html(response).fadeIn();
                }
                });
        }
        $('.selector-electiva select').change(function(){
                var v = $(this).val(); 
                $('#electiva').val(v);
        });

        $(".selector-lapso select").change(function() {       
                var v = $(this).val(); 
                $('#lapso').val(v);                             
        });

        buscarlapso();

        function buscarlapso(){
                var parametros = {                                      
                "opcion":"getlapso", 
                "campos":"lapso"      
                }
                $.ajax({
                data:parametros,
                type: "POST",
                url: "getselect.php",        
                success: function(response)
                {             
                    $('.selector-lapso select').html(response).fadeIn();
                }
                });
        }

        buscarseccion($('#pensum').val());

        function buscarseccion(val1){
                var parametros = {
                "pensum":val1,                                      
                "opcion":"getnumseccion" 
                }
                $.ajax({
                data:parametros,
                type: "POST",
                url: "getselect.php",        
                success: function(response)
                {
                    $('.selector-seccion select').html(response).fadeIn();
                }
                });
        }

        $(".selector-seccion select").change(function() {       
                var v = $(this).val(); 
                $('#seccion').val(v);                           
        });



        $(".selector-nota select").change(function() {       
                var v = $(this).val(); 
                $('#nota').val(v);
                var nota = $("#nota").val();
                if(nota>20){
                swal("Nota invalida");                
                }
                else{
                calcular(nota);
                }
        });

        function calcular(valor) {     
                total= parseInt(valor)*5; 
                if(total>=0){
                document.getElementById('acu').value=total;
                document.getElementById('acu1').value=total;
                }else{
                document.getElementById('acu').value=0; 
                document.getElementById('acu1').value=0; 
                }
        }

        $.ajax({
                type: "POST",
                url: "Formulario Nota_nota_select_combo.php",
                success: function(response)
                {
                $('.selector-nota select').html(response).fadeIn();
                }
        });


        // Show loading message
        function show_loading_message(){
          $('#marco').hide();                                             
          $('#loading_container').show();
        }
        // Hide loading message
        function hide_loading_message(){
          $('#loading_container').hide();
          $('#marco').show();                        
        }

});

//]]>
</script>
</body>
</html>
