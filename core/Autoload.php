<?php
/**
 * Autoload — Tienda Doña Elena
 *
 * Clase contenedora del autoloader. Permite registrar el SPL autoload
 * desde cualquier punto del código de manera explícita y testeable.
 *
 * En producción es suficiente con el spl_autoload_register de index.php;
 * esta clase existe para cumplir la estructura pedida y para uso en tests.
 */
class Autoload
{
    private static array $basePaths = [];

    /**
     * Registra el autoloader con los directorios base dados.
     *
     * @param array $basePaths Rutas absolutas donde buscar clases.
     */
    public static function register(array $basePaths): void
    {
        self::$basePaths = $basePaths;

        spl_autoload_register(function (string $class): void {
            foreach (self::$basePaths as $path) {
                $file = rtrim($path, '/') . '/' . $class . '.php';
                if (file_exists($file)) {
                    require_once $file;
                    return;
                }
            }
        });
    }

    /**
     * Devuelve los directorios registrados actualmente (útil para tests).
     */
    public static function getPaths(): array
    {
        return self::$basePaths;
    }
}
