# Sistema de Asistencia QR (Docente)

Sistema web sencillo en HTML, CSS, JS y PHP para que un docente genere un código QR
de asistencia y los estudiantes registren su asistencia escaneándolo. Pensado para
correr en XAMPP (Apache + MySQL).

## Instalación

1. Copia la carpeta `asistencia_qr` dentro de `htdocs` de tu XAMPP
   (ejemplo: `C:\xampp\htdocs\asistencia_qr`).
2. Abre phpMyAdmin (http://localhost/phpmyadmin) y crea la base de datos
   importando el archivo `database.sql` (Importar > seleccionar archivo > Continuar).
   Esto crea las tablas y un docente de prueba.
3. Verifica los datos de conexión en `includes/db.php` (por defecto usuario `root`
   sin contraseña, que es lo normal en XAMPP).
4. Inicia Apache y MySQL desde el panel de XAMPP.
5. Entra a: http://localhost/asistencia_qr/

## Acceso de prueba

- Usuario: `profesor`
- Contraseña: `12345`

## Cómo funciona

1. El docente inicia sesión y genera el QR de una clase (esto crea una "sesión" activa).
2. Los estudiantes escanean el QR con su celular (o entran manualmente a `escanear.php`)
   e ingresan su código de estudiante para registrar su asistencia.
3. En el panel del docente la tabla de asistencias se actualiza sola cada 5 segundos
   (tiempo real) y se puede filtrar por fecha, estudiante o materia.
4. En "Estudiantes" se pueden agregar o eliminar estudiantes y sus códigos.
5. En "Reportes" se pueden filtrar asistencias por rango de fechas y exportarlas a CSV.

## Notas

- El código QR se genera usando una imagen de la API pública api.qrserver.com,
  por lo que se necesita conexión a internet al momento de mostrarlo (no para
  registrar asistencias, eso sí funciona en red local).
- El "tiempo real" se hace con un refresco automático (polling) cada 5 segundos
  vía JavaScript/fetch, no con WebSockets, para mantenerlo simple.
- Puedes agregar más docentes insertando filas en la tabla `docentes` con
  `password_hash()` de PHP para generar la contraseña.
