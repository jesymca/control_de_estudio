import os

const_dir = r'C:\xampp\htdocs\control_de_estudio\admin\constancias'

mapping = {
    'pdf_adicion_retiro.php': 'adicion_retiro',
    'pdf_cambio_carrera.php': 'cambio_carrera',
    'pdf_cambio_seccion.php': 'cambio_seccion',
    'pdf_cambio_turno.php': 'cambio_turno',
    'pdf_carta_culminacion.php': 'carta_culminacion',
    'pdf_constancia.php': 'constancia',
    'pdf_constancia_reincorporacion.php': 'constancia_reincorporacion',
    'pdf_constancia_retiro.php': 'constancia_retiro',
    'pdf_constancia_traslado.php': 'constancia_traslado',
    'pdf_estudios.php': 'estudios',
    'pdf_evaluacion_extraordinaria.php': 'evaluacion_extraordinaria',
    'pdf_inscripcion.php': 'inscripcion',
    'pdf_inscripcion_practicas.php': 'inscripcion_practicas',
    'pdf_intensivo.php': 'intensivo',
    'pdf_notas_certificadas.php': 'notas_certificadas',
    'pdf_renuncia_cupo.php': 'renuncia_cupo',
    'pdf_retiro_documento.php': 'retiro_documento',
    'pdf_retiro_semestre.php': 'retiro_semestre',
    'pdf_servicio_comunitario.php': 'servicio_comunitario'
}

for fname, doc_type in mapping.items():
    file_path = os.path.join(const_dir, fname)
    content = f"""<?php
/**
 * Redireccionador / Wrapper de compatibilidad para {fname}
 * Centralizado en ../../constancias/generar_constancia.php
 */
$_POST['tipo'] = '{doc_type}';
if (!isset($_POST['id']) && isset($_GET['id'])) {{
    $_POST['id'] = $_GET['id'];
}}
require_once(__DIR__ . '/../../constancias/generar_constancia.php');
"""
    with open(file_path, 'w', encoding='utf-8') as f:
        f.write(content)
    print(f"Updated wrapper: {fname}")
