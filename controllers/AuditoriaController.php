<?php
/**
 * AuditoriaController — Solo consulta del historial de cambios
 */
class AuditoriaController extends Controller
{
    // ── GET /auditoria ─────────────────────────────────────────────────

    public function index(): void
    {
        Middleware::role(['Administrador']);

        /** @var Auditoria $auditoriaModel */
        $auditoriaModel = $this->model('Auditoria');

        // Filtros opcionales
        $tabla = limpiarDato(Helpers::get('tabla', ''));
        $accion = limpiarDato(Helpers::get('accion', ''));

        $auditorias = $auditoriaModel->obtenerTodas($tabla, $accion);

        // Lista de tablas disponibles para el filtro
        $tablas = $auditoriaModel->obtenerTablasUnicas();

        $this->view('auditoria/index', compact('auditorias', 'tablas', 'tabla', 'accion'));
    }
}