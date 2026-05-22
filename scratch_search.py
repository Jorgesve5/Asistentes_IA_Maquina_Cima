path = r"c:\Users\jorge\Desktop\PROYECTO 2 MAQUINAS\zona-maquinas-laravel\resources\views\livewire\machine-detail.blade.php"
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

import re
matches = re.finditer(r'\\\$|\\`|\\\'', content)
for m in matches:
    start = max(0, m.start() - 40)
    end = min(len(content), m.end() + 40)
    print(f"Match at position {m.start()}: {repr(content[start:end])}")





