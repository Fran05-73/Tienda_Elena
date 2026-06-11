<?php
/**
 * Proveedor — Tienda Doña Elena
 *
 * Modelo para la tabla proveedor.
 * Incluye consultas con joins a persona y empresa.
 */
class Proveedor extends Model
{
    protected string $table      = 'proveedor';
    protected string $primaryKey = 'id_proveedor';

    /**
     * Obtiene todos los proveedores con los datos de persona y empresa.
     */
    public function obtenerTodosConDetalles(): array
    {
        $sql = "SELECT p.id_proveedor, p.cargo, p.estado,
                       pe.nombre AS persona_nombre, pe.apellido AS persona_apellido, pe.ci,
                       e.nombre AS empresa_nombre, e.nit
                FROM proveedor p
                JOIN persona pe ON p.id_persona = pe.id_persona
                JOIN empresa e ON p.id_empresa = e.id_empresa
                ORDER BY pe.apellido, pe.nombre";
        return $this->fetchAll($sql);
    }

    /**
     * Busca un proveedor por ID con los datos completos.
     */
    public function obtenerPorId(int $id): array|false
    {
        $sql = "SELECT p.*, pe.nombre AS persona_nombre, pe.apellido AS persona_apellido,
                       pe.ci, pe.correo, pe.telefono,
                       e.nombre AS empresa_nombre, e.nit, e.direccion, e.telefono_principal, e.email_principal
                FROM proveedor p
                JOIN persona pe ON p.id_persona = pe.id_persona
                JOIN empresa e ON p.id_empresa = e.id_empresa
                WHERE p.id_proveedor = :id";
        return $this->fetch($sql, ['id' => $id]);
    }

    /**
     * Crea un nuevo proveedor.
     */
    public function crearProveedor(array $data): int
    {
        return $this->create($data);
    }

    /**
     * Actualiza un proveedor existente.
     */
    public function actualizarProveedor(int $id, array $data): bool
    {
        return $this->update($id, $data);
    }

    /**
     * Elimina un proveedor.
     */
    public function eliminarProveedor(int $id): bool
    {
        return $this->delete($id);
    }
}