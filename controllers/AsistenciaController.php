<?php

require_once __DIR__ . '/BaseController.php';
require_once dirname(__DIR__) . '/models/Sesion.php';
require_once dirname(__DIR__) . '/models/Estudiante.php';
require_once dirname(__DIR__) . '/models/Asistencia.php';

// Controlador de Asistencias: Gestiona el escaneo del QR, el guardado y la API en vivo

class AsistenciaController extends BaseController
{
    // Muestra la pantalla para ingresar el codigo de estudiante (con datos de la clase si vino en QR)
    public function mostrarEscanear(): void
    {
        $codigoSesion = strtoupper(trim($_GET['codigo'] ?? ''));
        $sesion = null;
        $error = null;

        if (!empty($codigoSesion)) {
            $sesion = Sesion::buscarPorCodigoActiva($codigoSesion);
            if (!$sesion) {
                $error = "La sesion de clase {$codigoSesion} no esta activa o no existe.";
            }
        }

        $this->vista('asistencia.escanear', [
            'base'         => self::obtenerRutaBase(),
            'codigoSesion' => $codigoSesion,
            'sesion'       => $sesion,
            'error'        => $error
        ]);
    }

    // Procesa el registro de la asistencia tras enviar el formulario
    public function registrar(): void
    {
        $codigoSesion     = strtoupper(trim($_POST['codigo_sesion'] ?? ''));
        $codigoEstudiante = strtoupper(trim($_POST['codigo_estudiante'] ?? ''));

        // Paso 1: Validar que los campos no esten vacios
        if (empty($codigoSesion) || empty($codigoEstudiante)) {
            $this->vista('asistencia.resultado', [
                'base'    => self::obtenerRutaBase(),
                'exito'   => false,
                'mensaje' => 'Por favor complete todos los datos requeridos.'
            ]);
            return;
        }

        // Paso 2: Validar que la sesion exista y este activa
        $sesion = Sesion::buscarPorCodigoActiva($codigoSesion);
        if (!$sesion) {
            $this->vista('asistencia.resultado', [
                'base'    => self::obtenerRutaBase(),
                'exito'   => false,
                'mensaje' => "La sesion {$codigoSesion} no esta activa o ya fue finalizada por el docente."
            ]);
            return;
        }

        // Paso 3: Validar que el estudiante este registrado en la institucion
        $estudiante = Estudiante::buscarPorCodigo($codigoEstudiante);
        if (!$estudiante) {
            $this->vista('asistencia.resultado', [
                'base'    => self::obtenerRutaBase(),
                'exito'   => false,
                'mensaje' => "El codigo {$codigoEstudiante} no corresponde a ningun estudiante registrado."
            ]);
            return;
        }

        // Paso 4: Evitar que el alumno registre doble asistencia en la misma clase
        if (Asistencia::existe((int)$sesion['id'], (int)$estudiante['id'])) {
            $this->vista('asistencia.resultado', [
                'base'       => self::obtenerRutaBase(),
                'exito'      => false,
                'mensaje'    => "El estudiante {$estudiante['nombre']} {$estudiante['apellido']} ya tiene registrada su asistencia en esta sesion.",
                'estudiante' => $estudiante,
                'sesion'     => $sesion
            ]);
            return;
        }

        // Paso 5: Registrar la asistencia en la base de datos
        if (Asistencia::registrar((int)$sesion['id'], (int)$estudiante['id'])) {
            $this->vista('asistencia.resultado', [
                'base'       => self::obtenerRutaBase(),
                'exito'      => true,
                'mensaje'    => 'Asistencia confirmada exitosamente.',
                'estudiante' => $estudiante,
                'sesion'     => $sesion,
                'hora'       => date('H:i:s')
            ]);
        } else {
            $this->vista('asistencia.resultado', [
                'base'    => self::obtenerRutaBase(),
                'exito'   => false,
                'mensaje' => 'Ocurrio un error al guardar la asistencia.'
            ]);
        }
    }

    // Endpoint API JSON: Responde a la peticion de JavaScript cada 5 segundos
    public function apiListarActivas(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($_SESSION['docente_id'])) {
            $this->json(['success' => false, 'error' => 'No autorizado']);
        }

        $docenteId = (int)$_SESSION['docente_id'];
        $sesionActiva = Sesion::obtenerActiva($docenteId);

        if (!$sesionActiva) {
            $this->json(['success' => true, 'activa' => false, 'asistencias' => []]);
        }

        $asistencias = Asistencia::listarPorSesion((int)$sesionActiva['id']);

        $this->json([
            'success'     => true,
            'activa'      => true,
            'sesion'      => [
                'id'            => $sesionActiva['id'],
                'codigo_sesion' => $sesionActiva['codigo_sesion'],
                'materia'       => $sesionActiva['materia']
            ],
            'asistencias' => $asistencias
        ]);
    }
}
