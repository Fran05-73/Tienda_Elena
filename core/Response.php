<?php
/**
 * Response — Tienda Doña Elena
 *
 * Helpers estáticos para respuestas HTTP.
 * Centraliza redirecciones, JSON y páginas de error.
 */
class Response
{
    /** Redirección HTTP 302. */
    public static function redirect(string $url): void
    {
        header("Location: {$url}");
        exit;
    }

    /** Respuesta JSON. */
    public static function json(array $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    /** Respuesta JSON de éxito. */
    public static function success(string $message, array $data = [], int $status = 200): void
    {
        self::json(['success' => true, 'message' => $message, 'data' => $data], $status);
    }

    /** Respuesta JSON de error. */
    public static function error(string $message, int $status = 400): void
    {
        self::json(['success' => false, 'message' => $message], $status);
    }

    /** Página de error genérica. */
    public static function abort(int $code, string $message = ''): void
    {
        http_response_code($code);
        $msg = $message ?: "Error {$code}";
        echo "<h1>{$code}</h1><p>" . htmlspecialchars($msg) . "</p>";
        exit;
    }
}
