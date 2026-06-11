<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px">
    <h2 class="app-card-title mb-0">👔 Empleados</h2>
    <a href="/empleados/crear" class="btn-app-primary">+ Nuevo Empleado</a>
</div>

<div class="app-card">
    <?php if (empty($empleados)): ?>
        <p class="text-muted py-3">No hay empleados registrados.</p>
    <?php else: ?>
    <table class="app-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>CI</th>
                <th>Cargo / Rol</th>
                <th>Salario (Bs.)</th>
                <th>Fecha Contrato</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($empleados as $emp): ?>
            <tr>
                <td><?= (int) $emp['id_empleado'] ?></td>
                <td><?= Helpers::e($emp['apellido'] . ' ' . $emp['nombre']) ?></td>
                <td><?= Helpers::e($emp['ci']) ?></td>
                <td><?= Helpers::e($emp['rol']) ?></td>
                <td><?= Helpers::formatCurrency((float) $emp['salario']) ?></td>
                <td><?= Helpers::dateFormat($emp['fecha_contratacion']) ?></td>
                <td>
                    <a href="/empleados/editar/<?= (int) $emp['id_empleado'] ?>" class="btn-icon">✏ Editar</a>
                    <a href="/empleados/eliminar/<?= (int) $emp['id_empleado'] ?>"
                       class="btn-icon danger"
                       onclick="return confirm('¿Eliminar al empleado «<?= Helpers::e($emp['nombre'] . ' ' . $emp['apellido']) ?>»?')">
                        🗑 Eliminar
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</div>