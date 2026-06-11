<div class="app-card">
    <div class="app-card-header">
        <span class="app-card-title">📋 Lista Completa de Predicciones — Semana <?= $semana ?> / <?= $anio ?></span>
        <a href="/dashboard" class="btn-app-secondary" style="font-size:.85rem">← Volver al Dashboard</a>
    </div>

    <?php if (empty($predicciones)): ?>
        <p class="text-muted py-3">No hay predicciones calculadas.</p>
    <?php else: ?>
        <table class="app-table">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Stock Actual</th>
                    <th style="text-align:right">Demanda Esperada (IA)</th>
                    <th style="text-align:center">Estado de Alerta</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($predicciones as $pred): ?>
                    <?php
                    $stock = (int) $pred['stock'];
                    $demanda = (float) $pred['cantidad_predicha'];
                    $alerta = ($stock < $demanda) ? 'danger' : 'success';
                    $textoAlerta = ($stock < $demanda) ? 'Abastecimiento Necesario' : 'Stock Seguro';
                    ?>
                    <tr>
                        <td><?= Helpers::e($pred['nombre']) ?></td>
                        <td><?= $stock ?></td>
                        <td style="text-align:right"><?= number_format($demanda, 2) ?></td>
                        <td style="text-align:center">
                            <span class="badge badge-<?= $alerta ?>"
                                style="font-size:0.8rem; padding:4px 10px; border-radius:12px; background:var(--<?= $alerta ?>); color:white;">
                                <?= $textoAlerta ?>
                            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>