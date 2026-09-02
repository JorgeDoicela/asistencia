# Guía de Despliegue y Mantenimiento

Instrucciones técnicas para desplegar, configurar y respaldar el sistema en entornos de desarrollo y producción.

---

## 1. Requisitos del Sistema

* **PHP:** Versión 8.0 o superior (recomendado PHP 8.2).
* **Extensiones PHP:** `pdo`, `pdo_mysql`, `mbstring`.
* **Servidor Web:** Apache 2.4 con módulo `mod_rewrite` activado.
* **Base de Datos:** MariaDB 10.4+ o MySQL 8.0+.
* **Opcional:** Docker y Docker Compose para despliegue contenerizado.

---

## 2. Despliegue con Docker (Recomendado para Producción y Staging)

El proyecto incluye configuración de Docker Compose con 3 servicios:
* `web`: Servidor Apache con PHP 8.2 y extensiones configuradas.
* `db`: Base de datos MariaDB 10.6 con volumen persistente y script inicializador.
* `phpmyadmin`: Gestor web de base de datos.

### Pasos:
1. Iniciar los contenedores:
   ```bash
   docker compose up -d --build
   ```
2. Verificar el estado de los contenedores:
   ```bash
   docker compose ps
   ```
3. Accesos:
   * **Sistema Web:** `http://<HOST>:8080/` (ej. `http://localhost:8080/` o `http://<IP_LOCAL>:8080/`)
   * **phpMyAdmin:** `http://<HOST>:8081/` (ej. `http://localhost:8081/`)

---

## 3. Despliegue en Servidores Locales (XAMPP / WampServer / LAMP)

1. Copia la carpeta del proyecto dentro del directorio web raíz:
   * **XAMPP / LAMP:** `<directorio_web>/htdocs/asistencia`
2. Inicia los servicios de **Apache** y **MySQL** en tu gestor de servicios.
3. Ingresa a phpMyAdmin (`http://<HOST>/phpmyadmin`) e importa el archivo `database/database.sql`.
4. Accede desde tu navegador web a:
   ```text
   http://<HOST>/asistencia/
   ```
   *(ejemplo: `http://localhost/asistencia/` o `http://<IP_LOCAL>/asistencia/`)*.
5. La aplicación redirigirá automáticamente el tráfico de la raíz a `public/` gracias al archivo `.htaccess`.

---

## 4. Variables de Entorno

El sistema lee las variables de entorno para la conexión en `config/database.php`. Si no se definen, utiliza valores por defecto compatibles con XAMPP:

| Variable | Descripción | Valor por Defecto |
|---|---|---|
| `DB_HOST` | Host del servidor de base de datos | `localhost` |
| `DB_PORT` | Puerto de conexión MySQL | `3306` |
| `DB_NAME` | Nombre de la base de datos | `asistencia_qr` |
| `DB_USER` | Usuario de la base de datos | `root` |
| `DB_PASS` | Contraseña de la base de datos | `""` (vacío) |

---

## 5. Mantenimiento y Respaldos

### 5.1. Respaldar la Base de Datos
Para generar un dump de respaldo de la base de datos en Docker:
```bash
docker exec asistencia_qr_db mysqldump -u root -prootpassword asistencia_qr > backup_asistencia_$(date +%Y%m%d).sql
```

En entornos tradicionales con `mysqldump`:
```bash
mysqldump -u root -p asistencia_qr > backup_asistencia.sql
```

### 5.2. Restaurar Respaldo
```bash
docker exec -i asistencia_qr_db mysql -u root -prootpassword asistencia_qr < backup_asistencia.sql
```

### 5.3. Agregar Nuevos Docentes
Para agregar nuevos docentes directamente mediante SQL, genera la clave con `password_hash()` de PHP (algoritmo BCRYPT) e insértala en la tabla `docentes`:
```sql
INSERT INTO docentes (nombre, usuario, password) 
VALUES ('Nombre Completo', 'nuevo.usuario', '$2y$10$HASH_GENERADO_DE_CONTRASENA');
```
