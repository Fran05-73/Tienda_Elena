<?php
class Factura extends Model
{
    protected string $table      = 'factura';
    protected string $primaryKey = 'id_factura';

    /**
     * Obtiene todas las facturas con datos básicos de venta.
     */
    public function obtenerTodas(): array
    {
        return $this->fetchAll(
            "SELECT f.id_factura, f.id_venta, f.nro_factura,
                    f.nro_autorizacion, f.nit_cliente, f.razon_social,
                    f.fecha_emision, f.cufd, f.leyenda,
                    v.total
             FROM factura f
             JOIN venta v ON f.id_venta = v.id_venta
             ORDER BY f.fecha_emision DESC"
        );
    }
}