<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px">
    <h2 class="app-card-title mb-0">📁 Subcategorías</h2>
    <a href="/subcategorias/crear" class="btn-app-primary">+ Nueva Subcategoría</a>
</div>

<div class="app-card">
    <?php if (empty($subcategorias)): ?>
        <p class="text-muted py-3">No hay subcategorías registradas.</p>
    <?php else: ?>
    <table class="app-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Categoría Padre</th>
                <th>Descripción</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($subcategorias as $sub): ?>
            <tr>
                <td><?= (int) $sub['id_subcategoria'] ?></td>
                <td><?= Helpers::e($sub['subcategoria']) ?></td>
                <td><?= Helpers::e($sub['categoria']) ?></td>
                <td><?= Helpers::e($sub['descripcion'] ?? '—') ?></td>
                <td>
                    <a href="/subcategorias/editar/<?= (int) $sub['id_subcategoria'] ?>" class="btn-icon">✏ Editar</a>
                    <a href="/subcategorias/eliminar/<?= (int) $sub['id_subcategoria'] ?>"
                       class="btn-icon danger"
                       onclick="return confirm('¿Eliminar la subcategoría «<?= Helpers::e($sub['subcategoria']) ?>»?')">
                        🗑 Eliminar
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</div>