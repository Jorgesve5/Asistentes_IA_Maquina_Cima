import os

search_dir = r"c:\Users\jorge\Desktop\PROYECTO 2 MAQUINAS\zona-maquinas-laravel"
patterns = ["parts[1]", "sizeInfo", "pdfWidget"]

for root, dirs, files in os.walk(search_dir):
    # skip node_modules, vendor, .git, storage
    if any(p in root for p in ["node_modules", "vendor", ".git", "storage"]):
        continue
    for file in files:
        if file.endswith((".php", ".js", ".vue", ".html")):
            path = os.path.join(root, file)
            try:
                with open(path, "r", encoding="utf-8") as f:
                    content = f.read()
                for p in patterns:
                    if p in content:
                        print(f"Found '{p}' in {path}")
            except Exception as e:
                pass
