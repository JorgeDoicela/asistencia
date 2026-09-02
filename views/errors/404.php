<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página no encontrada - 404</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f8fafc; color: #1e293b; display: flex; align-items: center; justify-content: center; height: 100vh; margin: 0; }
        .card { text-align: center; background: white; padding: 40px; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); max-width: 450px; }
        h1 { font-size: 72px; margin: 0; color: #2C356D; }
        p { color: #64748b; font-size: 16px; margin: 15px 0 25px; }
        a { background: #2C356D; color: white; text-decoration: none; padding: 10px 24px; border-radius: 6px; font-weight: 600; display: inline-block; transition: 0.3s; }
        a:hover { background: #B79B4A; color: #2C356D; }
    </style>
</head>
<body>
    <div class="card">
        <h1>404</h1>
        <h2>Página no encontrada</h2>
        <p>La ruta solicitada <code><?= htmlspecialchars($uri ?? '') ?></code> no existe en el sistema.</p>
        <a href="javascript:history.back()">Volver atrás</a>
    </div>
</body>
</html>
