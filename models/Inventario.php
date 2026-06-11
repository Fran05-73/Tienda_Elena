<?php
/**
 * Inventario — Tienda Doña Elena
 *
 * Modelo para movimientos de inventario.
 * Ahora trabaja contra inventario.stock_actual y registra
 * stock_anterior / stock_nuevo automáticamente.
 */
class Inventario extends Model
{
    protected string $table = 'movimientos_inventario';
    protected string $primaryKey = 'id_movimiento';

    /**
     * Registra un movimiento y actualiza inventario.stock_actual.
     */
    public function registrarMovimiento(
        int $productoId,
        string $tipo,
        int $cantidad,
        string $observacion = '',
        ?int $idEmpleado = null
    ): void {
        $this->beginTransaction();

        try {
            // Obtener stock actual desde inventario
            $row = $this->fetch(
                "SELECT stock_actual FROM inventario WHERE id_producto = :id FOR UPDATE",
                ['id' => $productoId]
            );

            if (!$row) {
                throw new \RuntimeException('El producto no tiene registro de inventario.');
            }

            $stockAnterior = (int) $row['stock_actual'];

            // Validar stock suficiente para salida
            if ($tipo === 'salida' && $stockAnterior < $cantidad) {
                throw new \RuntimeException("Stock insuficiente. Disponible: {$stockAnterior}.");
            }

            // Calcular nuevo stock
            $stockNuevo = $tipo === 'entrada'
                ? $stockAnterior + $cantidad
                : $stockAnterior - $cantidad;

            // Actualizar inventario
            $this->execute(
                "UPDATE inventario SET stock_actual = :nuevo, ultima_actualizacion = NOW()
                 WHERE id_producto = :id",
                ['nuevo' => $stockNuevo, 'id' => $productoId]
            );

            // Insertar movimiento con stock_anterior y stock_nuevo
            $this->create([
                'id_producto' => $productoId,
                'tipo' => $tipo,
                'cantidad' => $cantidad,
                'fecha' => date('Y-m-d H:i:s'),
                'observacion' => $observacion,
                'stock_anterior' => $stockAnterior,
                'stock_nuevo' => $stockNuevo,
                'id_empleado' => $idEmpleado,  // ya llega del controlador
            ]);

            $this->commit();
        } catch (\Exception $e) {
            $this->rollback();
            throw $e;
        }
    }

    /**
     * Historial de movimientos con nombre de producto, empleado.
     */
    public function movimientos(int $limite = 200): array
    {
        return $this->fetchAll(
            "SELECT m.*, p.nombre AS producto, p.sku,
                    CONCAT(pe.nombre, ' ', pe.apellido) AS empleado
             FROM movimientos_inventario m
             JOIN producto p ON m.id_producto = p.id_producto
             LEFT JOIN empleado e ON m.id_empleado = e.id_empleado
             LEFT JOIN persona pe ON e.id_persona = pe.id_persona
             ORDER BY m.fecha DESC
             LIMIT :limite",
            ['limite' => $limite]
        );
    }

    /**
     * Obtener todos los productos con su stock actual desde inventario.
     */
    public function allWithStock(): array
    {
        return $this->fetchAll(
            "SELECT p.id_producto, p.sku, p.codigo_barras, p.nombre, p.descripcion,
                p.precio, p.valor_magnitud, p.fecha_vencimiento, p.fecha_registro,
                p.peso_bruto, p.temperatura_almacenamiento, p.tipo_producto,
                p.codigo_visible,
                i.stock_actual, i.stock_minimo, i.stock_maximo,
                i.punto_reorden, i.ubicacion, i.ultima_actualizacion,
                s.nombre AS subcategoria,
                c.nombre AS categoria,
                m.nombre AS magnitud, m.abreviatura AS magnitud_abrev,
                CONCAT(per.nombre, ' ', per.apellido) AS proveedor,
                emp.nombre AS empresa_proveedor
         FROM producto p
         JOIN inventario i ON p.id_producto = i.id_producto
         LEFT JOIN subcategoria s ON p.id_subcategoria = s.id_subcategoria
         LEFT JOIN categoria c ON s.id_categoria = c.id_categoria
         LEFT JOIN magnitud m ON p.id_magnitud = m.id_magnitud
         LEFT JOIN proveedor prov ON p.id_proveedor = prov.id_proveedor
         LEFT JOIN persona per ON prov.id_persona = per.id_persona
         LEFT JOIN empresa emp ON prov.id_empresa = emp.id_empresa
         ORDER BY p.nombre"
        );
    }

    /**
     * Productos con bajo stock (según vista o directo).
     */
    public function bajoStock(int $limite = 10): array
    {
        return $this->fetchAll(
            "SELECT * FROM vista_productos_bajo_stock LIMIT :limite",
            ['limite' => $limite]
        );
    }

    /**
     * Productos vencidos (se mantiene desde producto).
     */
    public function expirados(): array
    {
        return $this->fetchAll(
            "SELECT * FROM producto WHERE fecha_vencimiento < CURRENT_DATE"
        );
    }
}