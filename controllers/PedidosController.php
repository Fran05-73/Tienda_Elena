<?php
declare(strict_types=1);
require_once __DIR__ . '/../core/Controller.php';
/*require_once __DIR__ . '/../models/PedidoModel.php';*/

class PedidosController extends Controller
{
    private Pedido $modelo;

    public function __construct()
    {
        $this->modelo = new Pedido();
    }

    public function index(): void
    {
        $hoy = new DateTime();
        $anio = (int)$hoy->format('Y');
        $semana = (int)$hoy->format('W') + 1;

        $sugerencias = $this->modelo->obtenerSugerenciasPendientes($anio, $semana);
        $this->view('pedidos/sugeridos', compact('sugerencias', 'anio', 'semana'));
    }

    public function confirmarPedido(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['status' => 'error', 'message' => 'Método no permitido'], 405);
        }

        $input = json_decode(file_get_contents('php://input'), true);
        if (empty($input['items']) || !is_array($input['items'])) {
            $this->json(['status' => 'error', 'message' => 'Formato inválido'], 400);
        }

        try {
            $this->modelo->abastecerMultiple($input['items']);
            $this->json(['status' => 'success', 'message' => 'Inventario actualizado correctamente.']);
        } catch (\Exception $e) {
            $this->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}