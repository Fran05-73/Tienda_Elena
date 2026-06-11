<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px">
    <h2 class="app-card-title mb-0">📏 Magnitudes</h2>
    <a href="/magnitudes/crear" class="btn-app-primary">+ Nueva Magnitud</a>
</div>

<div class="app-card">
    <?php if (empty($magnitudes)): ?>
        <p class="text-muted py-3">No hay magnitudes registradas.</p>
    <?php else: ?>
    <table class="app-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Abreviatura</th>
                <th>Tipo</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($magnitudes as $mag): ?>
            <tr>
                <td><?= (int) $mag['id_magnitud'] ?></td>
                <td><?= Helpers::e($mag['nombre']) ?></td>
                <td><?= Helpers::e($mag['abreviatura']) ?></td>
                <td><?= Helpers::e($mag['tipo'] ?? '—') ?></td>
                <td>
                    <a href="/magnitudes/editar/<?= (int) $mag['id_magnitud'] ?>" class="btn-icon">✏ Editar</a>
                    <a href="/magnitudes/eliminar/<?= (int) $mag['id_magnitud'] ?>"
                       class="btn-icon danger"
                       onclick="return confirm('¿Eliminar esta magnitud?')">🗑 Eliminar</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</div>