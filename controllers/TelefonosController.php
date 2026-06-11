<?php
/**
 * TelefonosController — CRUD unificado de teléfonos (persona/empresa)
 */
class TelefonosController extends Controller
{
    // ── GET /telefonos ───────────────────────────────────────────────

    public function index(): void
    {
        Middleware::auth();

        $db = Database::getConnection();
        $sql = "SELECT 'persona' AS origen,
                       tp.id_telefono_persona AS id,
                       tp.numero,
                       tp.tipo,
                       CONCAT(p.nombre, ' ', p.apellido) AS propietario,
                       p.ci AS identificador
                FROM telefono_persona tp
                JOIN persona p ON tp.id_persona = p.id_persona
                UNION ALL
                SELECT 'empresa',
                       te.id_telefono_empresa,
                       te.numero,
                       te.tipo,
                       e.nombre AS propietario,
                       e.nit AS identificador
                FROM telefono_empresa te
                JOIN empresa e ON te.id_empresa = e.id_empresa
                ORDER BY propietario, numero";

        $telefonos = $db->query($sql)->fetchAll();
        $this->view('telefonos/index', compact('telefonos'));
    }

    // ── GET /telefonos/crear ────────────────────────────────────────

    public function crear(): void
    {
        Middleware::auth();

        $personas = $this->model('Categoria')->fetchAll(
            "SELECT id_persona, CONCAT(nombre, ' ', apellido) AS nombre_completo, ci
             FROM persona ORDER BY apellido, nombre"
        );
        $empresas = $this->model('Categoria')->fetchAll(
            "SELECT id_empresa, nombre, nit FROM empresa ORDER BY nombre"
        );

        $this->view('telefonos/crear', compact('personas', 'empresas'));
    }

    // ── POST /telefonos/guardar ─────────────────────────────────────

    public function guardar(): void
    {
        Middleware::auth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/telefonos');
        }

        $tipoEntidad = Helpers::post('tipo_entidad'); // 'persona' o 'empresa'
        $numero      = limpiarDato(Helpers::post('numero'));
        $tipoTelefono = limpiarDato(Helpers::post('tipo', ''));

        $errores = [];

        if (!in_array($tipoEntidad, ['persona', 'empresa'])) {
            $errores[] = 'Debes seleccionar si el teléfono pertenece a una persona o empresa.';
        }

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

        // Validación básica del número (no vacío)
        if (empty($numero)) {
            $errores[] = 'El número de teléfono es obligatorio.';
        }

        if (!empty($errores)) {
            Flash::errors($errores);
            $this->redirect('/telefonos/crear');
        }

        $db = Database::getConnection();
        try {
            if ($tipoEntidad === 'persona') {
                $this->model('TelefonoPersona')->crearTelefonoPersona([
                    'numero'     => $numero,
                    'tipo'       => $tipoTelefono ?: null,
                    'id_persona' => $id_persona,
                ]);
            } else {
                // Generar ID manualmente con la secuencia compartida
                $seqId = $db->query("SELECT nextval('telefono_id_telefono_seq')")->fetchColumn();
                $this->model('TelefonoEmpresa')->crearTelefonoEmpresa([
                    'id_telefono_empresa' => $seqId,
                    'numero'              => $numero,
                    'tipo'                => $tipoTelefono ?: null,
                    'id_empresa'          => $id_empresa,
                ]);
            }
            Flash::success('Teléfono guardado correctamente.');
        } catch (\Exception $e) {
            Flash::error('Error al guardar: ' . $e->getMessage());
        }

        $this->redirect('/telefonos');
    }

    // ── GET /telefonos/editar/{id} ──────────────────────────────────

    public function editar(int $id): void
    {
        Middleware::auth();

        $telefonoData = $this->findTelefonoById($id);
        if (!$telefonoData) {
            Flash::error('Teléfono no encontrado.');
            $this->redirect('/telefonos');
        }

        // Cargar listas solo del tipo correspondiente (para mostrar el nombre)
        $personas = [];
        $empresas = [];
        if ($telefonoData['origen'] === 'persona') {
            $personas = $this->model('Categoria')->fetchAll(
                "SELECT id_persona, CONCAT(nombre, ' ', apellido) AS nombre_completo, ci
                 FROM persona ORDER BY apellido, nombre"
            );
        } else {
            $empresas = $this->model('Categoria')->fetchAll(
                "SELECT id_empresa, nombre, nit FROM empresa ORDER BY nombre"
            );
        }

        $this->view('telefonos/editar', compact('telefonoData', 'personas', 'empresas'));
    }

    // ── POST /telefonos/actualizar/{id} ─────────────────────────────

    public function actualizar(int $id): void
    {
        Middleware::auth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/telefonos');
        }

        $telefonoData = $this->findTelefonoById($id);
        if (!$telefonoData) {
            Flash::error('Teléfono no encontrado.');
            $this->redirect('/telefonos');
        }

        $numero      = limpiarDato(Helpers::post('numero'));
        $tipoTelefono = limpiarDato(Helpers::post('tipo', ''));

        $errores = [];
        if (empty($numero)) {
            $errores[] = 'El número de teléfono es obligatorio.';
        }

        if (!empty($errores)) {
            Flash::errors($errores);
            $this->redirect("/telefonos/editar/{$id}");
        }

        try {
            if ($telefonoData['origen'] === 'persona') {
                $this->model('TelefonoPersona')->actualizarTelefonoPersona($id, [
                    'numero' => $numero,
                    'tipo'   => $tipoTelefono ?: null,
                ]);
            } else {
                $this->model('TelefonoEmpresa')->actualizarTelefonoEmpresa($id, [
                    'numero' => $numero,
                    'tipo'   => $tipoTelefono ?: null,
                ]);
            }
            Flash::success('Teléfono actualizado.');
        } catch (\Exception $e) {
            Flash::error('Error al actualizar: ' . $e->getMessage());
        }

        $this->redirect('/telefonos');
    }

    // ── GET /telefonos/eliminar/{id} ────────────────────────────────

    public function eliminar(int $id): void
    {
        Middleware::auth();

        $telefonoData = $this->findTelefonoById($id);
        if (!$telefonoData) {
            Flash::error('Teléfono no encontrado.');
            $this->redirect('/telefonos');
        }

        try {
            if ($telefonoData['origen'] === 'persona') {
                $this->model('TelefonoPersona')->eliminarTelefonoPersona($id);
            } else {
                $this->model('TelefonoEmpresa')->eliminarTelefonoEmpresa($id);
            }
            Flash::success('Teléfono eliminado.');
        } catch (\Exception $e) {
            Flash::error('Error al eliminar: ' . $e->getMessage());
        }

        $this->redirect('/telefonos');
    }

    // ── Método auxiliar ─────────────────────────────────────────────

    private function findTelefonoById(int $id): ?array
    {
        $db = Database::getConnection();
        $stmt = $db->prepare(
            "SELECT 'persona' AS origen,
                    id_telefono_persona AS id,
                    numero,
                    tipo,
                    id_persona AS owner_id,
                    NULL AS id_empresa
             FROM telefono_persona WHERE id_telefono_persona = :id
             UNION ALL
             SELECT 'empresa',
                    id_telefono_empresa,
                    numero,
                    tipo,
                    NULL,
                    id_empresa
             FROM telefono_empresa WHERE id_telefono_empresa = :id"
        );
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }
}