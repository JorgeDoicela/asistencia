<?php

// Ejecutor General de Pruebas Automatizadas del Sistema
// Se puede ejecutar con: docker compose exec web php tests/test_runner.php

echo "\n=======================================================\n";
echo "    SISTEMA DE ASISTENCIA QR - SUITE DE PRUEBAS\n";
echo "=======================================================\n\n";

require_once __DIR__ . '/DatabaseTest.php';
require_once __DIR__ . '/DocenteTest.php';
require_once __DIR__ . '/EstudianteTest.php';
require_once __DIR__ . '/SesionTest.php';
require_once __DIR__ . '/AsistenciaTest.php';
require_once __DIR__ . '/FlujoCompletoTest.php';

$suites = [
    'Conexion y Base de Datos'   => [DatabaseTest::class, 'ejecutar'],
    'Autenticacion de Docentes'  => [DocenteTest::class, 'ejecutar'],
    'Operaciones de Estudiantes' => [EstudianteTest::class, 'ejecutar'],
    'Control de Sesiones QR'     => [SesionTest::class, 'ejecutar'],
    'Registro de Asistencias'    => [AsistenciaTest::class, 'ejecutar'],
    'Flujo Completo del Sistema' => [FlujoCompletoTest::class, 'ejecutar'],
];

$total = count($suites);
$pasadas = 0;

foreach ($suites as $nombre => $callable) {
    echo "[SUITE] {$nombre}\n";
    $inicio = microtime(true);
    
    try {
        $exito = call_user_func($callable);
        $duracion = round((microtime(true) - $inicio) * 1000, 2);
        
        if ($exito) {
            echo "  --> RESULTADO: PASO ({$duracion} ms)\n\n";
            $pasadas++;
        } else {
            echo "  --> RESULTADO: FALLO ({$duracion} ms)\n\n";
        }
    } catch (Throwable $e) {
        echo "  --> ERROR EXCEPCION: " . $e->getMessage() . "\n\n";
    }
}

echo "=======================================================\n";
echo "  RESUMEN: {$pasadas} de {$total} suites pasaron exitosamente.\n";
echo "=======================================================\n\n";

exit($pasadas === $total ? 0 : 1);
