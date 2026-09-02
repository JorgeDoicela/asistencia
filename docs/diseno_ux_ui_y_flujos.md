# Diseño UX/UI, Flujos de Navegación y Usabilidad

Este documento detalla la arquitectura de experiencia de usuario (UX), diseño visual (UI), componentes interactivos, navegación contextual y adaptabilidad móvil implementados en el **Sistema de Asistencia QR - ISTPET**.

---

## 1. Filosofía de Diseño y Principios Rectores

1. **Cero Fricción en el Aula:** El registro de asistencias debe ser instantáneo. Los estudiantes no deben lidiar con contraseñas complejas en el momento del escaneo; solo su código institucional de matrícula.
2. **Navegación Contextual Bidireccional:** Todo formulario, modal o subpágina dispone de vías claras y visibles para retornar a su pantalla de origen (`← Volver al Panel QR`, migas de pan *breadcrumbs*, atajos de teclado `ESC`, botones de cierre `×`).
3. **Visibilidad del Estado del Sistema:** El usuario siempre sabe en qué paso se encuentra (guía visual de 3 pasos en escaneo, badges de sesión activa con animación de pulso, indicadores de actualización en vivo cada 5 segundos).
4. **Respuesta Visual Inmediata (Feedback):** Acciones como copiar enlace, aplicar filtros rápidos o guardar datos ofrecen respuesta sensorial instantánea (toasts flotantes, pills removibles, chips de sugerencias dinámicas).
5. **Diseño Móvil Primero para Alumnos:** Dado que los estudiantes utilizan sus smartphones para escanear los códigos QR, la interfaz responde con media queries adaptadas para pantallas estrechas (< 768px y < 640px).

---

## 2. Mapa de Flujos de la Aplicación

```
                          [ / (Home) ]
                     Identificación de Rol
                      /                 \
                     /                   \
        [ Docentes ]                       [ Estudiantes ]
              |                                   |
       /login (Credenciales)                      |
              |                                   |
      /dashboard (Panel QR)                 /asistencia/escanear
         |       |        \                  (Cámara o Manual)
         |       |         \                      |
         |       |     /reportes                  |
         |       |  (CSV, Excel, PDF)       /asistencia/registrar
         |       |                                |
         |   /estudiantes                         v
         |   (Gestión CRUD)             /asistencia/resultado
         |                               (Éxito o Reintento)
         v                                        |
  Modo Proyector                                  v
 (Pantalla Completa)                     /estudiante/portal
                                        (Expediente Académico)
```

---

## 3. Componentes y Patrones de Interacción

### 3.1. Modo Proyector para Aulas de Clase
* **Ubicación:** `views/dashboard/index.php`
* **Propósito:** Permitir al docente proyectar en pantalla completa el código QR y el código manual en pantallas gigantes o proyectores de aula sin distracciones ni barras de navegación.
* **Características:**
  * Fondo oscuro con efecto *backdrop-filter: blur(8px)*.
  * Código QR ampliado de 280×280 px con borde institucional.
  * Código manual de 8 caracteres en tipografía monoespaciada gigante (2.1rem).
  * Cierre accesible mediante botón `×`, clic fuera del modal o tecla `ESC`.

### 3.2. Sugerencias Inteligentes de Materias
* **Ubicación:** Formulario de inicio de sesión de clase en `views/dashboard/index.php`.
* **Comportamiento:** Al seleccionar una carrera técnica (ej. *Desarrollo de Software*, *Mecánica Automotriz*, *Educación Inicial*), el sistema genera dinámicamente *chips* interactivos (`+ Programación Web II`, `+ Bases de Datos`, etc.).
* **Beneficio:** Reduce el tiempo de tipeo del profesor a un solo clic.

### 3.3. Copiado de Enlace Directo
* **Ubicación:** Tarjeta de clase activa en el Dashboard.
* **Comportamiento:** Copia `http://<host>/asistencia/escanear?codigo=<CODIGO>` en el portapapeles mediante la API nativa de JavaScript (`navigator.clipboard`) con fallback compatible, disparando una notificación toast flotante (`.toast-msg`).

