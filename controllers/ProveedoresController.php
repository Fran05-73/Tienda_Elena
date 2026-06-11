<?php
/**
 * ProveedoresController — Tienda Doña Elena
 *
 * CRUD de proveedores.
 * Permite asociar una persona y empresa existentes o crear nuevas.
 */
class ProveedoresController extends Controller
{
    // ── GET /proveedores ──────────────────────────────────────────────

    public function index(): void
    {
        Middleware::auth();

        /** @var Proveedor $proveedorModel */
        $proveedorModel = $this->model('Proveedor');
        $proveedores = $proveedorModel->obtenerTodosConDetalles();

        $this->view('proveedores/index', compact('proveedores'));
    }

    // ── GET /proveedores/crear ────────────────────────────────────────

    public function crear(): void
    {
        Middleware::auth();

        // Lista de personas y empresas existentes para los selects
        $personas = $this->model('Categoria')->fetchAll(
            "SELECT id_persona, nombre, apellido, CONCAT(nombre, ' ', apellido) AS nombre_completo, ci 
         FROM persona ORDER BY apellido, nombre"
        );
        $empresas = $this->model('Categoria')->fetchAll(
            "SELECT id_empresa, nombre, nit FROM empresa ORDER BY nombre"
        );

        $this->view('proveedores/crear', compact('personas', 'empresas'));
    }

    // ── POST /proveedores/guardar ────────────────────────────────────

    public function guardar(): void
    {
        Middleware::auth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/proveedores');
        }

        // ── Determinar modo persona ──────────────────────────────────
        $tipoPersona = Helpers::post('tipo_persona'); // 'existente' o 'nueva'
        $tipoEmpresa = Helpers::post('tipo_empresa'); // 'existente' o 'nueva'

        $errores = [];

        // ── Procesar persona ──────────────────────────────────────────
        if ($tipoPersona === 'existente') {
            $id_persona = (int) Helpers::post('id_persona');
            if ($id_persona <= 0) {
                $errores[] = 'Debes seleccionar una persona existente.';
            }
        } else {
            $nombre = limpiarDato(Helpers::post('persona_nombre'));
            $apellido = limpiarDato(Helpers::post('persona_apellido'));
            $ci = limpiarDato(Helpers::post('persona_ci'));
            $correo = limpiarDato(Helpers::post('persona_correo', ''));
            $telefono = limpiarDato(Helpers::post('persona_telefono', ''));

            $errores = array_merge($errores, recogerErrores([
                validarTexto($nombre, 'Nombre', 2),
                validarTexto($apellido, 'Apellido', 2),
                validarCi($ci),
            ]));
        }

        // ── Procesar empresa ──────────────────────────────────────────
        if ($tipoEmpresa === 'existente') {
            $id_empresa = (int) Helpers::post('id_empresa');
            if ($id_empresa <= 0) {
                $errores[] = 'Debes seleccionar una empresa existente.';
            }
        } else {
            $empresa_nombre = limpiarDato(Helpers::post('empresa_nombre'));
            $empresa_nit = limpiarDato(Helpers::post('empresa_nit'));
            $empresa_direccion = limpiarDato(Helpers::post('empresa_direccion', ''));
            $empresa_telefono = limpiarDato(Helpers::post('empresa_telefono', ''));
            $empresa_email = limpiarDato(Helpers::post('empresa_email', ''));

            $errores = array_merge($errores, recogerErrores([
                validarTexto($empresa_nombre, 'Nombre de empresa', 2),
                validarTexto($empresa_nit, 'NIT', 5),
            ]));
        }

        $cargo = limpiarDato(Helpers::post('cargo', ''));
        $estado = Helpers::post('estado', 'activo');
        if (!in_array($estado, ['activo', 'inactivo'])) {
            $errores[] = 'Estado no válido.';
        }

        if (!empty($errores)) {
            Flash::errors($errores);
            $this->redirect('/proveedores/crear');
        }

        // ── Persistir en transacción ─────────────────────────────────
        $db = Database::getConnection();
        $db->beginTransaction();

