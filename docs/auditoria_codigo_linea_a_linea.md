# Auditoría Exhaustiva de Código Línea a Línea

**Sistema:** Control de Asistencia QR  
**Institución:** Instituto Superior Tecnológico Mayor Pedro Traversari (ISTPET)  
**Patrón Arquitectónico:** Modelo-Vista-Controlador (MVC) en PHP 8.2 nativo con PDO y MariaDB  
**Convención:** Documentación técnica estricta sin elementos gráficos informales ni emojis  

---

## 1. Estructura General del Código Fuente

El proyecto sigue una separación clara de responsabilidades en tres capas fundamentales:

```
asistencia/
├── config/
│   └── Database.php                 Conexión PDO singleton y resolución de variables de entorno
├── controllers/
│   ├── BaseController.php          Clase base con helpers de vista, JSON y redirección
│   ├── HomeController.php          Página principal e institucional
│   ├── AuthController.php          Autenticación de docentes y acceso de estudiantes
│   ├── DashboardController.php     Panel del profesor, métricas dinámicas y control de sesiones QR
│   ├── EstudianteController.php    CRUD de estudiantes, autogeneración correlativa y portal
│   ├── AsistenciaController.php   Escaneo de QR, registro de asistencia y API en tiempo real
│   └── ReporteController.php       Filtros avanzados y exportación a CSV, Excel y PDF
├── models/
│   ├── Docente.php                 Búsquedas y credenciales de docentes
│   ├── Estudiante.php              Gestión de estudiantes y cálculo correlativo de códigos
│   ├── Sesion.php                  Ciclo de vida de sesiones QR (creación, activación, cierre)
│   └── Asistencia.php              Persistencia de marcas, consultas en vivo y filtros
├── libs/
│   ├── fpdf.php                    Biblioteca base de generación PDF
│   └── ReportePdf.php              Extensión FPDF personalizada con membrete institucional
├── views/
│   ├── layouts/ (header, footer)   Estructura HTML5, navbar condicional y estilos
│   ├── home/ (index, institucional)Pantallas públicas informativas
│   ├── auth/ (login-docente, ...)  Formularios de acceso seguro con credenciales de prueba
│   ├── dashboard/ (index)          Proyección QR en vivo, polling cada 5s y métricas SQL
│   ├── estudiantes/ (index, portal)Gestión tabular, modal accesible y expediente del alumno
│   ├── asistencia/ (escanear, ...) Pantalla de marcación y confirmación
│   └── reportes/ (index)           Tablero de filtros, periodos rápidos y botones de exportación
├── public/
│   ├── index.php                   Enrutador frontal (Front Controller)
│   ├── .htaccess                   Reescritura de URLs amigables
│   └── assets/ (css, js, img)      Hojas de estilo CSS nativas y recursos estáticos
└── docs/                           Documentación técnica del sistema
```

---

## 2. Capa de Configuración y Base de Datos

