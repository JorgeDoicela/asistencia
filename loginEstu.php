<?php
// Los estudiantes acceden directamente por QR, sin login
// Redirigir directo al formulario
header('Location: formulario.php');
exit;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Estudiante - Sistema de Asistencia QR</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <style>
        :root{
            --azul:#2C356D;
            --dorado:#B79B4A;
            --gris:#f4f4f4;
            --blanco:#ffffff;
            --texto:#333;
            --rojo:#e74c3c;
        }

        *{
            margin:0;
            padding:0;
            box-sizing:border-box;
            font-family:'Segoe UI',Tahoma,Geneva,Verdana,sans-serif;
        }

        body{
            background:
                linear-gradient(rgba(44,53,109,.75), rgba(44,53,109,.75)),
                url("https://images.unsplash.com/photo-1523240795612-9a054b0db644?auto=format&fit=crop&w=1920&q=80");
            background-size:cover;
            background-position:center;
            background-repeat:no-repeat;
            background-attachment:fixed;

            min-height:100vh;
            display:flex;
            justify-content:center;
            align-items:center;
            padding:20px;
        }

        .login-wrap{
            width:100%;
            max-width:430px;
        }

        .login-box{
            background:#fff;
            padding:40px 35px;
            border-radius:15px;
            box-shadow:0 15px 35px rgba(0,0,0,.35);
            border-top:6px solid var(--dorado);
            text-align:center;
            backdrop-filter:blur(5px);
        }

        .login-logo{
            width:140px;
            margin-bottom:20px;
            background:#fff;
            border-radius:10px;
            padding:5px;
        }

        h1{
            color:var(--azul);
            font-size:27px;
            margin-bottom:5px;
        }

        .login-box p{
            color:#666;
            margin-bottom:25px;
        }

        .error-msg{
            display:flex;
            align-items:center;
            gap:10px;
            background:#fdecea;
            color:var(--rojo);
            padding:12px;
            border-left:5px solid var(--rojo);
            border-radius:8px;
            margin-bottom:20px;
            text-align:left;
        }

        .input-group{
            position:relative;
            margin-bottom:18px;
        }

        .input-group i{
            position:absolute;
            left:15px;
            top:50%;
            transform:translateY(-50%);
            color:#888;
        }

        .input-group input{
            width:100%;
            padding:14px 14px 14px 45px;
            border:1px solid #ccc;
            border-radius:8px;
            font-size:16px;
            transition:.3s;
            background:#fafafa;
        }

        .input-group input:focus{
            border-color:var(--azul);
            box-shadow:0 0 10px rgba(44,53,109,.2);
            background:#fff;
            outline:none;
        }

        button{
            width:100%;
            background:var(--azul);
            color:#fff;
            border:none;
            padding:15px;
            border-radius:8px;
            font-size:16px;
            font-weight:bold;
            cursor:pointer;
            transition:.3s;
            text-transform:uppercase;
            letter-spacing:.5px;
        }

        button:hover{
            background:var(--dorado);
            color:var(--azul);
        }

        .demo-tag{
            display:block;
            margin-top:25px;
            padding:12px;
            background:#f7f7f7;
            border:1px dashed #bbb;
            border-radius:8px;
            color:#666;
            font-size:14px;
        }

        .demo-tag b{
            color:var(--azul);
        }

        @media(max-width:480px){
            .login-box{
                padding:30px 25px;
            }

            .login-logo{
                width:110px;
            }

            h1{
                font-size:23px;
            }
        }
    </style>
</head>

<body>

<div class="login-wrap">

<form class="login-box" method="POST">

    <img src="https://istpet.edu.ec/wp-content/uploads/2025/02/ISTPET-LOGO-300x300.jpg"
         class="login-logo"
         alt="ISTPET">

    <h1>Control de Asistencia</h1>

    <p>Portal para Estudiantes</p>

    <?php if($error): ?>
        <div class="error-msg">
            <i class="fas fa-exclamation-circle"></i>
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <div class="input-group">
        <i class="fas fa-user-graduate"></i>
        <input
            type="text"
            name="usuario"
            placeholder="Usuario / Matrícula"
            required
            autofocus>
    </div>

    <div class="input-group">
        <i class="fas fa-lock"></i>
        <input
            type="password"
            name="password"
            placeholder="Contraseña"
            required>
    </div>

    <button type="submit">
        <i class="fas fa-sign-in-alt"></i>
        Ingresar al Sistema
    </button>

    <span class="demo-tag">
        <i class="fas fa-key"></i>
        Demo: usuario <b>estudiante</b> /
        clave <b>12345</b>
    </span>

</form>

</div>

</body>
</html>