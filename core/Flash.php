<?php
/**
 * Flash — Tienda Doña Elena
 *
 * Manejo de mensajes de un solo uso almacenados en sesión.
 *
 * Uso en controlador:
 *   Flash::success('Producto guardado.');
 *   Flash::error('SKU ya existe.');
 *   Flash::errors(['Campo vacío', 'Precio inválido']);
 *
 * Uso en vista:
 *   Flash::render();
 */
class Flash
{
    private const KEY_MSG    = 'flash_mensaje';
    private const KEY_TYPE   = 'flash_tipo';
    private const KEY_ERRORS = 'flash_errores';

    // ── Escritura ────────────────────────────────────────────────────────────

    public static function success(string $mensaje): void
    {
        $_SESSION[self::KEY_MSG]  = $mensaje;
        $_SESSION[self::KEY_TYPE] = 'success';
    }

    public static function error(string $mensaje): void
    {
        $_SESSION[self::KEY_MSG]  = $mensaje;
        $_SESSION[self::KEY_TYPE] = 'danger';
    }

    public static function warning(string $mensaje): void
    {
        $_SESSION[self::KEY_MSG]  = $mensaje;
        $_SESSION[self::KEY_TYPE] = 'warning';
    }

    /** Almacena una lista de errores de validación. */
    public static function errors(array $errores): void
    {
        $_SESSION[self::KEY_ERRORS] = $errores;
    }

    // ── Lectura (consume y borra) ────────────────────────────────────────────

    public static function getMessage(): ?string
    {
        return $_SESSION[self::KEY_MSG] ?? null;
    }

    public static function getType(): string
    {
        return $_SESSION[self::KEY_TYPE] ?? 'info';
    }

    public static function getErrors(): array
    {
        return $_SESSION[self::KEY_ERRORS] ?? [];
    }

    public static function hasMessage(): bool
    {
        return isset($_SESSION[self::KEY_MSG]);
    }

    public static function hasErrors(): bool
    {
        return !empty($_SESSION[self::KEY_ERRORS]);
    }

    /** Elimina todos los mensajes flash de la sesión. */
    public static function clear(): void
    {
        unset($_SESSION[self::KEY_MSG], $_SESSION[self::KEY_TYPE], $_SESSION[self::KEY_ERRORS]);
    }

    // ── Renderizado HTML ─────────────────────────────────────────────────────

    /**
     * Imprime las alertas Bootstrap y las borra de sesión.
     * Llamar una vez en el layout (navbar o inicio del contenido).
     */
    public static function render(): void
    {
        if (self::hasErrors()) {
            $errores = self::getErrors();
            echo '<div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">';
            echo '<ul class="mb-0">';
            foreach ($errores as $e) {
                echo '<li>' . htmlspecialchars((string) $e) . '</li>';
            }
            echo '</ul>';
            echo '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
            echo '</div>';
            unset($_SESSION[self::KEY_ERRORS]);
        }

        if (self::hasMessage()) {
            $tipo = self::getType();
            $msg  = self::getMessage();
            echo '<div class="alert alert-' . htmlspecialchars($tipo) . ' alert-dismissible fade show mb-3" role="alert">';
            echo htmlspecialchars($msg);
            echo '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
            echo '</div>';
            unset($_SESSION[self::KEY_MSG], $_SESSION[self::KEY_TYPE]);
        }
    }
}
