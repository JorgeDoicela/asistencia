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
├── docs/                       # Documentacion tecnica y manuales
├── docker-compose.yml          # Despliegue automatico con Docker
└── Dockerfile                  # Configuracion de servidor PHP Apache
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

## Despliegue y Ejecución Dinámica

El sistema detecta automáticamente su entorno de ejecución (Docker, XAMPP, Nginx o IP de red local) y ajusta sus rutas y enlaces de forma 100% dinámica mediante `$base`.

### Opción 1: Con Docker (Contenerizado)
```bash
docker compose up -d --build
```
* **Aplicación Web:** `http://<HOST>:8080/` (ej. `http://localhost:8080/` o `http://<IP_LOCAL>:8080/`)
* **phpMyAdmin:** `http://<HOST>:8081/` (ej. `http://localhost:8081/`)

### Ejecutar Pruebas Automatizadas (Tests)
Para ejecutar la suite de pruebas unitarias y de integración:
```bash
docker compose exec web php tests/test_runner.php
```

### Opción 2: Con XAMPP / Apache Tradicional
1. Clona o copia la carpeta `asistencia` dentro del directorio web (`htdocs/` en Windows/Linux).
2. Importa el archivo `database/database.sql` en tu gestor de base de datos (`asistencia_qr`).
3. Accede desde tu navegador web a:
   ```text
   http://<HOST>/asistencia/
   ```
   *(ejemplo: `http://localhost/asistencia/` o `http://<IP_LOCAL>/asistencia/`)*.

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
* [docs/despliegue_y_mantenimiento.md](docs/despliegue_y_mantenimiento.md): Variables de entorno, Docker y respaldos.