### 3.4. Filtros Rápidos de Fecha y Búsqueda Integral en Reportes
* **Ubicación:** `views/reportes/index.php`
* **Botones de Periodo Rápido:**
  * `Hoy`: Asigna fecha inicio y fin al día en curso.
  * `Este Mes`: Asigna desde el día 1 del mes hasta hoy.
  * `Últimos 30 días`: Rango retrospectivo de 30 días.
  * `Todo el Historial`: Limpia el rango temporal.
* **Búsqueda por Alumno:** Nuevo campo de texto que permite filtrar asistencias por código (`EST001`) o por nombre del alumno.
* **Etiquetas de Filtros Activos (*Pills*):** Se despliegan chips azules con cada criterio aplicado y un botón rápido para restablecerlos.

### 3.5. Portal Estudiantil con Métricas
* **Ubicación:** `views/estudiantes/portal.php`
* **Métricas en Tarjetas:**
  * Total de clases asistidas.
  * Asistencias registradas en el mes actual.
  * Cantidad de materias distintas cursadas.
* **Navegación:** Migas de pan, botón directo a `Registrar Nueva Asistencia` y enlace seguro de salida.

### 3.6. Escaneo Directo con Cámara Web y Móvil (Visor HUD)
* **Ubicación:** `views/asistencia/escanear.php`
* **Propósito:** Permitir al alumno escanear el código QR directamente desde el navegador del celular o laptop sin descargar ninguna aplicación adicional de terceros.
* **Componentes:**
  * Botón interactivo: `Escanear Código QR con la Cámara`.
  * Visor HUD con animación de barrido láser (`.scanner-scan-bar`) y esquinas con borde institucional dorado.
  * Detección dual: `BarcodeDetector` nativo por hardware cuando está disponible, y decodificación universal mediante `jsQR` sobre canvas optimizado a 800px.
  * Fallback automático de captura fotográfica nativa para conexiones en red local HTTP sin HTTPS.

### 3.7. Feedback Acústico en Tiempo Real (Web Audio API)
* **Ubicación:** `views/asistencia/resultado.php` y `views/dashboard/index.php`
* **Propósito:** Brindar retroalimentación sensorial inmediata (auditiva) en el momento de la marcación y en el proyector de clase.
* **Comportamiento:**
  * **En el celular del alumno:** Doble tono armónico ascendente (880 Hz a 1760 Hz) al confirmar la asistencia; doble tono grave en caso de error o duplicidad.
  * **En el proyector del docente:** Tono armónico de campana institucional (784 Hz a 1046.5 Hz) en el momento en que un nuevo alumno marca en vivo, con botón para silenciar o activar a voluntad.
  * **Tecnología:** 100% nativa con `AudioContext` de JavaScript, sin descargas de archivos de audio externos (0 KB de latencia).

### 3.8. Generación Correlativa Inteligente y Editable de Códigos
* **Ubicación:** `views/estudiantes/index.php` y `models/Estudiante.php`
* **Comportamiento:** Al pulsar `+ Registrar Nuevo Estudiante`, el sistema analiza los códigos existentes, calcula el correlativo disponible más alto (ej. `EST009`) y lo precarga en el campo.
* **Facilidad de Uso:** El campo es 100% editable por si se requiere un código personalizado, el cursor salta de inmediato al campo de nombres para agilizar el registro, y cuenta con un botón para autogenerar o regenerar en cualquier instante.

---

## 4. Adaptabilidad Móvil y Diseño Responsivo Integral

El sistema implementa una arquitectura CSS responsiva estructurada en cascada para soportar cualquier dispositivo: smartphones pequeños (320px - 375px), teléfonos modernos (390px - 430px), tablets (768px - 900px) y monitores de escritorio (1024px+).

### 4.1. Matriz de Breakpoints y Comportamiento

