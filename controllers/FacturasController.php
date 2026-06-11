<?php
/**
 * FacturasController — Solo consulta (sin editar/eliminar)
 */
class FacturasController extends Controller
{
    // ── GET /facturas ─────────────────────────────────────────────────────
    public function index(): void
    {
        Middleware::auth();

        /** @var Factura $facturaModel */
        $facturaModel = $this->model('Factura');
        $facturas = $facturaModel->obtenerTodas();

        $this->view('facturas/index', compact('facturas'));
    }
}