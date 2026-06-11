<?php
/**
 * UsuarioController — Tienda Doña Elena
 *
 * CRUD de usuarios, ahora vinculado a empleado (id_empleado).
 * Permite crear un empleado nuevo desde el mismo formulario.
 * Solo accesible para Administrador.
 */
class UsuariosController extends Controller
{
    // ── GET /usuarios ─────────────────────────────────────────────────────

    public function index(): void
    {
        Middleware::role(['Administrador']);

        $usuarioModel = $this->model('Usuario');
        $usuarios = $usuarioModel->allWithRoles();

        // Empleados para el listado (no se usan en la tabla, pero sí en crear/editar)
        $this->view('usuarios/index', compact('usuarios'));
    }

    // ── GET /usuarios/crear ───────────────────────────────────────────────

    public function crear(): void
    {
        Middleware::role(['Administrador']);

        // Lista de empleados existentes con su rol
        $empleados = $this->model('Categoria')->fetchAll(
            "SELECT e.id_empleado, CONCAT(p.nombre, ' ', p.apellido) AS nombre_completo,
                    r.nombre AS nombre_rol
             FROM empleado e
             JOIN persona p ON e.id_persona = p.id_persona
             LEFT JOIN roles r ON e.id_rol = r.id_rol
             ORDER BY nombre_completo"
        );

        // Lista de roles para el caso de nuevo empleado
        $roles = $this->model('Categoria')->fetchAll(
            "SELECT id_rol, nombre FROM roles ORDER BY nombre"
        );

        $this->view('usuarios/crear', compact('empleados', 'roles'));
    }

    // ── POST /usuarios/guardar ────────────────────────────────────────────

    public function guardar(): void
    {
        Middleware::role(['Administrador']);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/usuarios');
        }

        $username = limpiarDato(Helpers::post('username'));
        $password = Helpers::post('password');
        $tipo = Helpers::post('tipo_empleado'); // 'existente' o 'nuevo'

        // ── Validaciones comunes ─────────────────────────────────────────
        $errores = recogerErrores([
            validarUsuario($username),
            validarPassword($password),
        ]);

        if ($tipo === 'existente') {
            $id_empleado = (int) Helpers::post('id_empleado');
            if ($id_empleado <= 0) {
                $errores[] = 'Debes seleccionar un empleado existente.';
            }
        } elseif ($tipo === 'nuevo') {
            $nombre = limpiarDato(Helpers::post('nombre'));
            $apellido = limpiarDato(Helpers::post('apellido'));
            $ci = limpiarDato(Helpers::post('ci'));
            $correo = limpiarDato(Helpers::post('correo'));
            $telefono = limpiarDato(Helpers::post('telefono'));
            $salario = (float) Helpers::post('salario');
            $id_rol = (int) Helpers::post('id_rol');

            $errores = array_merge($errores, recogerErrores([
                validarTexto($nombre, 'Nombre', 1),
                validarTexto($apellido, 'Apellido', 1),
                validarCi($ci),
                $salario > 0 ? true : 'El salario debe ser mayor a 0.',
                $id_rol > 0 ? true : 'Debes seleccionar un rol.',
            ]));
        } else {
            $errores[] = 'Debes elegir un tipo de vinculación (empleado existente o nuevo).';
        }

        // Verificar duplicado de username
        if (empty($errores)) {
            $usuarioModel = $this->model('Usuario');
            if ($usuarioModel->existsByUsername($username)) {
                $errores[] = "El usuario «{$username}» ya existe.";
            }
        }

        if (!empty($errores)) {
            Flash::errors($errores);
            $this->redirect('/usuarios/crear');
        }

        // ── Proceso de inserción (transacción) ───────────────────────────
        $db = Database::getConnection();
        $db->beginTransaction();

