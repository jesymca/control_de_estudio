import os

upload_dir = r'C:\xampp\htdocs\control_de_estudio\uploads\comprobantes_pagos'
os.makedirs(upload_dir, exist_ok=True)

# Add an index.html or .htaccess to prevent directory listing
with open(os.path.join(upload_dir, 'index.html'), 'w') as f:
    f.write('<!DOCTYPE html><html><head><title>Access Denied</title></head><body><h1>Access Denied</h1></body></html>')

print(f"Upload directory created: {upload_dir}")
