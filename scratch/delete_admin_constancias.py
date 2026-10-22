import os, shutil

base = r'C:\xampp\htdocs\control_de_estudio'
admin_constancias_dir = os.path.join(base, 'admin', 'constancias')

# Check if directory exists
if os.path.exists(admin_constancias_dir):
    files = os.listdir(admin_constancias_dir)
    print(f"Found {len(files)} files in admin/constancias to delete:")
    for f in files:
        print(f"  - {f}")
    
    # Remove all files and directory
    shutil.rmtree(admin_constancias_dir)
    print("Successfully deleted admin/constancias directory.")
else:
    print("Directory admin/constancias does not exist.")
