<?php
/**
 * InventarioController — Tienda Doña Elena
 *
 * Visualización y registro de movimientos de inventario.
 * Ahora usa la tabla inventario y la vista_stock_actual.
 */
class InventarioController extends Controller
{
    // ── GET /inventario ──────────────────────────────────────────────

    public function index(): void
    {
        Middleware::auth();

        /** @var Inventario $inventarioModel */
        $inventarioModel = $this->model('Inventario');

        $productos = $inventarioModel->allWithStock();
        $bajoStock = $inventarioModel->bajoStock(10);
        $expirados = $inventarioModel->expirados();

        $this->view('inventario/index', compact('productos', 'bajoStock', 'expirados'));
    }

    // ── GET /inventario/movimiento ───────────────────────────────────

    public function movimiento(): void
    {
        Middleware::auth();

        /** @var Inventario $inventarioModel */
        $inventarioModel = $this->model('Inventario');
        /** @var Producto $productoModel */
        $productoModel = $this->model('Producto');

        $movimientos = $inventarioModel->movimientos();
        $productos = $productoModel->all();

        // Obtener empleados para el selector
        $empleados = $this->model('Categoria')->fetchAll(
            "SELECT e.id_empleado,
                CONCAT(p.nombre, ' ', p.apellido) AS nombre_completo
         FROM empleado e
         JOIN persona p ON e.id_persona = p.id_persona
         ORDER BY p.nombre"
        );

        $this->view('inventario/movimiento', compact('movimientos', 'productos', 'empleados'));
    }

    // ── POST /inventario/registrar ───────────────────────────────────

    public function registrar(): void
{
    Middleware::auth();

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        $this->redirect('/inventario');
    }

    $id_producto = (int) Helpers::post('id_producto');
    $tipo        = limpiarDato(Helpers::post('tipo'));
    $cantidad    = (int) Helpers::post('cantidad');
    $id_empleado = (int) Helpers::post('id_empleado');  // nuevo campo

    // Validaciones
    $errores = [];

    if ($id_producto <= 0) {
        $errores[] = 'Debes seleccionar un producto.';
    }
    if (!in_array($tipo, ['entrada', 'salida'], true)) {
        $errores[] = 'Tipo de movimiento inválido.';
    }
    if ($cantidad <= 0) {
        $errores[] = 'La cantidad debe ser mayor que 0.';
    }
    if ($id_empleado <= 0) {
        $errores[] = 'Debes seleccionar el empleado responsable.';
    }

    if (!empty($errores)) {
        Flash::errors($errores);
        $this->redirect('/inventario/movimiento');
    }

    try {
        $this->model('Inventario')->registrarMovimiento(
            $id_producto,
            $tipo,
            $cantidad,
            '',           // observación
            $id_empleado  // aquí se pasa el empleado seleccionado
        );
        Flash::success('Movimiento registrado correctamente.');
    } catch (\Exception $e) {
        Flash::error('Error: ' . $e->getMessage());
    }

    $this->redirect('/inventario');
}
}