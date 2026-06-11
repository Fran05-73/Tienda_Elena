<?php
/**
 * ProductoController — Tienda Doña Elena
 *
 * CRUD completo de productos.
 * El stock inicial se inserta en inventario al crear el producto.
 * La actualización de stock se realiza mediante movimientos de inventario.
 */
class ProductosController extends Controller
{
    // ── GET /productos ────────────────────────────────────────────────────────

    public function index(): void
    {
        Middleware::auth();

        /** @var Producto $productoModel */
        $productoModel = $this->model('Producto');
        $productos = $productoModel->allWithDetails();

        // Datos auxiliares para los modales de creación/edición (se pasan a la vista)
        $categoriaModel = $this->model('Categoria');
        $subcategorias = $categoriaModel->allSubcategorias();
        $proveedores = $categoriaModel->fetchAll(
            "SELECT pr.id_proveedor, e.nombre AS empresa_nombre
     FROM proveedor pr
     JOIN empresa e ON pr.id_empresa = e.id_empresa
     ORDER BY e.nombre"
        );
        $magnitudes = $categoriaModel->fetchAll("SELECT * FROM magnitud ORDER BY nombre");

        $this->view('productos/index', compact(
            'productos',
            'subcategorias',
            'proveedores',
            'magnitudes'
        ));
    }

    // ── GET /productos/crear ──────────────────────────────────────────────────

    public function crear(): void
    {
        Middleware::auth();

        $categoriaModel = $this->model('Categoria');
        $subcategorias = $categoriaModel->allSubcategorias();
        $proveedores = $categoriaModel->fetchAll(
            "SELECT pr.id_proveedor, e.nombre AS empresa_nombre
     FROM proveedor pr
     JOIN empresa e ON pr.id_empresa = e.id_empresa
     ORDER BY e.nombre"
        );
        $magnitudes = $categoriaModel->fetchAll("SELECT * FROM magnitud ORDER BY nombre");

        $this->view('productos/crear', compact('subcategorias', 'proveedores', 'magnitudes'));
    }

    // ── POST /productos/guardar ───────────────────────────────────────────────

    public function guardar(): void
    {
        Middleware::auth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/productos');
        }

        // ── Recoger y sanitizar ──────────────────────────────────────────────
        $sku = limpiarDato(Helpers::post('sku'));
        $nombre = limpiarDato(Helpers::post('nombre'));
        $descripcion = limpiarDato(Helpers::post('descripcion', ''));
        $precio = Helpers::post('precio');
        $stockInicial = (int) Helpers::post('stock_inicial'); // nuevo campo
        $id_subcategoria = (int) Helpers::post('id_subcategoria');
        $id_proveedor = (int) Helpers::post('id_proveedor');
        $codigo_barras = limpiarDato(Helpers::post('codigo_barras', '')) ?: null;
        $id_magnitud = Helpers::post('id_magnitud') !== '' ? (int) Helpers::post('id_magnitud') : null;
        $valor_magnitud = Helpers::post('valor_magnitud') !== '' ? (float) Helpers::post('valor_magnitud') : null;
        $fecha_vencimiento = Helpers::post('fecha_vencimiento') ?: null;

        // ── Validaciones ─────────────────────────────────────────────────────
        $errores = recogerErrores([
            validarSKU($sku),
            validarTexto($nombre, 'Nombre', 2),
            validarPrecio($precio),
            validarStock($stockInicial), // validar cantidad positiva
            validarFecha((string) $fecha_vencimiento),
        ]);

        if ($id_subcategoria <= 0) {
            $errores[] = 'Debes seleccionar una subcategoría.';
        }
        if ($id_proveedor <= 0) {
            $errores[] = 'Debes seleccionar un proveedor.';
        }

        // Verificar SKU duplicado
        if (empty($errores)) {
            $existente = $this->model('Producto')->findBySKU($sku);
            if ($existente) {
                $errores[] = "El SKU «{$sku}» ya está en uso.";
            }
        }

        if (!empty($errores)) {
            Flash::errors($errores);
            $this->redirect('/productos/crear');
        }

        // ── Persistir producto e inventario ──────────────────────────────────
        $db = Database::getConnection();
        $db->beginTransaction();

        try {
            // Insertar producto
            $idProducto = $this->model('Producto')->create([
                'sku' => $sku,
                'codigo_barras' => $codigo_barras,
                'nombre' => $nombre,
                'descripcion' => $descripcion,
                'precio' => (float) $precio,
                'id_subcategoria' => $id_subcategoria,
                'id_proveedor' => $id_proveedor,
                'id_magnitud' => $id_magnitud,
                'valor_magnitud' => $valor_magnitud,
                'fecha_vencimiento' => $fecha_vencimiento,
                'tipo_producto' => 'general',
            ]);

            // Insertar stock inicial en inventario
            $stmt = $db->prepare(
                "INSERT INTO inventario (id_producto, stock_actual, stock_minimo, stock_maximo, punto_reorden)
                 VALUES (:id, :stock, 5, 200, 10)"
            );
            $stmt->execute(['id' => $idProducto, 'stock' => $stockInicial]);

            $db->commit();
            Flash::success("Producto «{$nombre}» guardado correctamente.");
        } catch (\Exception $e) {
            $db->rollBack();
            Flash::error('Error al guardar: ' . $e->getMessage());
        }

