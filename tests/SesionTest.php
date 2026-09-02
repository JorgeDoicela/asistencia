<?php

require_once dirname(__DIR__) . '/models/Docente.php';
require_once dirname(__DIR__) . '/models/Sesion.php';

class SesionTest
{
    public static function ejecutar(): bool
    {
        $docente = Docente::buscarPorUsuario('profesor');
        if (!$docente) {
            echo "FALLO: No hay docente para la prueba de sesion.\n";
            return false;
        }
        $docenteId = (int)$docente['id'];

        echo "  - Creando sesion de clase de prueba... ";
        $codigoSesion = 'TEST' . strtoupper(substr(md5(uniqid()), 0, 4));
        $creada = Sesion::crear($docenteId, $codigoSesion, 'Materia Test (Tercer Nivel)');
        if (!$creada) {
            echo "FALLO: No se pudo crear la sesion.\n";
            return false;
        }
        echo "OK (Codigo: {$codigoSesion})\n";

        echo "  - Buscando sesion activa por codigo... ";
        $sesion = Sesion::buscarPorCodigoActiva($codigoSesion);
        if (!$sesion || (int)$sesion['activa'] !== 1) {
            echo "FALLO: La sesion no aparece como activa.\n";
            return false;
        }
        echo "OK (ID: {$sesion['id']})\n";

        echo "  - Finalizando sesion de clase... ";
        $cerrada = Sesion::cerrar((int)$sesion['id'], $docenteId);
        if (!$cerrada) {
            echo "FALLO: No se pudo cerrar la sesion.\n";
            return false;
        }
        echo "OK\n";

        echo "  - Verificando que ya no figure como activa... ";
        $sesionCerrada = Sesion::buscarPorCodigoActiva($codigoSesion);
        if ($sesionCerrada !== null) {
            echo "FALLO: La sesion cerrada sigue respondiendo como activa.\n";
            return false;
        }
        echo "OK\n";

        return true;
    }
}
