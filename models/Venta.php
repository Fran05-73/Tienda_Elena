<?php
/**
 * Venta — Tienda Doña Elena
 *
 * Modelo de ventas.
 * createVenta() usa transacciones PostgreSQL explícitas:
 *   BEGIN → INSERT venta → INSERT detalle_venta (x N) →
 *   UPDATE producto stock (x N) → INSERT pago → INSERT pago_específico → COMMIT
 *   Si falla algo: ROLLBACK + lanza excepción.
 */
class Venta extends Model
{
    protected string $table = 'venta';
    protected string $primaryKey = 'id_venta';

    // ── Escritura ─────────────────────────────────────────────────────────────

    /**
     * Registra una venta completa en una única transacción.
     *
     * @param array $ventaData       Datos para tabla venta (id_cliente, id_empleado, fecha, total).
     * @param array $detalles        Array de ['id_producto', 'cantidad', 'subtotal'].
     * @param array $pagoData        Datos de pago ['monto', 'metodo'].
     * @param array $datosAdicionales Campos extra según método (recibido_por, banco_origen, etc.)
     * @return int                   ID de la venta generada.
     * @throws \Exception            Si cualquier paso falla (ROLLBACK automático).
     */
    public function createVenta(
        array $ventaData,
        array $detalles,
        array $pagoData,
        array $datosAdicionales = []
    ): int {
        $this->beginTransaction();

        try {
            // 1. Insertar cabecera de venta
            $idVenta = $this->create($ventaData);

            // 2. Preparar statement para detalles
            $stmtDetalle = $this->pdo->prepare(
                "INSERT INTO detalle_venta (id_venta, id_producto, cantidad, subtotal)
             VALUES (:id_venta, :id_producto, :cantidad, :subtotal)"
            );

            // 3. Insertar cada línea de detalle
            //    El trigger trg_actualizar_stock_venta descuenta el stock automáticamente
            foreach ($detalles as $det) {
                $idProducto = (int) $det['id_producto'];
                $cantidad = (int) $det['cantidad'];
                $subtotal = (float) $det['subtotal'];

                $stmtDetalle->execute([
                    'id_venta' => $idVenta,
                    'id_producto' => $idProducto,
                    'cantidad' => $cantidad,
                    'subtotal' => $subtotal,
                ]);
            }

            // 4. Insertar pago genérico y obtener su ID
            $idPago = $this->insertDetalle($idVenta, $pagoData);

            // 5. Insertar datos específicos según método de pago
            if (!empty($pagoData['metodo'])) {
                $this->insertDetalleEspecifico($idPago, $pagoData['metodo'], $datosAdicionales);
            }

            // 6. Generar factura automática
            $this->generarFactura($idVenta);

            $this->commit();
            return $idVenta;
        } catch (\Exception $e) {
            $this->rollback();
            throw $e;
        }
    }

