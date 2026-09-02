# Guía de Pruebas y Uso del Sistema de Asistencia QR

Esta guía detalla el procedimiento paso a paso para probar y validar el funcionamiento del sistema en cualquier entorno de ejecución (Docker, XAMPP, Nginx) tanto desde la computadora como desde un teléfono móvil conectado a la misma red Wi-Fi.

---

## Rutas Dinámicas de Acceso y Credenciales

El sistema resuelve automáticamente la variable `$base` para operar en la raíz del dominio o en subdirectorios:

| Servicio / Rol | URL Dinámica (Docker) | URL Dinámica (XAMPP) | Credenciales |
| :--- | :--- | :--- | :--- |
| **Panel Docente (PC)** | `http://<HOST>:8080/login` | `http://<HOST>/asistencia/login` | Usuario: `profesor`<br>Clave: `12345` |
| **Panel Docente (Red Wi-Fi)** | `http://<IP_LOCAL>:8080/login` | `http://<IP_LOCAL>/asistencia/login` | Usuario: `profesor`<br>Clave: `12345` |
| **Docente Secundario (Demo)** | `http://<HOST>:8080/login` | `http://<HOST>/asistencia/login` | Usuario: `Demo`<br>Clave: `Demo123` |
| **Escaneo de Asistencia** | `http://<HOST>:8080/asistencia/escanear` | `http://<HOST>/asistencia/asistencia/escanear` | Acceso directo / QR |
| **Portal del Estudiante** | `http://<HOST>:8080/login-estudiante` | `http://<HOST>/asistencia/login-estudiante` | Código (ej. `EST001`) |
| **phpMyAdmin** | `http://<HOST>:8081` | `http://<HOST>/phpmyadmin` | Servidor: `db` / `localhost` |

---

## Cómo Probar desde el Teléfono Móvil (Misma red Wi-Fi)

Para que los estudiantes puedan escanear el código QR con la cámara de sus teléfonos:

1. Asegúrate de que la computadora y el teléfono estén conectados a la misma red Wi-Fi.
2. Obtén la IP local de tu computadora:
   * En Windows: ejecuta `ipconfig` en PowerShell / CMD (busca la Dirección IPv4 del adaptador Wi-Fi, ejemplo: `192.168.X.X`).
   * En Linux / macOS: ejecuta `hostname -I` o `ifconfig`.
3. Abre el panel docente en tu computadora utilizando tu IP local:
   * En Docker: `http://<IP_LOCAL>:8080/login`
   * En XAMPP: `http://<IP_LOCAL>/asistencia/login`
4. Inicia sesión y genera el QR de clase:
   * El sistema detecta dinámicamente tu IP (`$_SERVER['HTTP_HOST']`) y codifica el QR con la URL accesible para todos los dispositivos de la red local:
     `http://<IP_LOCAL>:8080/asistencia/escanear?codigo=<CODIGO_SESION>`
5. Escanea desde el celular:
   * Con la cámara de tu teléfono o ingresando directamente a la pantalla de escaneo web en `http://<IP_LOCAL>:8080/asistencia/escanear`.

---

## Guía Paso a Paso para Probar el Sistema

### 1. Iniciar Sesión como Docente
1. Abre tu navegador e ingresa a la ruta de login: `http://<HOST>:8080/login` (o `http://localhost/asistencia/login`).
2. Ingresa las credenciales de prueba:
   * Usuario: `profesor`
   * Contraseña: `12345`
3. Haz clic en **Ingresar al Panel Docente**. Serás redirigido al panel principal (`/dashboard`).

---

### 2. Generar una Sesión de Clase y Código QR
1. En el formulario central:
   * Selecciona una **Carrera** (ej. *Desarrollo de Software*).
   * Selecciona un **Nivel** (ej. *Tercer Nivel*).
   * Escribe una **Materia** o selecciona uno de los chips sugeridos (ej. *Programación Web II*).
2. Presiona el botón **Generar Código QR de Asistencia**.
3. Verás aparecer el **Código QR** generado junto a su **Código Manual de 8 caracteres** (ej. `A1B2C3D4`) y la tabla de asistencias en vivo.

---

### 3. Registrar Asistencia de Estudiantes y Probar Cámara / Audio
1. Con la sesión abierta, abre otra pestaña en tu navegador o usa el teléfono móvil e ingresa a:
   `http://localhost:8080/asistencia/escanear` (o con tu IP local en el celular).
2. **Prueba con Cámara Web / Móvil:**
   * Pulsa el botón **Escanear Código QR con la Cámara**.
   * En PC o navegador compatible, se activará el visor de video con la línea de barrido láser. Apunta hacia el código QR de la clase.
   * En teléfonos móviles conectados por HTTP local, se activará directamente la cámara fotográfica nativa para tomar la foto del código QR.
   * En cuanto se reconoce el código QR, se emitirá un **tono armónico de confirmación (beep)** y se rellenará automáticamente el código de la sesión.
3. Ingresa un código de estudiante de prueba: `EST001`.
4. Presiona **Confirmar Mi Asistencia**. Verás la pantalla de confirmación con los datos del alumno y un sonido de acceso acreditado.
5. Vuelve a la pestaña del Docente (`/dashboard`):
   * En un máximo de 5 segundos, la tabla se actualizará automáticamente mostrando al estudiante.
   * Si el sonido del docente está activado, el proyector del aula emitirá un **aviso sonoro de campana** confirmando la llegada del nuevo alumno.
6. Intenta ingresar nuevamente con el código `EST001` en la misma clase: el sistema rechazará la duplicidad emitiendo un tono de advertencia.

---

### 4. Administrar Estudiantes (CRUD y Código Sugerido)
1. En la barra superior, haz clic en **Estudiantes** (`/estudiantes`).
2. Presiona el botón **+ Registrar Nuevo Estudiante**.
3. Observa cómo el sistema **sugiere automáticamente el siguiente código correlativo disponible** (ej. `EST009`), ubicando el cursor directo en el campo de nombres para ahorrar tiempo.
4. Si lo deseas, puedes editar el código sugerido o presionar el botón **Autogenerar**.
5. Completa los nombres, apellidos y selecciona la carrera institucional.
6. Haz clic en **Guardar Estudiante**. Verás que se agrega de inmediato a la base de datos y se actualiza el contador.

---

### 5. Consultar el Portal del Estudiante
1. Entra a `http://localhost:8080/login-estudiante`.
2. Ingresa el código de un alumno (ej. `EST001`).
3. Verás su expediente académico con métricas de asistencia, historial completo de materias cursadas y datos del docente titular.

---

### 6. Filtrar Reportes y Exportar (CSV, Excel y PDF)
1. En el menú del docente, haz clic en **Reportes** (`/reportes`).
2. Utiliza los botones de periodo rápido (`Hoy`, `Este Mes`, `Últimos 30 días`) o filtra por materia y nombre de alumno.
3. Prueba los 3 formatos oficiales de descarga:
   * **Descargar CSV:** Archivo estándar delimitado por comas con codificación UTF-8 BOM.
   * **Descargar Excel:** Archivo estructurado para Microsoft Excel con membrete y formato de celdas.
   * **Descargar PDF:** Documento formal apaisado (Landscape) con logotipo del ISTPET, tabla autoajustable y paginación.
