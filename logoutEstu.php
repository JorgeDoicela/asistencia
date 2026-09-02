<?php
session_start();

// Desvincular únicamente las variables asociadas al estudiante
unset($_SESSION['estudiante_id']);
unset($_SESSION['estudiante_nombre']);

// Opcional: Si quieres destruir toda la sesión por completo
// session_destroy();

header('Location: login_estudiante.php');
exit;
?>