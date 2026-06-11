<?php
declare(strict_types=1);

require_once __DIR__ . '/../core/Model.php';

class Prediccion extends Model
{
    /**
     * Obtiene las predicciones para una semana específica con los datos actuales del producto.
     */
    public function obtenerPrediccionesSemanales(int $anio, int $semana): array
    {
        $sql = "SELECT p.id_producto, p.nombre, COALESCE(i.stock_actual, 0) AS stock,
                   pd.cantidad_predicha, pd.cantidad_sugerida
            FROM prediccion_demanda pd
            INNER JOIN producto p ON pd.id_producto = p.id_producto
            LEFT JOIN inventario i ON p.id_producto = i.id_producto
            WHERE pd.anio = :anio AND pd.semana = :semana
            ORDER BY p.nombre";

        return $this->fetchAll($sql, ['anio' => $anio, 'semana' => $semana]);
    }
}