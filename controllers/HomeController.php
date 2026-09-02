<?php

require_once __DIR__ . '/BaseController.php';

// Controlador Home: Muestra la pantalla principal y la seccion informativa

class HomeController extends BaseController
{
    public function index(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $base = self::obtenerRutaBase();
        $this->vista('home.index', [
            'base' => $base
        ]);
    }

    public function institucional(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $base = self::obtenerRutaBase();
        $this->vista('home.institucional', [
            'base' => $base
        ]);
    }
}
