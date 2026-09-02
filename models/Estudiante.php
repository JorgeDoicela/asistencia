<?php

require_once dirname(__DIR__) . '/config/database.php';

// Modelo Estudiante: Gestiona todas las operaciones con la tabla estudiantes

class Estudiante
{
    // Obtiene la lista de estudiantes, con opcion de busqueda
    public static function listar(string $busqueda = ''): array
    {
        $db = Database::conectar();

        if (!empty($busqueda)) {
            $parametro = "%{$busqueda}%";
            $sql = "SELECT * FROM estudiantes 
                    WHERE codigo LIKE ? OR nombre LIKE ? OR apellido LIKE ? OR carrera LIKE ?
                    ORDER BY nombre ASC";
            $stmt = $db->prepare($sql);
            $stmt->execute([$parametro, $parametro, $parametro, $parametro]);
        } else {
            $stmt = $db->query("SELECT * FROM estudiantes ORDER BY nombre ASC");
        }

        return $stmt->fetchAll();
    }

    // Busca un estudiante por su codigo unico (ej. EST001)
    public static function buscarPorCodigo(string $codigo): ?array
    {
        $db = Database::conectar();
        $stmt = $db->prepare("SELECT * FROM estudiantes WHERE codigo = ? LIMIT 1");
        $stmt->execute([$codigo]);
        $estudiante = $stmt->fetch();
        return $estudiante ?: null;
    }

    // Busca un estudiante por su ID primario
    public static function buscarPorId(int $id): ?array
    {
        $db = Database::conectar();
        $stmt = $db->prepare("SELECT * FROM estudiantes WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        $estudiante = $stmt->fetch();
        return $estudiante ?: null;
    }

    // Inserta un nuevo estudiante en la base de datos
    public static function crear(string $codigo, string $nombre, string $apellido, string $carrera): bool
    {
        $db = Database::conectar();
        $sql = "INSERT INTO estudiantes (codigo, nombre, apellido, carrera) VALUES (?, ?, ?, ?)";
        $stmt = $db->prepare($sql);
        return $stmt->execute([$codigo, $nombre, $apellido, $carrera]);
    }

    // Modifica los datos de un estudiante existente
    public static function actualizar(int $id, string $codigo, string $nombre, string $apellido, string $carrera): bool
    {
        $db = Database::conectar();
        $sql = "UPDATE estudiantes SET codigo = ?, nombre = ?, apellido = ?, carrera = ? WHERE id = ?";
        $stmt = $db->prepare($sql);
        return $stmt->execute([$codigo, $nombre, $apellido, $carrera, $id]);
    }

    // Elimina a un estudiante por su ID
    public static function eliminar(int $id): bool
    {
        $db = Database::conectar();
        $stmt = $db->prepare("DELETE FROM estudiantes WHERE id = ?");
        return $stmt->execute([$id]);
    }

    // Cuenta el total de estudiantes registrados
    public static function contar(): int
    {
        $db = Database::conectar();
        $stmt = $db->query("SELECT COUNT(*) as total FROM estudiantes");
        $fila = $stmt->fetch();
        return (int) ($fila['total'] ?? 0);
    }
}
