<?php
/**
 * Router — Tienda Doña Elena
 *
 * Resolución automática de rutas al estilo h3_act3:
 *   /productos/editar/5  →  ProductoController->editar(5)
 *   /auth/login          →  AuthController->login()
 *   /                    →  AuthController->loginForm()
 *
 * Soporta: GET, POST, parámetros dinámicos, fallback 404,
 * eliminación automática de index.php en la URI.
 */
class Router
{
    public function dispatch(): void
    {
        $uri    = $_SERVER['REQUEST_URI'];
        $uri    = strtok($uri, '?');           // quitar query string
        $method = $_SERVER['REQUEST_METHOD'];

        // Eliminar el base path si la app está en un subdirectorio
        $scriptDir = dirname($_SERVER['SCRIPT_NAME']);
        if ($scriptDir !== '/' && strpos($uri, $scriptDir) === 0) {
            $uri = substr($uri, strlen($scriptDir));
        }

        $uri = trim($uri, '/');

        // Eliminar "index.php" del inicio si aparece
        if (str_starts_with(strtolower($uri), 'index.php')) {
            $uri = ltrim(substr($uri, strlen('index.php')), '/');
        }

        // ── Rutas con alias explícito ────────────────────────────────────────
        // Mapea URIs cortas que NO siguen el patrón /Controlador/accion
        // hacia el controlador y método correctos.
        $aliases = [
            ''        => ['Auth', 'loginForm'],
            'login'   => ['Auth', $method === 'POST' ? 'login' : 'loginForm'],
            'logout'  => ['Auth', 'logout'],
        ];

        if (array_key_exists($uri, $aliases)) {
            [$ctrl, $action] = $aliases[$uri];
            $this->call($ctrl, $action, [], $method);
            return;
        }

        $segments = explode('/', $uri);

        // Segmento 0 → controlador
        $controllerName = ucfirst(strtolower(array_shift($segments)));

        // Segmento 1 → acción (por defecto "index")
        $action = !empty($segments) ? array_shift($segments) : 'index';

        // Resto → parámetros
        $params = $segments;

        $this->call($controllerName, $action, $params, $method);
    }

    // ─────────────────────────────────────────────────────────────────────────
    private function call(
        string $controllerName,
        string $action,
        array  $params,
        string $method
    ): void {
        $className = $controllerName . 'Controller';
        $file      = __DIR__ . '/../controllers/' . $className . '.php';

        if (!file_exists($file)) {
            $this->notFound("Controlador '$controllerName' no encontrado.");
            return;
        }

        require_once $file;

        if (!class_exists($className)) {
            $this->notFound("Clase '$className' no definida.");
            return;
        }

        $controller = new $className();

        // Intentar método con prefijo HTTP primero: getIndex(), postGuardar()…
        $prefixedAction = strtolower($method) . ucfirst($action);
        if (method_exists($controller, $prefixedAction)) {
            call_user_func_array([$controller, $prefixedAction], $params);
            return;
        }

        // Fallback: método sin prefijo
        if (method_exists($controller, $action)) {
            call_user_func_array([$controller, $action], $params);
            return;
        }

        $this->notFound("Acción '$action' no encontrada en '$className'.");
    }

    // ─────────────────────────────────────────────────────────────────────────
    private function notFound(string $msg = '404 — Página no encontrada'): void
    {
        http_response_code(404);
        echo "<h1>404 — Página no encontrada</h1><p>" . htmlspecialchars($msg) . "</p>";
    }
}
