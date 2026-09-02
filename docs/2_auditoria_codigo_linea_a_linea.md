# Documento 2: Auditoría Exhaustiva de Código Línea a Línea

**Institución:** Instituto Superior Tecnológico Mayor Pedro Traversari (ISTPET)  
**Carrera:** Desarrollo de Software (Tercer Semestre)  
**Sistema:** Control y Registro de Asistencia QR en Tiempo Real  
**Enfoque:** Análisis técnico, línea a línea, de cada archivo, controlador, modelo, vista y biblioteca del sistema.

---

## 1. Capa de Configuración

### `config/Database.php`
* **Líneas 6-8:** Declaración de la clase `Database` con la propiedad estática privada `?PDO $conexion = null`. Implementa el patrón de diseño **Singleton** para garantizar una única conexión activa por ciclo de vida de la petición.
* **Líneas 10-18:** Método estático `conectar(): PDO`. Extrae las variables de entorno (`DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, `DB_PASS`) con valores de respaldo predeterminados (`localhost`, `3306`, `asistencia_qr`, `root`, `12345`).
* **Líneas 21-25:** Construcción del DSN de conexión `mysql:host=...;port=...;dbname=...;charset=utf8mb4`. Instanciación del objeto `PDO` configurando los atributos `ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION` (manejo de errores mediante excepciones) y `ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC` (retorno de filas como arreglos asociativos).
* **Líneas 26-28:** Bloque `catch (PDOException $e)` para captura de fallos de conexión sin exponer la traza completa ni contraseñas.

---

## 2. Enrutador Frontal y Despacho

### `public/index.php`
* **Líneas 3-8:** Verificación de entorno CLI `php_sapi_name() === 'cli-server'` para servir archivos estáticos (`.css`, `.js`, imágenes) de forma nativa sin pasar por el enrutador.
* **Líneas 10-15:** Importación de controladores mediante `require_once`.
* **Líneas 17-21:** Extracción del path de la URI solicitada mediante `parse_url` y normalización del método HTTP (`GET`, `POST`, transformando `HEAD` a `GET`).
* **Líneas 24-28:** Cálculo dinámico del prefijo de subdirectorio para compatibilidad total en subcarpetas de XAMPP (como `/asistencia`) o servidores virtuales en raíz. Normalización de la variable `$ruta`.
* **Líneas 31-120:** Sentencia `switch ("{$metodo} {$ruta}")` que despacha las rutas del sistema:
  * `GET /` e `institucional`: `HomeController`.
  * `GET/POST /login`, `GET /logout`, `GET/POST /login-estudiante`, `GET /logout-estudiante`: `AuthController`.
  * `GET /dashboard`, `POST /dashboard/sesion/crear`, `POST /dashboard/sesion/cerrar`: `DashboardController`.
  * `GET /estudiantes`, `POST /estudiantes/crear`, `POST /estudiantes/actualizar`, `POST /estudiantes/eliminar`, `GET /estudiante/portal`: `EstudianteController`.
  * `GET /asistencia/escanear`, `POST /asistencia/registrar`, `GET /api/asistencias/activas`: `AsistenciaController`.
  * `GET /reportes`, `GET /reportes/csv`, `GET /reportes/excel`, `GET /reportes/pdf`: `ReporteController`.
  * Bloque `default`: Envío de cabecera `HTTP/1.0 404 Not Found` y renderizado de la vista de error `views/errors/404.php`.

---

## 3. Capa de Controladores (`controllers/`)

### `controllers/BaseController.php`
* **Líneas 8-18:** Método protegido `vista(string $nombreVista, array $datos = [])`. Utiliza `extract($datos)` para exponer variables directamente en la vista y carga el archivo PHP correspondiente transformando puntos en barras (ej. `estudiantes.index` a `views/estudiantes/index.php`).
* **Líneas 21-26:** Método protegido `json(array $datos)`. Establece la cabecera `Content-Type: application/json; charset=utf-8`, serializa con `json_encode` y detiene el script con `exit`.
* **Líneas 29-34:** Método protegido `redireccionar(string $ruta)`. Concatena la ruta base calculada y emite la cabecera `Location: ...`.
* **Líneas 37-41:** Método público estático `obtenerRutaBase(): string`. Determina el prefijo de subcarpeta de ejecución.

### `controllers/HomeController.php`
* **Líneas 8-11:** `index()`. Carga la vista principal `views/home/index.php` con la selección de roles.
* **Líneas 14-17:** `institucional()`. Carga la vista `views/home/institucional.php` con la descripción de las 4 carreras técnicas del ISTPET.

### `controllers/AuthController.php`
* **Líneas 10-18:** `mostrarLoginDocente()`. Si existe `$_SESSION['docente_id']`, redirige directamente al dashboard; de lo contrario, renderiza `views/auth/login-docente.php`.
* **Líneas 21-42:** `loginDocente()`. Sanitiza `usuario` y `password`, consulta al modelo `Docente::buscarPorUsuario($usuario)` y valida la contraseña mediante `password_verify($password, $docente['password'])`. Si es correcta, regenera el ID de sesión, guarda las variables de sesión y redirige a `/dashboard`. En caso de fallo, recarga el formulario con mensaje de error.
* **Líneas 45-53:** `logoutDocente()`. Limpia las variables de sesión y redirige a `/login`.
* **Líneas 56-82:** `loginEstudiante()` y `logoutEstudiante()`. Permite el acceso al expediente del alumno mediante su código institucional validado en `Estudiante::buscarPorCodigo()`.

### `controllers/DashboardController.php`
* **Líneas 10-17:** Constructor con verificación de seguridad `requiereDocente()`. Si no hay sesión docente, redirige inmediatamente a `/login`.
* **Líneas 20-56:** `index()`. Obtiene la sesión activa mediante `Sesion::obtenerActiva($docenteId)`. Si existe, calcula la URL de escaneo (`$urlRegistro`), genera la URL de la imagen del código QR con codificación URL segura, consulta los asistentes de la clase y las métricas estadísticas de la jornada.
* **Líneas 59-80:** `crearSesion()`. Valida los campos de Carrera, Nivel y Materia. Genera un token aleatorio de 8 caracteres en mayúsculas mediante `bin2hex(random_bytes(4))` y persiste la nueva clase mediante `Sesion::crear()`.
* **Líneas 83-91:** `cerrarSesion()`. Invoca `Sesion::cerrar($sesionId, $docenteId)` para finalizar la admisión de asistencias y redirige al dashboard.

### `controllers/EstudianteController.php`
* **Líneas 15-28:** `index()`. Obtiene el parámetro opcional `buscar`. Si existe, ejecuta `Estudiante::buscar($criterio)`; de lo contrario, `Estudiante::listar()`. Calcula el siguiente código sugerido mediante `Estudiante::sugerirSiguienteCodigo()`.
* **Líneas 31-48:** `crear()`. Valida que el código no exista previamente mediante `Estudiante::buscarPorCodigo()` e inserta el nuevo alumno.
* **Líneas 51-78:** `actualizar()` y `eliminar()`. Procesa la edición y baja de alumnos en la base de datos.
* **Líneas 81-110:** `portal()`. Muestra el expediente académico del estudiante autenticado, con contadores de clases asistidas, materias cursadas e historial detallado.

### `controllers/AsistenciaController.php`
* **Líneas 10-28:** `mostrarEscanear()`. Recibe el código de sesión opcional en query string, busca la sesión en `Sesion::buscarPorCodigo()` y muestra el formulario de escaneo y cámara.
* **Líneas 31-80:** `registrar()`. Valida que la sesión exista y esté con `activa = 1`. Valida que el estudiante exista. Verifica que no haya registrado previamente mediante `Asistencia::yaRegistro()`. Si todo es válido, persiste la asistencia en `Asistencia::registrar()` y muestra la vista de éxito `views/asistencia/resultado.php`.
* **Líneas 83-110:** `asistenciasActivas()`. Endpoint JSON para el sondeo en tiempo real (*polling*) cada 5 segundos desde el panel docente.

### `controllers/ReporteController.php`
* **Líneas 12-40:** `index()`. Aplica filtros de rango de fechas (`fecha_inicio`, `fecha_fin`), materia (`materia`) y búsqueda de alumno (`busqueda`). Obtiene la lista mediante `Asistencia::filtrar($filtros)` y carga `views/reportes/index.php`.
* **Líneas 43-68:** `exportarCsv()`. Limpia el búfer de salida con `ob_end_clean()`, emite cabeceras de descarga `Content-Type: text/csv; charset=utf-8`, escribe el **BOM UTF-8** (`\xEF\xBB\xBF`) y exporta las filas mediante `fputcsv()`.
* **Líneas 71-125:** `exportarExcel()`. Genera un archivo SpreadsheetML con cabeceras institucionales en azul `#1A2B4C`, títulos dorados `#B8912E`, filas cebreadas y formato forzado de texto `mso-number-format:"\@"` para proteger códigos con ceros a la izquierda.
* **Líneas 128-170:** `exportarPdf()`. Instancia `libs/ReportePdf.php`, genera el documento apaisado A4 Landscape, imprime el membrete oficial del ISTPET, tabla cebreada con elipsis en celdas y envía el archivo al navegador con `$pdf->Output('D', 'asistencias_fecha.pdf')`.

---

## 4. Capa de Modelos (`models/`)

### `models/Docente.php`
* **`buscarPorUsuario(string $usuario)`:** Consulta parametrizada `SELECT * FROM docentes WHERE usuario = :usuario LIMIT 1`.
* **`buscarPorId(int $id)`:** Consulta por clave primaria.

### `models/Estudiante.php`
* **`listar()` y `buscar(string $criterio)`:** Consultas con ordenamiento `ORDER BY apellido ASC, nombre ASC`.
* **`sugerirSiguienteCodigo(): string`:** Recupera todos los códigos, extrae el número con `preg_match('/^EST(\d+)$/i')`, calcula el valor máximo y devuelve `sprintf('EST%03d', $max + 1)`.
* **`crear()`, `actualizar()`, `eliminar()`:** Operaciones CRUD parametrizadas con sentencias preparadas PDO.

### `models/Sesion.php`
* **`obtenerActiva(int $docenteId)`:** Consulta `SELECT * FROM sesiones WHERE docente_id = :docente_id AND activa = 1 ORDER BY id DESC LIMIT 1`.
* **`crear()`:** Inserta nueva sesión con `activa = 1`, fecha y hora actual.
* **`cerrar(int $id, int $docenteId)`:** Ejecuta `UPDATE sesiones SET activa = 0, hora_fin = CURTIME() WHERE id = :id AND docente_id = :docente_id`.

### `models/Asistencia.php`
* **`registrar(int $sesionId, int $estudianteId)`:** Inserta el registro con `fecha = CURDATE()` y `hora = CURTIME()`.
* **`yaRegistro(int $sesionId, int $estudianteId): bool`:** Verifica duplicidad previa mediante `SELECT COUNT(*) FROM asistencias WHERE sesion_id = :s AND estudiante_id = :e`.
* **`filtrar(array $filtros): array`:** Construye dinámicamente la consulta con cláusulas `WHERE` para fechas, materias y búsqueda textual, utilizando parámetros vinculados seguros.

---

## 5. Capa de Bibliotecas (`libs/`)

### `libs/ReportePdf.php`
* **Extensión de `FPDF`:** Sobrescribe los métodos `Header()` y `Footer()` para incorporar el membrete oficial del ISTPET, logotipo corporativo, datos de emisión, línea divisoria dorada y paginación automática `AliasNbPages()`.
* **`celdaAjustada($ancho, $alto, $texto, ...)`:** Función matemática que evalúa `GetStringWidth()`. Si el texto excede el ancho de la columna, lo trunca y agrega `...`, garantizando una tabla perfectamente alineada.
* **`conv(string $txt): string`:** Transforma cadenas UTF-8 a codificación ISO-8859-1 para soporte nativo de caracteres en español (tildes, eñes) en el motor tipográfico de FPDF.

---

## 6. Capa de Vistas y Layouts (`views/`)

### 6.1. Layouts Globales
* **`views/layouts/header.php`:**
  * Define la cabecera HTML5 estándar con `<!DOCTYPE html>`, charset `UTF-8` y meta `viewport` para diseño responsivo.
  * Carga tipografías de Google Fonts (*Inter* y *Outfit*).
  * Enlaza la hoja de estilos maestra [`public/assets/css/style.css`](../public/assets/css/style.css).
  * Renderiza la barra de navegación institucional superior con logotipo del ISTPET, enlaces contextuales según el rol autenticado (`$_SESSION['docente_id']` o `$_SESSION['estudiante_id']`) y contenedor con desplazamiento táctil horizontal inercial.
* **`views/layouts/footer.php`:**
  * Cierra las etiquetas de contenedor principal y cuerpo.
  * Imprime el pie de página institucional con derechos reservados y créditos académicos del ISTPET.

### 6.2. Vistas de Inicio e Institucional (`views/home/`)
* **`views/home/index.php`:** Pantalla principal de selección de rol. Presenta dos tarjetas interactivas (*Acceso Docente* y *Registrar Asistencia en Clase*) con microinteracciones CSS y enlaces directos a `/login` y `/asistencia/escanear`.
* **`views/home/institucional.php`:** Portal informativo sobre el ISTPET, su misión educativa y el catálogo de las 4 carreras técnicas disponibles.

### 6.3. Vistas de Autenticación (`views/auth/`)
* **`views/auth/login-docente.php`:** Formulario centrado con validación de credenciales para docentes (`usuario` y `password`), alertas de error estilizadas, asistente visual con credenciales de prueba preconfiguradas (`profesor`/`12345`) y enlace de retorno.
* **`views/auth/login-estudiante.php`:** Formulario de acceso al portal del estudiante mediante código institucional (ej. `EST001`), transformador a mayúsculas automático y atajos para pruebas rápidas.

### 6.4. Vistas del Panel y Estudiantes (`views/dashboard/` y `views/estudiantes/`)
* **`views/dashboard/index.php`:**
  * Formulario de apertura de clase con selección de Carrera, Nivel y generación dinámica de chips de materias sugeridas.
  * Tarjeta de clase activa con código QR ampliado, código manual de 8 caracteres, enlace copiable al portapapeles y botón para finalizar sesión.
  * **Modo Proyector de Aula:** Modal a pantalla completa con fondo oscurecido y desenfocado (`backdrop-filter: blur(8px)`) para proyectar el QR en grande ante los alumnos.
  * Tabla de asistencias en vivo con temporizador JavaScript que consulta `/api/asistencias/activas` cada 5 segundos y emite un tono de campana institucional al registrarse un nuevo alumno.
* **`views/estudiantes/index.php`:**
  * Tabla de mantenimiento de alumnos con buscador instantáneo.
  * Modal interactivo para crear nuevo estudiante con **código correlativo autogenerado** (ej. `EST009`), campo editable y foco automático en el nombre.
  * Modales para edición de datos y confirmación de eliminación segura.
* **`views/estudiantes/portal.php`:**
  * Tarjetas métricas con total de clases asistidas, porcentaje de asistencia y materias cursadas.
  * Historial tabular cronológico de todas las asistencias registradas por el estudiante.

### 6.5. Vistas de Asistencia y Reportes (`views/asistencia/`, `views/reportes/` y `views/errors/`)
* **`views/asistencia/escanear.php`:**
  * Guía visual paso a paso para el estudiante.
  * Visor HUD animado con línea láser de barrido (`.scanner-scan-bar`) y esquinas con marco dorado.
  * Decodificación dual con `BarcodeDetector` nativo por hardware y fallback local `jsQR` optimizado a 800px.
  * Formulario alternativo de ingreso manual para estudiantes con problemas en su cámara.
* **`views/asistencia/resultado.php`:**
  * Pantalla de confirmación con detalles de la clase y hora exacta de registro.
  * Sintetizador Web Audio API puro (`AudioContext`) que emite tonos armónicos ascendentes (880-1760 Hz) en éxito o graves (320-240 Hz) en caso de duplicidad.
* **`views/reportes/index.php`:**
  * Formulario de filtros por fechas (con botones rápidos *Hoy*, *Este Mes*, *Últimos 30 días*, *Histórico*), materia y búsqueda de estudiante.
  * Badges interactivos para remover filtros individuales.
  * Botones de descarga directa en **CSV** (con BOM UTF-8), **Excel** (SpreadsheetML) y **PDF** (A4 Landscape oficial).
* **`views/errors/404.php`:**
  * Pantalla de error 404 personalizada con código de estado HTTP adecuado, mensaje amigable y botón de regreso a la página principal.

---

## 7. Capa de Configuración Web y Estilos (`public/`)

### `public/.htaccess`
* Activa `RewriteEngine On` en servidores Apache.
* Reglas `RewriteCond %{REQUEST_FILENAME} !-f` y `RewriteCond %{REQUEST_FILENAME} !-d`: Si la petición no coincide con un archivo o directorio físico existente, reescribe transparentemente la URL hacia `index.php`, permitiendo URLs limpias y amigables.

### `public/assets/css/style.css` y `estilos.css`
* **Paleta de Colores Institucionales:** Azul Marino Profundo (`#1A2B4C`), Azul Real (`#2563EB`), Dorado Corporativo (`#B8912E`), Fondo de Interfaz (`#F4F6F9`) y Blanco Puro (`#FFFFFF`).
* **Sistema Responsivo en Cascada:** Media queries estructuradas en 4 niveles (1024px, 900px, 768px, 480px) con `-webkit-overflow-scrolling: touch` para desplazamiento inercial en tablas y `overflow-x: hidden` para prevenir desbordes laterales en móviles.

---

## 8. Análisis de Seguridad e Integridad

1. **Inyección SQL (100% Mitigada):** Todos los modelos interactúan con la base de datos utilizando exclusivamente sentencias preparadas de PDO (`$stmt->prepare()` y `$stmt->execute($params)`). Ningún dato introducido por el usuario se concatena directamente en las consultas SQL.
2. **Cross-Site Scripting (XSS):** Toda variable dinámica impresa en las vistas PHP pasa por `htmlspecialchars($var, ENT_QUOTES, 'UTF-8')`.
3. **Control de Acceso y Sesiones:** Validación de roles en controladores mediante `$_SESSION['docente_id']` y `$_SESSION['estudiante_id']`, redirigiendo de inmediato a `/login` ante peticiones no autenticadas.
4. **Manejo de Búferes:** Uso estricto de `ob_end_clean()` antes de emitir las cabeceras HTTP de descarga binaria (CSV, Excel, PDF) para garantizar archivos limpios y no corruptos.