    /**
     * Inserta el registro de pago asociado a una venta.
     * @return int id_pago generado
     */
    public function insertDetalle(int $idVenta, array $pagoData): int
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO pago (id_venta, monto, fecha_pago, metodo)
         VALUES (:id_venta, :monto, NOW(), :metodo)
         RETURNING id_pago"
        );
        $stmt->execute([
            'id_venta' => $idVenta,
            'monto' => $pagoData['monto'],
            'metodo' => $pagoData['metodo'],
        ]);
        return (int) $stmt->fetchColumn();
    }

    /**
     * Inserta en la tabla específica del método de pago.
     */
    private function insertDetalleEspecifico(int $idPago, string $metodo, array $datos): void
    {
        switch ($metodo) {
            case 'efectivo':
                $recibido = $datos['recibido_por'] ?? null;
                $this->execute(
                    "INSERT INTO pago_efectivo (id_pago, recibido_por) VALUES (:id_pago, :recibido)",
                    ['id_pago' => $idPago, 'recibido' => $recibido]
                );
                break;
            case 'tarjeta':
                $this->execute(
                    "INSERT INTO pago_tarjeta (id_pago, nombre_tarjeta, numero_tarjeta_mask, banco_emisor)
                     VALUES (:id_pago, :nombre, :numero, :banco)",
                    [
                        'id_pago' => $idPago,
                        'nombre' => $datos['nombre_tarjeta'] ?? null,
                        'numero' => $datos['numero_tarjeta_mask'] ?? null,
                        'banco' => $datos['banco_emisor'] ?? null,
                    ]
                );
                break;
            case 'transferencia':
                $this->execute(
                    "INSERT INTO pago_transferencia (id_pago, banco_origen, numero_operacion)
                     VALUES (:id_pago, :banco, :num_operacion)",
                    [
                        'id_pago' => $idPago,
                        'banco' => $datos['banco_origen'] ?? null,
                        'num_operacion' => $datos['numero_operacion'] ?? null,
                    ]
                );
                break;
        }
    }

    private function generarFactura(int $idVenta): void
    {
        // Obtener el siguiente número de factura
        $row = $this->fetch("SELECT COALESCE(MAX(nro_factura), 0) + 1 AS siguiente FROM factura");
        $nroFactura = (int) $row['siguiente'];

        // Generar campos derivados
        $nroAutorizacion = 'AUTH-' . str_pad($nroFactura, 8, '0', STR_PAD_LEFT);
        $codigoControl = substr(md5(uniqid(mt_rand(), true)), 0, 12);
        $cufd = 'CUFD-' . date('Ymd') . '-' . str_pad($nroFactura, 4, '0', STR_PAD_LEFT);

        $this->execute(
            "INSERT INTO factura (id_venta, nro_factura, nro_autorizacion, codigo_control,
                              nit_cliente, razon_social, fecha_emision, cufd, leyenda)
         VALUES (:id_venta, :nro_factura, :nro_autorizacion, :codigo_control,
                 '0000000', 'Cliente General', NOW(), :cufd,
                 'Factura generada automáticamente – Tienda Doña Elena')",
            [
                'id_venta' => $idVenta,
                'nro_factura' => $nroFactura,
                'nro_autorizacion' => $nroAutorizacion,
                'codigo_control' => $codigoControl,
                'cufd' => $cufd,
            ]
        );
    }

    // ── Consultas ─────────────────────────────────────────────────────────────

    /**
     * Últimas N ventas con nombre de cliente.
     */
    public function ventasRecientes(int $limite = 10): array
    {
        return $this->fetchAll(
            "SELECT v.id_venta, v.fecha, v.total,
                    CONCAT(p.nombre, ' ', p.apellido) AS cliente
             FROM venta v
             JOIN cliente c  ON v.id_cliente = c.id_cliente
             JOIN persona p  ON c.id_persona = p.id_persona
             ORDER BY v.fecha DESC
             LIMIT :limite",
            ['limite' => $limite]
        );
    }

    /**
     * Resumen de ventas de un período (para dashboard o reportes).
     */
    public function totalPorFecha(string $desde, string $hasta): float
    {
        $row = $this->fetch(
            "SELECT COALESCE(SUM(total), 0) AS total
             FROM venta
             WHERE fecha::date BETWEEN :desde AND :hasta",
            ['desde' => $desde, 'hasta' => $hasta]
        );
        return (float) ($row['total'] ?? 0);
    }

    /**
     * Obtiene todas las ventas con el nombre del cliente y del empleado.
     */
    public function obtenerTodasConDetalles(): array
    {
        return $this->fetchAll(
            "SELECT v.id_venta,
                v.fecha,
                v.total,
                CONCAT(pc.nombre, ' ', pc.apellido) AS cliente,
                CONCAT(pe.nombre, ' ', pe.apellido) AS empleado
         FROM venta v
         JOIN cliente c ON v.id_cliente = c.id_cliente
         JOIN persona pc ON c.id_persona = pc.id_persona
         JOIN empleado e ON v.id_empleado = e.id_empleado
         JOIN persona pe ON e.id_persona = pe.id_persona
         ORDER BY v.fecha DESC"
        );
    }
}