<?php
// Iniciar sesión PRIMERO
require_once('../funciones/functions.php');

// Verificar autenticación y rol
if (!isLoggedIn() || !isDocente()) {
    $_SESSION['msg'] = "Debes iniciar sesión como docente para acceder";
    header('location: ../login.php');
    exit();
}

// LLAMAR A LA FUNCIÓN DE VISITA
visita();

// Obtener ID del docente directamente de la sesión
$docente_id = obtenerIdUsuario();

if (!$docente_id) {
    die("Error: No se pudo identificar al usuario");
}

// Obtener secciones del docente
$result_secciones = obtenerSeccionesDocente($docente_id);

// HTML
$titulopag = "Registro de Notas";
include("includes/head.php");
?>

<!-- Añadir meta viewport y estilos responsivos adicionales -->
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=yes">
<style>
    @media (max-width: 768px) {
        .container-fluid { padding-left: 10px; padding-right: 10px; }
        h2.my-4 { font-size: 1.5rem; margin-top: 1rem !important; margin-bottom: 1rem !important; }
        .card-header h5 { font-size: 1rem; }
        .table-responsive { overflow-x: auto; -webkit-overflow-scrolling: touch; }
        .btn-sm { padding: 6px 8px; font-size: 0.75rem; margin-bottom: 5px; }
        td:last-child { min-width: 180px; }
        .form-group input, .form-group select, .form-group textarea { font-size: 16px !important; }
        label { font-size: 0.85rem; }
        .modal-dialog { margin: 10px; max-width: calc(100% - 20px); }
        .modal-body { padding: 12px; }
        #preview-table { font-size: 0.75rem; }
        #preview-table th, #preview-table td { padding: 6px; white-space: nowrap; }
        #volver-container { position: sticky; top: 0; background: white; z-index: 100; padding: 10px 0; margin-bottom: 15px; border-bottom: 1px solid #ddd; }
        input[type="number"] { font-size: 16px; width: 80px; }
        .card { margin-bottom: 15px; }
        .alert { font-size: 0.85rem; padding: 10px; }
    }
    @media (max-width: 480px) {
        .btn-sm { font-size: 0.7rem; padding: 5px 6px; }
        td:last-child { min-width: 200px; }
        .table th, .table td { padding: 6px; font-size: 0.75rem; }
        h2.my-4 { font-size: 1.3rem; }
    }
    .estudiante-row { transition: all 0.3s ease; }
    .estudiante-row:hover { background-color: #f5f5f5; }
    .acciones-botones { display: flex; flex-wrap: wrap; gap: 5px; align-items: center; }
    @media (max-width: 768px) {
        .acciones-botones { flex-direction: column; align-items: stretch; }
        .acciones-botones .btn, .acciones-botones label { width: 100%; margin: 2px 0 !important; text-align: center; }
    }
</style>

<div class="container-fluid">
    <h2 class="my-4">
        <i class="fas fa-chalkboard-teacher"></i> Registro de Notas
    </h2>
    
    <!-- Secciones del docente -->
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">
                <i class="fas fa-book"></i> Secciones y Materias
            </h5>
        </div>
        <div class="card-body">
            <?php if ($result_secciones->num_rows > 0): ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th>Sección</th>
                                <th>Carrera</th>
                                <th>Trayecto</th>
                                <th>Periodo</th>
                                <th>Materia</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($seccion = $result_secciones->fetch_assoc()): ?>
                                <tr class="seccion-row">
                                    <td><?= htmlspecialchars($seccion['codigo_seccion']) ?></td>
                                    <td><?= htmlspecialchars($seccion['nombre_carrera']) ?></td>
                                    <td><?= htmlspecialchars($seccion['nombre_trayecto']) ?></td>
                                    <td><?= htmlspecialchars($seccion['nombre_periodo']) ?></td>
                                    <td><?= htmlspecialchars($seccion['nombre_materia']) ?></td>
                                    <td>
                                        <div class="acciones-botones">
                                            <button class="btn btn-sm btn-primary btn-cargar" 
                                                    data-seccion="<?= $seccion['id_seccion'] ?>"
                                                    data-materia="<?= $seccion['id_materia'] ?>">
                                                <i class="fas fa-users"></i> Cargar
                                            </button>
                                            <button class="btn btn-sm btn-success btn-descargar-pdf" 
                                                    data-seccion="<?= $seccion['id_seccion'] ?>"
                                                    data-materia="<?= $seccion['id_materia'] ?>">
                                                <i class="fas fa-file-pdf mr-1"></i> PDF
                                            </button>
                                            <button class="btn btn-sm btn-outline-secondary btn-descargar-csv"
                                                    data-seccion="<?= $seccion['id_seccion'] ?>"
                                                    data-materia="<?= $seccion['id_materia'] ?>">
                                                <i class="fas fa-file-csv"></i> CSV
                                            </button>
                                            <label class="btn btn-sm btn-outline-primary mb-0" style="cursor:pointer;">
                                                <i class="fas fa-file-upload"></i> Importar
                                                <input type="file" accept=".csv,text/csv,application/vnd.ms-excel,.pdf,application/pdf" class="d-none input-import-csv" data-seccion="<?= $seccion['id_seccion'] ?>" data-materia="<?= $seccion['id_materia'] ?>">
                                            </label>
                                        </div>
                                    </div>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="alert alert-warning text-center">
                    <i class="fas fa-exclamation-triangle"></i> No tienes secciones asignadas
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Resultados -->
    <div id="resultados">
        <div class="text-right mb-3" id="volver-container" style="display: none;">
            <button class="btn btn-secondary" id="btn-volver">
                <i class="fas fa-arrow-left"></i> Volver a Secciones
            </button>
        </div>
    </div>
</div>

<!-- Modal resultado -->
<div class="modal fade" id="modalResultado" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header" id="modalResultadoHeader">
                <h5 class="modal-title" id="modalResultadoTitle">Resultado</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="modalResultadoBody"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal preview PDF en la misma página -->
<div class="modal fade" id="modalPreviewPDF" tabindex="-1" role="dialog" aria-labelledby="modalPreviewPDFTitle" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" style="max-width: 92vw; height: 90vh;" role="document">
        <div class="modal-content shadow-lg border-0 h-100" style="border-radius: 10px; overflow: hidden;">
            <div class="modal-header bg-dark text-white py-2 px-3 d-flex justify-content-between align-items-center">
                <h5 class="modal-title font-weight-bold mb-0" id="modalPreviewPDFTitle" style="font-size: 0.95rem;">
                    <i class="fas fa-file-pdf text-danger mr-2"></i> Vista Previa de Planilla de Notas (PDF)
                </h5>
                <div>
                    <a id="btnAbrirNuevaPestanaPDF" href="#" target="_blank" class="btn btn-sm btn-outline-light mr-2 font-weight-bold">
                        <i class="fas fa-external-link-alt mr-1"></i> Abrir en ventana
                    </a>
                    <a id="btnDescargarDirectoPDF" href="#" download class="btn btn-sm btn-success mr-2 font-weight-bold">
                        <i class="fas fa-download mr-1"></i> Descargar
                    </a>
                    <button type="button" class="btn btn-sm btn-danger font-weight-bold px-3" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i> Cerrar
                    </button>
                </div>
            </div>
            <div class="modal-body p-0 bg-secondary" style="height: calc(90vh - 55px);">
                <iframe id="iframePreviewPDF" src="" class="w-100 h-100 border-0" style="background-color: #525659;"></iframe>
            </div>
        </div>
    </div>
</div>

<!-- Modal preview import CSV -->
<div class="modal fade" id="modalPreviewCSV" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title"><i class="fas fa-search mr-1"></i> Vista Previa de Importación de Notas (CSV / PDF)</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="preview-summary" class="mb-3"></div>
                <div class="table-responsive" style="max-height:400px; overflow:auto;">
                    <table class="table table-sm table-bordered" id="preview-table">
                        <thead>
                            <tr>
                                <th>Línea</th>
                                <th>Cédula</th>
                                <th>Nombres</th>
                                <th>Notas</th>
                                <th>Campo</th>
                                <th>Mensaje</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="btn-apply-csv">Aplicar al formulario</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Cargar estudiantes
    $('.btn-cargar').click(function() {
        const seccionId = $(this).data('seccion');
        const materiaId = $(this).data('materia');
        
        $('#resultados').html(`
            <div class="text-right mb-3" id="volver-container">
                <button class="btn btn-secondary" id="btn-volver">
                    <i class="fas fa-arrow-left"></i> Volver a Secciones
                </button>
            </div>
            <div class="text-center py-5">
                <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;"></div>
                <p class="mt-3">Cargando estudiantes...</p>
            </div>
        `);
        
        $('html, body').animate({ scrollTop: 0 }, 300);
        
        fetch('cargar_estudiantes.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `seccion_id=${seccionId}&materia_id=${materiaId}`
        })
        .then(response => response.text())
        .then(html => {
            $('#resultados').html(`
                <div class="text-right mb-3" id="volver-container">
                    <button class="btn btn-secondary" id="btn-volver">
                        <i class="fas fa-arrow-left"></i> Volver a Secciones
                    </button>
                </div>
                ${html}
            `);
            $('input[type="number"]').addClass('form-control');
        })
        .catch(error => {
            $('#resultados').html(`
                <div class="text-right mb-3" id="volver-container">
                    <button class="btn btn-secondary" id="btn-volver">
                        <i class="fas fa-arrow-left"></i> Volver a Secciones
                    </button>
                </div>
                <div class="alert alert-danger">Error: ${error.message}</div>
            `);
        });
    });
    
        // Soporte Grupo - Vista previa interactiva de PDF o Imagen antes de subirlo
    $(document).on('change', '#soporte_grupo', function() {
        const file = this.files[0];
        const previewBox = document.getElementById('preview-grupo');
        const labelSoporte = document.getElementById('label-soporte-grupo');
        const infoSoporte = document.getElementById('info-archivo-soporte');

        if (!previewBox) return;

        if (!file) {
            if (labelSoporte) labelSoporte.textContent = 'Elegir archivo PDF o Imagen...';
            if (infoSoporte) infoSoporte.textContent = '';
            previewBox.innerHTML = `
                <div class="py-3 text-muted text-center">
                    <i class="fas fa-cloud-upload-alt fa-2x mb-1 text-secondary"></i>
                    <p class="mb-0 small">No se ha seleccionado ningún archivo de soporte aún.</p>
                </div>`;
            return;
        }

        const fileSizeMB = (file.size / (1024 * 1024)).toFixed(2);
        const fileName = file.name;
        if (labelSoporte) labelSoporte.textContent = fileName;
        if (infoSoporte) infoSoporte.innerHTML = `<span class="badge badge-info">${file.type || 'Archivo'}</span> <span class="badge badge-secondary">${fileSizeMB} MB</span>`;

        const fileUrl = URL.createObjectURL(file);

        if (file.type === 'application/pdf' || fileName.toLowerCase().endsWith('.pdf')) {
            previewBox.innerHTML = `
                <div class="text-left mb-2 d-flex justify-content-between align-items-center bg-white p-2 border rounded shadow-sm">
                    <div>
                        <i class="fas fa-file-pdf text-danger fa-lg mr-2"></i>
                        <strong class="text-dark">${fileName}</strong>
                        <span class="text-muted small ml-2">(${fileSizeMB} MB)</span>
                    </div>
                    <div>
                        <a href="${fileUrl}" target="_blank" class="btn btn-xs btn-outline-danger btn-sm font-weight-bold mr-1 py-1 px-2">
                            <i class="fas fa-external-link-alt mr-1"></i> Pantalla Completa
                        </a>
                        <button type="button" class="btn btn-xs btn-outline-secondary btn-sm py-1 px-2 btn-quitar-soporte-file">
                            <i class="fas fa-times mr-1"></i> Quitar
                        </button>
                    </div>
                </div>
                <div style="height: 420px; width: 100%; border-radius: 6px; overflow: hidden; border: 1px solid #dee2e6;">
                    <iframe src="${fileUrl}" style="width: 100%; height: 100%; border: none; background-color: #525659;"></iframe>
                </div>
            `;
        } else if (file.type.startsWith('image/')) {
            previewBox.innerHTML = `
                <div class="text-left mb-2 d-flex justify-content-between align-items-center bg-white p-2 border rounded shadow-sm">
                    <div>
                        <i class="fas fa-image text-success fa-lg mr-2"></i>
                        <strong class="text-dark">${fileName}</strong>
                        <span class="text-muted small ml-2">(${fileSizeMB} MB)</span>
                    </div>
                    <div>
                        <a href="${fileUrl}" target="_blank" class="btn btn-xs btn-outline-success btn-sm font-weight-bold mr-1 py-1 px-2">
                            <i class="fas fa-search-plus mr-1"></i> Ver en Grande
                        </a>
                        <button type="button" class="btn btn-xs btn-outline-secondary btn-sm py-1 px-2 btn-quitar-soporte-file">
                            <i class="fas fa-times mr-1"></i> Quitar
                        </button>
                    </div>
                </div>
                <div class="p-2 bg-white rounded border text-center">
                    <img src="${fileUrl}" class="img-fluid rounded shadow-sm" style="max-height: 350px; object-fit: contain;">
                </div>
            `;
        } else {
            previewBox.innerHTML = `
                <div class="alert alert-info mb-0">
                    <i class="fas fa-file mr-1"></i> Archivo seleccionado: <strong>${fileName}</strong> (${fileSizeMB} MB)
                </div>
            `;
        }
    });

    $(document).on('click', '.btn-quitar-soporte-file', function(e) {
        e.preventDefault();
        const input = document.getElementById('soporte_grupo');
        if (input) {
            input.value = '';
            $(input).trigger('change');
        }
    });

    // Volver a secciones
    $(document).on('click', '#btn-volver', function() {
        $('#resultados').html(`
            <div class="text-right mb-3" id="volver-container" style="display: none;">
                <button class="btn btn-secondary" id="btn-volver">
                    <i class="fas fa-arrow-left"></i> Volver a Secciones
                </button>
            </div>
        `);
        $('html, body').animate({ scrollTop: $('.card').first().offset().top - 20 }, 400);
    });
    
    // Vista previa de PDF en Modal (Misma Página)
    $(document).on('click', '.btn-descargar-pdf', function(e) {
        e.preventDefault();
        const seccionId = $(this).data('seccion');
        const materiaId = $(this).data('materia');
        const url = `descargar_planilla.php?seccion_id=${seccionId}&materia_id=${materiaId}`;
        
        const iframe = document.getElementById('iframePreviewPDF');
        const btnExt = document.getElementById('btnAbrirNuevaPestanaPDF');
        const btnDesc = document.getElementById('btnDescargarDirectoPDF');
        
        if (iframe) iframe.src = url;
        if (btnExt) btnExt.href = url;
        if (btnDesc) btnDesc.href = url;
        
        $('#modalPreviewPDF').modal('show');
    });

    // Limpiar iframe al cerrar el modal de PDF
    $('#modalPreviewPDF').on('hidden.bs.modal', function () {
        const iframe = document.getElementById('iframePreviewPDF');
        if (iframe) iframe.src = '';
    });

    // Descargar CSV
    $(document).on('click', '.btn-descargar-csv', function() {
        const seccionId = $(this).data('seccion');
        const materiaId = $(this).data('materia');
        window.location.href = `descargar_planilla_csv.php?seccion_id=${seccionId}&materia_id=${materiaId}`;
    });

    let currentSeccionId = null;
    let currentMateriaId = null;
    
    // Importar CSV
    $(document).on('change', '.input-import-csv', function(e) {
        const file = this.files[0];
        currentSeccionId = $(this).data('seccion');
        currentMateriaId = $(this).data('materia');
        if (!file) return;

        const fd = new FormData();
        fd.append('file', file);
        fd.append('seccion_id', currentSeccionId);
        fd.append('materia_id', currentMateriaId);

        $('#preview-table tbody').html('<tr><td colspan="6" class="text-center py-4"><div class="spinner-border text-info"></div> Procesando...<\/td><\/tr>');
        $('#preview-summary').html('<span class="text-info">Procesando archivo...</span>');
        $('#modalPreviewCSV').modal('show');

        fetch('import_preview_notas.php', { method: 'POST', body: fd })
            .then(r => r.json())
            .then(data => {
                if (data.error) {
                    $('#preview-summary').html(`<div class="alert alert-danger">${data.error}</div>`);
                    return;
                }

                const rows = data.previewRows || [];
                const summary = data.summary || {};
                $('#preview-summary').html(`
                    <div class="alert alert-info">
                        <strong>Total:</strong> ${summary.total} | 
                        <strong>Válidas:</strong> ${summary.validas} | 
                        <strong>Inválidas:</strong> ${summary.invalidas}
                    </div>
                `);

                const tbody = $('#preview-table tbody');
                tbody.empty();
                rows.forEach(r => {
                    const tr = $('<tr>');
                    tr.append($('<td>').text(r.line));
                    tr.append($('<td>').text(r.identificador || ''));
                    tr.append($('<td>').text(r.nombre || ''));
                    tr.append($('<td>').text(r.notas_texto || '-'));
                    tr.append($('<td>').text('Trimestres'));
                    tr.append($('<td>').html(r.mensaje));
                    tr.data('row', r);
                    tbody.append(tr);
                });
            })
            .catch(err => {
                $('#preview-summary').html(`<div class="alert alert-danger">Error: ${err.message}</div>`);
            });
    });

    // Aplicar CSV al formulario
    $('#btn-apply-csv').click(function() {
        const rows = [];
        $('#preview-table tbody tr').each(function() {
            const r = $(this).data('row');
            if (r && r.valido) rows.push(r);
        });

        if (rows.length === 0) {
            alert('No hay filas válidas para aplicar');
            return;
        }

        let applied = 0;
        let missing = 0;
        
        rows.forEach(r => {
            const estudianteId = r.estudiante_id;
            const notas = r.notas || {};
            
            if (notas.trimestre_1) {
                const selector = `input[name="notas[${estudianteId}][trimestre_1]"]`;
                const input = document.querySelector(selector);
                if (input) {
                    input.value = notas.trimestre_1;
                    $(input).trigger('change');
                    applied++;
                } else {
                    missing++;
                }
            }
            
            if (notas.trimestre_2) {
                const selector = `input[name="notas[${estudianteId}][trimestre_2]"]`;
                const input = document.querySelector(selector);
                if (input) {
                    input.value = notas.trimestre_2;
                    $(input).trigger('change');
                    applied++;
                } else {
                    missing++;
                }
            }
            
            if (notas.trimestre_3) {
                const selector = `input[name="notas[${estudianteId}][trimestre_3]"]`;
                const input = document.querySelector(selector);
                if (input) {
                    input.value = notas.trimestre_3;
                    $(input).trigger('change');
                    applied++;
                } else {
                    missing++;
                }
            }
        });

        $('#modalPreviewCSV').modal('hide');
        let msg = `✅ Se aplicaron ${applied} notas al formulario.`;
        if (missing) msg += ` ⚠️ ${missing} campos no se encontraron.`;
        alert(msg);
    });
    
    // Guardar notas
    $(document).on('submit', '#form-notas', function(e) {
        e.preventDefault();
        
        const soporteFile = $('#soporte_grupo')[0].files[0];
        if (!soporteFile) {
            alert('❌ Debes adjuntar un archivo de soporte (imagen o PDF) para poder guardar las notas.');
            $('#soporte_grupo').focus();
            return;
        }
        
        if (!confirm('¿Estás seguro de guardar las notas?')) return;
        
        $('#resultados').html(`
            <div class="text-right mb-3" id="volver-container">
                <button class="btn btn-secondary" id="btn-volver">
                    <i class="fas fa-arrow-left"></i> Volver a Secciones
                </button>
            </div>
            <div class="text-center py-5">
                <div class="spinner-border text-success" style="width: 3rem; height: 3rem;"></div>
                <p class="mt-3">Guardando notas y soporte...</p>
            </div>
        `);
        
        const formData = new FormData(this);
        
        fetch('guardar_notas.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            $('#resultados').html(`
                <div class="text-right mb-3" id="volver-container">
                    <button class="btn btn-secondary" id="btn-volver">
                        <i class="fas fa-arrow-left"></i> Volver a Secciones
                    </button>
                </div>
            `);

            const header = $('#modalResultadoHeader');
            const title = $('#modalResultadoTitle');
            const body = $('#modalResultadoBody');

            if (data.success) {
                header.removeClass('bg-danger').addClass('bg-success text-white');
                title.text('Éxito');
                body.html('<div class="alert alert-success">' + data.message + '</div>');
            } else {
                header.removeClass('bg-success').addClass('bg-danger text-white');
                title.text('Error');
                body.html('<div class="alert alert-danger">' + data.message + '</div>');
            }

            $('#modalResultado').modal('show');
        })
        .catch(error => {
            $('#resultados').html(`
                <div class="text-right mb-3" id="volver-container">
                    <button class="btn btn-secondary" id="btn-volver">
                        <i class="fas fa-arrow-left"></i> Volver a Secciones
                    </button>
                </div>
                <div class="alert alert-danger">Error al guardar: ${error.message}</div>
            `);
        });
    });
    
    // Preview de imagen
    $(document).on('change', '.soporte-grupo', function() {
        const file = this.files[0];
        const preview = $('#preview-grupo');
        const fileName = $('#nombre-archivo-grupo');
        
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                if (file.type.startsWith('image/')) {
                    preview.html(`<img src="${e.target.result}" class="img-fluid img-thumbnail" style="max-height: 150px;">`);
                } else {
                    preview.html(`
                        <div class="alert alert-info text-center">
                            <i class="fas fa-file-pdf fa-3x"></i><br>
                            <strong>${file.name}</strong>
                        </div>
                    `);
                }
                fileName.text(file.name);
            }
            reader.readAsDataURL(file);
        } else {
            preview.html('<small class="text-muted">No se ha seleccionado ningún archivo</small>');
            fileName.text('Ningún archivo seleccionado');
        }
    });
});
</script>

<?php include("includes/footer.php"); ?>