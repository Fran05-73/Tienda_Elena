<?php
/**
 * ClientesController — Tienda Doña Elena
 *
 * CRUD de clientes. Cada cliente se crea con una nueva persona.
 */
class ClientesController extends Controller
{
    // ── GET /clientes ─────────────────────────────────────────────────

    public function index(): void
    {
        Middleware::auth();

        /** @var Cliente $clienteModel */
        $clienteModel = $this->model('Cliente');
        $clientes = $clienteModel->obtenerTodosConPersona();

        $this->view('clientes/index', compact('clientes'));
    }

    // ── GET /clientes/crear ───────────────────────────────────────────

    public function crear(): void
    {
        Middleware::auth();
        $this->view('clientes/crear');
    }

    // ── POST /clientes/guardar ────────────────────────────────────────

    public function guardar(): void
    {
        Middleware::auth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/clientes');
        }

        // ── Datos de persona ─────────────────────────────────────────
        $nombre = limpiarDato(Helpers::post('nombre'));
        $apellido = limpiarDato(Helpers::post('apellido'));
        $ci = limpiarDato(Helpers::post('ci'));
        $correo = limpiarDato(Helpers::post('correo', ''));
        $telefono = limpiarDato(Helpers::post('telefono', ''));

        // ── Datos de cliente ─────────────────────────────────────────
        $direccion = limpiarDato(Helpers::post('direccion', ''));
        $nacionalidad = limpiarDato(Helpers::post('nacionalidad', ''));
        $preferencias = limpiarDato(Helpers::post('preferencias', ''));

        // ── Validaciones ─────────────────────────────────────────────
        $errores = recogerErrores([
            validarTexto($nombre, 'Nombre', 2),
            validarTexto($apellido, 'Apellido', 2),
            validarCi($ci),
        ]);

        if (!empty($errores)) {
            Flash::errors($errores);
            $this->redirect('/clientes/crear');
        }

        /** @var Cliente $clienteModel */
        $clienteModel = $this->model('Cliente');

        try {
            $clienteModel->crearClienteConPersona([
                'nombre' => $nombre,
                'apellido' => $apellido,
                'ci' => $ci,
                'correo' => $correo,
                'telefono' => $telefono,
                'direccion' => $direccion,
                'nacionalidad' => $nacionalidad,
                'preferencias' => $preferencias,
            ]);
            Flash::success("Cliente «{$nombre} {$apellido}» creado correctamente.");
            $this->redirect('/clientes');

        } catch (\Exception $e) {
            // Detectar error de CI duplicado
            if (
                str_contains($e->getMessage(), 'persona_ci_key') ||
                str_contains($e->getMessage(), '23505')
            ) {
                Flash::error("El CI {$ci} ya está registrado en el sistema. Ingrese un CI diferente.");
            } else {
                Flash::error('Error al crear el cliente: ' . $e->getMessage());
            }
            $this->redirect('/clientes/crear');
        }
    }

    // ── GET /clientes/editar/{id} ─────────────────────────────────────

    public function editar(int $id): void
    {
        Middleware::auth();

        /** @var Cliente $clienteModel */
        $clienteModel = $this->model('Cliente');
        $cliente = $clienteModel->obtenerPorId($id);

        if (!$cliente) {
            Flash::error('Cliente no encontrado.');
            $this->redirect('/clientes');
        }

        $this->view('clientes/editar', compact('cliente'));
    }

    // ── POST /clientes/actualizar/{id} ────────────────────────────────

    public function actualizar(int $id): void
    {
        Middleware::auth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/clientes');
        }

        $nombre = limpiarDato(Helpers::post('nombre'));
        $apellido = limpiarDato(Helpers::post('apellido'));
        $ci = limpiarDato(Helpers::post('ci'));
        $correo = limpiarDato(Helpers::post('correo', ''));
        $telefono = limpiarDato(Helpers::post('telefono', ''));
        $direccion = limpiarDato(Helpers::post('direccion', ''));
        $nacionalidad = limpiarDato(Helpers::post('nacionalidad', ''));
        $preferencias = limpiarDato(Helpers::post('preferencias', ''));

        $errores = recogerErrores([
            validarTexto($nombre, 'Nombre', 2),
            validarTexto($apellido, 'Apellido', 2),
            validarCi($ci),
        ]);

        if (!empty($errores)) {
            Flash::errors($errores);
            $this->redirect("/clientes/editar/{$id}");
        }

        /** @var Cliente $clienteModel */
        $clienteModel = $this->model('Cliente');
        try {
            $clienteModel->actualizarClienteConPersona($id, [
                'nombre' => $nombre,
                'apellido' => $apellido,
                'ci' => $ci,
                'correo' => $correo,
                'telefono' => $telefono,
                'direccion' => $direccion,
                'nacionalidad' => $nacionalidad,
                'preferencias' => $preferencias,
            ]);
            Flash::success("Cliente «{$nombre} {$apellido}» actualizado.");
        } catch (\Exception $e) {
            Flash::error('Error al actualizar: ' . $e->getMessage());
        }

        $this->redirect('/clientes');
    }

    // ── GET /clientes/eliminar/{id} ───────────────────────────────────

    public function eliminar(int $id): void
    {
        Middleware::auth();

        /** @var Cliente $clienteModel */
        $clienteModel = $this->model('Cliente');
        $cliente = $clienteModel->obtenerPorId($id);

        if (!$cliente) {
            Flash::error('Cliente no encontrado.');
            $this->redirect('/clientes');
        }

        $clienteModel->eliminarCliente($id);

        Flash::success("Cliente «{$cliente['nombre']} {$cliente['apellido']}» eliminado.");
        $this->redirect('/clientes');
    }
}