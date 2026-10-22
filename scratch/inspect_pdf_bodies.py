import os

const_dir = r'C:\xampp\htdocs\control_de_estudio\admin\constancias'
for f in sorted(os.listdir(const_dir)):
    if f.endswith('.php') and f != 'generar_reporte_pdf.php':
        path = os.path.join(const_dir, f)
        with open(path, 'r', encoding='utf-8', errors='ignore') as fl:
            content = fl.read()
            print(f"==================================================")
            print(f"FILE: {f}")
            print(f"==================================================")
            # Find class Header/Footer or key text
            for line in content.split('\n'):
                if 'class ' in line or 'Cell(' in line or 'Write(' in line or 'MultiCell(' in line or 'Image(' in line or 'Output(' in line:
                    if len(line.strip()) > 0:
                        print("  ", line.strip()[:110])
