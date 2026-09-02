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

---

## 4. Adaptabilidad Móvil (Responsive CSS)

Se incorporaron reglas `@media (max-width: 768px)` y `@media (max-width: 900px)` en `public/assets/css/style.css` y `public/assets/css/estilos.css`:

| Elemento | Comportamiento Desktop | Comportamiento Móvil (< 768px) |
| :--- | :--- | :--- |
| **Barra Superior (Navbar)** | Fija a 70px, enlaces alineados horizontalmente | Auto-ajustable, enlaces con scroll horizontal táctil (`overflow-x: auto`) |
| **Nombre de Usuario** | Texto completo al lado del botón de cierre | Oculto en móviles para ahorrar espacio en pantalla |
| **Panel QR y Tabla en Vivo** | Cuadrícula de 2 columnas (`380px 1fr`) | Colapso a 1 sola columna vertical apilada |
| **Tarjetas de Estadísticas** | Cuadrícula de 3 columnas | Cuadrícula de 1 columna táctil |
| **Modales** | 500px centrado | 95% del ancho de la pantalla con padding optimizado para dedos |
| **Formulario de Filtros** | Cuadrícula horizontal fluida | Apilamiento vertical accesible |

---

## 5. Accesibilidad y Atajos de Teclado

* **Tecla Escape (`ESC`):** Cierra instantáneamente tanto el modal de creación/edición de estudiantes como el modal de Modo Proyector de aula.
* **Autofocus Inteligente:** Al abrir el modal de nuevo estudiante, el cursor se posiciona automáticamente en el campo de código; al abrir el modal de edición, en el nombre.
* **Auto-formato de Códigos:** En las pantallas de asistencia, los campos de código institucional transforman automáticamente el texto ingresado a MAYÚSCULAS y remueven espacios en blanco accidentales.
