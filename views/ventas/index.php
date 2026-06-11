<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px">
    <h2 class="app-card-title mb-0">🧾 Historial de Ventas</h2>
</div>

<div class="app-card">
    <?php if (empty($ventas)): ?>
        <p class="text-muted py-3">No hay ventas registradas.</p>
    <?php else: ?>
    <table class="app-table">
        <thead>
            <tr>
                <th># Venta</th>
                <th>Cliente</th>
                <th>Empleado</th>
                <th>Fecha y Hora</th>
                <th style="text-align:right">Total</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($ventas as $v): ?>
            <tr>
                <td><?= (int) $v['id_venta'] ?></td>
                <td><?= Helpers::e($v['cliente']) ?></td>
                <td><?= Helpers::e($v['empleado']) ?></td>
                <td><?= Helpers::dateFormat($v['fecha']) ?></td>
                <td style="text-align:right"><?= Helpers::formatCurrency((float) $v['total']) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</div>