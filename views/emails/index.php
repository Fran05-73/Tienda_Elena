<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px">
    <h2 class="app-card-title mb-0">✉️ Correos Electrónicos</h2>
    <a href="/emails/crear" class="btn-app-primary">+ Nuevo Correo</a>
</div>

<div class="app-card">
    <?php if (empty($emails)): ?>
        <p class="text-muted py-3">No hay correos registrados.</p>
    <?php else: ?>
    <table class="app-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Correo</th>
                <th>Tipo</th>
                <th>Propietario</th>
                <th>ID / NIT</th>
                <th>Origen</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($emails as $email): ?>
            <tr>
                <td><?= (int) $email['id'] ?></td>
                <td><?= Helpers::e($email['correo']) ?></td>
                <td><?= Helpers::e($email['tipo'] ?? '—') ?></td>
                <td><?= Helpers::e($email['propietario']) ?></td>
                <td><?= Helpers::e($email['identificador']) ?></td>
                <td>
                    <span class="badge bg-<?= $email['origen'] === 'persona' ? 'info' : 'warning' ?>">
                        <?= $email['origen'] === 'persona' ? 'Persona' : 'Empresa' ?>
                    </span>
                </td>
                <td>
                    <a href="/emails/editar/<?= (int) $email['id'] ?>" class="btn-icon">✏ Editar</a>
                    <a href="/emails/eliminar/<?= (int) $email['id'] ?>"
                       class="btn-icon danger"
                       onclick="return confirm('¿Eliminar este correo?')">🗑 Eliminar</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</div>