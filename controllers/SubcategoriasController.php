<?php
/**
 * SubcategoriasController — Tienda Doña Elena
 *
 * CRUD de subcategorías de productos.
 */
class SubcategoriasController extends Controller
{
    // ── GET /subcategorias ───────────────────────────────────────────

    public function index(): void
    {
        Middleware::auth();

        /** @var Subcategoria $subcategoriaModel */
        $subcategoriaModel = $this->model('Subcategoria');
        $subcategorias = $subcategoriaModel->obtenerTodasConCategoria();

        // Para el select de categorías en el modal de creación/edición
        $categorias = $this->model('Categoria')->allCategorias();

        $this->view('subcategorias/index', compact('subcategorias', 'categorias'));
    }

    // ── GET /subcategorias/crear ─────────────────────────────────────

    public function crear(): void
    {
        Middleware::auth();

        $categorias = $this->model('Categoria')->allCategorias();
        $this->view('subcategorias/crear', compact('categorias'));
    }

    // ── POST /subcategorias/guardar ──────────────────────────────────

    public function guardar(): void
    {
        Middleware::auth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/subcategorias');
        }

        $nombre        = limpiarDato(Helpers::post('nombre'));
        $descripcion   = limpiarDato(Helpers::post('descripcion', ''));
        $id_categoria  = (int) Helpers::post('id_categoria');

        $errores = recogerErrores([
            validarTexto($nombre, 'Nombre', 2),
        ]);

        if ($id_categoria <= 0) {
            $errores[] = 'Debes seleccionar una categoría.';
        }

        if (!empty($errores)) {
            Flash::errors($errores);
            $this->redirect('/subcategorias/crear');
        }

        /** @var Subcategoria $subcategoriaModel */
        $subcategoriaModel = $this->model('Subcategoria');
        $subcategoriaModel->crearSubcategoria([
            'nombre'       => $nombre,
            'descripcion'  => $descripcion,
            'id_categoria' => $id_categoria,
        ]);

        Flash::success("Subcategoría «{$nombre}» creada correctamente.");
        $this->redirect('/subcategorias');
    }

    // ── GET /subcategorias/editar/{id} ───────────────────────────────

    public function editar(int $id): void
    {
        Middleware::auth();

        /** @var Subcategoria $subcategoriaModel */
        $subcategoriaModel = $this->model('Subcategoria');
        $subcategoria = $subcategoriaModel->obtenerPorId($id);

        if (!$subcategoria) {
            Flash::error('Subcategoría no encontrada.');
            $this->redirect('/subcategorias');
        }

        $categorias = $this->model('Categoria')->allCategorias();
        $this->view('subcategorias/editar', compact('subcategoria', 'categorias'));
    }

    // ── POST /subcategorias/actualizar/{id} ──────────────────────────

    public function actualizar(int $id): void
    {
        Middleware::auth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/subcategorias');
        }

        $nombre        = limpiarDato(Helpers::post('nombre'));
        $descripcion   = limpiarDato(Helpers::post('descripcion', ''));
        $id_categoria  = (int) Helpers::post('id_categoria');

        $errores = recogerErrores([
            validarTexto($nombre, 'Nombre', 2),
        ]);

        if ($id_categoria <= 0) {
            $errores[] = 'Debes seleccionar una categoría.';
        }

        if (!empty($errores)) {
            Flash::errors($errores);
            $this->redirect("/subcategorias/editar/{$id}");
        }

        /** @var Subcategoria $subcategoriaModel */
        $subcategoriaModel = $this->model('Subcategoria');
        $subcategoriaModel->actualizarSubcategoria($id, [
            'nombre'       => $nombre,
            'descripcion'  => $descripcion,
            'id_categoria' => $id_categoria,
        ]);

        Flash::success("Subcategoría «{$nombre}» actualizada.");
        $this->redirect('/subcategorias');
    }

    // ── GET /subcategorias/eliminar/{id} ─────────────────────────────

    public function eliminar(int $id): void
    {
        Middleware::auth();

        /** @var Subcategoria $subcategoriaModel */
        $subcategoriaModel = $this->model('Subcategoria');
        $subcategoria = $subcategoriaModel->obtenerPorId($id);

        if (!$subcategoria) {
            Flash::error('Subcategoría no encontrada.');
            $this->redirect('/subcategorias');
        }

        $subcategoriaModel->eliminarSubcategoria($id);

        Flash::success("Subcategoría «{$subcategoria['nombre']}» eliminada.");
        $this->redirect('/subcategorias');
    }
}