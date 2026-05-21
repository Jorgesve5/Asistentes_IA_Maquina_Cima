import paramiko

def run_sudo_cmd(ssh, cmd, password):
    # Ejecuta un comando con sudo y le pasa la contraseña automáticamente
    full_cmd = f"echo '{password}' | sudo -S {cmd}"
    stdin, stdout, stderr = ssh.exec_command(full_cmd)
    out = stdout.read().decode('utf-8')
    err = stderr.read().decode('utf-8')
    return out, err

def main():
    host = "192.168.10.210"
    username = "svr-maquinas"
    password = "Cima1100@"

    ssh = paramiko.SSHClient()
    ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
    ssh.connect(host, username=username, password=password)

    # 1. Modificar Nginx (cima)
    # Leemos la configuración actual
    stdin, stdout, stderr = ssh.exec_command("cat /etc/nginx/sites-available/cima")
    cima_content = stdout.read().decode('utf-8')

    if "client_max_body_size" not in cima_content:
        # Añadimos la directiva en el bloque server
        print("Modificando configuracion de Nginx...")
        updated_cima = cima_content.replace(
            "server {",
            "server {\n    client_max_body_size 50M;"
        )
        # Escribimos a archivo temporal y lo movemos
        sftp = ssh.open_sftp()
        temp_cima = sftp.file('/tmp/cima_new', 'w')
        temp_cima.write(updated_cima)
        temp_cima.close()
        sftp.close()

        out, err = run_sudo_cmd(ssh, "cp /tmp/cima_new /etc/nginx/sites-available/cima", password)
        print("Copiado Nginx:", out, err)
    else:
        print("Nginx ya tenia client_max_body_size configurado.")

    # 2. Modificar PHP php.ini
    print("Modificando limites de PHP en php.ini...")
    # Copiamos temporalmente para editarlo de forma segura
    run_sudo_cmd(ssh, "cp /etc/php/8.3/fpm/php.ini /tmp/php.ini.tmp", password)
    run_sudo_cmd(ssh, "chmod 777 /tmp/php.ini.tmp", password)
    
    sftp = ssh.open_sftp()
    f = sftp.file('/tmp/php.ini.tmp', 'r')
    php_ini_content = f.read().decode('utf-8')
    f.close()

    # Reemplazar límites
    php_ini_content = php_ini_content.replace("upload_max_filesize = 2M", "upload_max_filesize = 50M")
    php_ini_content = php_ini_content.replace("post_max_size = 8M", "post_max_size = 50M")

    f = sftp.file('/tmp/php.ini.tmp', 'w')
    f.write(php_ini_content)
    f.close()
    sftp.close()

    # Devolver php.ini a su lugar
    out, err = run_sudo_cmd(ssh, "cp /tmp/php.ini.tmp /etc/php/8.3/fpm/php.ini", password)
    print("Copiado php.ini:", out, err)

    # 3. Validar sintaxis y reiniciar servicios
    print("Validando sintaxis de Nginx...")
    out, err = run_sudo_cmd(ssh, "nginx -t", password)
    print("Sintaxis Nginx:", out, err)

    if "syntax is ok" in err or "test is successful" in err or "syntax is ok" in out:
        print("Reiniciando Nginx y PHP-FPM...")
        out_nginx, err_nginx = run_sudo_cmd(ssh, "systemctl restart nginx", password)
        out_php, err_php = run_sudo_cmd(ssh, "systemctl restart php8.3-fpm", password)
        print("Nginx restart output:", out_nginx, err_nginx)
        print("PHP-FPM restart output:", out_php, err_php)
        print("\nConfiguracion completada con exito y servicios reiniciados!")
    else:
        print("ERROR: La prueba de sintaxis de Nginx fallo. No se reiniciaron los servicios.")

    ssh.close()

if __name__ == '__main__':
    main()
