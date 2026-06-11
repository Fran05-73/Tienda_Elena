<?php
/**
 * Controller — Tienda Doña Elena
 *
 * Clase base para todos los controladores.
 * Provee: view(), redirect(), json(), requireAuth(), requireRole().
 * Incluye layout completo (header + navbar + sidebar + footer).
 */
class Controller
{
    /**
     * Renderiza una vista dentro del layout principal.
     *
     * @param string $view   Ruta relativa dentro de views/, sin extensión.
     *                       Ejemplo: 'productos/index'
     * @param array  $data   Variables que se inyectan en la vista via extract().
     */
    protected function view(string $view, array $data = []): void
    {
        extract($data, EXTR_SKIP);

        $viewFile = __DIR__ . "/../views/{$view}.php";

        if (!file_exists($viewFile)) {
            http_response_code(500);
            die("Vista no encontrada: <strong>{$view}.php</strong>");
        }

        require_once __DIR__ . '/../views/layout/header.php';
        require_once __DIR__ . '/../views/layout/navbar.php';
        require_once __DIR__ . '/../views/layout/sidebar.php';
        require_once $viewFile;
        require_once __DIR__ . '/../views/layout/footer.php';
    }

    /**
     * Renderiza una vista SIN layout (útil para login, páginas de error).
     */
    protected function viewRaw(string $view, array $data = []): void
    {
        extract($data, EXTR_SKIP);

        $viewFile = __DIR__ . "/../views/{$view}.php";

        if (!file_exists($viewFile)) {
            http_response_code(500);
            die("Vista no encontrada: <strong>{$view}.php</strong>");
        }

        require_once $viewFile;
    }

    /**
     * Instancia un modelo por nombre de clase.
     */
    protected function model(string $model): object
    {
        $file = __DIR__ . "/../models/{$model}.php";
        if (file_exists($file)) {
            require_once $file;
        }
        return new $model();
    }

    /**
     * Redirección HTTP con exit().
     */
    protected function redirect(string $url): void
    {
        header("Location: {$url}");
        exit;
    }

    /**
     * Respuesta JSON y exit().
     */
    protected function json(array $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    // ── Protección de acceso ─────────────────────────────────────────────────

    /** Redirige a login si no hay sesión activa. */
    protected function requireAuth(): void
    {
        if (!Session::isLoggedIn()) {
            Flash::error('Debes iniciar sesión para acceder.');
            header('Location: /login');
            exit;
        }
    }

    /**
     * Verifica rol. Redirige al dashboard si el rol no está permitido.
     *
     * @param string|array $roles Rol o lista de roles permitidos.
     */
    protected function requireRole(string|array $roles): void
    {
        $this->requireAuth();
        $roles   = (array) $roles;
        $current = Session::userRole();

        if (!in_array($current, $roles, true)) {
            Flash::error('No tienes permisos para acceder a esa sección.');
            $this->redirect('/dashboard');
        }
    }
}
