# Guía de Despliegue y Mantenimiento

Instrucciones técnicas para desplegar, configurar y respaldar el sistema en entornos de desarrollo y producción.

---

## 1. Requisitos del Sistema

* **PHP:** Versión 8.0 o superior (recomendado PHP 8.2).
* **Extensiones PHP:** `pdo`, `pdo_mysql`, `mbstring`.
* **Servidor Web:** Apache 2.4 con módulo `mod_rewrite` activado.
* **Base de Datos:** MySQL 8.0+ o MariaDB 10.4+ (gestionable mediante MySQL Workbench o phpMyAdmin).

---

## 2. Despliegue en XAMPP y Servidores Locales

El sistema está optimizado para ejecutarse directamente sobre XAMPP (Windows o Linux):

### 2.1. Instalación del Código
1. Copia o clona la carpeta `asistencia` dentro del directorio `htdocs` de XAMPP:
   * **Windows:** `C:\xampp\htdocs\asistencia`
   * **Linux:** `/opt/lampp/htdocs/asistencia`
2. Verifica que el módulo `mod_rewrite` de Apache se encuentre habilitado en `httpd.conf`.
3. Inicia los servicios de **Apache** y **MySQL** desde el Panel de Control de XAMPP.

### 2.2. Importación de la Base de Datos con MySQL Workbench o phpMyAdmin
1. Abre **MySQL Workbench** o **phpMyAdmin** (`http://localhost/phpmyadmin`).
2. Abre el archivo [`database/database.sql`](../database/database.sql).
3. Ejecuta el script completo. Se creará la base de datos `asistencia_qr`, las tablas relacionales y los datos de prueba.

### 2.3. Acceso al Sistema
* **Desde la Computadora:**
  ```text
  http://localhost/asistencia/
  ```
* **Desde Dispositivos Móviles (Red Wi-Fi):**
  ```text
  http://<IP_LOCAL>/asistencia/
  ```
  *(ejemplo: `http://192.168.1.15/asistencia/`)*.

---

## 3. Configuración de Base de Datos

El sistema lee los parámetros de conexión en `config/Database.php`. Por defecto utiliza los valores estándar de XAMPP y MySQL Workbench:

| Variable | Descripción | Valor por Defecto |
|---|---|---|
| `DB_HOST` | Host del servidor MySQL | `localhost` |
| `DB_PORT` | Puerto de conexión MySQL | `3306` |
| `DB_NAME` | Nombre de la base de datos | `asistencia_qr` |
| `DB_USER` | Usuario de la base de datos | `root` |
| `DB_PASS` | Contraseña de la base de datos | `""` (vacío) |

Si tu servidor MySQL Workbench cuenta con una contraseña personalizada para el usuario `root` (ej. `root`, `admin123`), puedes definir la variable de entorno `DB_PASS` o editar directamente el archivo `config/Database.php`.

---

## 4. Mantenimiento y Respaldos

### 4.1. Respaldar la Base de Datos
Desde MySQL Workbench (opción *Data Export*) o mediante la línea de comandos con `mysqldump`:
```bash
mysqldump -u root -p asistencia_qr > backup_asistencia.sql
```

### 4.2. Restaurar Respaldo
Desde MySQL Workbench (opción *Data Import*) o mediante la terminal:
```bash
mysql -u root -p asistencia_qr < backup_asistencia.sql
```

### 5.3. Agregar Nuevos Docentes
Para agregar nuevos docentes directamente mediante SQL, genera la clave con `password_hash()` de PHP (algoritmo BCRYPT) e insértala en la tabla `docentes`:
```sql
INSERT INTO docentes (nombre, usuario, password) 
VALUES ('Nombre Completo', 'nuevo.usuario', '$2y$10$HASH_GENERADO_DE_CONTRASENA');
```
