<?php
/**
 * Rol — Tienda Doña Elena
 *
 * Modelo para la tabla roles.
 * CRUD sencillo: solo nombre.
 */
class Rol extends Model
{
    protected string $table      = 'roles';
    protected string $primaryKey = 'id_rol';

    /**
     * Obtiene todos los roles ordenados por nombre.
     */
    public function obtenerTodos(): array
    {
        return $this->fetchAll("SELECT * FROM roles ORDER BY nombre");
    }

    /**
     * Busca un rol por ID.
     */
    public function obtenerPorId(int $id): array|false
    {
        return $this->find($id);
    }

    /**
     * Crea un nuevo rol.
     */
    public function crearRol(array $data): int
    {
        return $this->create($data);
    }

    /**
     * Actualiza un rol existente.
     */
    public function actualizarRol(int $id, array $data): bool
    {
        return $this->update($id, $data);
    }

    /**
     * Elimina un rol.
     */
    public function eliminarRol(int $id): bool
    {
        return $this->delete($id);
    }
}