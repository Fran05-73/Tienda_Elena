<?php
class DashboardController extends Controller
{
    public function index(): void
    {
        Middleware::auth();

        $productoModel = $this->model('Producto');
        $ventaModel = $this->model('Venta');
        $inventarioModel = $this->model('Inventario');

        $totalProductos = count($productoModel->all());
        $bajoStock = $inventarioModel->bajoStock(10);
        $expirados = $productoModel->expirados();
        $ventasRecientes = $ventaModel->ventasRecientes(5);

        $rowHoy = $ventaModel->fetch(
            "SELECT COALESCE(SUM(total), 0) AS total FROM venta WHERE fecha::date = CURRENT_DATE"
        );
        $totalVentasHoy = (float) ($rowHoy['total'] ?? 0);

        // ── Predicciones ──────────────────────────────────────
        $prediccionModel = $this->model('Prediccion');
        $hoy = new DateTime();
        $anio = (int) $hoy->format('Y');
        $semana = (int) $hoy->format('W') + 1;

        $predicciones = $prediccionModel->obtenerPrediccionesSemanales($anio, $semana);

        // Ordenar por demanda descendente
        usort($predicciones, fn($a, $b) => $b['cantidad_predicha'] <=> $a['cantidad_predicha']);
        $prediccionesTop20 = array_slice($predicciones, 0, 20);

        $this->view('dashboard/index', compact(
            'totalProductos',
            'bajoStock',
            'expirados',
            'totalVentasHoy',
            'ventasRecientes',
            'predicciones',
            'prediccionesTop20',
            'anio',
            'semana'
        ));
    }

    // GET /dashboard/lista
    public function lista(): void
    {
        Middleware::auth();

        $prediccionModel = $this->model('Prediccion');
        $hoy = new DateTime();
        $anio = (int) $hoy->format('Y');
        $semana = (int) $hoy->format('W') + 1;

        $finAnio = new DateTime($anio . '-12-31');
        if ($semana > (int) $finAnio->format('W')) {
            $anio++;
            $semana = 1;
        }

        $predicciones = $prediccionModel->obtenerPrediccionesSemanales($anio, $semana);
        usort($predicciones, fn($a, $b) => $b['cantidad_predicha'] <=> $a['cantidad_predicha']);

        $this->view('dashboard/lista', compact('predicciones', 'semana', 'anio'));
    }

    // GET /dashboard/grafico
    public function grafico(): void
    {
        Middleware::auth();

        $prediccionModel = $this->model('Prediccion');
        $hoy = new DateTime();
        $anio = (int) $hoy->format('Y');
        $semana = (int) $hoy->format('W') + 1;

        $finAnio = new DateTime($anio . '-12-31');
        if ($semana > (int) $finAnio->format('W')) {
            $anio++;
            $semana = 1;
        }

        $predicciones = $prediccionModel->obtenerPrediccionesSemanales($anio, $semana);
        usort($predicciones, fn($a, $b) => $b['cantidad_predicha'] <=> $a['cantidad_predicha']);

        $this->view('dashboard/grafico', compact('predicciones', 'semana', 'anio'));
    }
}