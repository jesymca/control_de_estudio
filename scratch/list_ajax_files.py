import os

base = r'C:\xampp\htdocs\control_de_estudio'
for root, dirs, files in os.walk(base):
    for f in files:
        if ('buscar' in f.lower() or 'ajax' in f.lower() or 'search' in f.lower()) and f.endswith('.php'):
            print(os.path.relpath(os.path.join(root, f), base))
