<div class="app-layout">
    <nav class="app-sidebar">
        <div class="sidebar-brand">
            <span class="brand-icon">🛒</span>
            <span class="brand-name">Tienda Elena</span>
        </div>
        <ul class="sidebar-nav">
            <?php
            $currentUri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
            ?>
            <!-- Menú Cajero -->
            <li class="nav-item <?= str_contains($_SERVER['REQUEST_URI'], '/dashboard') ? 'active' : '' ?>">
                <a class="nav-link" href="/dashboard">📊 Dashboard</a>
            </li>
            <li class="nav-item <?= $currentUri === 'ventas/pos' ? 'active' : '' ?>">
                <a class="nav-link" href="/ventas/pos">💰 Punto de Venta</a>
            </li>
            <li class="nav-item <?= str_contains($_SERVER['REQUEST_URI'], '/clientes') ? 'active' : '' ?>">
                <a class="nav-link" href="/clientes">👥 Clientes</a>
            </li>
            <li class="nav-item <?= str_contains($_SERVER['REQUEST_URI'], '/productos') ? 'active' : '' ?>">
                <a class="nav-link" href="/productos">📦 Productos</a>
            </li>
            <li class="nav-item <?= $currentUri === 'ventas' ? 'active' : '' ?>">
                <a class="nav-link" href="/ventas">🧾 Ventas</a>
            </li>
            <li class="nav-item <?= str_contains($_SERVER['REQUEST_URI'], '/facturas') ? 'active' : '' ?>">
                <a class="nav-link" href="/facturas">📄 Facturas</a>
            </li>

            <!-- Menú Administrador -->
            <?php if (Session::userRole() === 'Administrador'): ?>
                <li class="brand-name" style="margin: 12px 0 0; padding-left: 20px">Administración</li>
                <li class="nav-item <?= str_contains($_SERVER['REQUEST_URI'], '/inventario') ? 'active' : '' ?>">
                    <a class="nav-link" href="/inventario">📋 Inventario</a>
                </li>
                <li class="nav-item <?= str_contains($_SERVER['REQUEST_URI'], '/categorias') ? 'active' : '' ?>">
                    <a class="nav-link" href="/categorias">📂 Categorías</a>
                </li>
                <li class="nav-item <?= str_contains($_SERVER['REQUEST_URI'], '/subcategorias') ? 'active' : '' ?>">
                    <a class="nav-link" href="/subcategorias">📁 Subcategorías</a>
                </li>
                <li class="nav-item <?= str_contains($_SERVER['REQUEST_URI'], '/magnitudes') ? 'active' : '' ?>">
                    <a class="nav-link" href="/magnitudes">📏 Magnitudes</a>
                </li>
                <li class="nav-item <?= str_contains($_SERVER['REQUEST_URI'], '/proveedores') ? 'active' : '' ?>">
                    <a class="nav-link" href="/proveedores">🚚 Proveedores</a>
                </li>
                <li class="nav-item <?= str_contains($_SERVER['REQUEST_URI'], '/empleados') ? 'active' : '' ?>">
                    <a class="nav-link" href="/empleados">👔 Empleados</a>
                </li>
                <li class="nav-item <?= str_contains($_SERVER['REQUEST_URI'], '/usuarios') ? 'active' : '' ?>">
                    <a class="nav-link" href="/usuarios">👥 Usuarios</a>
                </li>
                <li class="nav-item <?= str_contains($_SERVER['REQUEST_URI'], '/roles') ? 'active' : '' ?>">
                    <a class="nav-link" href="/roles">🔐 Roles</a>
                </li>
                <li class="nav-item <?= str_contains($_SERVER['REQUEST_URI'], '/empresas') ? 'active' : '' ?>">
                    <a class="nav-link" href="/empresas">🏢 Empresas</a>
                </li>
                <li class="nav-item <?= str_contains($_SERVER['REQUEST_URI'], '/auditoria') ? 'active' : '' ?>">
                    <a class="nav-link" href="/auditoria">📋 Auditoría</a>
                </li>
                <li class="nav-item <?= str_contains($_SERVER['REQUEST_URI'], '/pedidos') ? 'active' : '' ?>">
                    <a class="nav-link" href="/pedidos">🛒 Pedidos Sugeridos</a>
                </li>
                <li class="nav-item <?= str_contains($_SERVER['REQUEST_URI'], '/emails') ? 'active' : '' ?>">
                    <a class="nav-link" href="/emails">✉️ Correos Electrónicos</a>
                </li>
                <li class="nav-item <?= str_contains($_SERVER['REQUEST_URI'], '/telefonos') ? 'active' : '' ?>">
                    <a class="nav-link" href="/telefonos">📞 Teléfonos</a>
                </li>
            <?php endif; ?>
        </ul>
    </nav>
    <div class="app-content">