import re

path = r"c:\Users\jorge\Desktop\PROYECTO 2 MAQUINAS\zona-maquinas-laravel\resources\views\livewire\admin-dashboard.blade.php"
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

lines = content.splitlines()
for idx, line in enumerate(lines):
    if '<label' in line or 'tracking-wider' in line or 'uppercase' in line:
        clean = line.strip()
        if clean:
            print(f"Line {idx+1}: {clean}")
