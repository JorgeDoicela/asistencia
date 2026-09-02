# Módulo de Reportes y Exportación Multiformato (CSV, Excel y PDF)

Este documento detalla la arquitectura, implementación técnica, resolución de incidencias y especificaciones del sistema de reportes y descargas del **Sistema de Asistencia QR - ISTPET**.

---

## 1. Visión General del Módulo

El módulo de reportes permite a los docentes autorizados:
1. **Consultar y filtrar asistencias:** Filtrado dinámico por rango de fechas (`fecha_inicio`, `fecha_fin`), por nombre de materia (`materia`) o por datos del estudiante (`busqueda`).
2. **Visualizar resultados paginados en pantalla:** Con contadores de registros, formato de hora/fecha legible y datos de estudiante/carrera.
3. **Exportar la información en tres formatos estándar de la industria:**
   - **CSV:** Formato universal delimitado por comas con compatibilidad internacional UTF-8 BOM.
   - **Excel (.xls):** Hoja de cálculo enriquecida con estilos visuales institucionales ISTPET, formatos tipográficos y preservación estricta de cadenas (códigos de alumno sin pérdida de ceros a la izquierda).
   - **PDF:** Documento institucional oficial en formato apaisado (A4 Landscape) con membrete corporativo, logotipo oficial, metadatos de emisión, tabla cebreada y paginación automática.

---

## 2. Diagnóstico y Corrección de Incidencias Técnicas

Durante la fase de auditoría se identificaron y solucionaron dos problemas críticos en la vista de reportes:

### 2.1. Problema: Texto de advertencia visible en el campo "Materia" y consola
* **Causa Raíz:** En `ReporteController::index()` se enviaban variables independientes (`$fechaInicio`, `$fechaFin`, `$materia`), pero la vista `views/reportes/index.php` intentaba acceder al arreglo `$filtros['materia']` y `$filtros['fecha_inicio']`. Al no estar definida la variable `$filtros`, el motor de PHP 8 emitió advertencias `Warning: Undefined variable $filtros`.
* **Impacto visual:** El texto HTML de la advertencia PHP se renderizó dentro del atributo `value="<br /><b>Warning...</b>"` del input de Materia, mientras que los inputs de tipo `date` lanzaron errores de validación en la consola del navegador por no cumplir el estándar `yyyy-MM-dd`.
* **Solución Arquitectural:**
  1. En `ReporteController.php`, se formalizó la construcción del arreglo asociativo `$filtros`:
     ```php
     $filtros = [
         'fecha_inicio' => $fechaInicio,
         'fecha_fin'    => $fechaFin,
         'materia'      => $materia,
         'busqueda'     => $busqueda
     ];
     ```
  2. En la vista `views/reportes/index.php`, se aplicó el operador coalescente nulo (`?? ''`) como estándar defensivo:
     ```php
     value="<?= htmlspecialchars($filtros['materia'] ?? '') ?>"
     ```

### 2.2. Problema: Enlace de descarga corrompido y fallo de cabeceras HTTP
* **Causa Raíz:** El botón de descarga generaba su URL embebiendo variables no inicializadas, corrompiendo el parámetro `href`. Asimismo, no se limpiaba el búfer de salida antes de enviar las cabeceras HTTP de los archivos descargables.
* **Solución:**
  1. Se unificó la serialización de parámetros mediante la función nativa `http_build_query()`.
  2. Se añadió la limpieza preventiva del búfer de salida activo mediante `ob_end_clean()` en todos los métodos de exportación antes de declarar `header('Content-Type: ...')`.

---

## 3. Especificaciones Técnicas de los Formatos de Exportación

### 3.1. Exportación CSV (`/reportes/csv`)
* **Controlador:** `ReporteController::exportarCsv()`
* **Tipo MIME:** `text/csv; charset=utf-8`
* **Nombre de Archivo:** `asistencias_YYYYMMDD_HHMMSS.csv`
* **Características Técnicas:**
  - Inyección de marca de orden de bytes (**UTF-8 BOM:** `0xEF, 0xBB, 0xBF`), permitiendo que Microsoft Excel abra el archivo directamente reconociendo acentos, tildes y caracteres especiales en español sin requerir asistentes de importación de texto.
  - Generación en streaming directo mediante `fopen('php://output', 'w')` y `fputcsv()`.

---

### 3.2. Exportación Excel Estructurado (`/reportes/excel`)
* **Controlador:** `ReporteController::exportarExcel()`
* **Tipo MIME:** `application/vnd.ms-excel; charset=utf-8`
* **Nombre de Archivo:** `asistencias_YYYYMMDD_HHMMSS.xls`
* **Características Técnicas:**
  - Estructura SpreadsheetML con compatibilidad universal en Microsoft Excel, LibreOffice Calc y Google Sheets.
  - **Identidad Gráfica:**
    - Título institucional en Azul Marino ISTPET (`#1A2B4C`).
    - Subtítulo en Dorado Acreditación (`#B8912E`).
    - Cabeceras de tabla en bloque sólido azul con texto blanco en negrita.
    - Filas alternas con efecto cebreado (`#F8FAFC`).
  - **Tipado estricto de celdas:**
    - Formato de texto forzado mediante `mso-number-format:"\@"` en códigos estudiantiles para evitar que hojas de cálculo trunquen ceros a la izquierda.
    - Formato de fecha `yyyy-mm-dd` y hora `hh:mm:ss` para habilitar operaciones y tablas dinámicas.

