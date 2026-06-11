<div class="auth-wrapper">
    <div class="auth-card">

        <div class="auth-brand">
            <span class="auth-icon">🛒</span>
            <h1 class="auth-title">Tienda Doña Elena</h1>
            <p class="auth-subtitle">Sistema de Punto de Venta</p>
        </div>

        <!-- Mensajes flash en login -->
        <?php Flash::render(); ?>

        <form method="POST" action="/login" novalidate>

            <!-- Token CSRF básico -->
            <input type="hidden" name="csrf_token"
                value="<?= Helpers::e($_SESSION['csrf_token'] ??= bin2hex(random_bytes(32))) ?>">

            <div class="mb-3">
                <label class="form-label fw-semibold" for="username">Usuario</label>
                <input type="text" id="username" name="username" class="form-control" placeholder="Ingresa tu usuario"
                    autocomplete="username" required>
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold" for="password">Contraseña</label>
                <input type="password" id="password" name="password" class="form-control" placeholder="********"
                    autocomplete="current-password" required>
            </div>

            <div class="mb-3 d-flex justify-content-center">
                <div class="g-recaptcha" data-sitekey="6LfhUe8sAAAAAJEr-DfRzNDowxc8olZ94k9Jd_xI"></div>
            </div>

            <button type="submit" class="btn btn-primary w-100 btn-login">
                Ingresar al sistema
            </button>

        </form>

    </div>
</div>