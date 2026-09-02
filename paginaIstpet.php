<?php
require_once 'includes/db.php';
session_start();

if (isset($_SESSION['docente_id'])) {
    header('Location: dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = trim($_POST['usuario'] ?? '');
    $clave = $_POST['password'] ?? '';

    $stmt = $pdo->prepare('SELECT * FROM docentes WHERE usuario = ?');
    $stmt->execute([$usuario]);
    $docente = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($docente && password_verify($clave, $docente['password'])) {
        $_SESSION['docente_id'] = $docente['id'];
        $_SESSION['docente_nombre'] = $docente['nombre'];
        header('Location: dashboard.php');
        exit;
    } else {
        $error = 'Usuario o contraseña incorrectos.';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Asistencia - Tecnológico Traversari</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <style>
        :root{
            --azul:#2C356D;
            --dorado:#B79B4A;
            --gris:#f4f4f4;
            --blanco:#ffffff;
            --rojo:#e74c3c;
        }

        *{
            margin:0;
            padding:0;
            box-sizing:border-box;
            font-family: 'Segoe UI', sans-serif;
        }

        body{
            background:var(--gris);
        }

        /* MENÚ */
        nav{
            background:var(--azul);
            position: sticky;
            top: 0;
            z-index: 999;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        nav ul{
            display:flex;
            justify-content:center;
            list-style:none;
        }

        nav a{
            color:white;
            text-decoration:none;
            padding:18px 25px;
            display:block;
            transition:.3s;
            font-weight: 600;
            cursor: pointer;
        }

        nav a:hover{
            background:var(--dorado);
            color: var(--azul);
        }

        /* BANNER CON IMAGEN DE FONDO */
        .banner{
            background:linear-gradient(
            rgba(44,53,109,.9),
            rgba(44,53,109,.9)),
            url("https://images.unsplash.com/photo-1522202176988-66273c2fd55f");
            
            background-size:cover;
            background-position:center;
            color:white;
            text-align:center;
            padding:140px 20px;
        }

        .banner h2{
            font-size:40px;
            margin-bottom:10px;
        }

        .banner p{
            font-size:18px;
        }

        /* Ventana Emergente Flotante (MODAL) */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(5px);
            display: none; /* Oculto por defecto */
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        /* Mostrar el modal si hay error en PHP o mediante JS */
        .modal-overlay.active {
            display: flex;
        }

        .login-wrap{
            width:100%;
            max-width:430px;
            padding: 20px;
            position: relative;
        }

        .login-box{
            background:#fff;
            padding:40px 35px;
            border-radius:15px;
            box-shadow:0 15px 35px rgba(0,0,0,.3);
            border-top:6px solid var(--dorado);
            text-align:center;
        }

        /* Botón cerrar modal (X) */
        .close-modal {
            position: absolute;
            top: 30px;
            right: 35px;
            font-size: 24px;
            color: #888;
            cursor: pointer;
            transition: 0.3s;
        }

        .close-modal:hover {
            color: var(--rojo);
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

        /* PIE DE PÁGINA */
        .footer{
            background:#2C356D;
            color:#ffffff;
            margin-top:0px;
        }

        .footer-container{
            max-width:1200px;
            margin:auto;
            padding:50px 30px;
            display:flex;
            justify-content:space-between;
            flex-wrap:wrap;
            gap:40px;
        }

        .footer-col{
            flex:1;
            min-width:280px;
        }

        .footer-logo{
            width:220px;
            margin-bottom:20px;
            background:white;
            border-radius:10px;
            padding:10px;
        }

        .footer-col h3{
            color:#B79B4A;
            font-size:28px;
            margin-bottom:20px;
            font-weight:700;
        }

        .footer-col p{
            line-height:1.8;
            font-size:16px;
            margin-bottom:15px;
        }

        .footer-col i{
            color:#ffffff;
            margin-right:10px;
        }

        .map-link{
            display:inline-block;
            color:#ffffff;
            text-decoration:none;
            margin-top:10px;
            font-weight:600;
            transition: 0.3s;
        }

        .map-link:hover{
            color:#B79B4A;
        }

        .social-icons{
            display:flex;
            gap:18px;
            margin-top:25px;
        }

        .social-icons a{
            color:#ffffff;
            font-size:32px;
            transition:0.3s;
        }

        .social-icons a:hover{
            color:#B79B4A;
            transform:translateY(-3px);
        }

        .footer-bottom{
            background:#B79B4A;
            color:#2C356D;
            text-align:center;
            padding:15px;
            font-weight:bold;
            font-size:15px;
        }

        @media(max-width:768px){
            .footer-container{
                flex-direction:column;
                text-align:center;
            }

            .social-icons{
                justify-content:center;
            }

            .footer-logo{
                margin:auto auto 20px auto;
            }

            .login-box{
                padding:30px 25px;
            }
        }
    </style>
</head>
<body>

<nav>
    <ul>
        <li><a href="escanear.php">Estudiantes</a></li>
        <li><a id="btn-docentes">Docentes</a></li>
    </ul>
</nav>

<section class="banner">
    <h2>Sistema de Control de Asistencia</h2>
    <p>Registro y seguimiento académico en tiempo real</p>
</section>

<div id="modalLogin" class="modal-overlay <?php echo $error ? 'active' : ''; ?>">
    <div class="login-wrap">
        <form class="login-box" method="POST" action="">
            <i class="fas fa-times close-modal" id="btn-close"></i>

            <img src="https://istpet.edu.ec/wp-content/uploads/2025/02/ISTPET-LOGO-300x300.jpg"
                 class="login-logo"
                 alt="ISTPET">

            <h1>Control de Asistencia</h1>
            <p>Acceso exclusivo para Docentes</p>

            <?php if($error): ?>
                <div class="error-msg">
                    <i class="fas fa-exclamation-circle"></i>
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <div class="input-group">
                <i class="fas fa-user"></i>
                <input
                    type="text"
                    name="usuario"
                    placeholder="Usuario"
                    required>
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
                Demo: usuario <b>profesor</b> / clave <b>12345</b>
            </span>
        </form>
    </div>
</div>

<footer class="footer">
    <div class="footer-container">
        <div class="footer-col">
            <img src="https://istpet.edu.ec/wp-content/uploads/2025/02/ISTPET-LOGO-300x300.jpg"
                 alt="Tecnológico Traversari"
                 class="footer-logo">
            <p>
                Formamos profesionales competitivos, creativos,
                íntegros y con valores, con elevado nivel académico,
                capaces de responder a las exigencias del mercado laboral.
            </p>
        </div>

        <div class="footer-col">
            <h3>Encuéntranos en</h3>
            <p>
                Av. Matilde Álvarez y Hugo Díaz Romero,
                Sector Chillogallo.
            </p>
            <a href="https://maps.google.com" target="_blank" class="map-link">
                <i class="fas fa-map-marker-alt"></i> Ir a Google Maps
            </a>

            <div class="social-icons">
                <a href="https://www.facebook.com/tecnologicotraversari" target="_blank"><i class="fab fa-facebook"></i></a>
                <a href="https://www.instagram.com/tecnologico_traversari/" target="_blank"><i class="fab fa-instagram"></i></a>
                <a href="https://www.tiktok.com/@tecnologico_traversari" target="_blank"><i class="fab fa-tiktok"></i></a>
                <a href="https://www.youtube.com/@tecnologico_traversari" target="_blank"><i class="fab fa-youtube"></i></a>
                <a href="https://www.linkedin.com/in/tecnologico-traversari/" target="_blank"><i class="fab fa-linkedin"></i></a>
            </div>
        </div>

        <div class="footer-col">
            <h3>Contáctanos</h3>
            <p><i class="fas fa-phone-alt"></i> 02 303 2894</p>
            <p><i class="fas fa-mobile-alt"></i> 098 403 3166</p>
            <p><i class="fas fa-envelope"></i> asistencias@istpet.edu.ec</p>
        </div>
    </div>

    <div class="footer-bottom">
        © 2026 Instituto Superior Tecnológico Traversari | Sistema de Control de Asistencia
    </div>
</footer>

<script>
    const modal = document.getElementById('modalLogin');
    const btnDocentes = document.getElementById('btn-docentes');
    const btnClose = document.getElementById('btn-close');

    // Al hacer clic en "Docentes" abre la ventana flotante
    btnDocentes.addEventListener('click', () => {
        modal.classList.add('active');
    });

    // Al hacer clic en la "X" cierra la ventana flotante
    btnClose.addEventListener('click', () => {
        modal.classList.remove('active');
    });

    // Al hacer clic fuera del cuadro blanco también se cierra
    window.addEventListener('click', (e) => {
        if (e.target === modal) {
            modal.classList.remove('active');
        }
    });
</script>

</body>
</html>