<?php
include 'db.php';
include 'menu.php';
?>
<head>
<link rel="stylesheet" href="css/dataTables.bootstrap.min.css" />
<script src="js/jquery.dataTables.min.js"></script>
<script src="js/dataTables.bootstrap.min.js"></script>
<script src="js/jquery.tabledit.js"></script>
</head>
<div class="container" id="marco">
    <form class="form-horizontal" id="effect2" method="post">
        <fieldset>
            <div class="form-group" id="titulo_formulario">
                <label id="titulo_formulario">Formulario inscribir_materia</label>
            </div>
            <br />
            <br />
            <div class="form-group" align="right">
                <div class="col-md-12">
                    <a href="Formulario_inscribir_materia_tabla_form_agregar.php" class="btn btn-default">Agregar</a>
                    <a href="principal.php" class="btn btn-default">Salir</a>
                </div>
            </div>

            <!--inicio de la Tabla-->
            <table id="editable_table" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th width="1%">Id</th>
                        <th width="1%">Alumno</th>
                        <th width="1%">Seccion</th>                        
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $query = "SELECT * FROM inscribir_materia ORDER BY id ASC";
                    $result = mysqli_query($conn, $query);
                    while($row = mysqli_fetch_array($result))
                    {
                        echo '<tr>
                              <td>'.$row["id"].'</td>
                              <td>'.$row["alumno"].'</td>
                              <td>'.$row["seccion"].'</td>                              
                              </tr>';
                    }
                    ?>
                </tbody>
            </table>
            <!--Fin de la Tabla-->

        </div>
    </form>
</fieldset>
</div>


</body>
</html>
<script>
    $(document).ready(function(){
        $('#editable_table').Tabledit({
            url:'Formulario_inscribir_materia_tabla_edit_delete.php',
            columns:{
                identifier:[0, "id"],
                editable:[[1,'alumno'], [2,'seccion']]
            },
            restoreButton:false,
            onSuccess:function(data, textStatus, jqXHR)
            {
                if(data.action == 'delete')
                {
                    $('#'+data.id).remove();
                }
            }
        });
    });
</script>
