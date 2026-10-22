import os

const_dir = r'C:\xampp\htdocs\control_de_estudio\admin\constancias'
for f in os.listdir(const_dir):
    if f.endswith('.php'):
        path = os.path.join(const_dir, f)
        with open(path, 'r', encoding='utf-8', errors='ignore') as fl:
            lines = fl.readlines()
            print(f"=== {f} ({len(lines)} lines) ===")
            # print first 15 lines
            for l in lines[:12]:
                print("  ", l.rstrip())
