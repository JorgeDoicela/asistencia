<?php

require_once dirname(__DIR__) . '/config/database.php';

// Modelo Sesion: Administra las sesiones de clase y los codigos QR generados por el docente

class Sesion
{
    // Obtiene la sesion que actualmente esta activa (activa = 1) para un docente
    public static function obtenerActiva(int $docenteId): ?array
    {
        $db = Database::conectar();
        $sql = "SELECT * FROM sesiones WHERE docente_id = ? AND activa = 1 ORDER BY id DESC LIMIT 1";
        $stmt = $db->prepare($sql);
        $stmt->execute([$docenteId]);
        $sesion = $stmt->fetch();
        return $sesion ?: null;
    }

    // Busca una sesion activa por su codigo (usado cuando el alumno escanea el QR)
    public static function buscarPorCodigoActiva(string $codigoSesion): ?array
    {
        $db = Database::conectar();
        $sql = "SELECT s.*, d.nombre as docente_nombre 
                FROM sesiones s
                JOIN docentes d ON s.docente_id = d.id
                WHERE s.codigo_sesion = ? AND s.activa = 1 
                LIMIT 1";
        $stmt = $db->prepare($sql);
        $stmt->execute([$codigoSesion]);
        $sesion = $stmt->fetch();
        return $sesion ?: null;
    }

    // Crea una nueva sesion de clase con hora de inicio
    public static function crear(int $docenteId, string $codigoSesion, string $materia): bool
    {
        $db = Database::conectar();
        $sql = "INSERT INTO sesiones (docente_id, codigo_sesion, materia, fecha, hora_inicio, activa) 
                VALUES (?, ?, ?, CURDATE(), CURTIME(), 1)";
        $stmt = $db->prepare($sql);
        return $stmt->execute([$docenteId, $codigoSesion, $materia]);
    }

    // Cierra una sesion de clase (pone activa = 0 y guarda la hora de fin)
    public static function cerrar(int $sesionId, int $docenteId): bool
    {
        $db = Database::conectar();
        $sql = "UPDATE sesiones SET activa = 0, hora_fin = CURTIME() WHERE id = ? AND docente_id = ?";
        $stmt = $db->prepare($sql);
        return $stmt->execute([$sesionId, $docenteId]);
    }

    // Obtiene el historial de las ultimas sesiones impartidas por el docente
    public static function listarHistorial(int $docenteId, int $limite = 8): array
    {
        $db = Database::conectar();
        $sql = "SELECT s.*, COUNT(a.id) as total_asistencias 
                FROM sesiones s 
                LEFT JOIN asistencias a ON s.id = a.sesion_id 
                WHERE s.docente_id = ? 
                GROUP BY s.id 
                ORDER BY s.id DESC 
                LIMIT " . (int)$limite;
        $stmt = $db->prepare($sql);
        $stmt->execute([$docenteId]);
        return $stmt->fetchAll();
    }

    // Cuenta el total de sesiones creadas por el docente
    public static function contarPorDocente(int $docenteId): int
    {
        $db = Database::conectar();
        $stmt = $db->prepare("SELECT COUNT(*) as total FROM sesiones WHERE docente_id = ?");
        $stmt->execute([$docenteId]);
        $fila = $stmt->fetch();
        return (int) ($fila['total'] ?? 0);
    }

    // Lista todas las sesiones activas en este momento en toda la institución (Monitoreo Admin)
    public static function listarTodasActivas(): array
    {
        $db = Database::conectar();
        $sql = "SELECT s.*, d.nombre as docente_nombre, d.usuario as docente_usuario,
                       COUNT(a.id) as total_asistencias
                FROM sesiones s
                JOIN docentes d ON s.docente_id = d.id
                LEFT JOIN asistencias a ON s.id = a.sesion_id
                WHERE s.activa = 1
                GROUP BY s.id
                ORDER BY s.hora_inicio DESC";
        $stmt = $db->query($sql);
        return $stmt->fetchAll();
    }

    // Cierre forzoso de una sesión por el Administrador (para clases olvidadas)
    public static function cerrarPorAdmin(int $sesionId): bool
    {
        $db = Database::conectar();
        $sql = "UPDATE sesiones SET activa = 0, hora_fin = CURTIME() WHERE id = ?";
        $stmt = $db->prepare($sql);
        return $stmt->execute([$sesionId]);
    }

    // Cuenta cuántas clases están activas en tiempo real en la institución
    public static function contarActivasGlobal(): int
    {
        $db = Database::conectar();
        $stmt = $db->query("SELECT COUNT(*) as total FROM sesiones WHERE activa = 1");
        $fila = $stmt->fetch();
        return (int) ($fila['total'] ?? 0);
    }

    // Cuenta el total histórico de clases dictadas en la institución
    public static function contarTotalInstitucional(): int
    {
        $db = Database::conectar();
        $stmt = $db->query("SELECT COUNT(*) as total FROM sesiones");
        $fila = $stmt->fetch();
        return (int) ($fila['total'] ?? 0);
    }

    // Historial reciente institucional de clases
    public static function listarRecientesGlobal(int $limite = 10): array
    {
        $db = Database::conectar();
        $sql = "SELECT s.*, d.nombre as docente_nombre, COUNT(a.id) as total_asistencias
                FROM sesiones s
                JOIN docentes d ON s.docente_id = d.id
                LEFT JOIN asistencias a ON s.id = a.sesion_id
                GROUP BY s.id
                ORDER BY s.id DESC
                LIMIT " . (int)$limite;
        $stmt = $db->query($sql);
        return $stmt->fetchAll();
    }
}

