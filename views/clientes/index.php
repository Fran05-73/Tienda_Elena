<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px">
    <h2 class="app-card-title mb-0">👥 Clientes</h2>
    <a href="/clientes/crear" class="btn-app-primary">+ Nuevo Cliente</a>
</div>

<div class="app-card">
    <?php if (empty($clientes)): ?>
        <p class="text-muted py-3">No hay clientes registrados.</p>
    <?php else: ?>
        <table class="app-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>CI</th>
                    <th>Correo</th>
                    <th>Teléfono</th>
                    <th>Dirección</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                    <?php foreach ($clientes as $cli): ?>
                    <tr>
                        <td><?= (int) $cli['id_cliente'] ?></td>
                        <td><?= Helpers::e($cli['apellido'] . ' ' . $cli['nombre']) ?></td>
                        <td><?= Helpers::e($cli['ci']) ?></td>
                        <td><?= Helpers::e($cli['correo'] ?? '—') ?></td>
                        <td><?= Helpers::e($cli['telefono'] ?? '—') ?></td>
                        <td><?= Helpers::e($cli['direccion'] ?? '—') ?></td>
                        <td>
                            <a href="/clientes/editar/<?= (int) $cli['id_cliente'] ?>" class="btn-icon">✏ Editar</a>
                                    <?php if (Session::userRole() === 'Administrador'): ?>
                                <a href="/clientes/eliminar/<?= (int) $cli['id_cliente'] ?>" class="btn-icon danger"
                                    onclick="return confirm('¿Eliminar al cliente «<?= Helpers::e($cli['nombre'] . ' ' . $cli['apellido']) ?>»?')">
                                    🗑 Eliminar
                                </a>
                                    <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>