        $this->redirect('/productos');
    }

    // ── GET /productos/editar/{id} ────────────────────────────────────────────

    public function editar(int $id): void
    {
        Middleware::auth();

        /** @var Producto $productoModel */
        $productoModel = $this->model('Producto');
        $producto = $productoModel->findWithDetails($id);

        if (!$producto) {
            Flash::error('Producto no encontrado.');
            $this->redirect('/productos');
        }

        $categoriaModel = $this->model('Categoria');
        $subcategorias = $categoriaModel->allSubcategorias();
        $proveedores = $categoriaModel->fetchAll(
            "SELECT pr.id_proveedor, e.nombre AS empresa_nombre
     FROM proveedor pr
     JOIN empresa e ON pr.id_empresa = e.id_empresa
     ORDER BY e.nombre"
        );
        $magnitudes = $categoriaModel->fetchAll("SELECT * FROM magnitud ORDER BY nombre");

        $this->view('productos/editar', compact('producto', 'subcategorias', 'proveedores', 'magnitudes'));
    }

    // ── POST /productos/actualizar/{id} ───────────────────────────────────────

    public function actualizar(int $id): void
    {
        Middleware::auth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/productos');
        }

        /** @var Producto $productoModel */
        $productoModel = $this->model('Producto');
        $productoActual = $productoModel->find($id);

        if (!$productoActual) {
            Flash::error('Producto no encontrado.');
            $this->redirect('/productos');
        }

        // ── Recoger y sanitizar ──────────────────────────────────────────────
        $sku = limpiarDato(Helpers::post('sku'));
        $nombre = limpiarDato(Helpers::post('nombre'));
        $descripcion = limpiarDato(Helpers::post('descripcion', ''));
        $precio = Helpers::post('precio');
        $id_subcategoria = (int) Helpers::post('id_subcategoria');
        $id_proveedor = (int) Helpers::post('id_proveedor');
        $codigo_barras = limpiarDato(Helpers::post('codigo_barras', '')) ?: null;
        $id_magnitud = Helpers::post('id_magnitud') !== '' ? (int) Helpers::post('id_magnitud') : null;
        $valor_magnitud = Helpers::post('valor_magnitud') !== '' ? (float) Helpers::post('valor_magnitud') : null;
        $fecha_vencimiento = Helpers::post('fecha_vencimiento') ?: null;

        // ── Validaciones ─────────────────────────────────────────────────────
        $errores = recogerErrores([
            validarSKU($sku),
            validarTexto($nombre, 'Nombre', 2),
            validarPrecio($precio),
            validarFecha((string) $fecha_vencimiento),
        ]);

        // SKU duplicado (excluyendo el producto actual)
        if (empty($errores)) {
            $existente = $productoModel->findBySKU($sku);
            if ($existente && (int) $existente['id_producto'] !== $id) {
                $errores[] = "El SKU «{$sku}» ya está en uso por otro producto.";
            }
        }

        if (!empty($errores)) {
            Flash::errors($errores);
            $this->redirect("/productos/editar/{$id}");
        }

        // ── Actualizar ───────────────────────────────────────────────────────
        $productoModel->update($id, [
            'sku' => $sku,
            'codigo_barras' => $codigo_barras,
            'nombre' => $nombre,
            'descripcion' => $descripcion,
            'precio' => (float) $precio,
            'id_subcategoria' => $id_subcategoria,
            'id_proveedor' => $id_proveedor,
            'id_magnitud' => $id_magnitud,
            'valor_magnitud' => $valor_magnitud,
            'fecha_vencimiento' => $fecha_vencimiento,
        ]);

        Flash::success("Producto «{$nombre}» actualizado correctamente.");
        $this->redirect('/productos');
    }

    // ── GET /productos/eliminar/{id} ──────────────────────────────────────────

    public function eliminar(int $id): void
    {
        Middleware::auth();

        /** @var Producto $productoModel */
        $productoModel = $this->model('Producto');
        $producto = $productoModel->find($id);

        if (!$producto) {
            Flash::error('Producto no encontrado.');
            $this->redirect('/productos');
        }

        $productoModel->delete($id);

        Flash::success("Producto «{$producto['nombre']}» eliminado.");
        $this->redirect('/productos');
    }

    // ── GET /productos/buscar?q=... (AJAX para POS) ───────────────────────────

    public function buscar(): void
    {
        Middleware::auth();

        $term = limpiarDato(Helpers::get('q', ''));
        $productos = $this->model('Producto')->search($term);
        $this->json($productos);
    }
}