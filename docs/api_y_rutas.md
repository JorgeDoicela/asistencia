# Catálogo de Rutas y Endpoints HTTP

Este documento detalla todas las rutas registradas en el Front Controller (`public/index.php`) y administradas por el Router.

---

## 1. Rutas Públicas e Institucionales

| Método | Ruta | Controlador / Método | Descripción |
|---|---|---|---|
| `GET` | `/` | `HomeController::index` | Pantalla de inicio con accesos para docentes, alumnos y QR. |
| `GET` | `/institucional` | `HomeController::institucional` | Página con información académica del ISTPET. |

---

## 2. Autenticación y Sesiones

| Método | Ruta | Parámetros | Controlador / Método | Middleware |
|---|---|---|---|---|
| `GET` | `/login` | - | `AuthController::mostrarLoginDocente` | `soloInvitados` |
| `POST`| `/login` | `usuario`, `password` | `AuthController::loginDocente` | - |
| `GET` | `/logout` | - | `AuthController::logoutDocente` | - |
| `GET` | `/login-estudiante` | - | `AuthController::mostrarLoginEstudiante` | - |
| `POST`| `/login-estudiante` | `codigo` | `AuthController::loginEstudiante` | - |
| `GET` | `/logout-estudiante` | - | `AuthController::logoutEstudiante` | - |

---

## 3. Panel Docente (Dashboard)

| Método | Ruta | Parámetros | Controlador / Método | Middleware |
|---|---|---|---|---|
| `GET` | `/dashboard` | - | `DashboardController::index` | `requiereDocente` |
| `POST`| `/dashboard/sesion/crear` | `carrera`, `nivel`, `materia` | `DashboardController::crearSesion` | `requiereDocente` |
| `POST`| `/dashboard/sesion/cerrar`| `sesion_id` | `DashboardController::cerrarSesion` | `requiereDocente` |

---

## 4. Gestión de Estudiantes (CRUD)

| Método | Ruta | Parámetros | Controlador / Método | Middleware |
|---|---|---|---|---|
| `GET` | `/estudiantes` | `buscar` (opcional) | `EstudianteController::index` | `requiereDocente` |
| `POST`| `/estudiantes/crear` | `codigo`, `nombre`, `apellido`, `carrera` | `EstudianteController::crear` | `requiereDocente` |
| `POST`| `/estudiantes/actualizar` | `id`, `codigo`, `nombre`, `apellido`, `carrera` | `EstudianteController::actualizar` | `requiereDocente` |
| `POST`| `/estudiantes/eliminar` | `id` | `EstudianteController::eliminar` | `requiereDocente` |
| `GET` | `/estudiante/portal` | - | `EstudianteController::portal` | `requiereEstudiante` |

---

## 5. Registro y Escaneo de Asistencia

| Método | Ruta | Parámetros | Controlador / Método | Descripción |
|---|---|---|---|---|
| `GET` | `/asistencia/escanear` | `codigo` (opcional en query) | `AsistenciaController::mostrarEscanear` | Formulario de confirmación tras escanear el QR o manual. |
| `POST`| `/asistencia/registrar`| `codigo_sesion`, `codigo_estudiante` | `AsistenciaController::registrar` | Valida reglas de negocio y persiste asistencia. |

---

## 6. Reportes y Exportación

| Método | Ruta | Parámetros Query | Controlador / Método | Salida / Middleware |
|---|---|---|---|---|
| `GET` | `/reportes` | `fecha_inicio`, `fecha_fin`, `materia`, `busqueda` | `ReporteController::index` | Vista HTML (`requiereDocente`) |
| `GET` | `/reportes/csv` | `fecha_inicio`, `fecha_fin`, `materia`, `busqueda` | `ReporteController::exportarCsv` | Descarga CSV UTF-8 (`requiereDocente`) |
| `GET` | `/reportes/excel` | `fecha_inicio`, `fecha_fin`, `materia`, `busqueda` | `ReporteController::exportarExcel` | Descarga Excel .xls (`requiereDocente`) |
| `GET` | `/reportes/pdf` | `fecha_inicio`, `fecha_fin`, `materia`, `busqueda` | `ReporteController::exportarPdf` | Descarga PDF A4 (`requiereDocente`) |

---

## 7. Endpoint API JSON (Polling en Tiempo Real)

### `GET /api/asistencias/activas`
Utilizado por el panel de control para actualizar la lista de asistentes cada 5 segundos mediante AJAX/Fetch sin recargar la página.

* **Middleware requerido:** Sesión de docente activa.
* **Respuesta Exitosa (Con sesión activa):**
```json
{
  "success": true,
  "activa": true,
  "sesion": {
    "id": 14,
    "codigo_sesion": "B7E1C840",
    "materia": "Programación Web II (Desarrollo de Software - Tercer Nivel)"
  },
  "asistencias": [
    {
      "id": 45,
      "fecha": "2026-09-02",
      "hora": "11:45:10",
      "codigo": "EST001",
      "nombre": "Juan",
      "apellido": "Pérez",
      "carrera": "Desarrollo de Software"
    }
  ]
}
```

* **Respuesta (Sin sesión activa en curso):**
```json
{
  "success": true,
  "activa": false,
  "asistencias": []
}
```

* **Respuesta (No autorizado - HTTP 401):**
```json
{
  "success": false,
  "error": "No autorizado"
}
```
