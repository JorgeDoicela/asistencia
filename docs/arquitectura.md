# Arquitectura MVC - ISTPET Asistencia QR

## 1. Que es el Modelo - Vista - Controlador (MVC)?

El patron MVC divide el codigo de una aplicacion web en tres componentes esenciales para evitar mezclar codigo HTML con consultas a la base de datos:

1. **Modelo (Model):** Representa las tablas y la informacion. Es el unico lugar del sistema donde se escriben consultas SQL (`SELECT`, `INSERT`, `UPDATE`, `DELETE`).
2. **Vista (View):** Es la interfaz visual que ve el usuario. Contiene HTML, estilos CSS y muestra las variables que le envia el controlador.
3. **Controlador (Controller):** Es el coordinador. Recibe las acciones del usuario (un clic en un enlace o el envio de un formulario), le pide datos al Modelo y decide que Vista debe mostrarse.

---

## 2. Diagrama del Flujo de Trabajo

```
   [1] El usuario hace clic o envia un formulario
                           │
                           ▼
             ┌───────────────────────────┐
             │    public/index.php       │
             │   (Enrutador Principal)   │
             └─────────────┬─────────────┘
                           │ [2] Llama al controlador
                           ▼
             ┌───────────────────────────┐
             │       Controlador         │
             │   (controllers/*.php)     │
             └───────┬───────────┬───────┘
                     │           │
     [3] Pide datos  │           │ [5] Envia datos a mostrar
                     ▼           ▼
         ┌───────────────┐   ┌───────────────┐
         │    Modelo     │   │     Vista     │
         │ (models/*.php)│   │ (views/*.php) │
         └───────┬───────┘   └───────────────┘
                 │
  [4] Consulta   ▼
         ┌───────────────┐
         │ Base de Datos │
         │   (MariaDB)   │
         └───────────────┘
```

---

## 3. Descripcion de las Carpetas del Proyecto

### 3.1. `config/`
* **`Database.php`:** Abre la conexion a MySQL usando **PDO**. Es el puente que usan los modelos para consultar datos.

### 3.2. `models/`
Contiene una clase por cada tabla principal de la base de datos:
* **`Docente.php`:** Buscar docentes por usuario para iniciar sesion.
* **`Estudiante.php`:** Funciones de crear, listar, buscar por codigo, modificar y borrar alumnos.
* **`Sesion.php`:** Crea sesiones con codigo QR y las finaliza.
* **`Asistencia.php`:** Registra asistencias de alumnos, comprueba que no se repitan y genera filtros para reportes.

### 3.3. `controllers/`
Reciben las peticiones del usuario y unen a los modelos con las vistas:
* **`BaseController.php`:** Proporciona los metodos `$this->vista()` para cargar pantallas y `$this->redireccionar()` para cambiar de pagina.
* **`HomeController.php`:** Muestra la bienvenida y la pagina institucional.
* **`AuthController.php`:** Valida usuarios y contrasenas con `password_verify`.
* **`DashboardController.php`:** Controla el panel del docente y el codigo QR en vivo.
* **`EstudianteController.php`:** Procesa las altas, bajas y cambios de estudiantes.
* **`AsistenciaController.php`:** Recibe el escaneo del QR, valida que la sesion este activa y guarda la asistencia.
* **`ReporteController.php`:** Filtra asistencias por fecha, materia o estudiante, y genera descargas multiformato en CSV, Excel (.xls) y PDF institucional.

### 3.4. `libs/`
Bibliotecas y utilidades internas sin necesidad de dependencias externas:
* **`fpdf/`:** Motor FPDF 1.86 puro para la generación de archivos PDF vectoriales sin dependencias de Composer.
* **`ReportePdf.php`:** Clase personalizada que extiende FPDF para aplicar el membrete oficial del ISTPET, formato apaisado A4, colores corporativos y paginación automática.

### 3.5. `views/`
Archivos PHP dedicados exclusivamente al diseno visual:
* **`layouts/header.php` y `footer.php`:** Barra de navegacion superior y pie de pagina compartidos.
* **`auth/`:** Formularios para iniciar sesion.
* **`dashboard/`:** Panel del docente con la tarjeta del QR y la tabla en vivo.
* **`estudiantes/`:** Tabla de alumnos con buscador y ventana modal para agregar nuevos.
* **`asistencia/`:** Pantalla donde el estudiante confirma su codigo tras escanear el QR.
* **`reportes/`:** Tabla de filtros con botones de descarga directa en CSV, Excel y PDF.
* **`errors/404.php`:** Mensaje amigable si el usuario escribe una ruta inexistente.

### 3.6. `public/`
La unica carpeta a la que el navegador web tiene acceso directo:
* **`index.php`:** El archivo principal que recibe cualquier peticion web y mediante una instruccion `switch` llama al controlador adecuado.
* **`.htaccess`:** Redirige las URLs limpias (sin `.php`) hacia `index.php`.
* **`assets/`:** Contiene los archivos `css/`, `img/` y fuentes.
