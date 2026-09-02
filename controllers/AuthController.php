<?php

require_once __DIR__ . '/BaseController.php';
require_once dirname(__DIR__) . '/models/Docente.php';
require_once dirname(__DIR__) . '/models/Estudiante.php';

// Controlador de Autenticacion: Controla el inicio y cierre de sesion

class AuthController extends BaseController
{
    // Muestra el formulario de login para docentes
    public function mostrarLoginDocente(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Si ya inicio sesion como docente, llevarlo directamente al panel
        if (!empty($_SESSION['docente_id'])) {
            $this->redireccionar('/dashboard');
        }

        $error = $_SESSION['flash_error'] ?? null;
        unset($_SESSION['flash_error']);

        $this->vista('auth.login-docente', [
            'base'  => self::obtenerRutaBase(),
            'error' => $error
        ]);
    }

    // Procesa los datos del formulario de login de docente
    public function loginDocente(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $usuario  = trim($_POST['usuario'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($usuario) || empty($password)) {
            $_SESSION['flash_error'] = 'Por favor complete todos los campos.';
            $this->redireccionar('/login');
        }

        // Consultar al modelo Docente
        $docente = Docente::buscarPorUsuario($usuario);

        // Verificar la contrasena cifrada con password_verify
        if ($docente && password_verify($password, $docente['password'])) {
            $_SESSION['docente_id']     = $docente['id'];
            $_SESSION['docente_nombre'] = $docente['nombre'];
            $_SESSION['docente_usuario']= $docente['usuario'];
            $this->redireccionar('/dashboard');
        } else {
            $_SESSION['flash_error'] = 'Usuario o contrasena incorrectos.';
            $this->redireccionar('/login');
        }
    }

    // Cierra la sesion del docente
    public function logoutDocente(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        unset($_SESSION['docente_id'], $_SESSION['docente_nombre'], $_SESSION['docente_usuario']);
        session_destroy();
        $this->redireccionar('/login');
    }

    // Muestra el formulario de acceso para estudiantes
    public function mostrarLoginEstudiante(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $error = $_SESSION['flash_error'] ?? null;
        unset($_SESSION['flash_error']);

        $this->vista('auth.login-estudiante', [
            'base'  => self::obtenerRutaBase(),
            'error' => $error
        ]);
    }

    // Procesa el acceso del estudiante mediante su codigo
    public function loginEstudiante(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $codigo = strtoupper(trim($_POST['codigo'] ?? ''));

        if (empty($codigo)) {
            $_SESSION['flash_error'] = 'Por favor ingrese su codigo de estudiante.';
            $this->redireccionar('/login-estudiante');
        }

        // Consultar al modelo Estudiante
        $estudiante = Estudiante::buscarPorCodigo($codigo);

        if ($estudiante) {
            $_SESSION['estudiante_id']     = $estudiante['id'];
            $_SESSION['estudiante_codigo'] = $estudiante['codigo'];
            $_SESSION['estudiante_nombre'] = $estudiante['nombre'] . ' ' . $estudiante['apellido'];
            $_SESSION['estudiante_carrera']= $estudiante['carrera'];
            $this->redireccionar('/estudiante/portal');
        } else {
            $_SESSION['flash_error'] = "El codigo {$codigo} no esta registrado.";
            $this->redireccionar('/login-estudiante');
        }
    }

    // Cierra la sesion del estudiante
    public function logoutEstudiante(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        unset($_SESSION['estudiante_id'], $_SESSION['estudiante_codigo'], $_SESSION['estudiante_nombre'], $_SESSION['estudiante_carrera']);
        $this->redireccionar('/');
    }
}
