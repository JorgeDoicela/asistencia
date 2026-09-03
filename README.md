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

## Despliegue y Ejecución del Proyecto

El sistema puede ejecutarse en cualquier computadora mediante dos métodos estándar según el software que tengas instalado:

---

### Método 1: Con XAMPP (Apache + MySQL / phpMyAdmin)

Ideal si utilizas la suite completa de XAMPP:

1. **Ubicación de la carpeta:**
   * Copia o clona la carpeta `asistencia` dentro de `htdocs`:
     * Windows: `C:\xampp\htdocs\asistencia`
     * Linux: `/opt/lampp/htdocs/asistencia`
2. **Importar la Base de Datos:**
   * Inicia **Apache** y **MySQL** desde el Panel de XAMPP.
   * Ingresa a phpMyAdmin (`http://localhost/phpmyadmin`).
   * Crea una base de datos o importa directamente el script [`database/database.sql`](database/database.sql).
3. **Acceder a la Aplicación:**
   * Computadora local: `http://localhost/asistencia/`
   * Teléfono móvil en la misma red Wi-Fi: `http://<IP_LOCAL>/asistencia/` *(ejemplo: `http://192.168.1.15/asistencia/`)*.

---

### Método 2: Con MySQL Workbench + PHP Standalone (Sin XAMPP)

Ideal si solo tienes instalado el motor MySQL oficial con MySQL Workbench:

1. **Importar la Base de Datos en MySQL Workbench:**
   * Abre **MySQL Workbench** y conéctate a tu instancia local (`localhost:3306`).
   * Abre el archivo [`database/database.sql`](database/database.sql) (*File -> Open SQL Script*).
   * Ejecuta el script (ícono de rayo). Se creará la base de datos `asistencia_qr` con todas sus tablas y registros de prueba.
2. **Configuración de Contraseña ([`config/Database.php`](config/Database.php)):**
   * El sistema viene preconfigurado con la clave `12345`. Si tu usuario `root` de MySQL tiene otra clave, ajústala en la línea 18 de `config/Database.php` o mediante la variable de entorno `DB_PASS`.
3. **Instalar y Configurar PHP (si no lo tienes en el sistema):**
   * Descarga PHP 8.2 o 8.3 para Windows (Non-Thread Safe o Thread Safe) y descomprímelo en `C:\php`.
   * En `C:\php\php.ini`, asegúrate de tener habilitadas las extensiones:
     ```ini
     extension_dir = "C:\php\ext"
     extension=pdo_mysql
     extension=mbstring
     ```
4. **Iniciar el Servidor Web Integrado:**
   * Abre una terminal (PowerShell o CMD) en la carpeta del proyecto y ejecuta:
     ```powershell
     php -S 0.0.0.0:8085 -t public public/index.php
     ```
5. **Acceder a la Aplicación:**
   * Computadora local: `http://localhost:8085/`
   * Teléfono móvil en la misma red Wi-Fi: `http://<IP_LOCAL>:8085/` *(ejemplo: `http://192.168.1.15:8085/`)*.

---

## Credenciales de Acceso

| Rol | Usuario / Código | Contraseña | Funcionalidad Principal |
|---|---|---|---|
| **Administrador General** | `admin` | `admin123` | Supervisión global, personal docente, reportes consolidados y cierre forzado |
| **Docente Titular** | `profesor` | `12345` | Generación de QR en vivo, monitoreo de aula y reportes propios |
| **Docente Demo** | `Demo` | `Demo123` | Docente secundario para pruebas multiusuario |
| **Estudiantes** | `EST001` a `EST008` | *(Ingreso directo con código)* | Escaneo QR y expediente histórico personal |

---

## Documentación Técnica Consolidada para Estudio y Defensa

Toda la documentación técnica del proyecto se encuentra estructurada y consolidada en **4 documentos maestros** ubicados en [`docs/`](docs/) sin omisión de detalles:

1. **[docs/1_arquitectura_mvc_y_base_de_datos.md](docs/1_arquitectura_mvc_y_base_de_datos.md):**
   * Fundamentos del patrón MVC clásico y ciclo de vida de peticiones HTTP.
   * Diccionario completo de base de datos (`docentes`, `estudiantes`, `sesiones`, `asistencias`).
   * Relaciones, claves primarias, foráneas y restricción `UNIQUE` de asistencia única.
   * Catálogo de rutas y API JSON de tiempo real (`/api/asistencias/activas`).

2. **[docs/2_auditoria_codigo_linea_a_linea.md](docs/2_auditoria_codigo_linea_a_linea.md):**
   * Auditoría técnica línea a línea de cada archivo del sistema.
   * Análisis de Controladores, Modelos, Vistas y Bibliotecas (`ReportePdf.php`).
   * Especificaciones de exportación multiformato (CSV BOM UTF-8, Excel SpreadsheetML y PDF A4 Landscape).
   * Análisis de seguridad (PDO parametrizado anti-SQLi, escapado XSS y control de sesiones).

3. **[docs/3_diseno_ux_ui_y_componentes.md](docs/3_diseno_ux_ui_y_componentes.md):**
   * Filosofía de diseño, principios de usabilidad y mapa de flujos interactivo.
   * Modo Proyector de aula para pantallas gigantes.
   * Escáner de cámara integrado con detección por hardware y fallback local a 800px.
   * Feedback acústico con Web Audio API pura (`AudioContext`).
   * Sugerencia correlativa de códigos de alumnos (`EST009`).
   * Matriz responsiva en cascada (Desktop, Tablets, Móviles y Pantallas estrechas).

4. **[docs/4_guia_instalacion_pruebas_y_defensa.md](docs/4_guia_instalacion_pruebas_y_defensa.md):**
   * Guía completa de instalación para XAMPP y MySQL Workbench + PHP Standalone.
   * Tabla de resolución de problemas comunes (Troubleshooting).
   * Manual de usuario paso a paso para docentes y alumnos.
   * Guía de pruebas en PC y móvil por Wi-Fi.
   * Guía de defensa ante el tribunal docente con preguntas frecuentes y respuestas recomendadas.
