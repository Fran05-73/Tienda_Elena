<?php
/**
 * RolesController — Tienda Doña Elena
 *
 * CRUD de roles del sistema.
 */
class RolesController extends Controller
{
    // ── GET /roles ────────────────────────────────────────────────────

    public function index(): void
    {
        Middleware::auth();

        /** @var Rol $rolModel */
        $rolModel = $this->model('Rol');
        $roles = $rolModel->obtenerTodos();

        $this->view('roles/index', compact('roles'));
    }

    // ── GET /roles/crear ──────────────────────────────────────────────

    public function crear(): void
    {
        Middleware::auth();
        $this->view('roles/crear');
    }

    // ── POST /roles/guardar ───────────────────────────────────────────

    public function guardar(): void
    {
        Middleware::auth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/roles');
        }

        $nombre = limpiarDato(Helpers::post('nombre'));

        $errores = recogerErrores([
            validarTexto($nombre, 'Nombre del rol', 2),
        ]);

        if (!empty($errores)) {
            Flash::errors($errores);
            $this->redirect('/roles/crear');
        }

        /** @var Rol $rolModel */
        $rolModel = $this->model('Rol');

        try {
            $rolModel->crearRol(['nombre' => $nombre]);
            Flash::success("Rol «{$nombre}» creado correctamente.");
        } catch (\Exception $e) {
            Flash::error('Error al crear el rol: ' . $e->getMessage());
        }

        $this->redirect('/roles');
    }

    // ── GET /roles/editar/{id} ────────────────────────────────────────

    public function editar(int $id): void
    {
        Middleware::auth();

        /** @var Rol $rolModel */
        $rolModel = $this->model('Rol');
        $rol = $rolModel->obtenerPorId($id);

        if (!$rol) {
            Flash::error('Rol no encontrado.');
            $this->redirect('/roles');
        }

        $this->view('roles/editar', compact('rol'));
    }

    // ── POST /roles/actualizar/{id} ───────────────────────────────────

    public function actualizar(int $id): void
    {
        Middleware::auth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/roles');
        }

        $nombre = limpiarDato(Helpers::post('nombre'));

        $errores = recogerErrores([
            validarTexto($nombre, 'Nombre del rol', 2),
        ]);

        if (!empty($errores)) {
            Flash::errors($errores);
            $this->redirect("/roles/editar/{$id}");
        }

        /** @var Rol $rolModel */
        $rolModel = $this->model('Rol');

        try {
            $rolModel->actualizarRol($id, ['nombre' => $nombre]);
            Flash::success("Rol «{$nombre}» actualizado.");
        } catch (\Exception $e) {
            Flash::error('Error al actualizar: ' . $e->getMessage());
        }

        $this->redirect('/roles');
    }

    // ── GET /roles/eliminar/{id} ──────────────────────────────────────

    public function eliminar(int $id): void
    {
        Middleware::auth();

        /** @var Rol $rolModel */
        $rolModel = $this->model('Rol');
        $rol = $rolModel->obtenerPorId($id);

        if (!$rol) {
            Flash::error('Rol no encontrado.');
            $this->redirect('/roles');
        }

        $rolModel->eliminarRol($id);
        Flash::success("Rol «{$rol['nombre']}» eliminado.");
        $this->redirect('/roles');
    }
}