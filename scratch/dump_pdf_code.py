import os

const_dir = r'C:\xampp\htdocs\control_de_estudio\admin\constancias'
files = [
    'pdf_inscripcion.php',
    'pdf_estudios.php',
    'pdf_intensivo.php',
    'pdf_evaluacion_extraordinaria.php',
    'pdf_adicion_retiro.php',
    'pdf_inscripcion_practicas.php',
    'pdf_cambio_seccion.php',
    'pdf_retiro_semestre.php',
    'pdf_cambio_carrera.php',
    'pdf_cambio_turno.php',
    'pdf_renuncia_cupo.php',
    'pdf_constancia_retiro.php',
    'pdf_constancia_traslado.php',
    'pdf_constancia_reincorporacion.php',
    'pdf_retiro_documento.php',
    'pdf_servicio_comunitario.php',
    'pdf_carta_culminacion.php',
    'pdf_notas_certificadas.php',
    'pdf_constancia.php'
]

with open(r'C:\xampp\htdocs\control_de_estudio\scratch\dump_all_pdf_code.txt', 'w', encoding='utf-8') as out:
    for f in files:
        p = os.path.join(const_dir, f)
        if os.path.exists(p):
            out.write(f"\n\n{'='*60}\nFILE: {f}\n{'='*60}\n")
            with open(p, 'r', encoding='utf-8', errors='ignore') as fl:
                out.write(fl.read())
print("Dump completed.")