---

### 3.3. Exportación PDF Institucional (`/reportes/pdf`)
* **Controlador:** `ReporteController::exportarPdf()`
* **Motor Generador:** [FPDF 1.86](../libs/fpdf/) embebido en la carpeta `libs/fpdf/`.
* **Clase Especializada:** [libs/ReportePdf.php](../libs/ReportePdf.php) (extiende `FPDF`).
* **Tipo MIME:** `application/pdf`
* **Nombre de Archivo:** `asistencias_YYYYMMDD_HHMMSS.pdf`
* **Características Técnicas:**
  - **Formato Apaisado (A4 Landscape):** Dimensiones de 297 mm x 210 mm, otorgando 273 mm útiles para presentar 7 columnas sin apiñamiento de texto.
  - **Membrete Corporativo:**
    - Logotipo oficial del ISTPET cargado desde `public/assets/img/logo-istpet.jpg`.
    - Nombre oficial del instituto y título del reporte.
    - Cuadro de metadatos de auditoría: fecha/hora de emisión, docente responsable, rango de fechas activo y materia.
    - Línea divisoria dorada (`#B8912E`).
  - **Ajuste de Celdas con Elipsis:** Implementación del método `celdaAjustada()` que mide la longitud en milímetros del texto (`GetStringWidth`) y, si excede el ancho de columna, trunca la cadena añadiendo puntos suspensivos (`...`), evitando el desbordamiento sobre columnas adyacentes.
  - **Conversión de Codificación:** Función `conv()` que normaliza cadenas UTF-8 a ISO-8859-1 para soporte pleno de caracteres en español (tildes, eñes) en el motor de renderizado de fuentes nativas.
  - **Paginación Automática:** Repetición automática del encabezado de la tabla en saltos de página y pie de página dinámico con `AliasNbPages()` (`Página X de Y`).

---

## 4. Estructura de Archivos del Módulo

```text
asistencia/
├── controllers/
│   └── ReporteController.php      # Lógica de negocio, filtros y controladores de descarga
├── libs/                          # Bibliotecas locales sin dependencias de Composer
│   ├── ReportePdf.php             # Clase personalizada con membrete y estilos ISTPET
│   └── fpdf/                      # Motor FPDF 1.86 oficial
│       ├── fpdf.php               # Núcleo de generación PDF
│       └── font/                  # Definición de fuentes tipográficas (Helvetica, etc.)
├── models/
│   └── Asistencia.php             # Asistencia::filtrar() con sentencias preparadas PDO
├── views/
│   └── reportes/
│       └── index.php              # Formulario de filtros y botones de descarga multiformato
└── public/
    └── index.php                  # Front Controller con enrutamiento a /reportes/*
```

---

## 5. Matriz de Rutas y Métodos HTTP

| Método | Ruta | Parámetros Query | Salida | Descripción |
|---|---|---|---|---|
| `GET` | `/reportes` | `fecha_inicio`, `fecha_fin`, `materia`, `busqueda` | HTML | Vista principal de consulta y filtrado de asistencias. |
| `GET` | `/reportes/csv` | `fecha_inicio`, `fecha_fin`, `materia`, `busqueda` | Archivo CSV | Descarga inmediata en formato de texto plano con BOM UTF-8. |
| `GET` | `/reportes/excel` | `fecha_inicio`, `fecha_fin`, `materia`, `busqueda` | Archivo XLS | Descarga directa de libro de Excel con formato gráfico. |
| `GET` | `/reportes/pdf` | `fecha_inicio`, `fecha_fin`, `materia`, `busqueda` | Archivo PDF | Descarga directa de documento oficial membretado en PDF. |

---

## 6. Procedimiento de Verificación y Pruebas

1. Iniciar sesión como docente titular (`profesor` / `12345`).
2. Ingresar al menú **Reportes** (`/reportes`).
3. Comprobar que los campos de fecha tengan el mes en curso por defecto y el campo **Materia** no contenga mensajes de error.
4. Aplicar un filtro (por ejemplo, escribir una materia o rango de fechas) y presionar **Filtrar**.
5. Probar cada botón de descarga:
   - **Descargar CSV:** Abrir el archivo en un editor de texto o Excel; verificar que tildes y eñes se lean correctamente.
   - **Descargar Excel:** Abrir en Microsoft Excel; comprobar que las cabeceras aparezcan coloreadas en azul institucional y que el código de alumno mantenga su formato textual.
   - **Descargar PDF:** Abrir en el visor de PDF; verificar el logotipo del ISTPET, el membrete superior y la alineación de todas las columnas.
