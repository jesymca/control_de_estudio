import re

with open(r'C:\xampp\htdocs\control_de_estudio\funciones\functions.php', 'r', encoding='utf-8', errors='ignore') as f:
    content = f.read()

funcs = [
    'inscribirMateriasEstudiante',
    'obtenerMateriasDisponibles',
    'obtenerMateriasInscritas',
    'obtenerInfoEstudiantePorId',
    'verificarAvanceTrayectoEstudiante'
]

for func in funcs:
    pattern = rf'function {func}\s*\([^)]*\)\s*\{{.*?\n\}}'
    match = re.search(pattern, content, re.DOTALL)
    if match:
        print(f"=== FUNCTION {func} ===")
        print(match.group(0)[:800])
    else:
        print(f"Function {func} not found directly with regex, searching lines:")
        lines = content.split('\n')
        for i, l in enumerate(lines):
            if f'function {func}' in l:
                print(f"Line {i+1}: {l}")
                print("\n".join(lines[i:i+40]))
                break
