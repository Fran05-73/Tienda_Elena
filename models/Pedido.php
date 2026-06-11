<?php
declare(strict_types=1);
require_once __DIR__ . '/../core/Model.php';

class Pedido extends Model
{
    /**
     * Obtiene todas las sugerencias pendientes con datos del producto y la demanda predicha.
     * Se une con prediccion_demanda para obtener la cantidad_predicha de la semana actual.
     */
    public function obtenerSugerenciasPendientes(int $anio, int $semana): array
    {
        $sql = "SELECT ps.id AS id_pedido, p.id_producto, p.nombre,
                   COALESCE(i.stock_actual, 0) AS stock_actual,
                   pd.cantidad_predicha,
                   ps.cantidad_sugerida
            FROM pedidos_sugeridos ps
            JOIN producto p ON ps.producto_id = p.id_producto
            LEFT JOIN inventario i ON p.id_producto = i.id_producto
            LEFT JOIN prediccion_demanda pd ON pd.id_producto = p.id_producto
                   AND pd.anio = :anio AND pd.semana = :semana
            WHERE ps.estado = 'pendiente'
            ORDER BY p.nombre";


        return $this->fetchAll($sql, ['anio' => $anio, 'semana' => $semana]);
    }

    /**
     * Procesa la confirmación de abastecimiento.
     * Recibe array de ['id_pedido' => x, 'id_producto' => y, 'cantidad' => z].
     * En una transacción:
     *   - Obtiene stock actual de inventario
     *   - Actualiza inventario.stock_actual
     *   - Registra movimiento de entrada
     *   - Marca el pedido_sugerido como 'realizado'
     */
    public function abastecerMultiple(array $items): void
    {
        $this->pdo->beginTransaction();
        try {
            $sqlUpdateStock = "UPDATE inventario SET stock_actual = stock_actual + :cant, ultima_actualizacion = NOW()
                               WHERE id_producto = :id";
            $sqlInsertMov = "INSERT INTO movimientos_inventario
                             (id_producto, tipo, cantidad, fecha, observacion, stock_anterior, stock_nuevo)
                             VALUES (:id, 'entrada', :cant, NOW(), 'Abastecimiento desde pedido sugerido', :stock_ant, :stock_nuevo)";
            $sqlUpdatePedido = "UPDATE pedidos_sugeridos SET estado = 'realizado' WHERE id = :id";

            $stmtStock = $this->pdo->prepare($sqlUpdateStock);
            $stmtMov = $this->pdo->prepare($sqlInsertMov);
            $stmtPed = $this->pdo->prepare($sqlUpdatePedido);

            foreach ($items as $item) {
                $idProducto = (int)$item['id_producto'];
                $cantidad   = (int)$item['cantidad'];
                $idPedido   = (int)$item['id_pedido'];

                // Leer stock actual
                $row = $this->fetch("SELECT stock_actual FROM inventario WHERE id_producto = :id FOR UPDATE",
                                    ['id' => $idProducto]);
                $stockAnterior = $row ? (int)$row['stock_actual'] : 0;
                $stockNuevo = $stockAnterior + $cantidad;

                // Asegurar que existe inventario
                if (!$row) {
                    $this->execute("INSERT INTO inventario (id_producto, stock_actual) VALUES (:id, :cant) ON CONFLICT DO NOTHING",
                                   ['id' => $idProducto, 'cant' => $stockNuevo]);
                } else {
                    $stmtStock->execute(['cant' => $cantidad, 'id' => $idProducto]);
                }

                // Registrar movimiento
                $stmtMov->execute([
                    'id' => $idProducto,
                    'cant' => $cantidad,
                    'stock_ant' => $stockAnterior,
                    'stock_nuevo' => $stockNuevo
                ]);

                // Marcar pedido como realizado
                $stmtPed->execute(['id' => $idPedido]);
            }

            $this->pdo->commit();
        } catch (\PDOException $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }
}