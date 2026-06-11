<?php
/**
 * Session — Tienda Doña Elena
 *
 * Abstracción sobre $_SESSION.
 * Gestiona inicio de sesión, datos de usuario y verificación de rol.
 */
class Session
{
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        return $_SESSION[$key] ?? $default;
    }

    public static function has(string $key): bool
    {
        return isset($_SESSION[$key]);
    }

    public static function remove(string $key): void
    {
        unset($_SESSION[$key]);
    }

    public static function destroy(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(
                session_name(), '', time() - 42000,
                $p['path'], $p['domain'], $p['secure'], $p['httponly']
            );
        }
        session_destroy();
    }

    // ── Helpers de autenticación ─────────────────────────────────────────────

    public static function isLoggedIn(): bool
    {
        return self::get('usuario_id') !== null;
    }

    public static function userId(): ?int
    {
        $id = self::get('usuario_id');
        return $id !== null ? (int) $id : null;
    }

    public static function username(): string
    {
        return (string) self::get('username', 'Usuario');
    }

    public static function userRole(): ?string
    {
        return self::get('rol');
    }
}
