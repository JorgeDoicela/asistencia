<?php

require_once dirname(__DIR__) . '/models/Estudiante.php';

class EstudianteTest
{
    public static function ejecutar(): bool
    {
        echo "  - Listando estudiantes existentes... ";
        $lista = Estudiante::listar();
        if (empty($lista)) {
            echo "FALLO: No se encontraron estudiantes en la base de datos.\n";
            return false;
        }
        echo "OK (Total: " . count($lista) . ")\n";

        echo "  - Creando estudiante de prueba 'TEST999'... ";
        // Asegurar limpieza previa si existiera
        $existente = Estudiante::buscarPorCodigo('TEST999');
        if ($existente) {
            Estudiante::eliminar((int)$existente['id']);
        }

        $creado = Estudiante::crear('TEST999', 'Prueba', 'Automatizada', 'Desarrollo de Software');
        if (!$creado) {
            echo "FALLO: No se pudo insertar el estudiante de prueba.\n";
            return false;
        }
        echo "OK\n";

        echo "  - Buscando estudiante creado por codigo... ";
        $estudiante = Estudiante::buscarPorCodigo('TEST999');
        if (!$estudiante || $estudiante['nombre'] !== 'Prueba') {
            echo "FALLO: No se recupero correctamente el registro.\n";
            return false;
        }
        echo "OK (ID: {$estudiante['id']})\n";

        echo "  - Actualizando estudiante 'TEST999'... ";
        $actualizado = Estudiante::actualizar((int)$estudiante['id'], 'TEST999', 'Prueba Modificada', 'Automatizada', 'Mecanica Automotriz');
        if (!$actualizado) {
            echo "FALLO: No se pudo actualizar el estudiante.\n";
            return false;
        }
        echo "OK\n";

        echo "  - Eliminando estudiante de prueba... ";
        $eliminado = Estudiante::eliminar((int)$estudiante['id']);
        if (!$eliminado) {
            echo "FALLO: No se pudo eliminar el estudiante.\n";
            return false;
        }
        echo "OK\n";

        return true;
    }
}
