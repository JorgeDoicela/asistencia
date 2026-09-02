<?php
echo "<h1>Estado del Sistema</h1>";

echo "<h2>PHP Info</h2>";
echo "PHP Version: " . phpversion() . "<br>";
echo "MySQL extension: " . (extension_loaded('mysqli') ? 'Si' : 'No') . "<br>";
echo "PDO MySQL: " . (extension_loaded('pdo_mysql') ? 'Si' : 'No') . "<br>";

echo "<h2>Intentando conectar a BD...</h2>";
try {
    require_once 'includes/db.php';
    echo "Conexion exitosa a BD<br>";
    $docentesCount = $pdo->query("SELECT COUNT(*) FROM docentes")->fetchColumn();
    $estudiantesCount = $pdo->query("SELECT COUNT(*) FROM estudiantes")->fetchColumn();
    echo "Docentes en BD: $docentesCount<br>";
    echo "Estudiantes en BD: $estudiantesCount<br>";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "<br>";
}

echo "<h2>Variables GET</h2>";
echo "clase = " . htmlspecialchars($_GET['clase'] ?? 'No enviado') . "<br>";
?>
