<?php

require_once __DIR__ . '/BaseController.php';
require_once dirname(__DIR__) . '/models/Docente.php';
require_once dirname(__DIR__) . '/models/Estudiante.php';
require_once dirname(__DIR__) . '/models/Sesion.php';
require_once dirname(__DIR__) . '/models/Asistencia.php';

// Controlador Administrativo: Gobierna personal docente, métricas y supervisión en vivo

class AdminController extends BaseController
{
    // Panel de Control Principal del Administrador
    public function index(): void
    {
        $this->verificarAdmin();

        $totalDocentes        = Docente::contarActivos('docente');
        $totalAdmins          = Docente::contarActivos('admin');
        $totalEstudiantes     = Estudiante::contar();
        $totalSesiones        = Sesion::contarTotalInstitucional();
        $asistenciasHoy       = Asistencia::contarHoyGlobal();
        $asistenciasTotal     = Asistencia::contarTotalGlobal();
        $sesionesActivas      = Sesion::listarTodasActivas();
        $distribucionCarreras = Asistencia::contarPorCarrera();
        $historialReciente    = Sesion::listarRecientesGlobal(8);

        $mensaje = $_SESSION['flash_mensaje'] ?? null;
        $error   = $_SESSION['flash_error'] ?? null;
        unset($_SESSION['flash_mensaje'], $_SESSION['flash_error']);

        $this->vista('admin.index', [
            'base'                 => self::obtenerRutaBase(),
            'adminNombre'          => $_SESSION['usuario_nombre'] ?? 'Administrador',
            'totalDocentes'        => $totalDocentes,
            'totalAdmins'          => $totalAdmins,
            'totalEstudiantes'     => $totalEstudiantes,
            'totalSesiones'        => $totalSesiones,
            'asistenciasHoy'       => $asistenciasHoy,
            'asistenciasTotal'     => $asistenciasTotal,
            'sesionesActivas'      => $sesionesActivas,
            'distribucionCarreras' => $distribucionCarreras,
            'historialReciente'    => $historialReciente,
            'mensaje'              => $mensaje,
            'error'                => $error
        ]);
    }

    // Directorio y Gestión de Personal Docente y Usuarios
    public function docentes(): void
    {
        $this->verificarAdmin();

        $busqueda = trim($_GET['buscar'] ?? '');
        $rol      = trim($_GET['rol'] ?? '');

        $docentes = Docente::listar($busqueda, $rol);
        $total    = count($docentes);

        $mensaje = $_SESSION['flash_mensaje'] ?? null;
        $error   = $_SESSION['flash_error'] ?? null;
        unset($_SESSION['flash_mensaje'], $_SESSION['flash_error']);

        $this->vista('admin.docentes', [
            'base'           => self::obtenerRutaBase(),
            'docentes'       => $docentes,
            'busqueda'       => $busqueda,
            'rolFiltro'      => $rol,
            'total'          => $total,
            'usuarioActualId'=> (int)($_SESSION['usuario_id'] ?? 0),
            'mensaje'        => $mensaje,
            'error'          => $error
        ]);
    }

    // Registra un nuevo docente o administrador
    public function crearDocente(): void
    {
        $this->verificarAdmin();

        $nombre   = trim($_POST['nombre'] ?? '');
        $usuario  = trim($_POST['usuario'] ?? '');
        $password = $_POST['password'] ?? '';
        $rol      = trim($_POST['rol'] ?? 'docente');

        if (!in_array($rol, ['docente', 'admin'], true)) {
            $rol = 'docente';
        }

        if (empty($nombre) || empty($usuario) || empty($password)) {
            $_SESSION['flash_error'] = 'Todos los campos son obligatorios.';
            $this->redireccionar('/admin/docentes');
        }

        if (mb_strlen($nombre) < 3) {
            $_SESSION['flash_error'] = 'El nombre completo debe tener al menos 3 caracteres.';
            $this->redireccionar('/admin/docentes');
        }

        if (strlen($usuario) < 3 || !preg_match('/^[a-zA-Z0-9_.-]+$/', $usuario)) {
            $_SESSION['flash_error'] = 'El usuario debe tener al menos 3 caracteres alfanuméricos.';
            $this->redireccionar('/admin/docentes');
        }

        if (strlen($password) < 4) {
            $_SESSION['flash_error'] = 'La contraseña debe tener al menos 4 caracteres.';
            $this->redireccionar('/admin/docentes');
        }

        // Verificar unicidad de usuario
        if (Docente::buscarPorUsuario($usuario)) {
            $_SESSION['flash_error'] = "El nombre de usuario '{$usuario}' ya está en uso.";
            $this->redireccionar('/admin/docentes');
        }

        if (Docente::crear($nombre, $usuario, $password, $rol)) {
            $_SESSION['flash_mensaje'] = "Usuario '{$nombre}' registrado exitosamente con rol " . strtoupper($rol) . '.';
        } else {
            $_SESSION['flash_error'] = 'No se pudo registrar al usuario en la base de datos.';
        }

        $this->redireccionar('/admin/docentes');
    }

