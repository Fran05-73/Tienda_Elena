<?php
/**
 * Database — Tienda Doña Elena
 *
 * Singleton PDO para PostgreSQL.
 * Centraliza la conexión; un único objeto PDO por request.
 *
 * CONFIGURAR las constantes de conexión en config/database.php
 * o directamente aquí antes de desplegar.
 */
class Database
{
    private static ?PDO $instance = null;

    /** Devuelve la conexión PDO (la crea si no existe). */
    public static function getConnection(): PDO
    {
        if (self::$instance === null) {
            self::$instance = self::connect();
        }
        return self::$instance;
    }

    // ── Construcción interna ─────────────────────────────────────────────────
    private static function connect(): PDO
    {
        // Leer configuración del archivo dedicado
        $cfg = require __DIR__ . '/../config/database.php';

        $dsn = sprintf(
            'pgsql:host=%s;port=%s;dbname=%s',
            $cfg['host'],
            $cfg['port'],
            $cfg['dbname']
        );

        try {
            $pdo = new PDO($dsn, $cfg['user'], $cfg['password'], [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
            $pdo->exec("SET timezone TO 'America/La_Paz'");
            return $pdo;
        } catch (PDOException $e) {
            // En producción loguear el error; nunca mostrar credenciales.
            error_log('DB Connection error: ' . $e->getMessage());
            die('Error de conexión a la base de datos. Contacte al administrador.');
        }
        
    }

    // Evitar instanciación y clonación externos
    private function __construct() {}
    private function __clone() {}
}
