<?php
/**
 * EmpleadosController — Tienda Doña Elena
 *
 * CRUD de empleados. Cada empleado se crea con una nueva persona.
 */
class EmpleadosController extends Controller
{
    // ── GET /empleados ──────────────────────────────────────────────────

    public function index(): void
    {
        Middleware::auth();

        /** @var Empleado $empleadoModel */
        $empleadoModel = $this->model('Empleado');
        $empleados = $empleadoModel->obtenerTodosConDetalles();

        $this->view('empleados/index', compact('empleados'));
    }

    // ── GET /empleados/crear ────────────────────────────────────────────

    public function crear(): void
    {
        Middleware::auth();

        // Obtener roles para el select
        $roles = $this->model('Categoria')->fetchAll("SELECT id_rol, nombre FROM roles ORDER BY nombre");
        $this->view('empleados/crear', compact('roles'));
    }

    // ── POST /empleados/guardar ─────────────────────────────────────────

    public function guardar(): void
    {
        Middleware::auth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/empleados');
        }

        // ── Datos de persona ─────────────────────────────────────────
        $nombre = limpiarDato(Helpers::post('nombre'));
        $apellido = limpiarDato(Helpers::post('apellido'));
        $ci = limpiarDato(Helpers::post('ci'));
        $correo = limpiarDato(Helpers::post('correo', ''));
        $telefono = limpiarDato(Helpers::post('telefono', ''));

        // ── Datos de empleado ────────────────────────────────────────
        $salario = (float) Helpers::post('salario');
        $id_rol = (int) Helpers::post('id_rol');
        $fecha_contratacion = limpiarDato(Helpers::post('fecha_contratacion', date('Y-m-d')));

        // ── Validaciones ─────────────────────────────────────────────
        $errores = recogerErrores([
            validarTexto($nombre, 'Nombre', 2),
            validarTexto($apellido, 'Apellido', 2),
            validarCi($ci),
            $salario > 0 ? true : 'El salario debe ser mayor a 0.',
            $id_rol > 0 ? true : 'Debes seleccionar un rol.',
        ]);

        if (!empty($errores)) {
            Flash::errors($errores);
            $this->redirect('/empleados/crear');
        }

        /** @var Empleado $empleadoModel */
        $empleadoModel = $this->model('Empleado');

        try {
            $empleadoModel->crearEmpleadoConPersona([
                'nombre' => $nombre,
                'apellido' => $apellido,
                'ci' => $ci,
                'correo' => $correo,
                'telefono' => $telefono,
                'salario' => $salario,
                'id_rol' => $id_rol,
                'fecha_contratacion' => $fecha_contratacion,
            ]);
            Flash::success("Empleado «{$nombre} {$apellido}» creado correctamente.");
            $this->redirect('/empleados');

        } catch (\Exception $e) {
            // Detectar error de CI duplicado
            if (
                str_contains($e->getMessage(), 'persona_ci_key') ||
                str_contains($e->getMessage(), '23505')
            ) {
                Flash::error("El CI {$ci} ya está registrado.");
            } else {
                Flash::error('Error al crear el empleado: ' . $e->getMessage());
            }
            $this->redirect('/empleados/crear');
        }
    }

    // ── GET /empleados/editar/{id} ──────────────────────────────────────

    public function editar(int $id): void
    {
        Middleware::auth();

        /** @var Empleado $empleadoModel */
        $empleadoModel = $this->model('Empleado');
        $empleado = $empleadoModel->obtenerPorId($id);

        if (!$empleado) {
            Flash::error('Empleado no encontrado.');
            $this->redirect('/empleados');
        }

        $roles = $this->model('Categoria')->fetchAll("SELECT id_rol, nombre FROM roles ORDER BY nombre");
        $this->view('empleados/editar', compact('empleado', 'roles'));
    }

    // ── POST /empleados/actualizar/{id} ─────────────────────────────────

    public function actualizar(int $id): void
    {
        Middleware::auth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/empleados');
        }

        $nombre = limpiarDato(Helpers::post('nombre'));
        $apellido = limpiarDato(Helpers::post('apellido'));
        $ci = limpiarDato(Helpers::post('ci'));
        $correo = limpiarDato(Helpers::post('correo', ''));
        $telefono = limpiarDato(Helpers::post('telefono', ''));

        $salario = (float) Helpers::post('salario');
        $fecha_contratacion = Helpers::post('fecha_contratacion', date('Y-m-d'));
        $id_rol = (int) Helpers::post('id_rol');

        $errores = recogerErrores([
            validarTexto($nombre, 'Nombre', 2),
            validarTexto($apellido, 'Apellido', 2),
            validarCi($ci),
        ]);

        if ($salario <= 0) {
            $errores[] = 'El salario debe ser mayor a 0.';
        }
        if ($id_rol <= 0) {
            $errores[] = 'Debes seleccionar un rol.';
        }

        if (!empty($errores)) {
            Flash::errors($errores);
            $this->redirect("/empleados/editar/{$id}");
        }

        /** @var Empleado $empleadoModel */
        $empleadoModel = $this->model('Empleado');
        try {
            $empleadoModel->actualizarEmpleadoConPersona($id, [
                'nombre' => $nombre,
                'apellido' => $apellido,
                'ci' => $ci,
                'correo' => $correo,
                'telefono' => $telefono,
                'salario' => $salario,
                'fecha_contratacion' => $fecha_contratacion,
                'id_rol' => $id_rol,
            ]);
            Flash::success("Empleado «{$nombre} {$apellido}» actualizado.");
        } catch (\Exception $e) {
            Flash::error('Error al actualizar: ' . $e->getMessage());
        }

        $this->redirect('/empleados');
    }

    // ── GET /empleados/eliminar/{id} ────────────────────────────────────

    public function eliminar(int $id): void
    {
        Middleware::auth();

        /** @var Empleado $empleadoModel */
        $empleadoModel = $this->model('Empleado');
        $empleado = $empleadoModel->obtenerPorId($id);

        if (!$empleado) {
            Flash::error('Empleado no encontrado.');
            $this->redirect('/empleados');
        }

        $empleadoModel->eliminarEmpleado($id);

        Flash::success("Empleado «{$empleado['nombre']} {$empleado['apellido']}» eliminado.");
        $this->redirect('/empleados');
    }
}