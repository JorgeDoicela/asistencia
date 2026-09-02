<?php

require_once dirname(__DIR__) . '/config/database.php';

// Modelo Docente: Representa a los profesores y gestiona sus consultas en la base de datos

class Docente
{
    // Busca un docente por su nombre de usuario (para el login)
    public static function buscarPorUsuario(string $usuario): ?array
    {
        $db = Database::conectar();
        $stmt = $db->prepare("SELECT * FROM docentes WHERE usuario = ? LIMIT 1");
        $stmt->execute([$usuario]);
        $docente = $stmt->fetch();
        return $docente ?: null;
    }

    // Busca un docente por su ID
    public static function buscarPorId(int $id): ?array
    {
        $db = Database::conectar();
        $stmt = $db->prepare("SELECT id, nombre, usuario, creado_en FROM docentes WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        $docente = $stmt->fetch();
        return $docente ?: null;
    }
}
