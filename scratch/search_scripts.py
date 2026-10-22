import os, re

files_to_check = [
    r'C:\xampp\htdocs\control_de_estudio\admin\estudiantes.php',
    r'C:\xampp\htdocs\control_de_estudio\admin\notas_pasadas.php',
    r'C:\xampp\htdocs\control_de_estudio\admin\grado.php',
    r'C:\xampp\htdocs\control_de_estudio\admin\correccion_notas.php',
    r'C:\xampp\htdocs\control_de_estudio\admin\constancias.php'
]

for f in files_to_check:
    if os.path.exists(f):
        with open(f, 'r', encoding='utf-8', errors='ignore') as fl:
            content = fl.read()
            # find script tags
            scripts = re.findall(r'<script.*?>.*?</script>', content, re.DOTALL | re.IGNORECASE)
            print(f"=== {os.path.basename(f)} ===")
            for s in scripts:
                if 'cedula' in s.lower() or 'search' in s.lower() or 'buscar' in s.lower() or 'keyup' in s.lower() or 'input' in s.lower():
                    # print cleaned lines
                    for line in s.split('\n'):
                        print(" ", line.encode('ascii', 'backslashreplace').decode('ascii'))
