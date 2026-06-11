<div class="app-card">
    <div class="app-card-header">
        <span class="app-card-title">🛒 Sugerencias de Compra Inteligente (IA)</span>
        <span class="text-muted" style="font-size:0.9rem;">Semana objetivo: <?= $semana ?> / <?= $anio ?></span>
    </div>

    <?php if (empty($sugerencias)): ?>
        <p class="text-muted py-3">No hay sugerencias pendientes para esta semana.</p>
    <?php else: ?>
    <table class="app-table" id="tabla-sugeridos">
        <thead>
            <tr>
                <th>Producto</th>
                <th>Stock Actual</th>
                <th>Demanda Predicha</th>
                <th>Cantidad a Pedir</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($sugerencias as $item): ?>
                <tr data-id="<?= $item['id_pedido'] ?>"
                    data-producto="<?= $item['id_producto'] ?>">
                    <td><?= Helpers::e($item['nombre']) ?></td>
                    <td><?= (int)$item['stock_actual'] ?></td>
                    <td><?= number_format((float)$item['cantidad_predicha'], 2) ?></td>
                    <td>
                        <input type="number"
                               class="form-input input-sugerido"
                               value="<?= (int)$item['cantidad_sugerida'] ?>"
                               min="0" step="1" style="width:100px;">
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="form-actions" style="margin-top: 1rem;">
        <button id="btn-confirmar" class="btn-app-primary">Confirmar y Abastecer Inventario</button>
    </div>
    <?php endif; ?>
</div>