        try {
            // Persona nueva → usar RETURNING para obtener el ID real
            if ($tipoPersona === 'nueva') {
                $stmt = $db->prepare(
                    "INSERT INTO persona (nombre, apellido, ci, correo, telefono)
                 VALUES (:nombre, :apellido, :ci, :correo, :telefono)
                 RETURNING id_persona"
                );
                $stmt->execute([
                    'nombre' => $nombre,
                    'apellido' => $apellido,
                    'ci' => $ci,
                    'correo' => $correo,
                    'telefono' => $telefono,
                ]);
                $id_persona = (int) $stmt->fetchColumn();
            }

            // Empresa nueva → usar RETURNING para obtener el ID real
            if ($tipoEmpresa === 'nueva') {
                $stmt = $db->prepare(
                    "INSERT INTO empresa (nombre, nit, direccion, telefono_principal, email_principal)
                 VALUES (:nombre, :nit, :direccion, :telefono, :email)
                 RETURNING id_empresa"
                );
                $stmt->execute([
                    'nombre' => $empresa_nombre,
                    'nit' => $empresa_nit,
                    'direccion' => $empresa_direccion,
                    'telefono' => $empresa_telefono,
                    'email' => $empresa_email,
                ]);
                $id_empresa = (int) $stmt->fetchColumn();
            }

            // Insertar proveedor
            $this->model('Proveedor')->crearProveedor([
                'id_persona' => $id_persona,
                'cargo' => $cargo,
                'id_empresa' => $id_empresa,
                'estado' => $estado,
            ]);

            $db->commit();
            Flash::success('Proveedor creado correctamente.');
        } catch (\Exception $e) {
            $db->rollBack();
            Flash::error('Error al crear proveedor: ' . $e->getMessage());
        }

        $this->redirect('/proveedores');
    }

    // ── GET /proveedores/editar/{id} ─────────────────────────────────

    public function editar(int $id): void
    {
        Middleware::auth();

        /** @var Proveedor $proveedorModel */
        $proveedorModel = $this->model('Proveedor');
        $proveedor = $proveedorModel->obtenerPorId($id);

        if (!$proveedor) {
            Flash::error('Proveedor no encontrado.');
            $this->redirect('/proveedores');
        }

        // Para los selects de edición
        $personas = $this->model('Categoria')->fetchAll(
            "SELECT id_persona, nombre, apellido, CONCAT(apellido, ' ', nombre) AS nombre_completo, ci
     FROM persona
     ORDER BY apellido, nombre"
        );
        $empresas = $this->model('Categoria')->fetchAll(
            "SELECT id_empresa, nombre, nit FROM empresa ORDER BY nombre"
        );

        $this->view('proveedores/editar', compact('proveedor', 'personas', 'empresas'));
    }

    // ── POST /proveedores/actualizar/{id} ────────────────────────────

    public function actualizar(int $id): void
    {
        Middleware::auth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/proveedores');
        }

        $id_persona = (int) Helpers::post('id_persona');
        $id_empresa = (int) Helpers::post('id_empresa');
        $cargo = limpiarDato(Helpers::post('cargo', ''));
        $estado = Helpers::post('estado', 'activo');

        $errores = [];
        if ($id_persona <= 0)
            $errores[] = 'Debes seleccionar una persona.';
        if ($id_empresa <= 0)
            $errores[] = 'Debes seleccionar una empresa.';
        if (!in_array($estado, ['activo', 'inactivo'])) {
            $errores[] = 'Estado no válido.';
        }

        if (!empty($errores)) {
            Flash::errors($errores);
            $this->redirect("/proveedores/editar/{$id}");
        }

        $this->model('Proveedor')->actualizarProveedor($id, [
            'id_persona' => $id_persona,
            'id_empresa' => $id_empresa,
            'cargo' => $cargo,
            'estado' => $estado,
        ]);

        Flash::success('Proveedor actualizado.');
        $this->redirect('/proveedores');
    }

    // ── GET /proveedores/eliminar/{id} ───────────────────────────────

    public function eliminar(int $id): void
    {
        Middleware::auth();

        $proveedorModel = $this->model('Proveedor');
        $proveedor = $proveedorModel->obtenerPorId($id);

        if (!$proveedor) {
            Flash::error('Proveedor no encontrado.');
            $this->redirect('/proveedores');
        }

        $proveedorModel->eliminarProveedor($id);

        Flash::success('Proveedor eliminado.');
        $this->redirect('/proveedores');
    }
}