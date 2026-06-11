<?php
/**
 * EmpresasController — CRUD de empresas
 */
class EmpresasController extends Controller
{
    // ── GET /empresas ─────────────────────────────────────────────────

    public function index(): void
    {
        Middleware::auth();

        /** @var Empresa $empresaModel */
        $empresaModel = $this->model('Empresa');
        $empresas = $empresaModel->obtenerTodas();

        $this->view('empresas/index', compact('empresas'));
    }

    // ── GET /empresas/crear ───────────────────────────────────────────

    public function crear(): void
    {
        Middleware::auth();
        $this->view('empresas/crear');
    }

    // ── POST /empresas/guardar ───────────────────────────────────────

    public function guardar(): void
    {
        Middleware::auth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/empresas');
        }

        $nombre      = limpiarDato(Helpers::post('nombre'));
        $nit         = limpiarDato(Helpers::post('nit'));
        $direccion   = limpiarDato(Helpers::post('direccion', ''));
        $telefono    = limpiarDato(Helpers::post('telefono_principal', ''));
        $email       = limpiarDato(Helpers::post('email_principal', ''));

        $errores = recogerErrores([
            validarTexto($nombre, 'Nombre', 2),
            validarTexto($nit, 'NIT', 5),
            // Validación opcional de email si se desea agregar
            // ($email !== '' ? validarEmail($email) : []),
        ]);

        if (!empty($errores)) {
            Flash::errors($errores);
            $this->redirect('/empresas/crear');
        }

        /** @var Empresa $empresaModel */
        $empresaModel = $this->model('Empresa');

        try {
            $empresaModel->crearEmpresa([
                'nombre'             => $nombre,
                'nit'                => $nit,
                'direccion'          => $direccion,
                'telefono_principal' => $telefono,
                'email_principal'    => $email,
            ]);
            Flash::success('Empresa creada correctamente.');
        } catch (\Exception $e) {
            Flash::error('Error al crear empresa: ' . $e->getMessage());
        }

        $this->redirect('/empresas');
    }

    // ── GET /empresas/editar/{id} ────────────────────────────────────

    public function editar(int $id): void
    {
        Middleware::auth();

        /** @var Empresa $empresaModel */
        $empresaModel = $this->model('Empresa');
        $empresa = $empresaModel->obtenerPorId($id);

        if (!$empresa) {
            Flash::error('Empresa no encontrada.');
            $this->redirect('/empresas');
        }

        $this->view('empresas/editar', compact('empresa'));
    }

    // ── POST /empresas/actualizar/{id} ───────────────────────────────

    public function actualizar(int $id): void
    {
        Middleware::auth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/empresas');
        }

        $nombre      = limpiarDato(Helpers::post('nombre'));
        $nit         = limpiarDato(Helpers::post('nit'));
        $direccion   = limpiarDato(Helpers::post('direccion', ''));
        $telefono    = limpiarDato(Helpers::post('telefono_principal', ''));
        $email       = limpiarDato(Helpers::post('email_principal', ''));

        $errores = recogerErrores([
            validarTexto($nombre, 'Nombre', 2),
            validarTexto($nit, 'NIT', 5),
        ]);

        if (!empty($errores)) {
            Flash::errors($errores);
            $this->redirect("/empresas/editar/{$id}");
        }

        /** @var Empresa $empresaModel */
        $empresaModel = $this->model('Empresa');

        $empresaModel->actualizarEmpresa($id, [
            'nombre'             => $nombre,
            'nit'                => $nit,
            'direccion'          => $direccion,
            'telefono_principal' => $telefono,
            'email_principal'    => $email,
        ]);

        Flash::success('Empresa actualizada.');
        $this->redirect('/empresas');
    }

    // ── GET /empresas/eliminar/{id} ──────────────────────────────────

    public function eliminar(int $id): void
    {
        Middleware::auth();

        /** @var Empresa $empresaModel */
        $empresaModel = $this->model('Empresa');
        $empresa = $empresaModel->obtenerPorId($id);

        if (!$empresa) {
            Flash::error('Empresa no encontrada.');
            $this->redirect('/empresas');
        }

        $empresaModel->eliminarEmpresa($id);
        Flash::success('Empresa eliminada.');
        $this->redirect('/empresas');
    }
}