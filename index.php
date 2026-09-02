<?php
session_start();

// Si ya hay sesión de docente, redirigir al dashboard
if (isset($_SESSION['docente_id'])) {
    header('Location: dashboard.php');
    exit;
}
// Si hay sesión de estudiante, redirigir a formulario
if (isset($_SESSION['estudiante_id'])) {
    header('Location: formulario.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Asistencia QR - ISTPET</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', sans-serif;
        }

        body {
            background: linear-gradient(135deg, #26346b 0%, #1f2c5c 50%, #141b3f 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .contenedor {
            max-width: 900px;
            width: 100%;
        }

        .header {
            text-align: center;
            margin-bottom: 50px;
            color: white;
        }

        .logo-container {
            margin-bottom: 30px;
        }

        .logo {
            width: 120px;
            height: 120px;
            margin: 0 auto 20px;
            background: white;
            border-radius: 20px;
            padding: 10px;
            box-shadow: 0 10px 30px rgba(0,0,0,.3);
        }

        .logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        h1 {
            font-size: 42px;
            margin-bottom: 10px;
            font-weight: 700;
            text-shadow: 2px 2px 4px rgba(0,0,0,.3);
        }

        .subtitle {
            font-size: 16px;
            color: #d9e0f5;
            letter-spacing: 1px;
        }

        .opciones {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-top: 50px;
        }

        .tarjeta {
            background: white;
            border-radius: 20px;
            padding: 40px 30px;
            text-align: center;
            box-shadow: 0 15px 40px rgba(0,0,0,.2);
            transition: all .4s ease;
            cursor: pointer;
            border: 3px solid transparent;
        }

        .tarjeta:hover {
            transform: translateY(-10px);
            box-shadow: 0 25px 60px rgba(0,0,0,.3);
            border-color: #d4a73d;
        }

        .tarjeta-icon {
            font-size: 60px;
            margin-bottom: 20px;
            color: #26346b;
        }

        .tarjeta-profesor .tarjeta-icon {
            color: #d4a73d;
        }

        .tarjeta h2 {
            font-size: 26px;
            color: #26346b;
            margin-bottom: 15px;
            font-weight: 700;
        }

        .tarjeta p {
            font-size: 14px;
            color: #666;
            margin-bottom: 25px;
            line-height: 1.6;
        }

        .btn {
            display: inline-block;
            padding: 14px 40px;
            background: linear-gradient(120deg, #26346b, #31468f);
            color: white;
            text-decoration: none;
            border-radius: 10px;
            font-weight: 600;
            transition: all .3s;
            border: none;
            cursor: pointer;
            font-size: 16px;
            letter-spacing: .5px;
        }

        .tarjeta-profesor .btn {
            background: linear-gradient(120deg, #d4a73d, #c99a2e);
            color: #26346b;
        }

        .btn:hover {
            transform: scale(1.05);
            box-shadow: 0 10px 25px rgba(0,0,0,.2);
        }

        @media (max-width: 768px) {
            .opciones {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            h1 {
                font-size: 32px;
            }

            .tarjeta {
                padding: 30px 20px;
            }

            .tarjeta-icon {
                font-size: 45px;
            }

            .tarjeta h2 {
                font-size: 22px;
            }
        }

        .info-codigo {
            background: rgba(212, 167, 61, 0.1);
            border: 2px solid #d4a73d;
            border-radius: 15px;
            padding: 20px;
            margin-top: 40px;
            color: white;
            text-align: center;
        }

        .info-codigo p {
            color: white;
            font-size: 14px;
            line-height: 1.8;
        }

        .codigo-destacado {
            background: #d4a73d;
            color: #26346b;
            padding: 12px 20px;
            border-radius: 8px;
            font-weight: 700;
            display: inline-block;
            margin: 10px 0;
            font-size: 16px;
        }
    </style>
</head>
<body>

<div class="contenedor">

    <div class="header">
        <div class="logo-container">
            <div class="logo">
                <img src="https://istpet.edu.ec/wp-content/uploads/2025/02/ISTPET-LOGO-300x300.jpg" alt="Logo ISTPET">
            </div>
        </div>
        <h1>Sistema de Asistencia QR</h1>
        <p class="subtitle">Instituto Superior Tecnológico Mayor Pedro Traversari</p>
    </div>

    <div class="opciones">

        <!-- Opción Profesor -->
        <div class="tarjeta tarjeta-profesor">
            <div class="tarjeta-icon">
                <i class="fas fa-chalkboard-user"></i>
            </div>
            <h2>Docentes</h2>
            <p>Accede como docente para generar códigos QR de asistencia y gestionar tus clases.</p>
            <a href="login.php" class="btn">Iniciar Sesión Docente</a>
        </div>

        <!-- Opción Estudiante -->
        <div class="tarjeta tarjeta-estudiante">
            <div class="tarjeta-icon">
                <i class="fas fa-qrcode"></i>
            </div>
            <h2>Estudiantes</h2>
            <p>Escanea el código QR de tu clase para registrar tu asistencia automáticamente.</p>
            <a href="formulario.php" class="btn">Registrar Asistencia</a>
        </div>

    </div>

    <div class="info-codigo">
        <p><strong>¿Cómo funciona?</strong></p>
        <p>El docente genera un QR único para cada clase. Los estudiantes escanean el QR con su teléfono y registran su asistencia al momento. 📱</p>
        <div class="codigo-destacado">
            Escanea → Rellena Datos → Registrado ✓
        </div>
    </div>

</div>

</body>
</html>