### `config/Database.php`
* **Líneas 6-8:** Declaración de la clase `Database` con propiedad estática `$conexion` de tipo `?PDO` inicializada en `null`. Implementa el patrón Singleton para evitar la apertura de múltiples conexiones en una misma petición HTTP.
* **Líneas 10-19:** Método `conectar()`. Evalúa variables de entorno (`DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, `DB_PASS`). Si no están definidas, aplica los valores predeterminados para entornos XAMPP locales (`localhost:3306`, base `asistencia_qr`, usuario `root`, contraseña vacía).
* **Líneas 20-25:** Construcción del Data Source Name (DSN) con codificación `utf8mb4`. Inicialización de la instancia `PDO` configurando los atributos `ATTR_ERRMODE => ERRMODE_EXCEPTION` (disparo de excepciones ante errores SQL) y `ATTR_DEFAULT_FETCH_MODE => FETCH_ASSOC` (retorno de arreglos asociativos).
* **Líneas 26-28:** Bloque `catch (PDOException)` que captura fallos de conexión e interrumpe la ejecución con un mensaje controlado para no exponer credenciales.
* **Líneas 31-32:** Retorno de la instancia de conexión compartida.

---

## 3. Enrutador Frontal (Front Controller)

### `public/index.php`
* **Líneas 7-12:** Importación de los controladores mediante `require_once`.
* **Líneas 15-19:** Extracción del path de la URI solicitada mediante `parse_url` y obtención del método HTTP (`GET`, `POST`, transformando `HEAD` a `GET` para inspección de encabezados).
* **Líneas 22-26:** Cálculo automático del prefijo de subdirectorio (para compatibilidad total tanto en raíz de Docker como en subcarpetas de XAMPP como `/asistencia/public`). Normalización de la variable `$ruta`.
* **Líneas 29-115:** Sentencia `switch ("{$metodo} {$ruta}")` que despacha las 20 rutas disponibles:
  * `GET /`: Despacha `HomeController->index()`.
  * `GET /institucional`: Despacha `HomeController->institucional()`.
  * `GET /login` y `POST /login`: Despacha `AuthController->mostrarLoginDocente()` y `AuthController->loginDocente()`.
  * `GET /logout`: Despacha `AuthController->logoutDocente()`.
  * `GET /login-estudiante` y `POST /login-estudiante`: Despacha acceso para alumnos.
  * `GET /logout-estudiante`: Limpieza de sesión estudiantil.
  * `GET /estudiante/portal`: Despacha `EstudianteController->portal()`.
  * `GET /dashboard`: Despacha `DashboardController->index()`.
  * `POST /dashboard/sesion/crear`: Despacha `DashboardController->crearSesion()`.
  * `POST /dashboard/sesion/cerrar`: Despacha `DashboardController->cerrarSesion()`.
  * `GET /estudiantes`: Despacha listado de estudiantes en `EstudianteController->index()`.
  * `POST /estudiantes/crear`: Inserción de alumno.
  * `POST /estudiantes/actualizar`: Edición de alumno.
  * `POST /estudiantes/eliminar`: Baja lógica/física de alumno.
  * `GET /asistencia/escanear`: Formulario de registro de asistencia.
  * `POST /asistencia/registrar`: Procesamiento de marca de asistencia.
  * `GET /api/asistencias/activas`: Endpoint JSON para refresco automático de la tabla del docente.
  * `GET /reportes`: Tablero de reportes interactivo.
  * `GET /reportes/csv`: Descarga de archivo CSV delimitado por comas con codificación UTF-8 BOM.
  * `GET /reportes/excel`: Descarga de archivo XML/HTML compatible nativo de Microsoft Excel.
  * `GET /reportes/pdf`: Generación y descarga de informe PDF vectorial.
* **Líneas 117-121:** Caso `default`. Emite código de estado `404 Not Found` y carga `views/errors/404.php`.

---

## 4. Capa de Modelos

### `models/Docente.php`
* **Líneas 10-17:** `buscarPorUsuario(string $usuario)`: Ejecuta sentencia preparada `SELECT * FROM docentes WHERE usuario = ? LIMIT 1`. Retorna el registro asociativo del docente o `null`.
* **Líneas 20-27:** `buscarPorId(int $id)`: Consulta por clave primaria omitiendo la contraseña cifrada para seguridad en consultas informativas.

### `models/Estudiante.php`
* **Líneas 10-26:** `listar(string $busqueda)`: Si se recibe parámetro de búsqueda, aplica un `WHERE codigo LIKE ? OR nombre LIKE ? OR apellido LIKE ? OR carrera LIKE ?` con orden ascendente por nombre. En caso contrario, lista la totalidad del alumnado.
* **Líneas 29-36:** `buscarPorCodigo(string $codigo)`: Consulta parametrizada por el identificador único del estudiante (`LIMIT 1`).
* **Líneas 39-46:** `buscarPorId(int $id)`: Consulta parametrizada por identificador numérico interno.
* **Líneas 49-55:** `crear(codigo, nombre, apellido, carrera)`: Inserta un nuevo alumno mediante sentencia preparada con parámetros posicionales.
* **Líneas 58-64:** `actualizar(id, codigo, nombre, apellido, carrera)`: Modifica los cuatro atributos de un alumno identificado por su `id`.
* **Líneas 67-72:** `eliminar(int $id)`: Ejecuta `DELETE FROM estudiantes WHERE id = ?`.
* **Líneas 75-81:** `contar()`: Ejecuta `SELECT COUNT(*) as total FROM estudiantes` para alimentar de manera dinámica el indicador del panel de control.
* **Líneas 84-102:** `sugerirSiguienteCodigo()`: Consulta todos los códigos con prefijo `EST%`. Mediante expresiones regulares extrae los sufijos numéricos, identifica el valor entero más alto e incrementa en uno formateando a 3 dígitos con ceros a la izquierda (ejemplo: si el mayor es `EST008`, genera automáticamente `EST009`).

### `models/Sesion.php`
* **Líneas 10-18:** `obtenerActiva(int $docenteId)`: Recupera la sesión con estado `activa = 1` perteneciente al docente, ordenada por `id DESC LIMIT 1`.
* **Líneas 21-33:** `buscarPorCodigoActiva(string $codigoSesion)`: Realiza un `INNER JOIN` entre sesiones y docentes para validar la vigencia de la clase cuando el estudiante escanea el código QR o ingresa manualmente.
* **Líneas 36-43:** `crear(docenteId, codigoSesion, materia)`: Inserta una sesión activa asignando `CURDATE()` y `CURTIME()`.
* **Líneas 46-52:** `cerrar(sesionId, docenteId)`: Modifica la sesión estableciendo `activa = 0` y registrando `hora_fin = CURTIME()`.
* **Líneas 55-68:** `listarHistorial(int $docenteId, int $limite)`: Realiza un `LEFT JOIN` con la tabla `asistencias` y agrupa por `s.id` para obtener el total de alumnos asistentes en cada sesión pasada.
* **Líneas 71-78:** `contarPorDocente(int $docenteId)`: Consulta `SELECT COUNT(*) as total FROM sesiones WHERE docente_id = ?`.

### `models/Asistencia.php`
* **Líneas 10-17:** `registrar(int $sesionId, int $estudianteId)`: Inserta la marca con `CURDATE()` y `CURTIME()`.
* **Líneas 20-27:** `existe(int $sesionId, int $estudianteId)`: Regla de integridad de negocio. Verifica si un alumno ya registró asistencia en la sesión para evitar registros dobles.
* **Líneas 30-41:** `listarPorSesion(int $sesionId)`: Recupera las marcas de asistencia en orden descendente con datos del estudiante para la actualización en vivo.
* **Líneas 44-56:** `listarPorEstudiante(int $estudianteId)`: Historial académico de un alumno individual con datos de materia y docente.
* **Líneas 59-100:** `filtrar(docenteId, inicio, fin, materia, busqueda)`: Generador de consultas dinámicas con array de parámetros preparados para el generador de reportes.
* **Líneas 103-114:** `contarHoyPorDocente(int $docenteId)`: Cuenta las asistencias del día (`CURDATE()`) asociadas a las sesiones del docente.

---

## 5. Capa de Controladores

### `controllers/BaseController.php`
* **Líneas 8-18:** `vista(string $nombreVista, array $datos)`: Convierte la notación por puntos a rutas de carpetas (ej. `reportes.index` a `views/reportes/index.php`), realiza `extract($datos)` e incluye el archivo PHP correspondiente.
* **Líneas 21-26:** `json(array $datos)`: Establece la cabecera `application/json; charset=utf-8`, serializa el arreglo mediante `json_encode` y termina la ejecución con `exit`.
* **Líneas 29-34:** `redireccionar(string $ruta)`: Construye la URL absoluta con el prefijo base del servidor y envía el encabezado `Location`.
* **Líneas 37-41:** `obtenerRutaBase()`: Normaliza la ruta relativa del script ejecutable para garantizar portabilidad multiplataforma.

### `controllers/AuthController.php`
* **Líneas 33-60:** `loginDocente()`: Valida la presencia de usuario y contraseña. Emplea `password_verify` para contrastar el hash almacenado en base de datos. Al tener éxito, inicializa las variables de sesión `docente_id`, `docente_nombre` y `docente_usuario`.
* **Líneas 63-71:** `logoutDocente()`: Destruye las variables de sesión del docente y redirecciona al formulario de acceso.
* **Líneas 90-116:** `loginEstudiante()`: Normaliza y valida el código institucional ingresado. Si existe en la base de datos, inicializa la sesión del estudiante para su expediente académico.

### `controllers/DashboardController.php`
* **Líneas 10-18:** `verificarDocente()`: Middleware de autorización que protege las rutas del profesor impidiendo accesos anónimos.
* **Líneas 20-76:** `index()`: Consulta la sesión activa del docente, las tres métricas dinámicas (`totalEstudiantes`, `asistenciasHoy`, `totalSesiones`) y las últimas sesiones del historial. Detecta si la petición proviene de `localhost` para informar sobre la configuración de red Wi-Fi para dispositivos móviles.
* **Líneas 78-111:** `crearSesion()`: Valida la presencia y consistencia de los campos `carrera`, `nivel` y `materia` contra catálogos permitidos. Si existía una sesión previa activa, la finaliza automáticamente. Genera un identificador aleatorio criptográficamente seguro de 8 caracteres alfanuméricos mediante `bin2hex(random_bytes(4))` y registra la nueva clase.
* **Líneas 113-125:** `cerrarSesion()`: Finaliza manualmente la clase activa del docente.

### `controllers/EstudianteController.php`
* **Líneas 20-43:** `index()`: Recupera la lista filtrada o total de estudiantes, el total registrado y calcula el siguiente código sugerido mediante `sugerirSiguienteCodigo()`.
* **Líneas 45-78:** `crear()`: Valida que ningún campo esté vacío, que el código tenga entre 3 y 15 caracteres alfanuméricos (`/^[A-Za-z0-9_-]+$/`), nombres y apellidos con longitud mínima de 2 caracteres, pertenencia de la carrera al catálogo institucional y ausencia de duplicados en la base de datos.
* **Líneas 80-142:** `actualizar()`: Valida la integridad de datos del estudiante existente y garantiza que un cambio de código no colisione con otro alumno.
* **Líneas 144-156:** `eliminar()`: Valida el ID numérico y ejecuta la baja.
* **Líneas 158-191:** `portal()`: Interfaz académica exclusiva para el alumno autenticado con métricas personales y tabla histórica de asistencias.

### `controllers/AsistenciaController.php`
* **Líneas 13-32:** `mostrarEscanear()`: Si se recibe un código de sesión por parámetro GET (típicamente desde el QR), busca y precarga los datos de la materia en el formulario.
* **Líneas 35-118:** `registrar()`: Proceso de validación en cinco fases:
  1. Comprobación de campos obligatorios no vacíos.
  2. Validación de sesión existente y activa.
  3. Comprobación de estudiante matriculado en el catálogo institucional.
  4. Prevención de doble asistencia en la misma clase.
  5. Registro persistente en base de datos e inicio automático de sesión en el portal del alumno.
* **Líneas 121-151:** `apiListarActivas()`: Endpoint REST/JSON invocado por JavaScript cada 5 segundos para actualizar en vivo la cuadrícula de asistencias del proyector del docente.

### `controllers/ReporteController.php`
* **Líneas 20-51:** `index()`: Procesa filtros de fechas, materia y búsqueda por nombre/código, retornando la lista tabular y totales.
* **Líneas 53-101:** `exportarCsv()`: Limpia búferes de salida con `ob_end_clean()`, emite encabezados MIME `text/csv`, inyecta el Byte Order Mark UTF-8 (`\xEF\xBB\xBF`) para compatibilidad directa con Microsoft Excel y vuelca los registros con `fputcsv`.
* **Líneas 103-185:** `exportarExcel()`: Genera una estructura HTML semántica con metadatos XML de Excel (`urn:schemas-microsoft-com:office:excel`), estilos tipográficos institucionales, formato de celdas de texto (`mso-number-format:"\@"`) y filas cebra.
* **Líneas 187-251:** `exportarPdf()`: Instancia `ReportePdf` en orientación apaisada (Landscape), calcula anchos milimétricos proporcionales, maneja encabezados de página automáticos y descarga el documento formal.

---

## 6. Módulo de Generación de Documentos PDF

### `libs/ReportePdf.php`
* **Líneas 1-25:** Subclase de `FPDF`. Sobrescribe los métodos `Header()` y `Footer()`.
* **Líneas 27-65:** En `Header()`: Dibuja barra superior con los colores institucionales (Azul Marino `#1A2B4C` y Dorado `#B8912E`), coloca el logotipo del ISTPET, imprime la denominación oficial y genera una franja de metadatos (docente titular, materia, rango de fechas y fecha/hora de emisión).
* **Líneas 67-85:** En `Footer()`: Traza línea de separación sutil, texto legal de certificación y paginador automático mediante `{nb}` (`PageNo() / AliasNbPages()`).
* **Líneas 87-108:** Método `celdaAjustada()`: Calcula mediante `GetStringWidth()` si un texto extenso supera el ancho de la celda; en tal caso, reduce gradualmente el tamaño tipográfico para evitar desbordes visuales en nombres largos o asignaturas complejas.
* **Líneas 110-117:** Método `conv()`: Convierte cadenas codificadas en UTF-8 a ISO-8859-1 mediante `mb_convert_encoding` o `iconv` para compatibilidad nativa con las fuentes estándar de FPDF.

---

## 7. Interfaces de Usuario y Hojas de Estilo

### `views/layouts/header.php` y `footer.php`
* Encabezado unificado con metadatos para visualización responsiva (`viewport`).
* Detección automática de URL activa para marcar la pestaña correspondiente sin depender de scripts en cliente.
* Barra de navegación con identidad institucional y separación de roles (etiqueta DOCENTE o ESTUDIANTE).

### `views/dashboard/index.php`
* Visualización dual: si no hay sesión abierta, renderiza el formulario de inicio de clase con sugerencias dinámicas de materias; si hay sesión activa, muestra el código QR dinámico generado con la API de `quickchart.io` en tamaño 200x200px.
* Modo Proyector a pantalla completa en ventana modal accesible para proyectores de aula o pantallas de televisión.
* Botón de copiado de enlace con notificación flotante (toast) e indicador de red local si se accede desde `localhost`.
* Polling asíncrono con JavaScript mediante `fetch('/api/asistencias/activas')` cada 5000 ms con emisión de alerta sonora automática mediante la Web Audio API cada vez que ingresa un nuevo estudiante en vivo, configurable mediante botón silenciador.

### `views/estudiantes/index.php`
* Formulario modal para alta y modificación con cabecera flex espaciada (`.modal-header-row`) y botón de cierre en esquina (`.modal-close-btn`).
* Sugerencia automática del siguiente correlativo (`EST009`) con foco inmediato en el campo de nombres para agilizar el registro.
* Validación estricta con expresiones regulares HTML5 y validación previa en JavaScript (`validarEstudiante(e)`).

### `views/asistencia/escanear.php`
* Escaneo directo con cámara web / móvil integrada mediante `navigator.mediaDevices.getUserMedia` y alternativa fotográfica automática mediante `<input capture="environment">` para redes locales sin HTTPS.
* Decodificación dual de alta velocidad: utiliza la API nativa del navegador `BarcodeDetector` cuando está disponible y conmuta automáticamente a la biblioteca autónoma `jsQR` renderizada sobre `HTMLCanvasElement` optimizado a 800px.
* Visor HUD con animación de barrido láser (`.scanner-scan-bar`) y esquinas estilizadas doradas.
* Feedback acústico en tiempo real: emite un sonido armónico sintetizado con la Web Audio API (`AudioContext`, 880 Hz a 1760 Hz) al momento exacto de reconocer el código QR.
* Autocompletado del código de sesión detectado, apagado automático del hardware de la cámara para ahorro de batería y desplazamiento de foco directo al campo de código de estudiante.

### `views/asistencia/resultado.php`
* Pantalla de confirmación y acreditación de asistencia.
* Feedback acústico instantáneo mediante oscilador de audio sintetizado: acorde armónico ascendente de confirmación en caso de éxito, y doble pulso grave en caso de error o intento de duplicidad.

### `views/reportes/index.php`
* Barra de filtros avanzados con selector rápido de periodos: *Hoy*, *Este Mes*, *Últimos 30 Días* y *Histórico Completo*.
* Selector de fechas con validación en cliente para impedir rangos invertidos.
* Botones directos de descarga para los tres formatos oficiales: CSV, Excel y PDF.

---

## 8. Análisis de Seguridad e Integridad

1. **Inyección SQL:** Mitigada al 100% mediante el uso de PDO y consultas preparadas parametrizadas en todas las interacciones con la base de datos.
2. **Cross-Site Scripting (XSS):** Todas las salidas de variables dinámicas en vistas PHP se encuentran escapadas con `htmlspecialchars($var, ENT_QUOTES, 'UTF-8')`.
3. **Control de Acceso y Sesiones:** Rutas protegidas mediante verificación de estado de sesión (`$_SESSION['docente_id']` y `$_SESSION['estudiante_id']`). Redirección inmediata al formulario de autenticación ante peticiones no autorizadas.
4. **Manejo de Búferes:** Uso de `ob_end_clean()` previo al envío de descargas binarias (CSV, Excel y PDF) para garantizar que ningún espacio en blanco corrompa los encabezados HTTP ni los archivos descargados.
5. **Cero Dependencias Externas en Producción:** El sistema funciona de manera autónoma con PHP nativo y FPDF embebido en la carpeta `libs/`, sin requerir gestores complejos ni dependencias frágiles.
6. **Diseño Responsivo y Ergonomía Móvil:** Arquitectura de hojas de estilo en cascada (`public/assets/css/style.css`) con media queries por rangos (1024px, 900px, 768px, 480px), prevención global de desbordamiento horizontal (`overflow-x: hidden`), tablas con desplazamiento inercial nativo y áreas táctiles mínimas de 44px para dispositivos móviles.
