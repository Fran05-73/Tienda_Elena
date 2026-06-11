<?php
/**
 * MagnitudesController — CRUD de magnitudes
 */
class MagnitudesController extends Controller
{
    // ── Tipos válidos según el CHECK de la tabla ─────────────────────
    private const TIPOS_VALIDOS = ['peso', 'volumen', 'unidad', 'longitud', 'otros'];

    // ── GET /magnitudes ──────────────────────────────────────────────

    public function index(): void
    {
        Middleware::auth();

        /** @var Magnitud $magnitudModel */
        $magnitudModel = $this->model('Magnitud');
        $magnitudes = $magnitudModel->obtenerTodas();

        $this->view('magnitudes/index', compact('magnitudes'));
    }

    // ── GET /magnitudes/crear ────────────────────────────────────────

    public function crear(): void
    {
        Middleware::auth();
        $this->view('magnitudes/crear');
    }

    // ── POST /magnitudes/guardar ────────────────────────────────────

    public function guardar(): void
    {
        Middleware::auth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/magnitudes');
        }

        $nombre     = limpiarDato(Helpers::post('nombre'));
        $abreviatura = limpiarDato(Helpers::post('abreviatura'));
        $tipo       = limpiarDato(Helpers::post('tipo', '')); // puede ser vacío -> null

        $errores = recogerErrores([
            validarTexto($nombre, 'Nombre', 2),
            validarTexto($abreviatura, 'Abreviatura', 1),
        ]);

        // Validar tipo si se seleccionó uno
        if ($tipo !== '' && !in_array($tipo, self::TIPOS_VALIDOS, true)) {
            $errores[] = 'Tipo de magnitud no válido.';
        }

        if (!empty($errores)) {
            Flash::errors($errores);
            $this->redirect('/magnitudes/crear');
        }

        /** @var Magnitud $magnitudModel */
        $magnitudModel = $this->model('Magnitud');

        try {
            $magnitudModel->crearMagnitud([
                'nombre'      => $nombre,
                'abreviatura' => $abreviatura,
                'tipo'        => $tipo !== '' ? $tipo : null,
            ]);
            Flash::success('Magnitud creada correctamente.');
        } catch (\Exception $e) {
            Flash::error('Error al crear magnitud: ' . $e->getMessage());
        }

        $this->redirect('/magnitudes');
    }

    // ── GET /magnitudes/editar/{id} ─────────────────────────────────

    public function editar(int $id): void
    {
        Middleware::auth();

        /** @var Magnitud $magnitudModel */
        $magnitudModel = $this->model('Magnitud');
        $magnitud = $magnitudModel->obtenerPorId($id);

        if (!$magnitud) {
            Flash::error('Magnitud no encontrada.');
            $this->redirect('/magnitudes');
        }

        $this->view('magnitudes/editar', compact('magnitud'));
    }

    // ── POST /magnitudes/actualizar/{id} ────────────────────────────

    public function actualizar(int $id): void
    {
        Middleware::auth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/magnitudes');
        }

        $nombre     = limpiarDato(Helpers::post('nombre'));
        $abreviatura = limpiarDato(Helpers::post('abreviatura'));
        $tipo       = limpiarDato(Helpers::post('tipo', ''));

        $errores = recogerErrores([
            validarTexto($nombre, 'Nombre', 2),
            validarTexto($abreviatura, 'Abreviatura', 1),
        ]);

        if ($tipo !== '' && !in_array($tipo, self::TIPOS_VALIDOS, true)) {
            $errores[] = 'Tipo de magnitud no válido.';
        }

        if (!empty($errores)) {
            Flash::errors($errores);
            $this->redirect("/magnitudes/editar/{$id}");
        }

        /** @var Magnitud $magnitudModel */
        $magnitudModel = $this->model('Magnitud');

        $magnitudModel->actualizarMagnitud($id, [
            'nombre'      => $nombre,
            'abreviatura' => $abreviatura,
            'tipo'        => $tipo !== '' ? $tipo : null,
        ]);

        Flash::success('Magnitud actualizada.');
        $this->redirect('/magnitudes');
    }

    // ── GET /magnitudes/eliminar/{id} ────────────────────────────────

    public function eliminar(int $id): void
    {
        Middleware::auth();

        /** @var Magnitud $magnitudModel */
        $magnitudModel = $this->model('Magnitud');
        $magnitud = $magnitudModel->obtenerPorId($id);

        if (!$magnitud) {
            Flash::error('Magnitud no encontrada.');
            $this->redirect('/magnitudes');
        }

        $magnitudModel->eliminarMagnitud($id);
        Flash::success('Magnitud eliminada.');
        $this->redirect('/magnitudes');
    }
}