# Guía de Pruebas y Uso del Sistema de Asistencia QR

Esta guía detalla el procedimiento paso a paso para probar y validar el funcionamiento del sistema tanto desde la computadora (navegador web) como desde un **teléfono móvil conectado a la misma red Wi-Fi**.

---

## Datos de Acceso y Credenciales

| Servicio / Rol | URL de Acceso | Credenciales |
| :--- | :--- | :--- |
| **Panel Docente (PC)** | `http://localhost:8080/login.php` | Usuario: `profesor`<br>Clave: `12345` |
| **Panel Docente (Móvil / Red Wi-Fi)** | `http://TU_IP_LOCAL:8080/login.php` | Usuario: `profesor`<br>Clave: `12345` |
| **Docente Secundario (Demo)** | `http://localhost:8080/login.php` | Usuario: `Demo`<br>Clave: `Demo123` |
| **Formulario Estudiantes** | `http://localhost:8080/formulario.php` | *(Acceso directo sin contraseña)* |
| **phpMyAdmin (Base de Datos)** | `http://localhost:8081` | Servidor: `db`<br>Usuario: `root`<br>Clave: `rootpassword` |

---

## Cómo Probar desde el Teléfono Móvil (Misma red Wi-Fi)

Para que los estudiantes puedan escanear el código QR con la cámara de sus teléfonos:

1. **Asegúrate de que la PC y el teléfono estén conectados a la misma red Wi-Fi.**
2. **Obtén la IP local de tu computadora:**
   * En Linux: ejecuta `hostname -I` (ejemplo: `192.168.110.30`).
   * En Windows: ejecuta `ipconfig` en la terminal (busca la dirección IPv4 del adaptador Wi-Fi).
3. **Abre el panel docente usando tu IP local:**
   * En lugar de entrar a `localhost:8080`, ingresa en el navegador de tu computadora a:
     ```text
     http://192.168.110.30:8080/login.php
     ```
     *(Reemplaza `192.168.110.30` por tu IP real)*.
4. **Inicia sesión y genera el QR:**
   * El sistema detectará automáticamente tu IP y creará el código QR con el enlace `http://192.168.110.30:8080/formulario.php?clase=CODIGO`.
5. **Escanea con tu celular:**
   * Abre la cámara o app lectora de QR de tu teléfono y apunta a la pantalla del computador.
   * Se abrirá de inmediato el formulario en el navegador del teléfono listo para registrar la asistencia.

---

## Guía Paso a Paso para Probar Manualmente el Sistema

### 1. Iniciar Sesión como Docente
1. Abre tu navegador e ingresa a: **[http://localhost:8080/login.php](http://localhost:8080/login.php)**.
2. Ingresa las credenciales de prueba:
   * **Usuario:** `profesor`
   * **Contraseña:** `12345`
3. Haz clic en **Ingresar**. Serás redirigido al panel principal (`dashboard.php`).

---

### 2. Generar una Sesión de Clase y Código QR
1. En la tarjeta de la izquierda:
   * Selecciona una **Carrera** (ej. *Desarrollo de Software*).
   * Selecciona un **Nivel** (ej. *3er Nivel*).
   * Escribe una **Materia** (ej. *Base de Datos*).
2. Presiona el botón **Generar Código QR**.
3. Verás que aparece el **Código QR** generado junto a un **Código de Sesión** de 8 caracteres (ej. `A1B2C3D4`).

---

### 3. Registrar Asistencia como Estudiante
Puedes probarlo de dos maneras:

#### Opción A: En una pestaña incógnito del navegador
1. Abre una ventana de incógnito y entra a:
   ```text
   http://localhost:8080/formulario.php?clase=TU_CODIGO_DE_SESION
   ```
   *(Reemplaza `TU_CODIGO_DE_SESION` por el código que generaste en el paso 2)*.
2. Llena los datos del alumno:
   * **Nombre:** Juan
   * **Apellido:** Pérez
   * **Carrera:** Desarrollo de Software
3. Haz clic en **Registrar Asistencia**. Verás un mensaje verde de confirmación: *"¡Asistencia registrada correctamente!"*.

#### Opción B: Escaneando con tu celular (Misma red Wi-Fi)
1. Para escanear el QR con la cámara de tu teléfono, ambos dispositivos deben estar en la misma red Wi-Fi.
2. Abre la URL usando la IP local de tu computador: `http://TU_IP_LOCAL:8080` *(puedes consultar tu IP con `hostname -I` o `ip a`)*.
3. Apunta la cámara del teléfono hacia el QR en la pantalla de la PC y presiona el enlace detectado.
4. Rellena los datos y confirma el registro.

---

### 4. Verificar la Actualización en Vivo (Tiempo Real)
1. Vuelve a la pestaña del **Panel Docente** sin recargar la página.
2. En menos de **5 segundos**, la tabla inferior *"Asistencias registradas"* mostrará automáticamente la fila de Juan Pérez gracias al refresco en vivo.

---

### 5. Validar Casos Límite y Reglas de Negocio
* **Prueba de Duplicados:** En la pestaña del estudiante, intenta presionar "Registrar Asistencia" nuevamente con el mismo nombre y apellido. El sistema responderá en rojo: *"Ya has registrado tu asistencia en esta sesión de clase."*
* **Prueba de Cierre de Sesión:** En el panel del docente, presiona el botón rojo **Cerrar sesión**. Luego intenta registrar otra asistencia desde el formulario; el sistema la rechazará avisando que la sesión ya finalizó.

---

### 6. Módulos Adicionales

* **Gestión de Estudiantes (`http://localhost:8080/estudiantes.php`):**
  * Abre el menú lateral y ve a **Estudiantes**.
  * Agrega un nuevo estudiante con su código y luego prueba eliminarlo con el botón de papelera.
* **Reportes y Exportación CSV (`http://localhost:8080/reportes.php`):**
  * Abre el menú lateral y ve a **Reportes**.
  * Filtra por materia o fecha y haz clic en **Exportar CSV** para descargar la hoja de cálculo con todas las asistencias registradas.
* **Gestor de Base de Datos (`http://localhost:8081`):**
  * Ingresa con usuario `root` y contraseña `rootpassword` para inspeccionar directamente las tablas `sesiones` y `asistencias`.

---

## Comandos de Gestión con Docker

* **Iniciar el sistema en segundo plano:**
  ```bash
  docker compose up -d
  ```
* **Ver logs en tiempo real:**
  ```bash
  docker compose logs -f web
  ```
* **Detener el sistema:**
  ```bash
  docker compose down
  ```
