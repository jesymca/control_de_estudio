
<?php
session_start();
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true) {
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
?>

 <div class="container" id="marco">
 <form class="form-horizontal" id="effect2" method="post" action="lista.php">
  <fieldset>
 <div class="form-group" id="titulo_formulario"> 
    <label id="titulo_formulario">Buscar alumno</label>
</div>


<center><table>
	<tr>
	<td width="100">		
  <!-- Text input-->
<div class="form-group">
<div class="col-md-12" style="width: 100%;margin-left: 0%;margin-top:0px">
		   <label for="Codigo">Codigo alumno</label>  
		   <input id="Codigo" name="Codigo" type="text" placeholder="Codigo" class="form-control">
   </div>
 </div>
</td>

<td width="10">
</td>

<td width="100">
<div class="form-group">
  <div class="col-md-12" style="width: 100%;margin-left: 26%;margin-top:23px">
  
<input type="submit" class="btn btn-default" name="submit" value="Imprimir" id="divcontenido1button"/> 
  
  </div>
 </div>
</td>

<td width="20">
</td>
<td width="100">
<div class="form-group">
  <div class="col-md-12" style="width: 100%;margin-left:10%;margin-top:23px">
  

  <a href="principal.php" class="btn btn-default">Cerrar</a>
  </div>
 </div>
</td>

</tr>
 </tr>
</table></center>









  </form>
 </div>
 </fieldset>
  </form>


</body>
</html>
