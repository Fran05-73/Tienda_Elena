<?php
/**
 * AuthController — Tienda Doña Elena
 *
 * Adaptado a la nueva relación usuarios → empleado → rol.
 */
class AuthController extends Controller
{
    public function loginForm(): void
    {
        Middleware::guest();
        require_once __DIR__ . '/../views/layout/header_auth.php';
        require_once __DIR__ . '/../views/auth/login.php';
        require_once __DIR__ . '/../views/layout/footer_auth.php';
    }

    public function login(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/login');
        }

        $username = limpiarDato(Helpers::post('username'));
        $password = Helpers::post('password');
        $recaptchaToken = Helpers::post('g-recaptcha-response');

        $errores = recogerErrores([
            validarTexto($username, 'Usuario', 1),
            validarTexto($password, 'Contraseña', 1),
            validarRecaptcha($recaptchaToken),
        ]);

        if (!empty($errores)) {
            Flash::errors($errores);
            $this->redirect('/login');
        }

        /** @var Usuario $usuarioModel */
        $usuarioModel = $this->model('Usuario');
        $user = $usuarioModel->findByUsername($username);

        if (!$user || !password_verify($password, $user['password_hash'])) {
            Flash::error('Usuario o contraseña incorrectos.');
            $this->redirect('/login');
        }

        // ── Obtener el rol a través del empleado ─────────────────────────
        $rol = $usuarioModel->getRoleByUserId((int)$user['id_usuario']);
        if ($rol === null) {
            Flash::error('El usuario no tiene un empleado asignado o rol definido.');
            $this->redirect('/login');
        }

        Session::set('usuario_id', $user['id_usuario']);
        Session::set('username',   $user['username']);
        Session::set('rol',        $rol);

        Flash::success('Bienvenido, ' . Helpers::e($user['username']) . '.');
        $this->redirect('/dashboard');
    }

    public function logout(): void
    {
        Session::destroy();
        $this->redirect('/login');
    }
}