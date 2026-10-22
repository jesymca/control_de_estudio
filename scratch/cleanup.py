import os

base = r'C:\xampp\htdocs\control_de_estudio\scratch'
for f in os.listdir(base):
    if f.startswith('test_') or f.startswith('check_') or f.startswith('debug_') or f.startswith('alter_') or f.startswith('describe_') or f.startswith('find_') or f.startswith('read_') or f.startswith('run_'):
        try:
            os.remove(os.path.join(base, f))
        except:
            pass
print("Temporary scratch test files cleaned.")
