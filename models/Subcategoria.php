<?php
/**
 * Subcategoria — Tienda Doña Elena
 *
 * Modelo para la tabla subcategoria.
 * Extiende la clase base Model para heredar CRUD genérico.
 */
class Subcategoria extends Model
{
    protected string $table      = 'subcategoria';
    protected string $primaryKey = 'id_subcategoria';

    /**
     * Obtiene todas las subcategorías con el nombre de la categoría padre.
     */
    public function obtenerTodasConCategoria(): array
    {
        $sql = "SELECT s.id_subcategoria, s.nombre AS subcategoria, s.descripcion,
                       c.nombre AS categoria, s.id_categoria
                FROM subcategoria s
                JOIN categoria c ON s.id_categoria = c.id_categoria
                ORDER BY c.nombre, s.nombre";
        return $this->fetchAll($sql);
    }

    /**
     * Busca una subcategoría por ID, incluyendo su categoría padre.
     */
    public function obtenerPorId(int $id): array|false
    {
        $sql = "SELECT s.*, c.nombre AS categoria_nombre
                FROM subcategoria s
                JOIN categoria c ON s.id_categoria = c.id_categoria
                WHERE s.id_subcategoria = :id";
        return $this->fetch($sql, ['id' => $id]);
    }

    /**
     * Crea una nueva subcategoría.
     */
    public function crearSubcategoria(array $data): int
    {
        return $this->create($data);
    }

    /**
     * Actualiza una subcategoría existente.
     */
    public function actualizarSubcategoria(int $id, array $data): bool
    {
        return $this->update($id, $data);
    }

    /**
     * Elimina una subcategoría.
     */
    public function eliminarSubcategoria(int $id): bool
    {
        return $this->delete($id);
    }
}