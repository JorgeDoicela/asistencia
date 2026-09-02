<?php

require_once dirname(__DIR__) . '/models/Docente.php';

class DocenteTest
{
    public static function ejecutar(): bool
    {
        echo "  - Buscando docente 'profesor'... ";
        $docente = Docente::buscarPorUsuario('profesor');
        if (!$docente) {
            echo "FALLO: Docente 'profesor' no encontrado.\n";
            return false;
        }
        echo "OK (ID: {$docente['id']})\n";

        echo "  - Verificando contrasena valida ('12345')... ";
        if (!password_verify('12345', $docente['password'])) {
            echo "FALLO: La contrasena no coincide con el hash.\n";
            return false;
        }
        echo "OK\n";

        echo "  - Verificando rechazo de contrasena invalida ('clave_falsa')... ";
        if (password_verify('clave_falsa', $docente['password'])) {
            echo "FALLO: Acepto contrasena invalida.\n";
            return false;
        }
        echo "OK\n";

        return true;
    }
}
