<?php
/**
 * Empresa — Modelo para la tabla empresa
 */
class Empresa extends Model
{
    protected string $table      = 'empresa';
    protected string $primaryKey = 'id_empresa';

    /**
     * Obtiene todas las empresas ordenadas por nombre.
     */
    public function obtenerTodas(): array
    {
        $sql = "SELECT * FROM {$this->table} ORDER BY nombre";
        return $this->fetchAll($sql);
    }

    /**
     * Busca una empresa por su ID.
     */
    public function obtenerPorId(int $id): array|false
    {
        return $this->find($id);
    }

    /**
     * Crea una nueva empresa.
     */
    public function crearEmpresa(array $data): int
    {
        return $this->create($data);
    }

    /**
     * Actualiza una empresa existente.
     */
    public function actualizarEmpresa(int $id, array $data): bool
    {
        return $this->update($id, $data);
    }

    /**
     * Elimina una empresa.
     */
    public function eliminarEmpresa(int $id): bool
    {
        return $this->delete($id);
    }
}