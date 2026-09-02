<?php

require_once __DIR__ . '/HttpClient.php';

// Test de Flujo Completo End-to-End (E2E)
// Simula el comportamiento real de navegadores web para Docente y Estudiante

class FlujoCompletoTest
{
    public static function ejecutar(): bool
    {
        $docente = new HttpClient();
        $estudiante = new HttpClient();

        // 1. Acceso a paginas publicas
        echo "  [1/13] Verificando paginas publicas (/ y /institucional)... ";
        $rHome = $docente->get('/');
        $rInst = $docente->get('/institucional');
        if ($rHome['codigo'] !== 200 || $rInst['codigo'] !== 200) {
            echo "FALLO: Codigos HTTP no validos ({$rHome['codigo']}, {$rInst['codigo']})\n";
            return false;
        }
        echo "OK (HTTP 200)\n";

        // 2. Proteccion de rutas privadas sin login
        echo "  [2/13] Verificando proteccion de ruta /dashboard sin sesion... ";
        $rPrivada = $docente->get('/dashboard');
        if ($rPrivada['codigo'] !== 302 || !str_contains($rPrivada['headers'], 'Location:')) {
            echo "FALLO: Permitió acceso no autorizado o no envio redireccion 302\n";
            return false;
        }
        echo "OK (Redirige 302 a /login)\n";

        // 3. Login de Docente
        echo "  [3/13] Iniciando sesion como Docente ('profesor')... ";
        $rLogin = $docente->post('/login', [
            'usuario'  => 'profesor',
            'password' => '12345'
        ]);
        if ($rLogin['codigo'] !== 302) {
            echo "FALLO: No redirigio tras login exitoso (HTTP {$rLogin['codigo']})\n";
            return false;
        }
        
        $rDash = $docente->get('/dashboard');
        if ($rDash['codigo'] !== 200 || !str_contains($rDash['body'], 'Panel de Asistencia')) {
            echo "FALLO: No cargo el Dashboard tras autenticarse\n";
            return false;
        }
        echo "OK (Autenticado y Dashboard cargado)\n";

        // 4. Creacion de nueva sesion de clase con codigo QR
        echo "  [4/13] Creando nueva sesion de clase con codigo QR... ";
        $rCrearSesion = $docente->post('/dashboard/sesion/crear', [
            'carrera' => 'Desarrollo de Software',
            'nivel'   => 'Tercer Nivel',
            'materia' => 'Arquitectura de Software E2E'
        ]);
        if ($rCrearSesion['codigo'] !== 302) {
            echo "FALLO: Error al crear sesion de clase\n";
            return false;
        }
        echo "OK\n";

        // 5. Verificacion en la API JSON de tiempo real
        echo "  [5/13] Consultando API JSON de tiempo real (/api/asistencias/activas)... ";
        $rApi = $docente->get('/api/asistencias/activas');
        $datosApi = json_decode($rApi['body'], true);
        if ($rApi['codigo'] !== 200 || empty($datosApi['activa']) || empty($datosApi['sesion']['codigo_sesion'])) {
            echo "FALLO: La API no devolvio la sesion activa generada\n";
            return false;
        }
        $codigoSesion = $datosApi['sesion']['codigo_sesion'];
        $sesionId = (int)$datosApi['sesion']['id'];
        echo "OK (Sesion Activa: {$codigoSesion})\n";

        // 6. El estudiante escanea el QR
        echo "  [6/13] Estudiante ingresa mediante enlace de QR (/asistencia/escanear)... ";
        $rEscanear = $estudiante->get("/asistencia/escanear?codigo={$codigoSesion}");
        if ($rEscanear['codigo'] !== 200 || !str_contains($rEscanear['body'], 'Arquitectura de Software E2E')) {
            echo "FALLO: El formulario de escaneo no mostro la materia de la sesion\n";
            return false;
        }
        echo "OK (Materia identificada correctamente)\n";

        // 7. El estudiante registra su asistencia
        echo "  [7/13] Estudiante registra asistencia con codigo 'EST001'... ";
        $rRegistro = $estudiante->post('/asistencia/registrar', [
            'codigo_sesion'     => $codigoSesion,
            'codigo_estudiante' => 'EST001'
        ]);
        if ($rRegistro['codigo'] !== 200 || !str_contains($rRegistro['body'], 'Asistencia Registrada')) {
            echo "FALLO: No se confirmo el registro de asistencia\n";
            return false;
        }
        echo "OK (Asistencia confirmada)\n";

        // 8. Regla de negocio: Prevencion de doble asistencia
        echo "  [8/13] Probando regla de negocio: Rechazo de doble asistencia... ";
        $rDoble = $estudiante->post('/asistencia/registrar', [
            'codigo_sesion'     => $codigoSesion,
            'codigo_estudiante' => 'EST001'
        ]);
        if (!str_contains($rDoble['body'], 'ya tiene registrada su asistencia')) {
            echo "FALLO: Permitió registrar doble asistencia al mismo alumno\n";
            return false;
        }
        echo "OK (Rechazo duplicado correctamente)\n";

        // 9. Verificacion en tiempo real por el Docente
        echo "  [9/13] Verificando actualizacion en tiempo real en la API del Docente... ";
        $rApiActualizada = $docente->get('/api/asistencias/activas');
        $datosActualizados = json_decode($rApiActualizada['body'], true);
        $asistentes = $datosActualizados['asistencias'] ?? [];
        $encontrado = false;
        foreach ($asistentes as $a) {
            if ($a['codigo'] === 'EST001') {
                $encontrado = true;
                break;
            }
        }
        if (!$encontrado) {
            echo "FALLO: El alumno EST001 no figura en la lista en vivo del docente\n";
            return false;
        }
        echo "OK (Alumno EST001 presente en la tabla en vivo)\n";

        // 10. Portal personal del Estudiante
        echo "  [10/13] Verificando Portal del Estudiante (/estudiante/portal)... ";
        $estudiante->post('/login-estudiante', ['codigo' => 'EST001']);
        $rPortal = $estudiante->get('/estudiante/portal');
        if ($rPortal['codigo'] !== 200 || !str_contains($rPortal['body'], $codigoSesion)) {
            echo "FALLO: El portal no mostro la clase registrada en su historial\n";
            return false;
        }
        echo "OK (Historial academico actualizado)\n";

        // 11. CRUD de Estudiantes por el Docente
        echo "  [11/13] Probando CRUD de estudiantes por el Docente... ";
        $rCrearEst = $docente->post('/estudiantes/crear', [
            'codigo'   => 'E2E99',
            'nombre'   => 'Prueba',
            'apellido' => 'Automatica',
            'carrera'  => 'Desarrollo de Software'
        ]);
        if ($rCrearEst['codigo'] !== 302) {
            echo "FALLO: Error al crear estudiante en modulo administrativo\n";
            return false;
        }

        // Buscar al nuevo estudiante para obtener su ID y luego eliminarlo
        $rBuscar = $docente->get('/estudiantes?buscar=E2E99');
        if (!str_contains($rBuscar['body'], 'E2E99')) {
            echo "FALLO: No se encontro al estudiante en la busqueda\n";
            return false;
        }

        // Recuperar ID y eliminar
        require_once dirname(__DIR__) . '/models/Estudiante.php';
        $estTmp = Estudiante::buscarPorCodigo('E2E99');
        if ($estTmp) {
            $docente->post('/estudiantes/eliminar', ['id' => $estTmp['id']]);
        }
        echo "OK (Alta, busqueda y baja correctas)\n";

        // 12. Reportes y descarga en CSV
        echo "  [12/13] Probando Reportes y Descarga de CSV (/reportes y /reportes/csv)... ";
        $rReportes = $docente->get('/reportes');
        $rCsv = $docente->get('/reportes/csv');
        if ($rReportes['codigo'] !== 200 || $rCsv['codigo'] !== 200) {
            echo "FALLO: Error en pantalla de reportes o descarga CSV\n";
            return false;
        }
        if (!str_contains($rCsv['headers'], 'Content-Type: text/csv') || !str_contains($rCsv['body'], 'EST001')) {
            echo "FALLO: El CSV descargado no contiene los datos esperados\n";
            return false;
        }
        echo "OK (Reporte HTML y archivo CSV validados)\n";

        // 13. Finalizar sesion de clase y cerrar sesion
        echo "  [13/13] Finalizando clase y cerrando sesion de docente... ";
        $docente->post('/dashboard/sesion/cerrar', ['sesion_id' => $sesionId]);
        $rApiFinal = $docente->get('/api/asistencias/activas');
        $datosFinales = json_decode($rApiFinal['body'], true);
        if ($datosFinales['activa'] !== false) {
            echo "FALLO: La sesion continua activa tras solicitar cierre\n";
            return false;
        }

        $rLogout = $docente->get('/logout');
        if ($rLogout['codigo'] !== 302) {
            echo "FALLO: Error al cerrar sesion\n";
            return false;
        }
        echo "OK (Clase finalizada y sesion destruida con exito)\n";

        return true;
    }
}