        try {
            if ($tipo === 'nuevo') {
                // 1) Insertar persona
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

                // 2) Insertar empleado
                $stmt = $db->prepare(
                    "INSERT INTO empleado (id_persona, id_rol, salario, fecha_contratacion)
                 VALUES (:id_persona, :id_rol, :salario, CURRENT_DATE)
                 RETURNING id_empleado"
                );
                $stmt->execute([
                    'id_persona' => $id_persona,
                    'id_rol' => $id_rol,
                    'salario' => $salario,
                ]);
                $id_empleado = (int) $stmt->fetchColumn();
            } else {
                $id_empleado = (int) Helpers::post('id_empleado');
            }

            // 3) Crear usuario
            $this->model('Usuario')->createUser([
                'username' => $username,
                'password_hash' => $password,
                'id_empleado' => $id_empleado,
            ]);

            $db->commit();
            Flash::success("Usuario «{$username}» creado correctamente.");
            $this->redirect('/usuarios');

        } catch (\Exception $e) {
            $db->rollBack();

            // Detectar error de CI duplicado
            if (
                str_contains($e->getMessage(), 'persona_ci_key') ||
                str_contains($e->getMessage(), '23505')
            ) {
                Flash::error("El CI {$ci} ya está registrado.");
            } else {
                Flash::error('Error al crear el usuario: ' . $e->getMessage());
            }

            $this->redirect('/usuarios/crear');
        }
    }

    // ── GET /usuarios/editar/{id} ─────────────────────────────────────────

    public function editar(int $id): void
    {
        Middleware::role(['Administrador']);

        $usuarioModel = $this->model('Usuario');
        $usuario = $usuarioModel->getWithRole($id);

        if (!$usuario) {
            Flash::error('Usuario no encontrado.');
            $this->redirect('/usuarios');
        }

        // Empleados para el select (con rol para información)
        $empleados = $this->model('Categoria')->fetchAll(
            "SELECT e.id_empleado, CONCAT(p.nombre, ' ', p.apellido) AS nombre_completo,
                    r.nombre AS nombre_rol
             FROM empleado e
             JOIN persona p ON e.id_persona = p.id_persona
             LEFT JOIN roles r ON e.id_rol = r.id_rol
             ORDER BY nombre_completo"
        );

        $this->view('usuarios/editar', compact('usuario', 'empleados'));
    }

    // ── POST /usuarios/actualizar/{id} ────────────────────────────────────

    public function actualizar(int $id): void
    {
        Middleware::role(['Administrador']);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/usuarios');
        }

        $username = limpiarDato(Helpers::post('username'));
        $password = Helpers::post('password');
        $id_empleado = (int) Helpers::post('id_empleado');

        $errores = recogerErrores([
            validarUsuario($username),
        ]);

        if ($id_empleado <= 0) {
            $errores[] = 'Debes seleccionar un empleado.';
        }

        if ($password !== '') {
            $resPass = validarPassword($password);
            if ($resPass !== true) {
                $errores[] = $resPass;
            }
        }

        if (empty($errores)) {
            $usuarioModel = $this->model('Usuario');
            $existente = $usuarioModel->findByUsername($username);
            if ($existente && (int) $existente['id_usuario'] !== $id) {
                $errores[] = "El usuario «{$username}» ya está en uso.";
            }
        }

        if (!empty($errores)) {
            Flash::errors($errores);
            $this->redirect("/usuarios/editar/{$id}");
        }

        $data = [
            'username' => $username,
            'id_empleado' => $id_empleado,
        ];
        if ($password !== '') {
            $data['password_hash'] = $password;
        }

        $this->model('Usuario')->updateUser($id, $data);

        Flash::success("Usuario «{$username}» actualizado correctamente.");
        $this->redirect('/usuarios');
    }

    // ── GET /usuarios/eliminar/{id} ───────────────────────────────────────

    public function eliminar(int $id): void
    {
        Middleware::role(['Administrador']);

        if (Session::userId() === $id) {
            Flash::error('No puedes eliminar tu propia cuenta.');
            $this->redirect('/usuarios');
        }

        $usuarioModel = $this->model('Usuario');
        $usuario = $usuarioModel->find($id);

        if (!$usuario) {
            Flash::error('Usuario no encontrado.');
            $this->redirect('/usuarios');
        }

        $usuarioModel->delete($id);
        Flash::success("Usuario «{$usuario['username']}» eliminado.");
        $this->redirect('/usuarios');
    }
}