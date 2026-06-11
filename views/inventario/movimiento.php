<div class="app-card" style="max-width:560px">
    <div class="app-card-header">
        <span class="app-card-title">⟳ Registrar Movimiento de Inventario</span>
        <a href="/inventario" class="btn-app-secondary" style="font-size:.85rem">← Volver</a>
    </div>

    <form action="/inventario/registrar" method="POST" novalidate>

        <div class="form-group mb-3">
            <label class="form-label fw-semibold">
                Producto <span class="required">*</span>
            </label>
            <select name="id_producto" class="form-select" required>
                <option value="">Seleccionar producto...</option>
                <?php foreach ($productos as $p): ?>
                    <option value="<?= (int) $p['id_producto'] ?>">
                        <?= Helpers::e($p['nombre']) ?>
                        (stock actual: <?= (int) ($p['stock_actual'] ?? 0) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group mb-3">
            <label class="form-label fw-semibold">
                Tipo de Movimiento <span class="required">*</span>
            </label>
            <div style="display:flex;gap:20px;margin-top:6px">
                <label class="d-flex align-items-center gap-2" style="cursor:pointer">
                    <input type="radio" name="tipo" value="entrada" required> Entrada
                </label>
                <label class="d-flex align-items-center gap-2" style="cursor:pointer">
                    <input type="radio" name="tipo" value="salida"> Salida
                </label>
            </div>
        </div>

        <div class="form-group mb-3">
            <label class="form-label fw-semibold">
                Cantidad <span class="required">*</span>
            </label>
            <input type="number" name="cantidad" min="1" class="form-control" placeholder="Ej: 10" required>
        </div>

        <div class="form-group mb-3">
            <label class="form-label fw-semibold">
                Empleado Responsable <span class="required">*</span>
            </label>
            <select name="id_empleado" class="form-select" required>
                <option value="">Seleccionar empleado...</option>
                <?php foreach ($empleados as $e): ?>
                    <option value="<?= (int) $e['id_empleado'] ?>">
                        <?= Helpers::e($e['nombre_completo']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-actions">
            <a href="/inventario" class="btn-app-secondary">Cancelar</a>
            <button type="submit" class="btn-app-primary">Registrar</button>
        </div>

    </form>
</div>

<!-- Historial reciente con detalles ampliados -->
<div class="app-card" style="margin-top:24px">
    <div class="app-card-header">
        <span class="app-card-title">📅 Historial de Movimientos</span>
    </div>

    <?php if (empty($movimientos)): ?>
        <p class="text-muted py-2">Sin movimientos registrados.</p>
    <?php else: ?>
        <div style="overflow-x:auto">
            <table class="app-table">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Producto</th>
                        <th>SKU</th>
                        <th>Tipo</th>
                        <th>Cantidad</th>
                        <th>Stock Ant.</th>
                        <th>Stock Nuevo</th>
                        <th>Empleado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($movimientos as $m): ?>
                        <tr>
                            <td><?= Helpers::dateFormat($m['fecha']) ?></td>
                            <td><?= Helpers::e($m['producto']) ?></td>
                            <td><span class="sku-code"><?= Helpers::e($m['sku']) ?></span></td>
                            <td>
                                <span class="badge bg-<?= $m['tipo'] === 'entrada' ? 'success' : 'danger' ?>">
                                    <?= Helpers::e(ucfirst($m['tipo'])) ?>
                                </span>
                            </td>
                            <td style="font-weight:600"><?= (int) $m['cantidad'] ?></td>
                            <td><?= (int) $m['stock_anterior'] ?></td>
                            <td><?= (int) $m['stock_nuevo'] ?></td>
                            <td><?= Helpers::e($m['empleado'] ?? '—') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>