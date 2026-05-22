import os
import re

search_dir = r"c:\Users\jorge\Desktop\PROYECTO 2 MAQUINAS\zona-maquinas-laravel\resources\views"
pattern = re.compile(r'label|text-\[.*?\]|uppercase|tracking-wider|text-xs', re.IGNORECASE)

results = []
for root, dirs, files in os.walk(search_dir):
    for file in files:
        if file.endswith(".blade.php"):
            path = os.path.join(root, file)
            # Try utf-8 first, then utf-16le
            encodings = ['utf-8', 'utf-16le', 'latin1']
            content = None
            for enc in encodings:
                try:
                    with open(path, 'r', encoding=enc) as f:
                        content = f.read()
                        # check if it starts with null bytes (often utf-16)
                        if '\x00' in content:
                            continue
                        break
                except Exception:
                    continue
            
            if not content:
                # If everything failed, try simple binary read or skip
                continue
                
            lines = content.splitlines()
            for idx, line in enumerate(lines):
                if any(kw in line.lower() for kw in ['nuevo estado', 'detalles del motivo', 'registrar estado', 'reportar incidencia']):
                    results.append((path, idx + 1, line.strip()))

print(f"Found {len(results)} matches:")
for path, line_num, text in results:
    print(f"{os.path.basename(path)}:{line_num}: {text}")
