<?php
/**
 * EmailsController — CRUD unificado de correos (persona/empresa)
 */
class EmailsController extends Controller
{
    // ── GET /emails ──────────────────────────────────────────────────

    public function index(): void
    {
        Middleware::auth();

        $db = Database::getConnection();
        $sql = "SELECT 'persona' AS origen,
                       ep.id_email_persona AS id,
                       ep.correo,
                       ep.tipo,
                       CONCAT(p.nombre, ' ', p.apellido) AS propietario,
                       p.ci AS identificador
                FROM email_persona ep
                JOIN persona p ON ep.id_persona = p.id_persona
                UNION ALL
                SELECT 'empresa',
                       ee.id_email_empresa,
                       ee.correo,
                       ee.tipo,
                       e.nombre AS propietario,
                       e.nit AS identificador
                FROM email_empresa ee
                JOIN empresa e ON ee.id_empresa = e.id_empresa
                ORDER BY propietario, correo";

        $emails = $db->query($sql)->fetchAll();
        $this->view('emails/index', compact('emails'));
    }

    // ── GET /emails/crear ────────────────────────────────────────────

    public function crear(): void
    {
        Middleware::auth();

        // Listas para los dropdowns
        $personas = $this->model('Categoria')->fetchAll(
            "SELECT id_persona, CONCAT(nombre, ' ', apellido) AS nombre_completo, ci
             FROM persona ORDER BY apellido, nombre"
        );
        $empresas = $this->model('Categoria')->fetchAll(
            "SELECT id_empresa, nombre, nit FROM empresa ORDER BY nombre"
        );

        $this->view('emails/crear', compact('personas', 'empresas'));
    }

    // ── POST /emails/guardar ────────────────────────────────────────

    public function guardar(): void
    {
        Middleware::auth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/emails');
        }

        $tipoEntidad = Helpers::post('tipo_entidad'); // 'persona' o 'empresa'
        $correo      = limpiarDato(Helpers::post('correo'));
        $tipoEmail   = limpiarDato(Helpers::post('tipo', ''));

        $errores = [];

        // Validar selección de entidad
        if (!in_array($tipoEntidad, ['persona', 'empresa'])) {
            $errores[] = 'Debes seleccionar si el correo pertenece a una persona o empresa.';
        }

        // Validar propietario
        if ($tipoEntidad === 'persona') {
            $id_persona = (int) Helpers::post('id_persona');
            if ($id_persona <= 0) {
                $errores[] = 'Selecciona una persona.';
            }
        } else {
            $id_empresa = (int) Helpers::post('id_empresa');
            if ($id_empresa <= 0) {
                $errores[] = 'Selecciona una empresa.';
            }
        }

        // Validar formato de correo
        if (empty($correo) || !filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            $errores[] = 'Ingresa un correo electrónico válido.';
        }

        if (!empty($errores)) {
            Flash::errors($errores);
            $this->redirect('/emails/crear');
        }

        // Persistir
        $db = Database::getConnection();
        try {
            if ($tipoEntidad === 'persona') {
                $this->model('EmailPersona')->crearEmailPersona([
                    'correo'     => $correo,
                    'tipo'       => $tipoEmail ?: null,
                    'id_persona' => $id_persona,
                ]);
            } else {
                // La tabla email_empresa no tiene DEFAULT para el ID, lo generamos con la secuencia compartida
                $seqId = $db->query("SELECT nextval('email_id_email_seq')")->fetchColumn();
                $this->model('EmailEmpresa')->crearEmailEmpresa([
                    'id_email_empresa' => $seqId,
                    'correo'           => $correo,
                    'tipo'             => $tipoEmail ?: null,
                    'id_empresa'       => $id_empresa,
                ]);
            }
            Flash::success('Correo guardado correctamente.');
        } catch (\Exception $e) {
            Flash::error('Error al guardar: ' . $e->getMessage());
        }

        $this->redirect('/emails');
    }

    // ── GET /emails/editar/{id} ─────────────────────────────────────

    public function editar(int $id): void
    {
        Middleware::auth();

        $emailData = $this->findEmailById($id);
        if (!$emailData) {
            Flash::error('Correo no encontrado.');
            $this->redirect('/emails');
        }

        // Solo necesitamos las listas para mostrar el propietario (no se permitirá cambiarlo)
        $personas = [];
        $empresas = [];
        if ($emailData['origen'] === 'persona') {
            $personas = $this->model('Categoria')->fetchAll(
                "SELECT id_persona, CONCAT(nombre, ' ', apellido) AS nombre_completo, ci
                 FROM persona ORDER BY apellido, nombre"
            );
        } else {
            $empresas = $this->model('Categoria')->fetchAll(
                "SELECT id_empresa, nombre, nit FROM empresa ORDER BY nombre"
            );
        }

        $this->view('emails/editar', compact('emailData', 'personas', 'empresas'));
    }

    // ── POST /emails/actualizar/{id} ────────────────────────────────

    public function actualizar(int $id): void
    {
        Middleware::auth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/emails');
        }

        $emailData = $this->findEmailById($id);
        if (!$emailData) {
            Flash::error('Correo no encontrado.');
            $this->redirect('/emails');
        }

        $correo    = limpiarDato(Helpers::post('correo'));
        $tipoEmail = limpiarDato(Helpers::post('tipo', ''));

        $errores = [];
        if (empty($correo) || !filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            $errores[] = 'Ingresa un correo electrónico válido.';
        }

        if (!empty($errores)) {
            Flash::errors($errores);
            $this->redirect("/emails/editar/{$id}");
        }

        try {
            if ($emailData['origen'] === 'persona') {
                $this->model('EmailPersona')->actualizarEmailPersona($id, [
                    'correo' => $correo,
                    'tipo'   => $tipoEmail ?: null,
                ]);
            } else {
                $this->model('EmailEmpresa')->actualizarEmailEmpresa($id, [
                    'correo' => $correo,
                    'tipo'   => $tipoEmail ?: null,
                ]);
            }
            Flash::success('Correo actualizado.');
        } catch (\Exception $e) {
            Flash::error('Error al actualizar: ' . $e->getMessage());
        }

        $this->redirect('/emails');
    }

    // ── GET /emails/eliminar/{id} ───────────────────────────────────

    public function eliminar(int $id): void
    {
        Middleware::auth();

        $emailData = $this->findEmailById($id);
        if (!$emailData) {
            Flash::error('Correo no encontrado.');
            $this->redirect('/emails');
        }

        try {
            if ($emailData['origen'] === 'persona') {
                $this->model('EmailPersona')->eliminarEmailPersona($id);
            } else {
                $this->model('EmailEmpresa')->eliminarEmailEmpresa($id);
            }
            Flash::success('Correo eliminado.');
        } catch (\Exception $e) {
            Flash::error('Error al eliminar: ' . $e->getMessage());
        }

        $this->redirect('/emails');
    }

    // ── Método auxiliar: buscar email por ID en ambas tablas ─────────

    private function findEmailById(int $id): ?array
    {
        $db = Database::getConnection();
        $stmt = $db->prepare(
            "SELECT 'persona' AS origen,
                    id_email_persona AS id,
                    correo,
                    tipo,
                    id_persona AS owner_id,
                    NULL AS id_empresa
             FROM email_persona WHERE id_email_persona = :id
             UNION ALL
             SELECT 'empresa',
                    id_email_empresa,
                    correo,
                    tipo,
                    NULL,
                    id_empresa
             FROM email_empresa WHERE id_email_empresa = :id"
        );
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }
}