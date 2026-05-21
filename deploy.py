import os
import sys
import paramiko

def put_dir(sftp, local_dir, remote_dir):
    # Sube recursivamente el directorio al servidor
    for root, dirs, files in os.walk(local_dir):
        rel_path = os.path.relpath(root, local_dir)
        if rel_path == ".":
            dest_dir = remote_dir
        else:
            dest_dir = os.path.join(remote_dir, rel_path).replace("\\", "/")
        
        try:
            sftp.mkdir(dest_dir)
            print(f"Directorio creado en servidor: {dest_dir}")
        except IOError:
            pass
        
        for file in files:
            local_file = os.path.join(root, file)
            remote_file = os.path.join(dest_dir, file).replace("\\", "/")
            print(f"Subiendo: {local_file} -> {remote_file}")
            sftp.put(local_file, remote_file)

def main():
    host = "192.168.10.210"
    username = "svr-maquinas"
    password = "Cima1100@"
    app_path = "/home/svr-maquinas/apps/Asistentes_IA_Maquina_Cima"
    local_path = r"C:\Users\practicas.e5\Desktop\jesus\Asistentes_IA_Maquina_Cima"

    print(f"Conectando a SSH en {username}@{host}...")
    ssh = paramiko.SSHClient()
    ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
    try:
        ssh.connect(host, username=username, password=password)
        print("Conectado con exito!")
    except Exception as e:
        print(f"Error de conexion: {e}")
        sys.exit(1)

    # Abrir canal SFTP
    sftp = ssh.open_sftp()

    # Subir archivos especificos modificados
    files_to_upload = [
        (r"app\Livewire\MachineDetail.php", "app/Livewire/MachineDetail.php"),
        (r"app\Livewire\AdminDashboard.php", "app/Livewire/AdminDashboard.php"),
        (r"resources\views\livewire\machine-detail.blade.php", "resources/views/livewire/machine-detail.blade.php"),
        (r"resources\views\livewire\admin-dashboard.blade.php", "resources/views/livewire/admin-dashboard.blade.php"),
    ]

    for loc, rem in files_to_upload:
        full_local = os.path.join(local_path, loc)
        full_remote = f"{app_path}/{rem}"
        print(f"Subiendo archivo: {loc} -> {rem}...")
        sftp.put(full_local, full_remote)

    # Subir public/build recursivamente
    print("Subiendo carpeta de assets compilados public/build...")
    put_dir(sftp, os.path.join(local_path, "public", "build"), f"{app_path}/public/build")

    sftp.close()

    print("\nArchivos subidos con exito. Ejecutando comandos en servidor...")

    # Ejecutar comandos de cache y permisos
    commands = [
        f"cd {app_path} && echo 'Cima1100@' | sudo -S chmod -R 775 storage bootstrap/cache",
        f"cd {app_path} && php artisan storage:link || true",
        f"cd {app_path} && php artisan config:clear",
        f"cd {app_path} && php artisan view:clear",
        f"cd {app_path} && php artisan cache:clear"
    ]

    for cmd in commands:
        print(f"Ejecutando: {cmd}")
        stdin, stdout, stderr = ssh.exec_command(cmd)
        out = stdout.read().decode('utf-8')
        err = stderr.read().decode('utf-8')
        if out:
            print(f"STDOUT:\n{out}")
        if err:
            print(f"STDERR:\n{err}")

    # Diagnosticar configuracion de Nginx/PHP para resolver el error 413
    print("\n[DIAGNOSTICO] Buscando archivos de configuracion de Nginx y PHP...")
    diag_cmds = [
        "grep -rn 'client_max_body_size' /etc/nginx/ || true",
        "find /etc/nginx/sites-enabled/ -type f | xargs grep -rn '' || true",
        "php -i | grep -E 'upload_max_filesize|post_max_size' || true"
    ]

    for cmd in diag_cmds:
        print(f"\nEjecutando diagnostico: {cmd}")
        stdin, stdout, stderr = ssh.exec_command(cmd)
        out = stdout.read().decode('utf-8')
        if out:
            print(f"STDOUT:\n{out}")

    ssh.close()
    print("\nProceso de despliegue y diagnostico finalizado.")

if __name__ == '__main__':
    main()
