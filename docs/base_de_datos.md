# Diccionario y Modelo de Base de Datos

## 1. Esquema General

* **Nombre de la base de datos:** `asistencia_qr`
* **Motor:** InnoDB
* **Collation:** `utf8mb4_unicode_ci`

```
┌─────────────────┐       1:N       ┌─────────────────┐       1:N       ┌─────────────────┐
│    docentes     ├─────────────────┤    sesiones     ├─────────────────┤   asistencias   │
│                 │                 │                 │                 │                 │
│ id (PK)         │                 │ id (PK)         │                 │ id (PK)         │
│ nombre          │                 │ docente_id (FK) │                 │ sesion_id (FK)  │
│ usuario (UQ)    │                 │ codigo_sesion   │            ┌────┤ estudiante_id   │
│ password        │                 │ materia         │            │    │ fecha, hora     │
└─────────────────┘                 │ activa          │            │    └─────────────────┘
                                    └─────────────────┘            │
                                                                   │ 1:N
                                                            ┌──────┴──────────┐
                                                            │   estudiantes   │
                                                            │                 │
                                                            │ id (PK)         │
                                                            │ codigo (UQ)     │
                                                            │ nombre, apellido│
                                                            │ carrera         │
                                                            └─────────────────┘
```

---

## 2. Diccionario de Tablas

### 2.1. Tabla: `docentes`
Almacena las credenciales de acceso para los profesores de la institución.

| Campo | Tipo de Dato | Nulo | Descripción |
|---|---|---|---|
| `id` | `INT AUTO_INCREMENT` | NO | Clave primaria. Identificador único del docente. |
| `nombre` | `VARCHAR(150)` | NO | Nombres y apellidos completos del docente. |
| `usuario` | `VARCHAR(100)` | NO | Nombre de usuario para autenticación (Índice Único). |
| `password` | `VARCHAR(255)` | NO | Contraseña cifrada mediante hash bcrypt (`password_hash`). |
| `creado_en` | `TIMESTAMP` | NO | Fecha y hora de creación del registro (Default `CURRENT_TIMESTAMP`). |

---

### 2.2. Tabla: `estudiantes`
Catálogo de alumnos habilitados para registrar asistencia en las sesiones académicas.

| Campo | Tipo de Dato | Nulo | Descripción |
|---|---|---|---|
| `id` | `INT AUTO_INCREMENT` | NO | Clave primaria. Identificador único del estudiante. |
| `codigo` | `VARCHAR(50)` | NO | Código de matrícula institucional (Ej: `EST001`). Índice Único. |
| `nombre` | `VARCHAR(150)` | NO | Nombres del estudiante. |
| `apellido` | `VARCHAR(150)` | SÍ | Apellidos del estudiante. |
| `carrera` | `VARCHAR(100)` | SÍ | Carrera a la que pertenece (Ej: `Desarrollo de Software`). |
| `fecha_registro`| `TIMESTAMP` | NO | Fecha y hora de alta en el sistema (Default `CURRENT_TIMESTAMP`). |

---

### 2.3. Tabla: `sesiones`
Representa una clase o jornada académica generada por un docente con su respectivo código QR.

| Campo | Tipo de Dato | Nulo | Descripción |
|---|---|---|---|
| `id` | `INT AUTO_INCREMENT` | NO | Clave primaria. Identificador único de la sesión. |
| `docente_id` | `INT` | NO | Clave foránea referenciando a `docentes(id)` con `ON DELETE CASCADE`. |
| `codigo_sesion` | `VARCHAR(20)` | NO | Código alfanumérico único de 8 caracteres (Índice Único). |
| `materia` | `VARCHAR(150)` | NO | Nombre de la asignatura con carrera y nivel entre paréntesis. |
| `fecha` | `DATE` | NO | Fecha en que se impartió la clase. |
| `hora_inicio` | `TIME` | SÍ | Hora en la que se generó el código QR. |
| `hora_fin` | `TIME` | SÍ | Hora en la que el docente finalizó la sesión. |
| `activa` | `TINYINT(1)` | NO | Estado de la sesión: `1` = Abierta para escaneo, `0` = Cerrada. |
| `creado_en` | `TIMESTAMP` | NO | Timestamp de creación. |

---

### 2.4. Tabla: `asistencias`
Registro individual de asistencia de un estudiante a una sesión de clase específica.

| Campo | Tipo de Dato | Nulo | Descripción |
|---|---|---|---|
| `id` | `INT AUTO_INCREMENT` | NO | Clave primaria. |
| `sesion_id` | `INT` | NO | Clave foránea referenciando a `sesiones(id)` con `ON DELETE CASCADE`. |
| `estudiante_id` | `INT` | NO | Clave foránea referenciando a `estudiantes(id)` con `ON DELETE CASCADE`. |
| `fecha` | `DATE` | NO | Fecha del registro de asistencia. |
| `hora` | `TIME` | NO | Hora exacta en que se confirmó el escaneo. |
| `fecha_registro`| `TIMESTAMP` | NO | Timestamp de inserción. |

#### Restricciones e Índices Especiales:
* **`UNIQUE KEY unica_asistencia (sesion_id, estudiante_id)`**: Evita a nivel de motor de base de datos que un estudiante registre su asistencia más de una vez en la misma sesión.
