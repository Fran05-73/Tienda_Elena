<?php
/**
 * CategoriasController — Tienda Doña Elena
 *
 * CRUD de categorías de productos.
 */
class CategoriasController extends Controller
{
    // ── GET /categorias ─────────────────────────────────────────────────

    public function index(): void
    {
        Middleware::auth();

        /** @var Categoria $categoriaModel */
        $categoriaModel = $this->model('Categoria');
        $categorias = $categoriaModel->obtenerTodas();

        $this->view('categorias/index', compact('categorias'));
    }

    // ── GET /categorias/crear ───────────────────────────────────────────

    public function crear(): void
    {
        Middleware::auth();
        $this->view('categorias/crear');
    }

    // ── POST /categorias/guardar ────────────────────────────────────────

    public function guardar(): void
    {
        Middleware::auth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/categorias');
        }

        $nombre      = limpiarDato(Helpers::post('nombre'));
        $descripcion = limpiarDato(Helpers::post('descripcion', ''));
        $estado      = Helpers::post('estado', 'activa');

        $errores = recogerErrores([
            validarTexto($nombre, 'Nombre', 2),
        ]);

        if (!in_array($estado, ['activa', 'inactiva'])) {
            $errores[] = 'Estado no válido.';
        }

        if (!empty($errores)) {
            Flash::errors($errores);
            $this->redirect('/categorias/crear');
        }

        /** @var Categoria $categoriaModel */
        $categoriaModel = $this->model('Categoria');
        $categoriaModel->crearCategoria([
            'nombre'      => $nombre,
            'descripcion' => $descripcion,
            'estado'      => $estado,
        ]);

        Flash::success("Categoría «{$nombre}» creada correctamente.");
        $this->redirect('/categorias');
    }

    // ── GET /categorias/editar/{id} ─────────────────────────────────────

    public function editar(int $id): void
    {
        Middleware::auth();

        /** @var Categoria $categoriaModel */
        $categoriaModel = $this->model('Categoria');
        $categoria = $categoriaModel->obtenerPorId($id);

        if (!$categoria) {
            Flash::error('Categoría no encontrada.');
            $this->redirect('/categorias');
        }

        $this->view('categorias/editar', compact('categoria'));
    }

    // ── POST /categorias/actualizar/{id} ────────────────────────────────

    public function actualizar(int $id): void
    {
        Middleware::auth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/categorias');
        }

        $nombre      = limpiarDato(Helpers::post('nombre'));
        $descripcion = limpiarDato(Helpers::post('descripcion', ''));
        $estado      = Helpers::post('estado', 'activa');

        $errores = recogerErrores([
            validarTexto($nombre, 'Nombre', 2),
        ]);

        if (!in_array($estado, ['activa', 'inactiva'])) {
            $errores[] = 'Estado no válido.';
        }

        if (!empty($errores)) {
            Flash::errors($errores);
            $this->redirect("/categorias/editar/{$id}");
        }

        /** @var Categoria $categoriaModel */
        $categoriaModel = $this->model('Categoria');
        $categoriaModel->actualizarCategoria($id, [
            'nombre'      => $nombre,
            'descripcion' => $descripcion,
            'estado'      => $estado,
        ]);

        Flash::success("Categoría «{$nombre}» actualizada.");
        $this->redirect('/categorias');
    }

    // ── GET /categorias/eliminar/{id} ───────────────────────────────────

    public function eliminar(int $id): void
    {
        Middleware::auth();

        /** @var Categoria $categoriaModel */
        $categoriaModel = $this->model('Categoria');
        $categoria = $categoriaModel->obtenerPorId($id);

        if (!$categoria) {
            Flash::error('Categoría no encontrada.');
            $this->redirect('/categorias');
        }

        $categoriaModel->eliminarCategoria($id);

        Flash::success("Categoría «{$categoria['nombre']}» eliminada.");
        $this->redirect('/categorias');
    }
}