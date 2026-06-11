<!-- //? Informacion General -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-label">Total Productos</div>
        <div class="stat-number"><?= (int) $totalProductos ?></div>
    </div>

    <div class="stat-card warning">
        <div class="stat-label">Bajo Stock (&lt;10)</div>
        <div class="stat-number"><?= count($bajoStock) ?></div>
    </div>

    <div class="stat-card danger">
        <div class="stat-label">Productos Vencidos</div>
        <div class="stat-number"><?= count($expirados) ?></div>
    </div>

    <div class="stat-card success">
        <div class="stat-label">Ventas Hoy</div>
        <div class="stat-number"><?= Helpers::formatCurrency($totalVentasHoy) ?></div>
    </div>
</div>

<!-- //* Ventas recientes y botnes de acceso rápido -->

<div class="app-card">
    <div class="app-card-header">
        <span class="app-card-title">⏱ Últimas Ventas</span>
        <div>
            <a href="/ventas/pos" class="btn-app-primary" style="font-size:.85rem">+ Nueva Venta</a>
            <a href="/productos/" class="btn-app-primary" style="font-size:.85rem"> Productos </a>
            <a href="/clientes/" class="btn-app-primary" style="font-size:.85rem"> Clientes </a>
            <a href="/ventas/" class="btn-app-primary" style="font-size:.85rem"> Historial de Ventas </a>
        </div>
    </div>

    <?php if (empty($ventasRecientes)): ?>
        <p class="text-muted py-3">No hay ventas registradas hoy.</p>
    <?php else: ?>
        <table class="app-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Cliente</th>
                    <th>Fecha</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($ventasRecientes as $v): ?>
                    <tr>
                        <td><?= (int) $v['id_venta'] ?></td>
                        <td><?= Helpers::e($v['cliente']) ?></td>
                        <td><?= Helpers::dateFormat($v['fecha']) ?></td>
                        <td><?= Helpers::formatCurrency((float) $v['total']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<!-- //! Productos con bajo stock -->
<?php if (!empty($bajoStock)): ?>
    <div class="app-card">
        <div class="app-card-header">
            <span class="app-card-title">⚠ Productos con Bajo Stock</span>
        </div>
        <table class="app-table">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>SKU</th>
                    <th style="text-align:right">Stock Actual</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($bajoStock as $p): ?>
                    <tr>
                        <td><?= Helpers::e($p['nombre']) ?></td>
                        <td><span class="sku-code"><?= Helpers::e($p['sku'] ?? '—') ?></span></td>
                        <td style="text-align:right;font-weight:700;color:var(--danger)"><?= (int) $p['stock_actual'] ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<!-- ================================================================ -->
<!-- //todo INICIO BLOQUE IA — Predicción de Demanda (Próxima Semana)         -->
<!-- ================================================================ -->
<div class="app-card" id="cardPrediccion"
    data-predicciones="<?= htmlspecialchars(json_encode($prediccionesTop20), ENT_QUOTES, 'UTF-8') ?>">
    <div class="app-card-header">
        <span class="app-card-title">📊 Predicción de Demanda — Semana <?= $semana ?> / <?= $anio ?></span>
        <div class="tab-buttons">
            <button id="btnLista" class="btn-app-primary">Lista</button>
            <button id="btnGrafico" class="btn-app-secondary">Gráfico</button>
        </div>
    </div>

    <!-- Contenedor del gráfico (oculto inicialmente) -->
    <div id="contenedorGrafico" style="display:none;">
        <div style="max-height:500px; overflow-y:auto; position:relative;">
            <canvas id="graficoDemanda"
                style="width:100%; height:<?= max(400, count($prediccionesTop20) * 25) ?>px;"></canvas>
        </div>
        <?php if (count($predicciones) > 20): ?>
            <div style="text-align:center; padding: 10px 0; margin-top:8px;">
                <a href="/dashboard/grafico" class="btn-app-secondary" style="display:inline-block;">📊 Ver gráfico
                    completo</a>
            </div>
        <?php endif; ?>
    </div>

    <!-- Contenedor de la tabla (vista lista) -->
    <div id="tablaPredicciones" style="transition: none;">
        <?php if (empty($prediccionesTop20)): ?>
            <p class="text-muted py-3">No hay predicciones calculadas para la próxima semana.</p>
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
                    <?php foreach ($prediccionesTop20 as $pred): ?>
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

            <?php if (count($predicciones) > 20): ?>
                <div style="text-align:center; padding: 10px 0;">
                    <a href="/dashboard/lista" class="btn-app-secondary" style="display:inline-block;">📋 Ver lista completa</a>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<!-- ================================================================ -->
<!-- //todo FIN BLOQUE IA                                                     -->
<!-- ================================================================ -->