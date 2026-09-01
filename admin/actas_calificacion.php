<?php
require_once('../funciones/functions.php');

cargarPermisosUsuario();
verificarPermiso('actas_calificacion');
visita();
error_reporting(E_ALL);
ini_set('display_errors', 1);

$titulopag = "Actas de Calificación Final";

// --- LÓGICA AJAX ---
if (isset($_GET['action'])) {
    if (ob_get_length()) ob_clean();

    if ($_GET['action'] == 'get_materias') {
        $carrera_id = $_GET['carrera_id'] ?? '';
        $query = "SELECT m.id_materia, m.nombre_materia FROM materias m 
                  INNER JOIN carrera_materia cm ON m.id_materia = cm.id_materia 
                  WHERE cm.id_carrera = ? ORDER BY m.nombre_materia ASC";
        $stmt = $db->prepare($query);
        $stmt->bind_param("i", $carrera_id);
        $stmt->execute();
        $result = $stmt->get_result();
        echo '<option value="">Seleccione Materia</option>';
        while ($row = $result->fetch_assoc()) echo '<option value="'.$row['id_materia'].'">'.$row['nombre_materia'].'</option>';
        exit;
    }

    if ($_GET['action'] == 'get_secciones') {
        $materia_id = $_GET['materia_id'] ?? '';
        $periodo_id = $_GET['periodo_id'] ?? '';
        // Buscamos en secciones filtrando por materia y periodo
        $query = "SELECT id_seccion, codigo_seccion FROM secciones 
                  WHERE id_materia = ? AND id_periodo = ? ORDER BY codigo_seccion ASC";
        $stmt = $db->prepare($query);
        $stmt->bind_param("ii", $materia_id, $periodo_id);
        $stmt->execute();
        $result = $stmt->get_result();
        echo '<option value="">Seleccione Sección</option>';
        while ($row = $result->fetch_assoc()) echo '<option value="'.$row['id_seccion'].'">'.$row['codigo_seccion'].'</option>';
        exit;
    }
}

if (!isLoggedIn() || !isAdmin()) { header('location: ../login.php'); exit(); }
include("includes/head.php");
?>

<div class="container-fluid">
    <h2 class="mt-4 mb-4 border-bottom pb-2"><i class="fas fa-file-signature text-primary"></i> Actas de Calificación</h2>
    
    <div class="card shadow mb-4">
        <div class="card-body">
            <form id="form-actas" class="row">
                <div class="col-md-3 mb-3">
                    <label>Carrera</label>
                    <select name="id_carrera" id="id_carrera" class="form-control" required>
                        <option value="">Seleccione...</option>
                        <?php
                        $res = $db->query("SELECT id_carrera, nombre_carrera FROM carreras ORDER BY nombre_carrera ASC");
                        while($c = $res->fetch_assoc()) echo "<option value='{$c['id_carrera']}'>{$c['nombre_carrera']}</option>";
                        ?>
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label>Periodo</label>
                    <select name="id_periodo" id="id_periodo" class="form-control" required>
                        <option value="">Seleccione...</option>
                        <?php
                        $res = $db->query("SELECT id_periodo, nombre_periodo FROM periodos_academicos ORDER BY id_periodo DESC");
                        while($p = $res->fetch_assoc()) echo "<option value='{$p['id_periodo']}'>{$p['nombre_periodo']}</option>";
                        ?>
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label>Materia</label>
                    <select name="id_materia" id="id_materia" class="form-control" required></select>
                </div>
                <div class="col-md-3 mb-3">
                    <label>Sección</label>
                    <select name="id_seccion" id="id_seccion" class="form-control" required></select>
                </div>
                <div class="col-12 text-right"><button type="submit" class="btn btn-primary">Generar Listado</button></div>
            </form>
        </div>
    </div>
    <div id="resultados"></div>
</div>

<script>
$(document).ready(function() {
    $('#id_carrera').change(function() {
        $.get('actas_calificacion.php', { action: 'get_materias', carrera_id: $(this).val() }, function(data) {
            $('#id_materia').html(data);
        });
    });
    $('#id_materia, #id_periodo').change(function() {
        const m = $('#id_materia').val(); const p = $('#id_periodo').val();
        if(m && p) $.get('actas_calificacion.php', { action: 'get_secciones', materia_id: m, periodo_id: p }, function(data) {
            $('#id_seccion').html(data);
        });
    });
    $('#form-actas').submit(function(e) {
        e.preventDefault();
        $('#resultados').html('<div class="text-center p-5"><i class="fas fa-spinner fa-spin fa-2x"></i></div>');
        $.post('cargar_estudiantes_acta.php', $(this).serialize(), function(data) { $('#resultados').html(data); });
    });
});
</script>
<?php include("includes/footer.php"); ?>