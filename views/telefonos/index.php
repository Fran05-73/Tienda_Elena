<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px">
    <h2 class="app-card-title mb-0">📞 Teléfonos</h2>
    <a href="/telefonos/crear" class="btn-app-primary">+ Nuevo Teléfono</a>
</div>

<div class="app-card">
    <?php if (empty($telefonos)): ?>
        <p class="text-muted py-3">No hay teléfonos registrados.</p>
    <?php else: ?>
    <table class="app-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Número</th>
                <th>Tipo</th>
                <th>Propietario</th>
                <th>ID / NIT</th>
                <th>Origen</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($telefonos as $tel): ?>
            <tr>
                <td><?= (int) $tel['id'] ?></td>
                <td><?= Helpers::e($tel['numero']) ?></td>
                <td><?= Helpers::e($tel['tipo'] ?? '—') ?></td>
                <td><?= Helpers::e($tel['propietario']) ?></td>
                <td><?= Helpers::e($tel['identificador']) ?></td>
                <td>
                    <span class="badge bg-<?= $tel['origen'] === 'persona' ? 'info' : 'warning' ?>">
                        <?= $tel['origen'] === 'persona' ? 'Persona' : 'Empresa' ?>
                    </span>
                </td>
                <td>
                    <a href="/telefonos/editar/<?= (int) $tel['id'] ?>" class="btn-icon">✏ Editar</a>
                    <a href="/telefonos/eliminar/<?= (int) $tel['id'] ?>"
                       class="btn-icon danger"
                       onclick="return confirm('¿Eliminar este teléfono?')">🗑 Eliminar</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</div>