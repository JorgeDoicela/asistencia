<?php

// Archivo de conexion a la base de datos
// Utiliza PDO para realizar consultas seguras con sentencias preparadas

class Database
{
    private static ?PDO $conexion = null;

    public static function conectar(): PDO
    {
        if (self::$conexion === null) {
            // Lee variables de entorno o usa los valores por defecto de XAMPP
            $host   = getenv('DB_HOST') ?: 'localhost';
            $port   = getenv('DB_PORT') ?: '3306';
            $dbname = getenv('DB_NAME') ?: 'asistencia_qr';
            $user   = getenv('DB_USER') ?: 'root';
            $pass   = getenv('DB_PASS') !== false ? getenv('DB_PASS') : '';

            try {
                $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4";
                self::$conexion = new PDO($dsn, $user, $pass, [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ]);
            } catch (PDOException $e) {
                die("Error al conectar con la base de datos: " . $e->getMessage());
            }
        }

        return self::$conexion;
    }
}