| Componente | Desktop (> 1024px) | Tablets (769px - 1024px) | Móviles Estándar (< 768px) | Pantallas Estrechas (< 480px) |
| :--- | :--- | :--- | :--- | :--- |
| **Barra de Navegación** | Fija a 70px, enlaces horizontales con nombre de usuario | Enlaces compactos | Auto-ajustable, enlaces con scroll táctil (`overflow-x: auto; scrollbar-width: none`), oculta nombre | Padding 10px 12px, logo 32px |
| **Panel Docente (Dashboard)** | Cuadrícula 2 columnas (`360px 1fr`) | Cuadrícula 2 columnas (`320px 1fr`) | Colapso a 1 columna vertical apilada | 1 columna, botones al 100% |
| **Modo Proyector de Aula** | Modal centrado de 620px con QR de 280px | Modal 560px con QR de 240px | Ancho 95vw, alto máx 90vh con scroll interno, QR autoajustable a 210px | Tipografía de código con `clamp(1.4rem, 5vw, 2.1rem)` |
| **Tablas de Datos (En vivo, CRUD, Reportes, Portal)** | Tabla completa con encabezados fijos | Tabla completa adaptable | Contenedor `.table-responsive` con scroll horizontal táctil inercial (`-webkit-overflow-scrolling: touch`) | Celdas compactas, botones de acción optimizados para dedos |
| **Tarjetas Métricas (`.stats-grid`)** | Cuadrícula de 3 columnas | Cuadrícula de 3 columnas | Cuadrícula de 1 columna vertical | Valores escalados a 1.8rem para evitar cortes numéricos |
| **Formularios de Filtros** | Cuadrícula fluida de 4 columnas horizontales | Cuadrícula de 2 a 4 columnas | 1 columna vertical apilada | Botones de acción (`Filtrar` / `Limpiar`) al 100% |
| **Tarjetas de Autenticación (`.auth-card`)** | Centrado con máx 440px y padding de 44px | Centrado con máx 440px | Ancho al 100% con padding de 26px | Asistente de credenciales apilado verticalmente |
| **Escáner HUD de Cámara** | Visor de 280px con marco láser | Visor de 280px | Visor adaptativo a 240px de altura | Campos de entrada con `font-size: 1.05rem` centrado |
| **Pantalla de Inicio (`/`)** | Cuadrícula de 2 roles con padding de 38px | 2 columnas | 1 columna vertical con padding de 26px | Título fluido escalable con `clamp(1.5rem, 5.5vw, 2rem)` |

### 4.2. Principios Técnicos de Ergonomía Táctil
1. **Prevención Global de Desbordamiento:** `html, body { width: 100%; max-width: 100%; overflow-x: hidden; }` para evitar desplazamientos horizontales accidentales en dispositivos móviles.
2. **Área Táctil Mínima (Touch Targets):** Todos los botones primarios, enlaces de menú y selectores disponen de una altura mínima de 44px a 48px con padding generoso para facilitar la pulsación con el pulgar.
3. **Tipografía Fluida y Legibilidad:** Utilización de funciones matemáticas CSS `clamp()` para asegurar que los títulos institucionales y códigos de 8 caracteres nunca se trunquen ni salten de línea de forma antiestética en teléfonos pequeños.
4. **Tablas Seguras con Scroll Inercial:** Ninguna tabla de base de datos rompe el ancho del contenedor en móviles; el usuario puede deslizar horizontalmente la tabla con fluidez nativa mientras el resto de la interfaz permanece fija y centrada.

---

## 5. Accesibilidad y Atajos de Teclado

* **Tecla Escape (`ESC`):** Cierra instantáneamente tanto el modal de creación/edición de estudiantes como el modal de Modo Proyector de aula.
* **Autofocus Inteligente:** Al abrir el modal de nuevo estudiante, el cursor se posiciona automáticamente en el campo de código; al abrir el modal de edición, en el nombre.
* **Auto-formato de Códigos:** En las pantallas de asistencia, los campos de código institucional transforman automáticamente el texto ingresado a MAYÚSCULAS y remueven espacios en blanco accidentales.
