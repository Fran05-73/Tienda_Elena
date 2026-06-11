<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px">
    <h2 class="app-card-title mb-0">🔐 Roles del Sistema</h2>
    <a href="/roles/crear" class="btn-app-primary">+ Nuevo Rol</a>
</div>

<div class="app-card">
    <?php if (empty($roles)): ?>
        <p class="text-muted py-3">No hay roles registrados.</p>
    <?php else: ?>
    <table class="app-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($roles as $rol): ?>
            <tr>
                <td><?= (int) $rol['id_rol'] ?></td>
                <td><?= Helpers::e($rol['nombre']) ?></td>
                <td>
                    <a href="/roles/editar/<?= (int) $rol['id_rol'] ?>" class="btn-icon">✏ Editar</a>
                    <a href="/roles/eliminar/<?= (int) $rol['id_rol'] ?>"
                       class="btn-icon danger"
                       onclick="return confirm('¿Eliminar el rol «<?= Helpers::e($rol['nombre']) ?>»?')">
                        🗑 Eliminar
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</div>