
<?php
session_start();
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true && $_SESSION['historiales']==1) {
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
<html>
<head>
  <style>
    body    

 

  </style>
</head>
<body>
 <div class="container" id="marco" style="width:50%">
   <form class="form-horizontal" id="effect2" method="post" action="REPORTE DE CARGA DE NOTAS2.php">
    <fieldset>
     <div class="form-group" id="titulo_formulario"> 
      <label id="titulo_formulario"><span class="fa-building-o fa"></span> Docentes sin carga de notas</label>
    </div>

    <center><table >
      <tr>
  
          <td style="width: 300px;"> 
          	<div class="selector-lapso">
          		<div class="form-group">
          			<div class="col-md-12" style="width: 120px;margin-left: 0%;margin-top:15px">
          				<label for="buscar">Lapso</label>  
          				<input id="buscar2" name="buscar2" type="text" placeholder="buscar" class="form-control" required>
          			</div>
          		</div>

          		<div class="form-group">
          			<div class="col-md-12" style="width: 120px;margin-top: -52px;margin-left: 100px">
          				<select class="form-control" id="selectlapso" style="background-color:#F0F0F0">											
          				</select>
          			</div>
          		</div>
          	</div>

        </td>
              
        <td style="width: 5px;"></td> 
        <td>
          <div class="form-group">
            <div class="col-md-12" style="margin-top: 27px;margin-left: -100px">                                
              
               <input type="submit" class="btn btn-primary" name="submit" value="Imprimir" style="background: #0C4783;width:120px"/>          
            </div>
          </div>
        </td>
      
   
      <td></td>

      <td width="100">
        <div class="form-group">
          <div class="col-md-12" style="width: 100%;margin-left:10%;margin-top:27px">
            <a href="principal.php" class="btn btn-primary" style="width: 100px;background: #0C4783;"><span class="glyphicon glyphicon-log-out"></span> Salir</a>
          </div>
        </div>
      </td>
    </tr>
  </table>


  <table id="editable_table" class="table table-bordered table-striped"></table></center>


</form>
</div>
</fieldset>
</form>


<script type="text/javascript">
  $(document).ready(function() {

    $.ajax({
			type: "POST",
			url: "getlapso_0.php",
			success: function(response)
			{
				$('.selector-lapso select').html(response).fadeIn();
			}
		});

    $('#selectlapso').click(function(){
			var v = $(this).val(); 
			$('#buscar2').val(v);
		}); 

  });
</script>

</body>
</html>


        