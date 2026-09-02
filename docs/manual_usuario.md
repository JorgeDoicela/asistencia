# Manual de Usuario - ISTPET Asistencia QR

Guía paso a paso para el uso de la plataforma tanto para el personal docente como para los estudiantes.

---

## 1. Guía para Docentes

### 1.1. Inicio de Sesión
1. Accede a la URL principal y presiona **"Acceso Docente"** o dirígete directamente a `/login`.
2. Introduce tu usuario y contraseña institucional:
   * **Usuario:** `profesor`
   * **Contraseña:** `12345`
3. Al ingresar serás redirigido automáticamente a tu **Panel de Control (Dashboard)**.

### 1.2. Crear una Sesión de Clase y Mostrar el Código QR
1. En el panel principal verás el formulario **"Iniciar Nueva Sesión de Clase"**.
2. Selecciona la **Carrera** (ej. *Desarrollo de Software*).
3. Selecciona el **Nivel / Semestre** (ej. *Tercer Nivel*).
4. Escribe el nombre de la **Materia** (ej. *Programación Web*).
5. Haz clic en **"Generar Código QR de Asistencia"**.
6. El sistema generará una tarjeta con:
   * El **Código QR** dinámico de alta resolución para proyectar en el aula o compartir.
   * El **Código de Acceso Manual** de 8 caracteres (por si algún alumno tiene problemas con su cámara).
   * La tabla de **Asistencias en Vivo**, la cual se refresca cada 5 segundos conforme los estudiantes se registran.

### 1.3. Finalizar una Sesión
* Cuando termine el tiempo de tolerancia para el registro, presiona el botón rojo **"Finalizar Sesión de Clase"**.
* Una vez cerrada, ningún estudiante podrá registrar asistencias adicionales con ese código QR.

### 1.4. Gestión de Estudiantes
1. Dirígete a la pestaña **"Estudiantes"** en la barra de navegación superior.
2. Para **registrar uno nuevo**: Haz clic en `+ Registrar Nuevo Estudiante`, completa el código institucional (ej. `EST010`), nombres, apellidos y carrera, y guarda.
3. Para **editar o eliminar**: Usa los botones correspondientes en la fila del estudiante.
4. Para **buscar**: Usa el buscador superior para filtrar por código, apellido o carrera.

### 1.5. Reportes y Descarga en Excel (CSV)
1. Haz clic en **"Reportes"** en la barra superior.
2. Aplica los filtros deseados (rango de fechas desde/hasta, materia o estudiante).
3. Haz clic en **"Descargar CSV (Excel)"** para obtener el archivo con formato UTF-8 listo para abrir en Microsoft Excel.

---

## 2. Guía para Estudiantes

### 2.1. Registro de Asistencia mediante Código QR
1. Abre la cámara de tu teléfono móvil o lector de códigos QR y apunta al QR proyectado por el docente.
2. Se abrirá la página web de confirmación con el código de sesión prellenado.
3. Ingresa tu **Código de Estudiante** (ej. `EST001`).
4. Haz clic en **"Confirmar Asistencia"**.
5. Verás una pantalla de confirmación verde con tus nombres completos, carrera, materia y hora exacta de registro.

> [!NOTE]
> Si intentas registrarte dos veces en la misma sesión, el sistema te notificará amigablemente que tu asistencia ya fue registrada previamente y no duplicará la fila.

### 2.2. Registro Manual (Si tu cámara no funciona)
1. Entra a la página principal del sistema y presiona **"Registrar Asistencia"** (`/asistencia/escanear`).
2. Escribe el código de sesión de 8 caracteres que el docente tiene en pantalla.
3. Escribe tu código de estudiante y confirma.

### 2.3. Consultar tu Historial Académico de Asistencias
1. En la página de inicio, presiona **"Portal Estudiante"** (`/login-estudiante`).
2. Ingresa tu código de estudiante.
3. Se mostrará tu expediente con todas las asistencias que tienes registradas en el instituto, organizadas por fecha, hora, materia y nombre del docente.
