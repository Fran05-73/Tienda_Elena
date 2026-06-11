<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px">
    <h2 class="app-card-title mb-0">🏢 Empresas</h2>
    <a href="/empresas/crear" class="btn-app-primary">+ Nueva Empresa</a>
</div>

<div class="app-card">
    <?php if (empty($empresas)): ?>
        <p class="text-muted py-3">No hay empresas registradas.</p>
    <?php else: ?>
    <table class="app-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>NIT</th>
                <th>Dirección</th>
                <th>Teléfono</th>
                <th>Email</th>
                <th>Fecha Registro</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($empresas as $emp): ?>
            <tr>
                <td><?= (int) $emp['id_empresa'] ?></td>
                <td><?= Helpers::e($emp['nombre']) ?></td>
                <td><?= Helpers::e($emp['nit']) ?></td>
                <td><?= Helpers::e($emp['direccion'] ?? '—') ?></td>
                <td><?= Helpers::e($emp['telefono_principal'] ?? '—') ?></td>
                <td><?= Helpers::e($emp['email_principal'] ?? '—') ?></td>
                <td><?= Helpers::e($emp['fecha_registro'] ?? '—') ?></td>
                <td>
                    <a href="/empresas/editar/<?= (int) $emp['id_empresa'] ?>" class="btn-icon">✏ Editar</a>
                    <a href="/empresas/eliminar/<?= (int) $emp['id_empresa'] ?>"
                       class="btn-icon danger"
                       onclick="return confirm('¿Eliminar esta empresa?')">🗑 Eliminar</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</div>