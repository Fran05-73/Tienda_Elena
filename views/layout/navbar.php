<div class="app-wrapper">
    <header class="app-topbar">
        <div class="topbar-left">
            <button class="topbar-menu-btn" id="sidebarToggle">&#9776;</button>
            <span class="topbar-title" id="pageTitle">Panel</span>
        </div>
        <div class="topbar-right">
            <span class="topbar-date" id="topbarDate"></span>
            <div class="dropdown">
                <button class="topbar-user" data-bs-toggle="dropdown" aria-expanded="false">
                    👤 <?= Helpers::e(Session::username()) ?> ▾
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="/logout">🚪 Cerrar sesión</a></li>
                </ul>
            </div>
        </div>
    </header>

    <main class="app-main">
        <br>
        <br>
        <!-- ── Mensajes flash ─────────────────────────────────────────────── -->
        <div class="flash-container">
            <?php Flash::render(); ?>
        </div>