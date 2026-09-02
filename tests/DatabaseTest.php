<?php

require_once dirname(__DIR__) . '/config/database.php';

class DatabaseTest
{
    public static function ejecutar(): bool
    {
        echo "  - Probando conexion con PDO... ";
        $db = Database::conectar();
        if (!$db) {
            echo "FALLO: No se pudo obtener conexion.\n";
            return false;
        }
        echo "OK\n";

        echo "  - Verificando existencia de tablas requeridas... ";
        $tablasRequeridas = ['docentes', 'estudiantes', 'sesiones', 'asistencias'];
        $stmt = $db->query("SHOW TABLES");
        $tablas = $stmt->fetchAll(PDO::FETCH_COLUMN);

        foreach ($tablasRequeridas as $tabla) {
            if (!in_array($tabla, $tablas)) {
                echo "FALLO: La tabla '{$tabla}' no existe.\n";
                return false;
            }
        }
        echo "OK (Tablas: " . implode(', ', $tablasRequeridas) . ")\n";

        return true;
    }
}
