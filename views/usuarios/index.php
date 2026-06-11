<div class="app-card-header" style="margin-bottom:16px">
    <span class="app-card-title">👥 Usuarios del Sistema</span>
    <a href="/usuarios/crear" class="btn-app-primary">
        + Nuevo Usuario
    </a>
</div>

<div class="app-card">
    <?php if (empty($usuarios)): ?>
        <p class="text-muted py-3">No hay usuarios registrados.</p>
    <?php else: ?>
    <table class="app-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Usuario</th>
                <th>Empleado</th>
                <th>Rol</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($usuarios as $u): ?>
            <tr>
                <td><?= (int) $u['id_usuario'] ?></td>
                <td><?= Helpers::e($u['username']) ?></td>
                <td><?= Helpers::e($u['empleado_nombre'] ?? '—') ?></td>
                <td>
                    <span class="badge bg-<?= $u['rol'] === 'Administrador' ? 'danger' : 'secondary' ?>">
                        <?= Helpers::e($u['rol'] ?? 'Sin rol') ?>
                    </span>
                </td>
                <td>
                    <a href="/usuarios/editar/<?= (int) $u['id_usuario'] ?>"
                       class="btn-icon">✏ Editar</a>
                    <?php if ((int) $u['id_usuario'] !== (int) Session::userId()): ?>
                    <a href="/usuarios/eliminar/<?= (int) $u['id_usuario'] ?>"
                       class="btn-icon danger"
                       onclick="return confirm('¿Eliminar al usuario «<?= Helpers::e($u['username']) ?>»?')">
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