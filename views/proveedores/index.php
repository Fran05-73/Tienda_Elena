<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px">
    <h2 class="app-card-title mb-0">🚚 Proveedores</h2>
    <a href="/proveedores/crear" class="btn-app-primary">+ Nuevo Proveedor</a>
</div>

<div class="app-card">
    <?php if (empty($proveedores)): ?>
        <p class="text-muted py-3">No hay proveedores registrados.</p>
    <?php else: ?>
    <table class="app-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Persona</th>
                <th>CI</th>
                <th>Empresa</th>
                <th>NIT</th>
                <th>Cargo</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($proveedores as $prov): ?>
            <tr>
                <td><?= (int) $prov['id_proveedor'] ?></td>
                <td><?= Helpers::e($prov['persona_apellido'] . ' ' . $prov['persona_nombre']) ?></td>
                <td><?= Helpers::e($prov['ci']) ?></td>
                <td><?= Helpers::e($prov['empresa_nombre']) ?></td>
                <td><?= Helpers::e($prov['nit']) ?></td>
                <td><?= Helpers::e($prov['cargo'] ?? '—') ?></td>
                <td>
                    <span class="badge bg-<?= $prov['estado'] === 'activo' ? 'success' : 'secondary' ?>">
                        <?= Helpers::e(ucfirst($prov['estado'])) ?>
                    </span>
                </td>
                <td>
                    <a href="/proveedores/editar/<?= (int) $prov['id_proveedor'] ?>" class="btn-icon">✏ Editar</a>
                    <a href="/proveedores/eliminar/<?= (int) $prov['id_proveedor'] ?>"
                       class="btn-icon danger"
                       onclick="return confirm('¿Eliminar este proveedor?')">🗑 Eliminar</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</div>