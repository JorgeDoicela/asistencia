<?php
require_once '../includes/auth.php';
requiereLogin();
require_once '../includes/db.php';

header('Content-Type: application/json');

$fecha = $_GET['fecha'] ?? '';
$nombre = $_GET['nombre'] ?? '';
$materia = $_GET['materia'] ?? '';

$sql = "SELECT a.id, e.nombre AS estudiante, e.codigo, s.materia, a.fecha, a.hora
        FROM asistencias a
        JOIN estudiantes e ON e.id = a.estudiante_id
        JOIN sesiones s ON s.id = a.sesion_id
        WHERE s.docente_id = ?";
$params = [$_SESSION['docente_id']];

if ($fecha !== '') {
    $sql .= " AND a.fecha = ?";
    $params[] = $fecha;
}
if ($nombre !== '') {
    $sql .= " AND (e.nombre LIKE ? OR e.codigo LIKE ?)";
    $params[] = "%$nombre%";
    $params[] = "%$nombre%";
}
if ($materia !== '') {
    $sql .= " AND s.materia LIKE ?";
    $params[] = "%$materia%";
}

$sql .= " ORDER BY a.id DESC LIMIT 200";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$filas = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode(['ok' => true, 'data' => $filas]);
