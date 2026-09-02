<?php

require_once dirname(__DIR__) . '/config/database.php';

// Modelo Asistencia: Gestiona el registro y consulta de asistencias de los estudiantes

class Asistencia
{
    // Inserta la asistencia de un alumno a una sesion (fecha y hora actuales)
    public static function registrar(int $sesionId, int $estudianteId): bool
    {
        $db = Database::conectar();
        $sql = "INSERT INTO asistencias (sesion_id, estudiante_id, fecha, hora) 
                VALUES (?, ?, CURDATE(), CURTIME())";
        $stmt = $db->prepare($sql);
        return $stmt->execute([$sesionId, $estudianteId]);
    }

    // Verifica si un estudiante ya registro su asistencia en una sesion dada
    public static function existe(int $sesionId, int $estudianteId): bool
    {
        $db = Database::conectar();
        $sql = "SELECT id FROM asistencias WHERE sesion_id = ? AND estudiante_id = ? LIMIT 1";
        $stmt = $db->prepare($sql);
        $stmt->execute([$sesionId, $estudianteId]);
        return (bool)$stmt->fetch();
    }

    // Lista las asistencias de una sesion activa (usada para la tabla en vivo del docente)
    public static function listarPorSesion(int $sesionId): array
    {
        $db = Database::conectar();
        $sql = "SELECT a.id, a.fecha, a.hora, e.codigo, e.nombre, e.apellido, e.carrera 
                FROM asistencias a 
                JOIN estudiantes e ON a.estudiante_id = e.id 
                WHERE a.sesion_id = ? 
                ORDER BY a.id DESC";
        $stmt = $db->prepare($sql);
        $stmt->execute([$sesionId]);
        return $stmt->fetchAll();
    }

    // Lista el historial academico de asistencias de un alumno en particular
    public static function listarPorEstudiante(int $estudianteId): array
    {
        $db = Database::conectar();
        $sql = "SELECT a.id, a.fecha, a.hora, s.materia, s.codigo_sesion, d.nombre as docente 
                FROM asistencias a 
                JOIN sesiones s ON a.sesion_id = s.id 
                JOIN docentes d ON s.docente_id = d.id 
                WHERE a.estudiante_id = ? 
                ORDER BY a.fecha DESC, a.hora DESC";
        $stmt = $db->prepare($sql);
        $stmt->execute([$estudianteId]);
        return $stmt->fetchAll();
    }

    // Filtra las asistencias segun criterios para la pantalla de Reportes
    public static function filtrar(int $docenteId, ?string $inicio, ?string $fin, ?string $materia, ?string $busqueda): array
    {
        $db = Database::conectar();

        $sql = "SELECT a.id, a.fecha, a.hora, e.codigo, CONCAT(e.nombre, ' ', e.apellido) as estudiante, 
                       e.carrera, s.materia, s.codigo_sesion, d.nombre as docente
                FROM asistencias a
                JOIN estudiantes e ON a.estudiante_id = e.id
                JOIN sesiones s ON a.sesion_id = s.id
                JOIN docentes d ON s.docente_id = d.id
                WHERE s.docente_id = ?";
        
        $parametros = [$docenteId];

        if (!empty($inicio)) {
            $sql .= " AND a.fecha >= ?";
            $parametros[] = $inicio;
        }

        if (!empty($fin)) {
            $sql .= " AND a.fecha <= ?";
            $parametros[] = $fin;
        }

        if (!empty($materia)) {
            $sql .= " AND s.materia LIKE ?";
            $parametros[] = "%{$materia}%";
        }

        if (!empty($busqueda)) {
            $sql .= " AND (e.codigo LIKE ? OR e.nombre LIKE ? OR e.apellido LIKE ?)";
            $parametros[] = "%{$busqueda}%";
            $parametros[] = "%{$busqueda}%";
            $parametros[] = "%{$busqueda}%";
        }

        $sql .= " ORDER BY a.fecha DESC, a.hora DESC";

        $stmt = $db->prepare($sql);
        $stmt->execute($parametros);
        return $stmt->fetchAll();
    }

    // Cuenta cuantas asistencias se han registrado hoy en las materias del docente
    public static function contarHoyPorDocente(int $docenteId): int
    {
        $db = Database::conectar();
        $sql = "SELECT COUNT(a.id) as total 
                FROM asistencias a 
                JOIN sesiones s ON a.sesion_id = s.id 
                WHERE s.docente_id = ? AND a.fecha = CURDATE()";
        $stmt = $db->prepare($sql);
        $stmt->execute([$docenteId]);
        $fila = $stmt->fetch();
        return (int) ($fila['total'] ?? 0);
    }
}
