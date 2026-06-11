<?php
/**
 * Magnitud — Modelo para la tabla magnitud
 */
class Magnitud extends Model
{
    protected string $table      = 'magnitud';
    protected string $primaryKey = 'id_magnitud';

    /**
     * Obtiene todas las magnitudes ordenadas por nombre.
     */
    public function obtenerTodas(): array
    {
        $sql = "SELECT * FROM {$this->table} ORDER BY nombre";
        return $this->fetchAll($sql);
    }

    /**
     * Busca una magnitud por su ID.
     */
    public function obtenerPorId(int $id): array|false
    {
        return $this->find($id);
    }

    /**
     * Crea una nueva magnitud.
     */
    public function crearMagnitud(array $data): int
    {
        return $this->create($data);
    }

    /**
     * Actualiza una magnitud existente.
     */
    public function actualizarMagnitud(int $id, array $data): bool
    {
        return $this->update($id, $data);
    }

    /**
     * Elimina una magnitud.
     */
    public function eliminarMagnitud(int $id): bool
    {
        return $this->delete($id);
    }
}