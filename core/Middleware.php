<?php
/**
 * Middleware — Tienda Doña Elena
 *
 * Protección de rutas por autenticación y rol.
 * Usado directamente en los controladores.
 */
class Middleware
{
    /**
     * Exige sesión activa. Redirige a /login si no hay.
     */
    public static function auth(): void
    {
        if (!Session::isLoggedIn()) {
            Flash::error('Debes iniciar sesión para continuar.');
            Response::redirect('/login');
        }
    }

    /**
     * Exige rol específico. Redirige al dashboard si el rol no coincide.
     *
     * @param string|array $allowedRoles Rol o lista de roles permitidos.
     */
    public static function role(string|array $allowedRoles): void
    {
        self::auth();
        $allowedRoles = (array) $allowedRoles;
        $currentRole  = Session::userRole();

        if (!in_array($currentRole, $allowedRoles, true)) {
            Flash::error('No tienes permiso para acceder a esa sección.');
            Response::redirect('/dashboard');
        }
    }

    /**
     * Solo para invitados (no logueados). Redirige al dashboard si ya hay sesión.
     */
    public static function guest(): void
    {
        if (Session::isLoggedIn()) {
            Response::redirect('/dashboard');
        }
    }
}
