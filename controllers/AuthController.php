<?php

require_once __DIR__ . '/BaseController.php';
require_once dirname(__DIR__) . '/models/Docente.php';
require_once dirname(__DIR__) . '/models/Estudiante.php';

// Controlador de Autenticacion: Controla el inicio y cierre de sesion

class AuthController extends BaseController
{
    // Muestra el formulario de login para docentes y administradores
    public function mostrarLoginDocente(): void
    {
        $this->iniciarSesion();

        // Si ya inició sesión, redirigir según su rol
        if (!empty($_SESSION['usuario_id']) || !empty($_SESSION['docente_id'])) {
            $rol = $_SESSION['usuario_rol'] ?? $_SESSION['docente_rol'] ?? 'docente';
            if ($rol === 'admin') {
                $this->redireccionar('/admin');
            } else {
                $this->redireccionar('/dashboard');
            }
        }

        $error = $_SESSION['flash_error'] ?? null;
        unset($_SESSION['flash_error']);

        $this->vista('auth.login-docente', [
            'base'  => self::obtenerRutaBase(),
            'error' => $error
        ]);
    }

    // Procesa los datos del formulario de login (docente o administrador)
    public function loginDocente(): void
    {
        $this->iniciarSesion();

        $usuario  = trim($_POST['usuario'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($usuario) || empty($password)) {
            $_SESSION['flash_error'] = 'Por favor complete todos los campos.';
            $this->redireccionar('/login');
        }

        // Consultar al modelo Docente
        $docente = Docente::buscarPorUsuario($usuario);

        // Verificar existencia y contraseña cifrada con password_verify
        if ($docente && password_verify($password, $docente['password'])) {
            // Verificar si el usuario está activo
            if (isset($docente['activo']) && (int)$docente['activo'] === 0) {
                $_SESSION['flash_error'] = 'Su cuenta institucional está inactiva. Contacte al Administrador.';
                $this->redireccionar('/login');
            }

            $rol = $docente['rol'] ?? 'docente';

            $_SESSION['usuario_id']      = (int)$docente['id'];
            $_SESSION['usuario_nombre']  = $docente['nombre'];
            $_SESSION['usuario_usuario'] = $docente['usuario'];
            $_SESSION['usuario_rol']     = $rol;

            // Variables de retrocompatibilidad
            $_SESSION['docente_id']      = (int)$docente['id'];
            $_SESSION['docente_nombre']  = $docente['nombre'];
            $_SESSION['docente_usuario'] = $docente['usuario'];
            $_SESSION['docente_rol']     = $rol;

            // Redirección inteligente según el rol institucional
            if ($rol === 'admin') {
                $this->redireccionar('/admin');
            } else {
                $this->redireccionar('/dashboard');
            }
        } else {
            $_SESSION['flash_error'] = 'Usuario o contraseña incorrectos.';
            $this->redireccionar('/login');
        }
    }

    // Cierra la sesión del docente o administrador
    public function logoutDocente(): void
    {
        $this->iniciarSesion();
        unset(
            $_SESSION['usuario_id'],
            $_SESSION['usuario_nombre'],
            $_SESSION['usuario_usuario'],
            $_SESSION['usuario_rol'],
            $_SESSION['docente_id'],
            $_SESSION['docente_nombre'],
            $_SESSION['docente_usuario'],
            $_SESSION['docente_rol']
        );
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
