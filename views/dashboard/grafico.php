<div class="app-card">
    <div class="app-card-header">
        <span class="app-card-title">📊 Gráfico Completo — Semana <?= $semana ?> / <?= $anio ?></span>
        <a href="/dashboard" class="btn-app-secondary" style="font-size:.85rem">← Volver al Dashboard</a>
    </div>

    <div style="max-height:4000px; overflow-y:auto; position:relative;">
        <canvas id="graficoCompleto" style="width:100%; height:<?= max(400, count($predicciones) * 25) ?>px;"></canvas>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    const predicciones = <?= json_encode($predicciones) ?>;
    const canvas = document.getElementById("graficoCompleto");
    if (!canvas || predicciones.length === 0) return;

    const nombres = predicciones.map(p => p.nombre);
    const demandas = predicciones.map(p => parseFloat(p.cantidad_predicha));

    new Chart(canvas, {
        type: "bar",
        data: {
            labels: nombres,
            datasets: [{
                label: "Demanda Esperada",
                data: demandas,
                backgroundColor: "rgba(54, 162, 235, 0.6)",
                borderColor: "rgba(54, 162, 235, 1)",
                borderWidth: 1,
                barThickness: 8,
            }],
        },
        options: {
            indexAxis: "y",
            responsive: true,
            maintainAspectRatio: false,
            categoryPercentage: 0.9,
            barPercentage: 0.9,
            plugins: { legend: { display: false } },
            scales: { x: { beginAtZero: true, title: { display: true, text: "Unidades" } } },
        }
    });
});
</script>