
<?php include 'menu.php' ?>

<style type="text/css">
   #marco
   {
       width:400px;
       min-width: 400px;
   }
</style>



 <!--Formulario-->
 <div class="container" id="marco">
 <form class="form-horizontal" id="effect2" method="post" action="Formulario_notas_tabla_insert.php">
   <fieldset>
 <div class="form-group" id="titulo_formulario"> 
       <label id="titulo_formulario">Formulario Notas</label>
</div>
            <br />
     <table>
      <tr>
        <td style="width: 10px;"></td>
        <td style="width: 100px;">
          <input type="submit" id="Guardar" class="btn btn-primary" name="submit" value="Guardar" style="background: #0C4783;width:120px"/>
        </td>
        <td style="width: 10px;"></td>
        <td style="width: 120px;">
          <a href="Formulario_notas_tabla_index.php" class="btn btn-primary" style="background: #0C4783;width:120px"><span class="glyphicon glyphicon-log-out"></span> Salir</a>
        </td>
      </tr>
    </table>
            <br />
  <!-- Text input-->
<div style="padding-left: 10px;padding-right:10px;">
<div class="form-group">
<div class="col-md-12" style="width: 100%;margin-left: 0%;margin-top:0px">
          <label for="codigo">Codigo</label>  
          <input id="codigo" name="codigo" type="text" placeholder="Codigo" class="form-control">
   </div>
 </div>
 </div>


  <!-- Text input-->
<div style="padding-left: 10px;padding-right:10px;">
<div class="form-group">
<div class="col-md-12" style="width: 100%;margin-left: 0%;margin-top:0px">
          <label for="cod_mat">Cod Mat</label>  
          <input id="cod_mat" name="cod_mat" type="text" placeholder="Cod Mat" class="form-control">
   </div>
 </div>
 </div>


  <!-- Text input-->
<div style="padding-left: 10px;padding-right:10px;">
<div class="form-group">
<div class="col-md-12" style="width: 100%;margin-left: 0%;margin-top:0px">
          <label for="carrera">Carrera</label>  
          <input id="carrera" name="carrera" type="text" placeholder="Carrera" class="form-control">
   </div>
 </div>
 </div>


  <!-- Text input-->
<div style="padding-left: 10px;padding-right:10px;">
<div class="form-group">
<div class="col-md-12" style="width: 100%;margin-left: 0%;margin-top:0px">
          <label for="nota">Nota</label>  
          <input id="nota" name="nota" type="text" placeholder="Nota" class="form-control">
   </div>
 </div>
 </div>


  <!-- Text input-->
<div style="padding-left: 10px;padding-right:10px;">
<div class="form-group">
<div class="col-md-12" style="width: 100%;margin-left: 0%;margin-top:0px">
          <label for="lapso">Lapso</label>  
          <input id="lapso" name="lapso" type="text" placeholder="Lapso" class="form-control">
   </div>
 </div>
 </div>


  <!-- Text input-->
<div style="padding-left: 10px;padding-right:10px;">
<div class="form-group">
<div class="col-md-12" style="width: 100%;margin-left: 0%;margin-top:0px">
          <label for="tiplap">Tiplap</label>  
          <input id="tiplap" name="tiplap" type="text" placeholder="Tiplap" class="form-control">
   </div>
 </div>
 </div>


  <!-- Text input-->
<div style="padding-left: 10px;padding-right:10px;">
<div class="form-group">
<div class="col-md-12" style="width: 100%;margin-left: 0%;margin-top:0px">
          <label for="cod_doc">Cod Doc</label>  
          <input id="cod_doc" name="cod_doc" type="text" placeholder="Cod Doc" class="form-control">
   </div>
 </div>
 </div>


  <!-- Text input-->
<div style="padding-left: 10px;padding-right:10px;">
<div class="form-group">
<div class="col-md-12" style="width: 100%;margin-left: 0%;margin-top:0px">
          <label for="cod_usu">Cod Usu</label>  
          <input id="cod_usu" name="cod_usu" type="text" placeholder="Cod Usu" class="form-control">
   </div>
 </div>
 </div>


  <!-- Text input-->
<div style="padding-left: 10px;padding-right:10px;">
<div class="form-group">
<div class="col-md-12" style="width: 100%;margin-left: 0%;margin-top:0px">
          <label for="acu">Acu</label>  
          <input id="acu" name="acu" type="text" placeholder="Acu" class="form-control">
   </div>
 </div>
 </div>


  <!-- Text input-->
<div style="padding-left: 10px;padding-right:10px;">
<div class="form-group">
<div class="col-md-12" style="width: 100%;margin-left: 0%;margin-top:0px">
          <label for="seccion">Seccion</label>  
          <input id="seccion" name="seccion" type="text" placeholder="Seccion" class="form-control">
   </div>
 </div>
 </div>


 </form>
 </div>
   </fieldset>
  </form>
<script src="js/jquery-3.1.1.min3.js"></script>
<script src="js/bootstrap.min.js"></script>


<script type="text/javascript">
$(document).ready(function() {

 });

</script>
</body>
</html>
