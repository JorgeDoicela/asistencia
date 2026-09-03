<?php

require_once dirname(__DIR__) . '/config/database.php';

// Modelo Docente: Representa a los profesores y administradores del sistema

class Docente
{
    // Busca un docente o administrador por su nombre de usuario (para el login)
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
        $stmt = $db->prepare("SELECT id, nombre, usuario, password, rol, activo, creado_en FROM docentes WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        $docente = $stmt->fetch();
        return $docente ?: null;
    }

    // Lista docentes/usuarios con opción de búsqueda y filtro por rol
    public static function listar(string $busqueda = '', string $rol = ''): array
    {
        $db = Database::conectar();
        $sql = "SELECT d.id, d.nombre, d.usuario, d.rol, d.activo, d.creado_en,
                       COUNT(DISTINCT s.id) as total_sesiones,
                       COUNT(a.id) as total_asistencias
                FROM docentes d
                LEFT JOIN sesiones s ON d.id = s.docente_id
                LEFT JOIN asistencias a ON s.id = a.sesion_id
                WHERE 1=1";
        
        $params = [];

        if (!empty($busqueda)) {
            $sql .= " AND (d.nombre LIKE ? OR d.usuario LIKE ?)";
            $params[] = "%{$busqueda}%";
            $params[] = "%{$busqueda}%";
        }

        if (!empty($rol)) {
            $sql .= " AND d.rol = ?";
            $params[] = $rol;
        }

        $sql .= " GROUP BY d.id ORDER BY d.nombre ASC";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    // Registra un nuevo docente o administrador con contraseña cifrada en Bcrypt
    public static function crear(string $nombre, string $usuario, string $password, string $rol = 'docente'): bool
    {
        $db = Database::conectar();
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $sql = "INSERT INTO docentes (nombre, usuario, password, rol, activo) VALUES (?, ?, ?, ?, 1)";
        $stmt = $db->prepare($sql);
        return $stmt->execute([$nombre, $usuario, $hash, $rol]);
    }

    // Actualiza datos básicos y rol de un docente
    public static function actualizar(int $id, string $nombre, string $usuario, string $rol, int $activo): bool
    {
        $db = Database::conectar();
        $sql = "UPDATE docentes SET nombre = ?, usuario = ?, rol = ?, activo = ? WHERE id = ?";
        $stmt = $db->prepare($sql);
        return $stmt->execute([$nombre, $usuario, $rol, $activo, $id]);
    }

    // Restablece la contraseña de un docente/usuario
    public static function cambiarPassword(int $id, string $nuevaPassword): bool
    {
        $db = Database::conectar();
        $hash = password_hash($nuevaPassword, PASSWORD_BCRYPT);
        $sql = "UPDATE docentes SET password = ? WHERE id = ?";
        $stmt = $db->prepare($sql);
        return $stmt->execute([$hash, $id]);
    }

    // Cambia el estado activo/inactivo de un docente
    public static function cambiarEstado(int $id, int $activo): bool
    {
        $db = Database::conectar();
        $sql = "UPDATE docentes SET activo = ? WHERE id = ?";
        $stmt = $db->prepare($sql);
        return $stmt->execute([$activo, $id]);
    }

    // Elimina a un docente si no tiene clases registradas
    public static function eliminar(int $id): bool
    {
        $db = Database::conectar();
        $stmt = $db->prepare("DELETE FROM docentes WHERE id = ?");
        return $stmt->execute([$id]);
    }

    // Cuenta el total de docentes activos
    public static function contarActivos(?string $rol = null): int
    {
        $db = Database::conectar();
        if ($rol !== null) {
            $stmt = $db->prepare("SELECT COUNT(*) as total FROM docentes WHERE rol = ? AND activo = 1");
            $stmt->execute([$rol]);
        } else {
            $stmt = $db->query("SELECT COUNT(*) as total FROM docentes WHERE activo = 1");
        }
        $fila = $stmt->fetch();
        return (int) ($fila['total'] ?? 0);
    }
}

