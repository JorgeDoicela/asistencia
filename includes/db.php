<?php
// Configuracion de conexion a la base de datos (XAMPP por defecto)
$host = 'localhost';
$dbname = 'asistencia_qr';
$user = 'root';
$pass = ''; // por defecto XAMPP no tiene clave en root

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Error de conexion a la base de datos: ' . $e->getMessage());
}
