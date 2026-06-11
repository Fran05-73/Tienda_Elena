<?php
/**
 * VentaController — Tienda Doña Elena
 *
 * Punto de Venta (POS).
 * Implementa:
 *   - Validación de stock antes de procesar la venta
 *   - Transacciones PostgreSQL (BEGIN / COMMIT / ROLLBACK)
 *   - Descuento automático de stock en tabla producto
 *   - Respuesta JSON para el frontend del POS
 */
class VentasController extends Controller
{
    // ── GET /ventas/pos ───────────────────────────────────────────────────────

    public function pos(): void
    {
        Middleware::auth();

        /** @var Categoria $catModel */
        $catModel = $this->model('Categoria');

        $clientes = $catModel->fetchAll(
            "SELECT c.id_cliente,
                    CONCAT(p.nombre, ' ', p.apellido) AS nombre_completo
             FROM cliente c
             JOIN persona p ON c.id_persona = p.id_persona
             ORDER BY p.nombre"
        );

        $empleados = $catModel->fetchAll(
            "SELECT e.id_empleado,
                    CONCAT(p.nombre, ' ', p.apellido) AS nombre_completo
             FROM empleado e
             JOIN persona p ON e.id_persona = p.id_persona
             ORDER BY p.nombre"
        );

        $this->view('ventas/pos', compact('clientes', 'empleados'));
    }

    // ── POST /ventas/guardar (JSON) ───────────────────────────────────────────

    public function guardarVenta(): void
    {
        Middleware::auth();

        // Leer body JSON del POS
        $input = json_decode(file_get_contents('php://input'), true);

        if (!$input) {
            $this->json(['success' => false, 'message' => 'Datos de venta inválidos.'], 400);
            return;
        }

        $cliente_id = (int) ($input['cliente_id'] ?? 0);
        $empleado_id = (int) ($input['empleado_id'] ?? 0);
        $total = (float) ($input['total'] ?? 0);
        $detalles = $input['detalles'] ?? [];
        $pago = $input['pago'] ?? [];

        // ── Validaciones básicas ─────────────────────────────────────────────
        if ($cliente_id <= 0 || $empleado_id <= 0) {
            $this->json(['success' => false, 'message' => 'Cliente y empleado son obligatorios.'], 422);
            return;
        }

        if (empty($detalles)) {
            $this->json(['success' => false, 'message' => 'El carrito está vacío.'], 422);
            return;
        }

        if ($total <= 0) {
            $this->json(['success' => false, 'message' => 'El total debe ser mayor que 0.'], 422);
            return;
        }

        if (empty($pago['metodo'])) {
            $this->json(['success' => false, 'message' => 'Debes seleccionar un método de pago.'], 422);
            return;
        }

        // ── Validar stock desde inventario ────────────────────────────────────
        /** @var Producto $productoModel */
        $productoModel = $this->model('Producto');

        foreach ($detalles as $det) {
            $idProd = (int) $det['id_producto'];
            $cantidad = (int) $det['cantidad'];

            // Consultar producto con stock desde inventario
            $producto = $productoModel->fetch(
                "SELECT p.nombre, p.precio, i.stock_actual AS stock
             FROM producto p
             JOIN inventario i ON p.id_producto = i.id_producto
             WHERE p.id_producto = :id",
                ['id' => $idProd]
            );

            if (!$producto) {
                $this->json([
                    'success' => false,
                    'message' => "Producto con ID {$idProd} no encontrado.",
                ], 422);
                return;
            }

            if ((int) $producto['stock'] < $cantidad) {
                $this->json([
                    'success' => false,
                    'message' => "Stock insuficiente para «{$producto['nombre']}». "
                        . "Disponible: {$producto['stock']}, solicitado: {$cantidad}.",
                ], 422);
                return;
            }
        }

        $metodosValidos = ['efectivo', 'tarjeta', 'transferencia'];
        if (!in_array($pago['metodo'], $metodosValidos, true)) {
            $this->json(['success' => false, 'message' => 'Método de pago no válido.'], 422);
            return;
        }

        // Extraer datos adicionales del pago
        $datosAdicionales = $pago['datos_adicionales'] ?? [];

        // ── Procesar venta en transacción ─────────────────────────────────────
        /** @var Venta $ventaModel */
        $ventaModel = $this->model('Venta');

        try {
            $idVenta = $ventaModel->createVenta(
                [
                    'id_cliente' => $cliente_id,
                    'id_empleado' => $empleado_id,
                    'fecha' => date('Y-m-d H:i:s'),
                    'total' => $total,
                ],
                $detalles,
                [
                    'monto' => (float) ($pago['monto'] ?? $total),
                    'metodo' => $pago['metodo'],
                ],
                $datosAdicionales
            );

            $this->json(['success' => true, 'id_venta' => $idVenta]);
        } catch (\Exception $e) {
            $this->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function index(): void
    {
        Middleware::auth();

        /** @var Venta $ventaModel */
        $ventaModel = $this->model('Venta');
        $ventas = $ventaModel->obtenerTodasConDetalles();

        $this->view('ventas/index', compact('ventas'));
    }
}
