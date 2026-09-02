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
│   └── ReporteController.php   # Filtros de fecha y descarga en Excel (CSV)
├── views/                      # VISTAS: Pantallas con HTML y datos
│   ├── layouts/                # Encabezado (header.php) y pie de pagina (footer.php)
│   ├── auth/                   # Formularios de acceso
│   ├── dashboard/              # Panel del docente con tabla en vivo
│   ├── estudiantes/            # Listado, nuevo alumno y portal del estudiante
│   ├── asistencia/             # Confirmacion de escaneo del QR
│   ├── reportes/               # Tabla con filtros y boton de descarga
│   └── errors/                 # Pagina 404
├── public/                     # Carpeta publica accesible desde el navegador
│   ├── index.php               # Front Controller (enrutador con switch/case)
│   ├── .htaccess               # Enrutamiento de URLs amigables
│   └── assets/                 # Hojas de estilo CSS y scripts JS
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

## Despliegue y Ejecucion

### Opcion 1: Con Docker
```bash
docker compose up -d --build
```
* **Aplicacion:** http://localhost:8080/
* **phpMyAdmin:** http://localhost:8081/

### Opcion 2: Con XAMPP
1. Copia la carpeta `asistencia` dentro de `C:\xampp\htdocs\`.
2. Importa el archivo `database/database.sql` en phpMyAdmin (`asistencia_qr`).
3. Accede desde tu navegador a:
   ```text
   http://localhost/asistencia/
   ```

---

## Credenciales de Acceso

| Rol | Usuario / Codigo | Contrasena |
|---|---|---|
| Docente Titular | `profesor` | `12345` |
| Docente Demo | `Demo` | `Demo123` |
| Estudiantes | `EST001` a `EST008` | (Solo requiere el codigo) |

---

## Documentacion Detallada

En la carpeta [`docs/`](file:///c:/Users/DESARROLLADOR/Desktop/Proyectos/asistencia/docs/) encontraras guias para exponer y defender el proyecto:
* [docs/arquitectura.md](file:///c:/Users/DESARROLLADOR/Desktop/Proyectos/asistencia/docs/arquitectura.md): Explicacion del patron MVC pedagogico.
* [docs/base_de_datos.md](file:///c:/Users/DESARROLLADOR/Desktop/Proyectos/asistencia/docs/base_de_datos.md): Tablas y relaciones.
* [docs/api_y_rutas.md](file:///c:/Users/DESARROLLADOR/Desktop/Proyectos/asistencia/docs/api_y_rutas.md): Rutas y API JSON.
* [docs/manual_usuario.md](file:///c:/Users/DESARROLLADOR/Desktop/Proyectos/asistencia/docs/manual_usuario.md): Manual paso a paso.
* [docs/guia_pruebas.md](file:///c:/Users/DESARROLLADOR/Desktop/Proyectos/asistencia/docs/guia_pruebas.md): Guia de pruebas paso a paso desde PC y movil por Wi-Fi.
* [docs/despliegue_y_mantenimiento.md](file:///c:/Users/DESARROLLADOR/Desktop/Proyectos/asistencia/docs/despliegue_y_mantenimiento.md): Variables de entorno, Docker y respaldos.
