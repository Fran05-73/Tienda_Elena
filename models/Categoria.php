<?php
/**
 * Categoria — Tienda Doña Elena
 *
 * Modelo de categorías, subcategorías, tipos de categoría, proveedores y magnitudes.
 * Se usa principalmente para poblar selects en formularios.
 */
class Categoria extends Model
{
    protected string $table      = 'categoria';
    protected string $primaryKey = 'id_categoria';

    /**
     * Subcategorías de una categoría específica.
     */
    public function subcategorias(int $categoriaId): array
    {
        return $this->fetchAll(
            "SELECT * FROM subcategoria WHERE id_categoria = :id ORDER BY nombre",
            ['id' => $categoriaId]
        );
    }

    /**
     * Todas las subcategorías con el nombre de su categoría padre.
     * Usada para el select de productos.
     */
    public function allSubcategorias(): array
    {
        return $this->fetchAll(
            "SELECT s.id_subcategoria, s.nombre,
                    c.nombre AS categoria_nombre, c.id_categoria
             FROM subcategoria s
             JOIN categoria c ON s.id_categoria = c.id_categoria
             ORDER BY c.nombre, s.nombre"
        );
    }

    /**
     * Todas las categorías activas (por defecto).
     */
    public function allCategorias(): array
    {
        return $this->fetchAll("SELECT * FROM categoria ORDER BY nombre");
    }

    /**
     * Tipos de una categoría (según la nueva tabla tipo_categoria).
     */
    public function tiposPorCategoria(int $categoriaId): array
    {
        return $this->fetchAll(
            "SELECT * FROM tipo_categoria WHERE id_categoria = :id ORDER BY tipo",
            ['id' => $categoriaId]
        );
    }

    /**
     * Obtiene todas las categorías para el listado del CRUD.
     */
    public function obtenerTodas(): array
    {
        return $this->fetchAll(
            "SELECT * FROM categoria ORDER BY nombre ASC"
        );
    }

    /**
     * Busca una categoría por ID (para edición).
     */
    public function obtenerPorId(int $id): array|false
    {
        return $this->find($id);
    }

    /**
     * Crea una nueva categoría.
     * Retorna el ID insertado.
     */
    public function crearCategoria(array $data): int
    {
        // La fecha de creación se asigna automáticamente en PostgreSQL
        return $this->create($data);
    }

    /**
     * Actualiza una categoría existente.
     */
    public function actualizarCategoria(int $id, array $data): bool
    {
        return $this->update($id, $data);
    }

    /**
     * Elimina una categoría (soft o hard). Aquí se hace hard delete.
     * Si quieres soft delete, cambia a UPDATE estado = 'inactiva'.
     */
    public function eliminarCategoria(int $id): bool
    {
        return $this->delete($id);
    }
}