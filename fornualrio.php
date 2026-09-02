<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Asistencia - ISTPET</title>
    <style>
        :root {
            --azul-marino: #2C356D;
            --dorado: #B79B4A;
            --blanco: #ffffff;
            --gris-fondo: #f4f6f9;
            --gris-borde: #dcdfe6;
            --texto-oscuro: #2c3e50;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', sans-serif; }
        body { background-color: var(--gris-fondo); color: var(--texto-oscuro); }
        .container { max-width: 600px; margin: 40px auto; padding: 0 20px; }
        .card { background: var(--blanco); padding: 30px; border-radius: 8px; border-top: 5px solid var(--azul-marino); box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        .card h2 { color: var(--azul-marino); margin-bottom: 5px; }
        .card p { color: var(--dorado); font-weight: 600; margin-bottom: 20px; }
        .grupo { display: flex; flex-direction: column; gap: 6px; margin-bottom: 15px; }
        .grupo label { font-size: 14px; font-weight: 600; color: var(--azul-marino); }
        .grupo input, .grupo select { padding: 10px; border: 1px solid var(--gris-borde); border-radius: 4px; font-size: 14px; }
        .input-readonly { background-color: #e9ecef; font-weight: bold; color: var(--azul-marino); }
        .btn-submit { background: var(--azul-marino); color: var(--blanco); border: none; padding: 12px; font-size: 15px; font-weight: 600; border-radius: 4px; cursor: pointer; width: 100%; transition: 0.3s; }
        .btn-submit:hover { background: var(--dorado); color: var(--azul-marino); }
    </style>
</head>
<body>

<div class="container">
    <div class="card">
        <header>
            <h2>Registro de Asistencia</h2>
            <p>Instituto Superior Tecnológico Mayor Pedro Traversari</p>
        </header>

        <form action="procesar.php" method="POST">

            <!-- Captura automática del código de la sesión -->
            <div class="grupo">
                <label for="codigo_sesion">Código de Sesión</label>
                <input type="text" id="codigo_sesion" name="codigo_sesion" 
                       value="<?php echo isset($_GET['sesion']) ? htmlspecialchars($_GET['sesion']) : ''; ?>" 
                       class="input-readonly" readonly required placeholder="Ej: BFD20D36">
            </div>

            <div class="grupo">
                <label for="nombre">Nombre</label>
                <input type="text" id="nombre" name="nombre" required>
            </div>

            <div class="grupo">
                <label for="apellido">Apellido</label>
                <input type="text" id="apellido" name="apellido" required>
            </div>

            <div class="grupo">
                <label for="carrera">Carrera</label>
                <select id="carrera" name="carrera" required>
                    <option value="Programación Web">Programación Web (Desarrollo de Software)</option>
                    <option value="Mecánica Automotriz">Mecánica Automotriz</option>
                    <option value="Entrenamiento Deportivo">Entrenamiento Deportivo</option>
                </select>
            </div>

            <button type="submit" class="btn-submit">Registrar Asistencia</button>
        </form>
    </div>
</div>

</body>
</html>