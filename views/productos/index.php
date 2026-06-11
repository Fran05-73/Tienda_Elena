<!-- ── Encabezado con botón para nuevo producto ──────────────────── -->
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px">
    <h2 class="app-card-title mb-0">📦 Productos</h2>
    <?php if (Session::userRole() === 'Administrador'): ?>
        <a href="/productos/crear" class="btn-app-primary">+ Nuevo Producto</a>
    <?php endif; ?>
</div>

<!-- ── Tabla Productos Existentes ──────────────────────────────────── -->
<div class="app-card">
    <div class="app-card-header">
        <span class="app-card-title">☰ Productos Existentes (<?= count($productos) ?>)</span>
        <input type="text" id="buscadorProducto" class="form-input" style="width:240px"
            placeholder="Filtrar en tabla...">
    </div>

    <?php if (empty($productos)): ?>
        <p class="text-muted py-3">No hay productos registrados aún.</p>
    <?php else: ?>
        <table class="app-table" id="tablaProductos">
            <thead>
                <tr>
                    <th>#</th>
                    <th>SKU</th>
                    <th>Nombre</th>
                    <th>Categoría</th>
                    <th>Proveedor</th>
                    <th>Precio (Bs.)</th>
                    <th>Stock Actual</th>
                    <th>Vencimiento</th>
                    <?php if (Session::userRole() === 'Administrador'): ?>
                        <th>Acciones</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($productos as $i => $p): ?>
                    <?php $stock = (int) ($p['stock_actual'] ?? 0); ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td><span class="sku-code"><?= Helpers::e($p['sku']) ?></span></td>
                        <td><?= Helpers::e($p['nombre']) ?></td>
                        <td><?= Helpers::e($p['subcategoria'] ?? '—') ?></td>
                        <td><?= Helpers::e($p['proveedor'] ?? '—') ?></td>
                        <td><?= Helpers::formatCurrency((float) $p['precio']) ?></td>
                        <td class="<?= $stock <= ($p['stock_minimo'] ?? 5) ? 'text-danger fw-bold' : '' ?>">
                            <?= $stock ?>
                        </td>
                        <td>
                            <?php if ($p['fecha_vencimiento']): ?>
                                <?php $vencido = strtotime($p['fecha_vencimiento']) < time(); ?>
                                <span class="<?= $vencido ? 'text-danger fw-bold' : '' ?>">
                                    <?= Helpers::dateFormat($p['fecha_vencimiento']) ?>
                                </span>
                            <?php else: ?>
                                —
                            <?php endif; ?>
                        </td>

                        <?php if (Session::userRole() === 'Administrador'): ?>
                            <td>
                                <a href="/productos/editar/<?= (int) $p['id_producto'] ?>" class="btn-icon">✏ Editar</a>
                                <a href="/productos/eliminar/<?= (int) $p['id_producto'] ?>" class="btn-icon danger"
                                    onclick="return confirm('¿Eliminar «<?= Helpers::e($p['nombre']) ?>»?')">
                                    🗑 Eliminar
                                </a>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<script>
    document.getElementById('buscadorProducto').addEventListener('input', function () {
        const term = this.value.toLowerCase();
        document.querySelectorAll('#tablaProductos tbody tr').forEach(row => {
            row.style.display = row.textContent.toLowerCase().includes(term) ? '' : 'none';
        });
    });
</script>