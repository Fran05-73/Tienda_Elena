<?php
/**
 * Front Controller — Tienda Doña Elena
 * Punto de entrada único. Usa autoload dinámico estilo h3_act3.
 */
require_once __DIR__ . '/../config/config.php';
// ── Mostrar errores (quitar en producción) ───────────────────────────────────
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// ── Iniciar sesión antes de cualquier salida ──────────────────────────────────
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

date_default_timezone_set('America/La_Paz');

// ── Autoload dinámico (basado en h3_act3) ────────────────────────────────────
spl_autoload_register(function (string $class): void {
    $paths = [
        __DIR__ . '/../config/',
        __DIR__ . '/../core/',
        __DIR__ . '/../controllers/',
        __DIR__ . '/../models/',
    ];

    foreach ($paths as $path) {
        $file = $path . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// ── Archivos que NO son clases (helpers, funciones globales) ─────────────────
require_once __DIR__ . '/../core/Helpers.php';
require_once __DIR__ . '/../functions/validacion.php';

// ── Iniciar enrutamiento automático ─────────────────────────────────────────
$router = new Router();
$router->dispatch();
