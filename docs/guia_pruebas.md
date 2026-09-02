# Guia de Pruebas y Uso del Sistema de Asistencia QR

Esta guia detalla el procedimiento paso a paso para probar y validar el funcionamiento del sistema tanto desde la computadora (navegador web) como desde un telefono movil conectado a la misma red Wi-Fi.

---

## Datos de Acceso y Credenciales

| Servicio / Rol | URL de Acceso | Credenciales |
| :--- | :--- | :--- |
| Panel Docente (PC) | `http://localhost:8080/login` | Usuario: `profesor`<br>Clave: `12345` |
| Panel Docente (Movil / Red Wi-Fi) | `http://TU_IP_LOCAL:8080/login` | Usuario: `profesor`<br>Clave: `12345` |
| Docente Secundario (Demo) | `http://localhost:8080/login` | Usuario: `Demo`<br>Clave: `Demo123` |
| Formulario de Estudiantes | `http://localhost:8080/asistencia/escanear` | (Acceso directo para escanear QR) |
| Portal del Estudiante | `http://localhost:8080/login-estudiante` | Con codigo de estudiante (ej. `EST001`) |
| phpMyAdmin (Base de Datos) | `http://localhost:8081` | Servidor: `db`<br>Usuario: `root`<br>Clave: `rootpassword` |

---

## Como Probar desde el Telefono Movil (Misma red Wi-Fi)

Para que los estudiantes puedan escanear el codigo QR con la camara de sus telefonos:

1. Asegurate de que la PC y el telefono esten conectados a la misma red Wi-Fi.
2. Obten la IP local de tu computadora:
   * En Windows: ejecuta `ipconfig` en la terminal (busca la direccion IPv4 del adaptador Wi-Fi, ejemplo: `192.168.1.15`).
   * En Linux: ejecuta `hostname -I`.
3. Abre el panel docente usando tu IP local:
   * En lugar de entrar a `localhost:8080`, ingresa en el navegador de tu computadora a:
     ```text
     http://192.168.1.15:8080/login
     ```
     *(Reemplaza `192.168.1.15` por tu IP real)*.
4. Inicia sesion y genera el QR:
   * El sistema detectara automaticamente tu IP y creara el codigo QR con el enlace `http://192.168.1.15:8080/asistencia/escanear?codigo=CODIGO`.
5. Escanea con tu celular:
   * Abre la camara o app lectora de QR de tu telefono y apunta a la pantalla del computador.
   * Se abrira de inmediato la pantalla en el navegador del telefono con el codigo de la clase listo para registrar la asistencia.

---

## Guia Paso a Paso para Probar el Sistema

### 1. Iniciar Sesion como Docente
1. Abre tu navegador e ingresa a: `http://localhost:8080/login`.
2. Ingresa las credenciales de prueba:
   * Usuario: `profesor`
   * Contrasena: `12345`
3. Haz clic en **Ingresar al Panel**. Seras redirigido al panel principal (`/dashboard`).

---

### 2. Generar una Sesion de Clase y Codigo QR
1. En el formulario central:
   * Selecciona una **Carrera** (ej. *Desarrollo de Software*).
   * Selecciona un **Nivel** (ej. *Tercer Nivel*).
   * Escribe una **Materia** (ej. *Programacion Web II*).
2. Presiona el boton **Generar Codigo QR de Asistencia**.
3. Veras aparecer el **Codigo QR** generado junto a un **Codigo de Sesion** de 8 caracteres (ej. `A1B2C3D4`) y la tabla de asistencias en vivo.

---

### 3. Registrar Asistencia de Estudiantes
1. Con la sesion abierta, abre otra pestana en tu navegador (o usa el telefono) y ve a:
   `http://localhost:8080/asistencia/escanear?codigo=A1B2C3D4`
   *(reemplaza `A1B2C3D4` por el codigo de tu sesion activa)*.
2. Ingresa un codigo de estudiante de prueba: `EST001`.
3. Presiona **Confirmar Asistencia**. Veras la pantalla verde de confirmacion con los datos del alumno.
4. Vuelve a la pestana del Docente (`/dashboard`) y observa como en un maximo de 5 segundos aparece automaticamente el estudiante en la tabla sin necesidad de recargar la pagina.
5. Intenta ingresar nuevamente con el codigo `EST001` en la misma clase. El sistema te avisara amigablemente que el alumno ya tiene asistencia registrada para esa clase.

---

### 4. Administrar Estudiantes (CRUD)
1. En la barra superior, haz clic en **Estudiantes** (`/estudiantes`).
2. Presiona el boton **+ Registrar Nuevo Estudiante**.
3. Completa los datos (ejemplo: Codigo `EST010`, Nombres `Pedro`, Apellidos `Salazar`, Carrera `Desarrollo de Software`).
4. Haz clic en **Guardar Estudiante**. Veras que se agrega a la lista.
5. Prueba editarlo o buscarlo en la barra superior.

---

### 5. Consultar el Portal del Estudiante
1. Entra a `http://localhost:8080/login-estudiante`.
2. Ingresa el codigo de un alumno (ej. `EST001`).
3. Veras su expediente academico con todas las clases a las que ha asistido, la hora exacta y el nombre del profesor.

---

### 6. Filtrar Reportes y Exportar a Excel
1. En el menu del docente, haz clic en **Reportes** (`/reportes`).
2. Aplica filtros por fecha o materia.
3. Haz clic en **Descargar CSV (Excel)** para obtener el archivo descargable.
