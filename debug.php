<?php
echo "<h1>Estado del Sistema</h1>";

echo "<h2>PHP Info</h2>";
echo "PHP Version: " . phpversion() . "<br>";
echo "MySQL extension: " . (extension_loaded('mysqli') ? '✓ Sí' : '✗ No') . "<br>";
echo "PDO MySQL: " . (extension_loaded('pdo_mysql') ? '✓ Sí' : '✗ No') . "<br>";

echo "<h2>Intentando conectar a BD...</h2>";
try {
    $pdo = new PDO("mysql:host=localhost;dbname=asistencia_qr;charset=utf8mb4", "root", "");
    echo "✓ Conexión exitosa a BD<br>";
} catch (PDOException $e) {
    echo "✗ Error: " . $e->getMessage() . "<br>";
    echo "<p><strong>Solución:</strong></p>";
    echo "<ul>";
    echo "<li>¿Tienes XAMPP instalado?</li>";
    echo "<li>¿Está corriendo MySQL?</li>";
    echo "<li>¿Existe la base de datos 'asistencia_qr'?</li>";
    echo "</ul>";
}

echo "<h2>Variables GET</h2>";
echo "clase = " . htmlspecialchars($_GET['clase'] ?? 'No enviado') . "<br>";
?>
