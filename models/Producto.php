<?php
/**
 * Producto — Tienda Doña Elena
 *
 * Modelo de producto. Extiende Model base.
 * Ahora el stock se obtiene desde la tabla inventario.
 */
class Producto extends Model
{
    protected string $table = 'producto';
    protected string $primaryKey = 'id_producto';

    // ── Consultas ─────────────────────────────────────────────────────────────

    /**
     * Todos los productos con JOIN de subcategoría, categoría, proveedor,
     * magnitud y stock desde inventario.
     */
    public function allWithDetails(): array
    {
        $sql = "SELECT p.*,
                   c.nombre  AS categoria,
                   s.nombre  AS subcategoria,
                   e.nombre  AS proveedor,
                   m.nombre  AS magnitud,
                   COALESCE(i.stock_actual, 0) AS stock_actual,
                   i.stock_minimo,
                   i.stock_maximo
            FROM producto p
            LEFT JOIN subcategoria s  ON p.id_subcategoria = s.id_subcategoria
            LEFT JOIN categoria c     ON s.id_categoria    = c.id_categoria
            LEFT JOIN proveedor pr    ON p.id_proveedor    = pr.id_proveedor
            LEFT JOIN empresa e       ON pr.id_empresa     = e.id_empresa
            LEFT JOIN magnitud m      ON p.id_magnitud     = m.id_magnitud
            LEFT JOIN inventario i    ON p.id_producto     = i.id_producto
            ORDER BY p.nombre ASC";

        return $this->fetchAll($sql);
    }

    /**
     * Un producto con detalles y stock.
     */
    public function findWithDetails(int $id): array|false
    {
        $sql = "SELECT p.*,
                   c.nombre  AS categoria,
                   s.nombre  AS subcategoria,
                   e.nombre  AS proveedor,
                   m.nombre  AS magnitud,
                   COALESCE(i.stock_actual, 0) AS stock_actual,
                   i.stock_minimo,
                   i.stock_maximo
            FROM producto p
            LEFT JOIN subcategoria s  ON p.id_subcategoria = s.id_subcategoria
            LEFT JOIN categoria c     ON s.id_categoria    = c.id_categoria
            LEFT JOIN proveedor pr    ON p.id_proveedor    = pr.id_proveedor
            LEFT JOIN empresa e       ON pr.id_empresa     = e.id_empresa
            LEFT JOIN magnitud m      ON p.id_magnitud     = m.id_magnitud
            LEFT JOIN inventario i    ON p.id_producto     = i.id_producto
            WHERE p.id_producto = :id";

        return $this->fetch($sql, ['id' => $id]);
    }

    /**
     * Busca por SKU exacto. Útil para verificar duplicados.
     */
    public function findBySKU(string $sku): array|false
    {
        return $this->fetch(
            "SELECT * FROM producto WHERE sku = :sku",
            ['sku' => $sku]
        );
    }

    /**
     * Productos con fecha de vencimiento pasada.
     */
    public function expirados(): array
    {
        return $this->fetchAll(
            "SELECT * FROM producto
         WHERE fecha_vencimiento IS NOT NULL
           AND fecha_vencimiento < CURRENT_DATE
         ORDER BY fecha_vencimiento ASC"
        );
    }

    /**
     * Búsqueda para POS (ILIKE es case-insensitive en PostgreSQL).
     */
    public function search(string $term): array
    {
        $sql = "SELECT p.id_producto, p.sku, p.nombre, p.precio,
                   p.codigo_barras, m.nombre AS magnitud, p.valor_magnitud,
                   COALESCE(i.stock_actual, 0) AS stock
            FROM producto p
            LEFT JOIN magnitud m ON p.id_magnitud = m.id_magnitud
            LEFT JOIN inventario i ON p.id_producto = i.id_producto
            WHERE p.nombre        ILIKE :term
               OR p.sku           ILIKE :term
               OR p.codigo_barras ILIKE :term
            ORDER BY p.nombre
            LIMIT 20";

        return $this->fetchAll($sql, ['term' => "%{$term}%"]);
    }

    public function lowStock(int $umbral = 5): array
    {
        return $this->fetchAll(
            "SELECT p.*, i.stock_actual AS stock
         FROM producto p
         JOIN inventario i ON p.id_producto = i.id_producto
         WHERE i.stock_actual < :umbral
         ORDER BY i.stock_actual ASC",
            ['umbral' => $umbral]
        );
    }
}