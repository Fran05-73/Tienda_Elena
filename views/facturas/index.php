<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px">
    <h2 class="app-card-title mb-0">📄 Facturas Emitidas</h2>
    <!-- Sin botón de nuevo (solo se generan automáticamente) -->
</div>

<div class="app-card">
    <?php if (empty($facturas)): ?>
        <p class="text-muted py-3">No hay facturas registradas.</p>
    <?php else: ?>
    <table class="app-table">
        <thead>
            <tr>
                <th>Factura #</th>
                <th>Nro. Autorización</th>
                <th>Venta #</th>
                <th>Cliente</th>
                <th>Fecha Emisión</th>
                <th style="text-align:right">Total Venta</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($facturas as $f): ?>
            <tr>
                <td><?= (int) $f['nro_factura'] ?></td>
                <td><?= Helpers::e($f['nro_autorizacion']) ?></td>
                <td><?= (int) $f['id_venta'] ?></td>
                <td><?= Helpers::e($f['razon_social']) ?> (NIT: <?= Helpers::e($f['nit_cliente']) ?>)</td>
                <td><?= Helpers::dateFormat($f['fecha_emision']) ?></td>
                <td style="text-align:right"><?= Helpers::formatCurrency((float) $f['total']) ?></td>
                <td>
                    <button class="btn-icon" disabled title="Generar PDF (próximamente)">📥 PDF</button>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</div>