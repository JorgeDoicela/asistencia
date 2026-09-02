<?php

require_once __DIR__ . '/BaseController.php';
require_once dirname(__DIR__) . '/models/Estudiante.php';
require_once dirname(__DIR__) . '/models/Asistencia.php';

// Controlador de Estudiantes: Administra el CRUD de alumnos y el portal del estudiante

class EstudianteController extends BaseController
{
    private function verificarDocente(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (empty($_SESSION['docente_id'])) {
            $this->redireccionar('/login');
        }
    }

    // Lista de estudiantes con buscador
    public function index(): void
    {
        $this->verificarDocente();

        $busqueda = trim($_GET['buscar'] ?? '');
        $estudiantes = Estudiante::listar($busqueda);
        $total = Estudiante::contar();

        $mensaje = $_SESSION['flash_mensaje'] ?? null;
        $error   = $_SESSION['flash_error'] ?? null;
        unset($_SESSION['flash_mensaje'], $_SESSION['flash_error']);

        $this->vista('estudiantes.index', [
            'base'        => self::obtenerRutaBase(),
            'estudiantes' => $estudiantes,
            'busqueda'    => $busqueda,
            'total'       => $total,
            'mensaje'     => $mensaje,
            'error'       => $error
        ]);
    }

    // Registra un nuevo estudiante
    public function crear(): void
    {
        $this->verificarDocente();

        $codigo   = strtoupper(trim($_POST['codigo'] ?? ''));
        $nombre   = trim($_POST['nombre'] ?? '');
        $apellido = trim($_POST['apellido'] ?? '');
        $carrera  = trim($_POST['carrera'] ?? '');

        if (empty($codigo) || empty($nombre) || empty($apellido) || empty($carrera)) {
            $_SESSION['flash_error'] = 'Todos los campos son obligatorios.';
            $this->redireccionar('/estudiantes');
        }

        // Verificar si el codigo ya existe
        if (Estudiante::buscarPorCodigo($codigo)) {
            $_SESSION['flash_error'] = "El codigo {$codigo} ya esta registrado con otro alumno.";
            $this->redireccionar('/estudiantes');
        }

        if (Estudiante::crear($codigo, $nombre, $apellido, $carrera)) {
            $_SESSION['flash_mensaje'] = "Estudiante {$nombre} {$apellido} agregado con exito.";
        } else {
            $_SESSION['flash_error'] = 'No se pudo guardar al estudiante.';
        }

        $this->redireccionar('/estudiantes');
    }

    // Actualiza datos de un estudiante
    public function actualizar(): void
    {
        $this->verificarDocente();

        $id       = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);
        $codigo   = strtoupper(trim($_POST['codigo'] ?? ''));
        $nombre   = trim($_POST['nombre'] ?? '');
        $apellido = trim($_POST['apellido'] ?? '');
        $carrera  = trim($_POST['carrera'] ?? '');

        if (!$id || empty($codigo) || empty($nombre) || empty($apellido) || empty($carrera)) {
            $_SESSION['flash_error'] = 'Datos no validos para actualizar.';
            $this->redireccionar('/estudiantes');
        }

        // Verificar que el codigo no este repetido en otro alumno
        $existente = Estudiante::buscarPorCodigo($codigo);
        if ($existente && (int)$existente['id'] !== $id) {
            $_SESSION['flash_error'] = "El codigo {$codigo} ya le pertenece a otro estudiante.";
            $this->redireccionar('/estudiantes');
        }

        if (Estudiante::actualizar($id, $codigo, $nombre, $apellido, $carrera)) {
            $_SESSION['flash_mensaje'] = "Estudiante {$codigo} actualizado correctamente.";
        } else {
            $_SESSION['flash_error'] = 'Error al actualizar estudiante.';
        }

        $this->redireccionar('/estudiantes');
    }

    // Elimina a un estudiante
    public function eliminar(): void
    {
        $this->verificarDocente();

        $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);

        if ($id && Estudiante::eliminar($id)) {
            $_SESSION['flash_mensaje'] = 'Estudiante eliminado con exito.';
        } else {
            $_SESSION['flash_error'] = 'No se pudo eliminar al estudiante.';
        }

        $this->redireccionar('/estudiantes');
    }

    // Portal personal del estudiante para ver sus asistencias
    public function portal(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($_SESSION['estudiante_id'])) {
            $this->redireccionar('/login-estudiante');
        }

        $estudianteId = (int)$_SESSION['estudiante_id'];
        $asistencias = Asistencia::listarPorEstudiante($estudianteId);

        $this->vista('estudiantes.portal', [
            'base'        => self::obtenerRutaBase(),
            'estudiante'  => [
                'id'      => $estudianteId,
                'codigo'  => $_SESSION['estudiante_codigo'],
                'nombre'  => $_SESSION['estudiante_nombre'],
                'carrera' => $_SESSION['estudiante_carrera']
            ],
            'asistencias' => $asistencias
        ]);
    }
}
