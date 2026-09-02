<?php

require_once dirname(__DIR__) . '/models/Docente.php';
require_once dirname(__DIR__) . '/models/Sesion.php';
require_once dirname(__DIR__) . '/models/Estudiante.php';
require_once dirname(__DIR__) . '/models/Asistencia.php';

class AsistenciaTest
{
    public static function ejecutar(): bool
    {
        $docente = Docente::buscarPorUsuario('profesor');
        $estudiante = Estudiante::buscarPorCodigo('EST001');

        if (!$docente || !$estudiante) {
            echo "FALLO: Datos base insuficientes para prueba de asistencia.\n";
            return false;
        }

        $docenteId = (int)$docente['id'];
        $estudianteId = (int)$estudiante['id'];

        echo "  - Creando sesion temporal para registrar asistencia... ";
        $codigoSesion = 'ASIS' . strtoupper(substr(md5(uniqid()), 0, 4));
        Sesion::crear($docenteId, $codigoSesion, 'Materia Asistencia Test');
        $sesion = Sesion::buscarPorCodigoActiva($codigoSesion);
        $sesionId = (int)$sesion['id'];
        echo "OK (Sesion ID: {$sesionId})\n";

        echo "  - Verificando que inicialmente NO exista asistencia... ";
        if (Asistencia::existe($sesionId, $estudianteId)) {
            echo "FALLO: Detecto asistencia que aun no se ha registrado.\n";
            return false;
        }
        echo "OK\n";

        echo "  - Registrando primera asistencia... ";
        $registrada = Asistencia::registrar($sesionId, $estudianteId);
        if (!$registrada) {
            echo "FALLO: No se pudo registrar la asistencia.\n";
            return false;
        }
        echo "OK\n";

        echo "  - Verificando deteccion de asistencia registrada... ";
        if (!Asistencia::existe($sesionId, $estudianteId)) {
            echo "FALLO: La asistencia registrada no fue detectada.\n";
            return false;
        }
        echo "OK\n";

        echo "  - Verificando listado de asistencias de la sesion... ";
        $lista = Asistencia::listarPorSesion($sesionId);
        if (count($lista) !== 1 || $lista[0]['codigo'] !== 'EST001') {
            echo "FALLO: El listado no devolvio la fila esperada.\n";
            return false;
        }
        echo "OK (1 asistente encontrado)\n";

        echo "  - Limpiando sesion temporal... ";
        Sesion::cerrar($sesionId, $docenteId);
        echo "OK\n";

        return true;
    }
}
