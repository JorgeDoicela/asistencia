<?php
// Conexión a la base de datos (se asume que existe db.php)
require_once 'includes/db.php';

// Si la petición viene por POST vía AJAX (Fetch desde JavaScript)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');

    $nombre    = trim($_POST['nombre'] ?? '');
    $apellido  = trim($_POST['apellido'] ?? '');
    $fecha     = trim($_POST['fecha'] ?? '');
    $hora      = trim($_POST['hora'] ?? '');
    $carrera   = trim($_POST['carrera'] ?? '');
    $codigoSesion = trim($_POST['codigo_sesion'] ?? '');

    if (empty($nombre) || empty($apellido) || empty($fecha) || empty($hora) || empty($carrera)) {
        echo json_encode(['status' => 'error', 'mensaje' => 'Por favor, complete todos los campos.']);
        exit;
    }

    try {
        // 1. Buscar la sesión por código
        $stmt = $pdo->prepare('SELECT id FROM sesiones WHERE codigo_sesion = ? AND activa = 1');
        $stmt->execute([$codigoSesion]);
        $sesion = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$sesion) {
            echo json_encode(['status' => 'error', 'mensaje' => 'El código de clase no es válido o la sesión ya fue cerrada.']);
            exit;
        }

        $sesionId = $sesion['id'];

        // 2. Buscar al estudiante por nombre y apellido (o crear uno nuevo)
        $stmt = $pdo->prepare('SELECT id FROM estudiantes WHERE nombre = ? AND apellido = ?');
        $stmt->execute([$nombre, $apellido]);
        $estudiante = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$estudiante) {
            // Si no existe, crear nuevo estudiante
            $codigo = strtoupper('EST' . substr(md5($nombre . $apellido), 0, 6));
            $stmt = $pdo->prepare('INSERT INTO estudiantes (codigo, nombre, apellido, carrera) VALUES (?, ?, ?, ?)');
            $stmt->execute([$codigo, $nombre, $apellido, $carrera]);
            $estudianteId = $pdo->lastInsertId();
        } else {
            $estudianteId = $estudiante['id'];
        }

        // 3. Registrar la asistencia
        $stmt = $pdo->prepare('INSERT INTO asistencias (sesion_id, estudiante_id, fecha, hora) VALUES (?, ?, ?, ?)');
        $stmt->execute([$sesionId, $estudianteId, $fecha, $hora]);

        echo json_encode(['status' => 'success', 'mensaje' => '¡Asistencia registrada correctamente!']);
    } catch (PDOException $e) {
        error_log("Error al guardar asistencia: " . $e->getMessage());
        echo json_encode(['status' => 'error', 'mensaje' => 'Error al guardar: ' . $e->getMessage()]);
    }
    exit;
}

