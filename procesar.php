<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $codigo_sesion = htmlspecialchars(trim($_POST['codigo_sesion'] ?? ''));
    $nombre        = htmlspecialchars(trim($_POST['nombre'] ?? ''));
    $apellido      = htmlspecialchars(trim($_POST['apellido'] ?? ''));
    $carrera       = htmlspecialchars(trim($_POST['carrera'] ?? ''));

    if (empty($codigo_sesion) || empty($nombre) || empty($apellido)) {
        die("Error: Faltan datos obligatorios o la sesión no es válida.");
    }

    /*
    // Ejemplo de inserción a la base de datos (PDO)
    $stmt = $conexion->prepare("INSERT INTO asistencias (codigo_sesion, estudiante_nombre, estudiante_apellido, carrera, fecha_registro) VALUES (?, ?, ?, ?, NOW())");
    $stmt->execute([$codigo_sesion, $nombre, $apellido, $carrera]);
    */

    echo "<h1>¡Asistencia registrada exitosamente para la sesión: $codigo_sesion!</h1>";
}
?>