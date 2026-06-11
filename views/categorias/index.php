<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px">
    <h2 class="app-card-title mb-0">📂 Categorías</h2>
    <a href="/categorias/crear" class="btn-app-primary">+ Nueva Categoría</a>
</div>

<div class="app-card">
    <?php if (empty($categorias)): ?>
        <p class="text-muted py-3">No hay categorías registradas.</p>
    <?php else: ?>
    <table class="app-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Descripción</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($categorias as $cat): ?>
            <tr>
                <td><?= (int) $cat['id_categoria'] ?></td>
                <td><?= Helpers::e($cat['nombre']) ?></td>
                <td><?= Helpers::e($cat['descripcion'] ?? '—') ?></td>
                <td>
                    <span class="badge bg-<?= $cat['estado'] === 'activa' ? 'success' : 'secondary' ?>">
                        <?= Helpers::e(ucfirst($cat['estado'])) ?>
                    </span>
                </td>
                <td>
                    <a href="/categorias/editar/<?= (int) $cat['id_categoria'] ?>" class="btn-icon">✏ Editar</a>
                    <a href="/categorias/eliminar/<?= (int) $cat['id_categoria'] ?>"
                       class="btn-icon danger"
                       onclick="return confirm('¿Eliminar la categoría «<?= Helpers::e($cat['nombre']) ?>»?')">
                        🗑 Eliminar
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</div>