// Capturar el código de sesión/clase desde el parámetro URL si viene de un escaneo QR
$codigoSesion = htmlspecialchars($_GET['clase'] ?? '');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario Estudiante - ISTPET</title>
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', sans-serif;
        }

        body {
            background: linear-gradient(160deg, #eef2f7 0%, #e4e9f5 45%, #eef2f7 100%);
            min-height: 100vh;
        }

        /* CONTENEDOR */
        .container {
            display: flex;
        }

        /* SIDEBAR */
        .sidebar {
            width: 280px;
            height: 100vh;
            background: linear-gradient(180deg, #26346b 0%, #1f2c5c 45%, #141b3f 100%);
            position: fixed;
            left: 0;
            top: 0;
            color: white;
            border-right: 4px solid #d4a73d;

            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            padding-top: 40px;
        }

        /* LOGO */
        .logo {
            width: 210px;
            padding: 25px 20px;
            text-align: center;
            background: #ffffff;
            border-radius: 22px;
            box-shadow: 0 10px 25px rgba(0,0,0,.3);
        }

        .logo img {
            width: 130px;
            height: auto;
            display: block;
            margin: 0 auto;
        }

        .logo span {
            display: block;
            margin-top: 14px;
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 1.5px;
            color: #1f2c5c;
        }

        .logo span b {
            color: #c99a2e;
        }

        /* CONTENIDO */
        .contenido {
            margin-left: 280px;
            width: calc(100% - 280px);
            min-height: 100vh;

            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;

            padding: 40px;
        }

        /* PANEL */
        .panel {
            width: 100%;
            max-width: 650px;
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 12px 30px rgba(31, 44, 92, .2);
        }

        /* CABECERA */
        header {
            width: 100%;
            background: linear-gradient(120deg, #1f2c5c 0%, #31468f 60%, #3a53a8 100%);
            padding: 28px 20px 24px;
            text-align: center;
            border-bottom: 4px solid #d4a73d;
        }

        header h2 {
            color: #ffffff;
            letter-spacing: .5px;
            font-size: 26px;
        }

        header p {
            color: #d9e0f5;
            font-size: 13px;
            margin-top: 6px;
            letter-spacing: .5px;
        }

        /* TARJETAS Y MENSAJES */
        .card {
            width: 100%;
            background: #ffffff;
            padding: 35px;
        }

        .alerta {
            padding: 12px 15px;
            border-radius: 8px;
            margin-bottom: 18px;
            font-size: 14px;
            font-weight: 600;
            display: none;
            text-align: center;
        }

        .alerta.exito {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alerta.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        /* FORMULARIO */
        .grupo {
            margin-bottom: 18px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #1f2c5c;
        }

        input,
        select {
            width: 100%;
            padding: 12px;
            border: 1px solid #d7dbe6;
            border-radius: 8px;
            font-size: 15px;
            background: #f8f9fc;
            transition: .3s;
        }

        input:focus,
        select:focus {
            outline: none;
            border-color: #d4a73d;
            background: #ffffff;
            box-shadow: 0 0 8px rgba(212, 167, 61, .35);
        }

        button {
            width: 100%;
            padding: 14px;
            background: linear-gradient(120deg, #1f2c5c, #31468f);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 17px;
            font-weight: 600;
            letter-spacing: .5px;
            cursor: pointer;
            transition: .3s;
            margin-top: 10px;
        }

        button:hover {
            background: linear-gradient(120deg, #d4a73d, #c99a2e);
            color: #1f2c5c;
        }

        /* RESPONSIVE */
        @media(max-width:768px) {
            .container {
                flex-direction: column;
            }

            .sidebar {
                position: relative;
                width: 100%;
                height: auto;
                padding: 25px 0;
                border-right: none;
                border-bottom: 4px solid #d4a73d;
            }

            .contenido {
                margin-left: 0;
                width: 100%;
                padding: 25px 15px;
            }

            .logo {
                width: 180px;
                padding: 18px;
            }

            .logo img {
                width: 100px;
            }

            header h2 {
                font-size: 22px;
            }

            .card {
                padding: 25px;
            }
        }

        @media(max-width:480px) {
            .logo {
                width: 150px;
                padding: 15px;
                border-radius: 16px;
            }

            .logo img {
                width: 80px;
            }

            header {
                padding: 22px 15px 18px;
            }

            header h2 {
                font-size: 19px;
            }

            .card {
                padding: 18px;
            }

            button {
                font-size: 15px;
                padding: 12px;
            }
        }
    </style>
</head>
<body>

<div class="container">

    <!-- Menú lateral -->
    <aside class="sidebar">
        <div class="logo">
            <img src="https://istpet.edu.ec/wp-content/uploads/2025/02/ISTPET-LOGO-300x300.jpg" alt="Logo ISTPET">
            <span>IST <b>PET</b> TRAVERSARI</span>
        </div>
    </aside>

    <!-- Contenido Principal -->
    <main class="contenido">

        <div class="panel">

            <header>
                <h2>Registro de Asistencia</h2>
                <p>Instituto Superior Tecnológico Mayor Pedro Traversari</p>
            </header>

            <div class="card">

                <div id="alerta" class="alerta"></div>

                <form id="formAsistencia">

                    <input type="hidden" id="codigo_sesion" value="<?= htmlspecialchars($codigoSesion) ?>">

                    <div class="grupo">
                        <label for="nombre">Nombre</label>
                        <input type="text" id="nombre" name="nombre" placeholder="Ingrese su nombre" required>
                    </div>

                    <div class="grupo">
                        <label for="apellido">Apellido</label>
                        <input type="text" id="apellido" name="apellido" placeholder="Ingrese su apellido" required>
                    </div>

                    <div class="grupo">
                        <label for="fecha">Fecha</label>
                        <input type="date" id="fecha" name="fecha" required>
                    </div>

                    <div class="grupo">
                        <label for="hora">Hora</label>
                        <input type="time" id="hora" name="hora" required>
                    </div>

                    <div class="grupo">
                        <label for="carrera">Carrera</label>
                        <select id="carrera" name="carrera" required>
                            <option value="">Seleccione Carrera</option>
                            <option value="Desarrollo de Software">Desarrollo de Software</option>
                            <option value="Mecánica Automotriz">Mecánica Automotriz</option>
                            <option value="Entrenamiento Deportivo">Entrenamiento Deportivo</option>
                            <option value="Educación Inicial">Educación Inicial</option>
                        </select>
                    </div>

                    <button type="submit" id="btnGuardar">Guardar Asistencia</button>

                </form>

            </div>

        </div>

    </main>

</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const fechaInput = document.getElementById('fecha');
    const horaInput = document.getElementById('hora');
    const form = document.getElementById('formAsistencia');
    const alerta = document.getElementById('alerta');
    const btnGuardar = document.getElementById('btnGuardar');

    // 1. Asignar Fecha y Hora actuales automáticamente al cargar la página
    const ahora = new Date();
    fechaInput.value = ahora.toISOString().split('T')[0];
    
    const horas = String(ahora.getHours()).padStart(2, '0');
    const minutos = String(ahora.getMinutes()).padStart(2, '0');
    horaInput.value = `${horas}:${minutos}`;

    // 2. Procesar el envío del formulario vía AJAX
    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        const codigoSesion = document.getElementById('codigo_sesion').value;

        // Validar que haya un código de sesión
        if (!codigoSesion || codigoSesion.trim() === '') {
            alerta.style.display = 'block';
            alerta.className = 'alerta error';
            alerta.innerText = '⚠️ Debe escanear un código QR válido';
            return;
        }

        const formData = new FormData(form);
        formData.append('codigo_sesion', codigoSesion);

        // Mostrar loading
        btnGuardar.disabled = true;
        btnGuardar.innerText = 'Guardando...';

        try {
            const response = await fetch(window.location.href, {
                method: 'POST',
                body: formData
            });

            const res = await response.json();

            if (res.status === 'success') {
                // Limpiar y resetear
                form.reset();
                fechaInput.value = new Date().toISOString().split('T')[0];
                horaInput.value = `${horas}:${minutos}`;
                
                // Mostrar confirmación
                alerta.style.display = 'block';
                alerta.className = 'alerta exito';
                alerta.innerText = '✓ Asistencia registrada correctamente';
                
                // Limpiar en 3 segundos
                setTimeout(() => {
                    alerta.style.display = 'none';
                }, 3000);
            } else {
                alerta.style.display = 'block';
                alerta.className = 'alerta error';
                alerta.innerText = '❌ ' + res.mensaje;
            }
        } catch (err) {
            alerta.style.display = 'block';
            alerta.className = 'alerta error';
            alerta.innerText = '❌ Error de conexión';
        } finally {
            // Restaurar botón
            btnGuardar.disabled = false;
            btnGuardar.innerText = 'Guardar Asistencia';
        }
    });
});
</script>

</body>
</html>