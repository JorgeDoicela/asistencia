# Sistema de Asistencia QR - ISTPET (Arquitectura MVC Clasica)

Sistema web educativo desarrollado bajo el patron **Modelo - Vista - Controlador (MVC)**, disenado para el registro y monitoreo de asistencia mediante codigos QR en tiempo real.

Estructurado de forma intuitiva y didactica para estudiantes de tercer semestre de desarrollo de software.

---

## Estructura del Proyecto (MVC)

```text
asistencia/
├── config/
│   └── Database.php            # Conexion simple y segura mediante PDO
├── models/                     # MODELOS: Consultas SQL y logica de datos
│   ├── Docente.php             # Login y busqueda de docentes
│   ├── Estudiante.php          # CRUD completo del catalogo de alumnos
│   ├── Sesion.php              # Creacion, cierre y QR de clases
│   └── Asistencia.php          # Registro de escaneo, filtros y conteos
├── controllers/                # CONTROLADORES: Reciben datos y llaman a los modelos
│   ├── BaseController.php      # Metodos auxiliares para cargar vistas y redirigir
│   ├── HomeController.php      # Pantalla de bienvenida e institucional
│   ├── AuthController.php      # Inicio y cierre de sesion (docente y alumno)
│   ├── DashboardController.php # Panel de control en vivo y generador QR
│   ├── EstudianteController.php# Administracion de alumnos y portal estudiantil
│   ├── AsistenciaController.php# Confirmacion del QR y API de tiempo real
│   └── ReporteController.php   # Filtros y exportación multiformato (CSV, Excel, PDF)
├── libs/                       # BIBLIOTECAS: Motor FPDF y reporte institucional
│   ├── ReportePdf.php          # Clase personalizada con membrete ISTPET y tabla A4
│   └── fpdf/                   # FPDF 1.86 autónomo (sin dependencias Composer)
├── views/                      # VISTAS: Pantallas con HTML y datos
│   ├── layouts/                # Encabezado (header.php) y pie de pagina (footer.php)
│   ├── auth/                   # Formularios de acceso
│   ├── dashboard/              # Panel del docente con tabla en vivo
│   ├── estudiantes/            # Listado, nuevo alumno y portal del estudiante
│   ├── asistencia/             # Confirmacion de escaneo del QR
│   ├── reportes/               # Tabla con filtros y botones de descarga (CSV, Excel, PDF)
│   └── errors/                 # Pagina 404
├── public/                     # Carpeta publica accesible desde el navegador
│   ├── index.php               # Front Controller (enrutador con switch/case)
│   ├── .htaccess               # Enrutamiento de URLs amigables
│   └── assets/                 # Hojas de estilo CSS, scripts JS e imágenes
├── database/                   # Script de creacion y datos de prueba
│   └── database.sql
└── docs/                       # Documentacion tecnica y manuales
```

---

## Como Funciona el Patron MVC en este Proyecto

1. **El Usuario hace una peticion:** Escribe una direccion en el navegador (ejemplo: `/estudiantes`).
2. **El Enrutador (`public/index.php`):** Detecta la ruta y llama al controlador correspondiente (`EstudianteController`).
3. **El Controlador (`controllers/`):** 
   * Recibe la peticion y los datos de formularios (`$_POST`).
   * Le pide la informacion al **Modelo** (`Estudiante::listar()`).
   * Envia los resultados a la **Vista** (`$this->vista('estudiantes.index', $datos)`).
4. **El Modelo (`models/`):** Ejecuta la consulta SQL con sentencias preparadas en la base de datos MariaDB/MySQL.
5. **La Vista (`views/`):** Imprime el HTML con los datos recibidos y se muestra en pantalla.

---

## Despliegue y Ejecución en XAMPP y MySQL Workbench

El sistema corre de forma nativa sobre servidores web Apache con PHP 8.x y bases de datos MySQL / MariaDB instaladas localmente.

### Pasos de Instalación:

1. **Colocar el Proyecto:**
   * Copia o clona la carpeta `asistencia` dentro del directorio `htdocs` de tu servidor XAMPP:
     `C:\xampp\htdocs\asistencia` (en Windows) o `/opt/lampp/htdocs/asistencia` (en Linux).

2. **Crear e Importar la Base de Datos:**
   * Abre **MySQL Workbench** o **phpMyAdmin** (`http://localhost/phpmyadmin`).
   * Abre y ejecuta el script SQL ubicado en [`database/database.sql`](database/database.sql).
   * Este script creará automáticamente la base de datos `asistencia_qr`, sus 4 tablas relacionales (`docentes`, `estudiantes`, `sesiones`, `asistencias`) y cargará los datos iniciales de prueba.

3. **Iniciar Servicios:**
   * Abre el Panel de Control de XAMPP e inicia los módulos de **Apache** y **MySQL**.

4. **Acceder a la Aplicación:**
   * Abre tu navegador web e ingresa a:
     ```text
     http://localhost/asistencia/
     ```
   * *Para acceso desde teléfonos móviles en la misma red Wi-Fi:*
     ```text
     http://<IP_LOCAL>/asistencia/
     ```
     *(ejemplo: `http://192.168.1.15/asistencia/`)*.

---

## Credenciales de Acceso

| Rol | Usuario / Código | Contraseña |
|---|---|---|
| Docente Titular | `profesor` | `12345` |
| Docente Demo | `Demo` | `Demo123` |
| Estudiantes | `EST001` a `EST008` | (Solo requiere el código institucional) |

---

## Documentación Técnica Detallada

En la carpeta [`docs/`](docs/) encontrarás las especificaciones completas:
* [docs/auditoria_codigo_linea_a_linea.md](docs/auditoria_codigo_linea_a_linea.md): Auditoría exhaustiva línea a línea de todo el código fuente del sistema.
* [docs/arquitectura.md](docs/arquitectura.md): Explicación del patrón MVC pedagógico y flujo de peticiones.
* [docs/diseno_ux_ui_y_flujos.md](docs/diseno_ux_ui_y_flujos.md): Diseño UX/UI, escáner de cámara HUD, feedback de audio y adaptabilidad móvil.
* [docs/modulo_reportes_y_exportacion.md](docs/modulo_reportes_y_exportacion.md): Especificación del módulo de reportes y exportaciones en CSV, Excel y PDF.
* [docs/base_de_datos.md](docs/base_de_datos.md): Esquema relacional, tablas, índices y reglas de integridad.
* [docs/api_y_rutas.md](docs/api_y_rutas.md): Catálogo de endpoints HTTP y API JSON de tiempo real.
* [docs/manual_usuario.md](docs/manual_usuario.md): Manual de uso para docentes y estudiantes.
* [docs/guia_pruebas.md](docs/guia_pruebas.md): Guía de pruebas paso a paso desde PC y móvil por Wi-Fi.
* [docs/despliegue_y_mantenimiento.md](docs/despliegue_y_mantenimiento.md): Configuración en XAMPP, MySQL Workbench y respaldos.
