import re

path = r"c:\Users\jorge\Desktop\PROYECTO 2 MAQUINAS\zona-maquinas-laravel\resources\views\livewire\machine-detail.blade.php"
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

# Let's find lines with 'tracking-wider' or 'uppercase' or 'text-[9px]' or 'text-xs'
lines = content.splitlines()
for idx, line in enumerate(lines):
    if 'tracking-wider' in line or 'uppercase' in line or 'text-[9px]' in line or 'text-[10px]' in line or 'text-xs' in line:
        # print if it contains text or tags
        clean = line.strip()
        if clean:
            print(f"Line {idx+1}: {clean}")