    // Actualiza datos y rol de un docente/usuario
    public function actualizarDocente(): void
    {
        $this->verificarAdmin();

        $id      = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);
        $nombre  = trim($_POST['nombre'] ?? '');
        $usuario = trim($_POST['usuario'] ?? '');
        $rol     = trim($_POST['rol'] ?? 'docente');
        $activo  = isset($_POST['activo']) ? (int)$_POST['activo'] : 1;

        if (!$id || empty($nombre) || empty($usuario)) {
            $_SESSION['flash_error'] = 'Datos incompletos para actualizar el usuario.';
            $this->redireccionar('/admin/docentes');
        }

        if (!in_array($rol, ['docente', 'admin'], true)) {
            $rol = 'docente';
        }

        // Evitar que el administrador actual se inactive o se quite el rol admin
        $adminId = (int)($_SESSION['usuario_id'] ?? 0);
        if ($id === $adminId && ($rol !== 'admin' || $activo === 0)) {
            $_SESSION['flash_error'] = 'No puedes revocar tus propios privilegios de administrador ni desactivar tu propia cuenta.';
            $this->redireccionar('/admin/docentes');
        }

        // Verificar si el usuario ya existe en otra cuenta
        $existente = Docente::buscarPorUsuario($usuario);
        if ($existente && (int)$existente['id'] !== $id) {
            $_SESSION['flash_error'] = "El nombre de usuario '{$usuario}' ya le pertenece a otra cuenta.";
            $this->redireccionar('/admin/docentes');
        }

        if (Docente::actualizar($id, $nombre, $usuario, $rol, $activo)) {
            $_SESSION['flash_mensaje'] = "Datos de '{$nombre}' actualizados correctamente.";
        } else {
            $_SESSION['flash_error'] = 'No se pudieron actualizar los datos del usuario.';
        }

        $this->redireccionar('/admin/docentes');
    }

    // Restablece la contraseña de un usuario
    public function resetearPassword(): void
    {
        $this->verificarAdmin();

        $id       = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);
        $password = $_POST['password'] ?? '';

        if (!$id || empty($password)) {
            $_SESSION['flash_error'] = 'La nueva contraseña no puede estar vacía.';
            $this->redireccionar('/admin/docentes');
        }

        if (strlen($password) < 4) {
            $_SESSION['flash_error'] = 'La nueva contraseña debe tener al menos 4 caracteres.';
            $this->redireccionar('/admin/docentes');
        }

        if (Docente::cambiarPassword($id, $password)) {
            $_SESSION['flash_mensaje'] = 'Contraseña restablecida exitosamente.';
        } else {
            $_SESSION['flash_error'] = 'Error al restablecer la contraseña.';
        }

        $this->redireccionar('/admin/docentes');
    }

    // Activa o desactiva a un usuario
    public function cambiarEstadoDocente(): void
    {
        $this->verificarAdmin();

        $id     = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);
        $activo = isset($_POST['activo']) ? (int)$_POST['activo'] : 1;

        $adminId = (int)($_SESSION['usuario_id'] ?? 0);
        if ($id === $adminId) {
            $_SESSION['flash_error'] = 'No puedes desactivar tu propia cuenta de administrador.';
            $this->redireccionar('/admin/docentes');
        }

        if ($id && Docente::cambiarEstado($id, $activo)) {
            $estadoTexto = $activo === 1 ? 'activada' : 'desactivada';
            $_SESSION['flash_mensaje'] = "Cuenta {$estadoTexto} correctamente.";
        } else {
            $_SESSION['flash_error'] = 'No se pudo cambiar el estado de la cuenta.';
        }

        $this->redireccionar('/admin/docentes');
    }

    // Elimina a un usuario si no tiene registros dependientes
    public function eliminarDocente(): void
    {
        $this->verificarAdmin();

        $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);

        $adminId = (int)($_SESSION['usuario_id'] ?? 0);
        if ($id === $adminId) {
            $_SESSION['flash_error'] = 'No puedes eliminar tu propia cuenta de administrador.';
            $this->redireccionar('/admin/docentes');
        }

        try {
            if ($id && Docente::eliminar($id)) {
                $_SESSION['flash_mensaje'] = 'Usuario eliminado de la base de datos.';
            } else {
                $_SESSION['flash_error'] = 'No se pudo eliminar al usuario.';
            }
        } catch (PDOException $e) {
            // Error de integridad referencial
            $_SESSION['flash_error'] = 'No se puede eliminar el docente porque tiene sesiones históricas o asistencias vinculadas. Recomendamos desactivar la cuenta en su lugar.';
        }

        $this->redireccionar('/admin/docentes');
    }

    // Finaliza forzosamente una sesión activa (supervisión institucional)
    public function cerrarSesionForzada(): void
    {
        $this->verificarAdmin();

        $sesionId = filter_var($_POST['sesion_id'] ?? null, FILTER_VALIDATE_INT);

        if ($sesionId && Sesion::cerrarPorAdmin($sesionId)) {
            $_SESSION['flash_mensaje'] = 'La sesión de clase ha sido finalizada por el Administrador.';
        } else {
            $_SESSION['flash_error'] = 'No se pudo finalizar la sesión seleccionada.';
        }

        $this->redireccionar('/admin');
    }
}
