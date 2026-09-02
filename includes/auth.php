<?php
session_start();

// Revisa que el docente este logueado, si no lo manda al login
function requiereLogin() {
    if (!isset($_SESSION['docente_id'])) {
        header('Location: login.php');
        exit;
    }
}
