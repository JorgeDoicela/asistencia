<?php
require_once 'includes/db.php';

$resultado = null; // 'ok' | 'error'
$mensaje = '';
$claseCodigo = $_GET['clase'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $claseCodigo = trim($_POST['clase'] ?? '');
    $codigoEstudiante = trim($_POST['codigo'] ?? '');

    // Buscar sesion activa con ese codigo
    $stmt = $pdo->prepare('SELECT * FROM sesiones WHERE codigo_sesion = ? AND activa = 1');
    $stmt->execute([$claseCodigo]);
    $sesion = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$sesion) {
        $resultado = 'error';
        $mensaje = 'El código de clase no es válido o la sesión ya fue cerrada.';
    } else {
        // Buscar estudiante
        $stmt = $pdo->prepare('SELECT * FROM estudiantes WHERE codigo = ?');
        $stmt->execute([$codigoEstudiante]);
        $estudiante = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$estudiante) {
            $resultado = 'error';
            $mensaje = 'No se encontró un estudiante con ese código.';
        } else {
            try {
                $stmt = $pdo->prepare('INSERT INTO asistencias (sesion_id, estudiante_id, fecha, hora) VALUES (?, ?, CURDATE(), CURTIME())');
                $stmt->execute([$sesion['id'], $estudiante['id']]);
                $resultado = 'ok';
                $mensaje = 'Asistencia registrada correctamente, ' . $estudiante['nombre'] . '.';
            } catch (PDOException $e) {
                $resultado = 'error';
                $mensaje = 'Ya registraste tu asistencia en esta clase.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registrar Asistencia</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="scan-wrap">
        <div class="scan-box">
            <h2>Registrar asistencia</h2>
            <p>Ingresa tu código de estudiante</p>

            <?php if ($resultado === 'ok'): ?>
                <div class="ok-msg"><?= htmlspecialchars($mensaje) ?></div>
            <?php elseif ($resultado === 'error'): ?>
                <div class="fail-msg"><?= htmlspecialchars($mensaje) ?></div>
            <?php endif; ?>

            <form method="POST" style="margin-top:18px; text-align:left;">
                <div class="form-group" style="margin-bottom:12px;">
                    <label>Código de clase</label>
                    <input type="text" name="clase" value="<?= htmlspecialchars($claseCodigo) ?>" readonly style="background-color: #f0f0f0; cursor: not-allowed;">
                </div>
                <div class="form-group" style="margin-bottom:16px;">
                    <label>Tu código de estudiante</label>
                    <input type="text" name="codigo" placeholder="Ej. EST001" required autofocus>
                </div>
                <button class="btn" style="width:100%; padding:12px;" type="submit">Registrar Asistencia</button>
            </form>
        </div>
    </div>
</body>
</html>
