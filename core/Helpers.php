<?php
/**
 * Helpers — Tienda Doña Elena
 *
 * Funciones de utilidad global.
 * Este archivo NO es una clase; se carga con require_once en index.php.
 */
class Helpers
{
    /** Redirección HTTP. */
    public static function redirect(string $url): void
    {
        header("Location: {$url}");
        exit;
    }

    /**
     * Sanitiza una cadena: elimina espacios, tags HTML y escapa caracteres especiales.
     */
    public static function sanitize(string $data): string
    {
        return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
    }

    /** Obtiene y sanitiza un valor POST. */
    public static function post(string $key, mixed $default = ''): mixed
    {
        return $_POST[$key] ?? $default;
    }

    /** Obtiene un valor GET. */
    public static function get(string $key, mixed $default = ''): mixed
    {
        return $_GET[$key] ?? $default;
    }

    /**
     * Formatea un número como moneda boliviana.
     * Ejemplo: 1234.5 → "Bs. 1,234.50"
     */
    public static function formatCurrency(float $amount): string
    {
        return 'Bs. ' . number_format($amount, 2, '.', ',');
    }

    /**
     * Formatea una fecha a dd/mm/AAAA.
     */
    public static function dateFormat(?string $date): string
    {
        if (!$date) {
            return '—';
        }
        $ts = strtotime($date);
        return $ts ? date('d/m/Y', $ts) : $date;
    }

    /**
     * Escapa HTML para salida segura en vistas.
     */
    public static function e(mixed $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}
