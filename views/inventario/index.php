<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px">
    <h2 class="app-card-title mb-0">📋 Inventario</h2>
    <div style="display:flex;gap:10px">
        <a href="/inventario/movimiento" class="btn-app-primary">+ Registrar Movimiento</a>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px">

    <!-- Bajo Stock -->
    <div class="app-card">
        <div class="app-card-header">
            <span class="app-card-title">⚠ Bajo Stock</span>
            <span class="badge bg-warning text-dark"><?= count($bajoStock) ?> productos</span>
        </div>

        <?php if (empty($bajoStock)): ?>
            <p class="text-muted py-2">Sin productos con bajo stock.</p>
        <?php else: ?>
        <table class="app-table">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>SKU</th>
                    <th style="text-align:right">Stock Actual</th>
                    <th style="text-align:right">Stock Mín.</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($bajoStock as $p): ?>
                <tr>
                    <td><?= Helpers::e($p['nombre']) ?></td>
                    <td><span class="sku-code"><?= Helpers::e($p['sku'] ?? '—') ?></span></td>
                    <td style="text-align:right;font-weight:700;color:var(--danger)">
                        <?= (int) $p['stock_actual'] ?>
                    </td>
                    <td style="text-align:right"><?= (int) ($p['stock_minimo'] ?? 5) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>

    <!-- Vencidos -->
    <div class="app-card">
        <div class="app-card-header">
            <span class="app-card-title">⛔ Vencidos</span>
            <span class="badge bg-danger"><?= count($expirados) ?> productos</span>
        </div>

        <?php if (empty($expirados)): ?>
            <p class="text-muted py-2">Sin productos vencidos.</p>
        <?php else: ?>
        <table class="app-table">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>SKU</th>
                    <th>Vencimiento</th>
                    <th style="text-align:right">Stock Actual</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($expirados as $p): ?>
                <tr>
                    <td><?= Helpers::e($p['nombre']) ?></td>
                    <td><span class="sku-code"><?= Helpers::e($p['sku'] ?? '—') ?></span></td>
                    <td style="color:var(--danger);font-weight:600">
                        <?= Helpers::dateFormat($p['fecha_vencimiento']) ?>
                    </td>
                    <td style="text-align:right;font-weight:700">
                        <?= (int) ($p['stock_actual'] ?? 0) ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>

</div>

<!-- Tabla completa con todos los campos de inventario -->
<div class="app-card" style="margin-top:20px">
    <div class="app-card-header">
        <span class="app-card-title">☰ Todos los Productos (<?= count($productos) ?>)</span>
        <input type="text" id="filtroInventario" class="form-input"
               style="width:240px" placeholder="Filtrar...">
    </div>
    <div style="overflow-x:auto">
        <table class="app-table" id="tablaInventario">
            <thead>
                <tr>
                    <th>SKU</th>
                    <th>Nombre</th>
                    <th>Categoría</th>
                    <th>Proveedor</th>
                    <th>Precio</th>
                    <th style="text-align:right">Stock Actual</th>
                    <th style="text-align:right">Stock Mín.</th>
                    <th style="text-align:right">Stock Máx.</th>
                    <th style="text-align:right">Punto Reorden</th>
                    <th>Ubicación</th>
                    <th>Última Actualización</th>
                    <th>Vencimiento</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($productos as $p): 
                    $stockActual = (int) ($p['stock_actual'] ?? 0);
                    $stockMinimo = (int) ($p['stock_minimo'] ?? 5);
                    $bajoStockClass = $stockActual <= $stockMinimo ? 'text-danger fw-bold' : '';
                ?>
                <tr>
                    <td><span class="sku-code"><?= Helpers::e($p['sku']) ?></span></td>
                    <td><?= Helpers::e($p['nombre']) ?></td>
                    <td><?= Helpers::e($p['subcategoria'] ?? '—') ?></td>
                    <td><?= Helpers::e($p['proveedor'] ?? '—') ?></td>
                    <td><?= Helpers::formatCurrency((float) $p['precio']) ?></td>
                    <td style="text-align:right;font-weight:700" class="<?= $bajoStockClass ?>">
                        <?= $stockActual ?>
                    </td>
                    <td style="text-align:right"><?= $stockMinimo ?></td>
                    <td style="text-align:right"><?= (int) ($p['stock_maximo'] ?? 100) ?></td>
                    <td style="text-align:right"><?= (int) ($p['punto_reorden'] ?? 10) ?></td>
                    <td><?= Helpers::e($p['ubicacion'] ?? '—') ?></td>
                    <td style="font-size:0.85rem">
                        <?= $p['ultima_actualizacion'] ? Helpers::dateFormat($p['ultima_actualizacion']) : '—' ?>
                    </td>
                    <td><?= $p['fecha_vencimiento'] ? Helpers::dateFormat($p['fecha_vencimiento']) : '—' ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
document.getElementById('filtroInventario').addEventListener('input', function () {
    const term = this.value.toLowerCase();
    document.querySelectorAll('#tablaInventario tbody tr').forEach(row => {
        row.style.display = row.textContent.toLowerCase().includes(term) ? '' : 'none';
    });
});
</script>