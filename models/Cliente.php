<?php
/**
 * Cliente — Tienda Doña Elena
 *
 * Modelo para la tabla cliente.
 * Gestiona la creación conjunta de persona + cliente.
 */
class Cliente extends Model
{
    protected string $table = 'cliente';
    protected string $primaryKey = 'id_cliente';

    /**
     * Obtiene todos los clientes con los datos de persona.
     */
    public function obtenerTodosConPersona(): array
    {
        $sql = "SELECT c.id_cliente, c.direccion, c.nacionalidad, c.preferencias,
                       p.nombre, p.apellido, p.ci, p.correo, p.telefono, p.fecha_registro
                FROM cliente c
                JOIN persona p ON c.id_persona = p.id_persona
                ORDER BY p.apellido, p.nombre";
        return $this->fetchAll($sql);
    }

    /**
     * Busca un cliente por ID con los datos de persona.
     */
    public function obtenerPorId(int $id): array|false
    {
        $sql = "SELECT c.*, p.nombre, p.apellido, p.ci, p.correo, p.telefono
                FROM cliente c
                JOIN persona p ON c.id_persona = p.id_persona
                WHERE c.id_cliente = :id";
        return $this->fetch($sql, ['id' => $id]);
    }

    /**
     * Crea un cliente y su persona asociada en una transacción.
     * $data debe contener los campos de persona y cliente.
     */
    public function crearClienteConPersona(array $data): int
    {
        $this->beginTransaction();

        try {
            // 1. Insertar persona y obtener el ID real
            $stmt = $this->pdo->prepare(
                "INSERT INTO persona (nombre, apellido, ci, correo, telefono)
             VALUES (:nombre, :apellido, :ci, :correo, :telefono)
             RETURNING id_persona"
            );
            $stmt->execute([
                'nombre' => $data['nombre'],
                'apellido' => $data['apellido'],
                'ci' => $data['ci'],
                'correo' => $data['correo'] ?? '',
                'telefono' => $data['telefono'] ?? '',
            ]);
            $id_persona = (int) $stmt->fetchColumn();

            // 2. Insertar cliente con el ID de persona correcto
            $stmt = $this->pdo->prepare(
                "INSERT INTO cliente (id_persona, direccion, nacionalidad, preferencias)
             VALUES (:id_persona, :direccion, :nacionalidad, :preferencias)
             RETURNING id_cliente"
            );
            $stmt->execute([
                'id_persona' => $id_persona,
                'direccion' => $data['direccion'] ?? '',
                'nacionalidad' => $data['nacionalidad'] ?? '',
                'preferencias' => $data['preferencias'] ?? '',
            ]);
            $id_cliente = (int) $stmt->fetchColumn();

            $this->commit();
            return $id_cliente;

        } catch (\Exception $e) {
            $this->rollback();
            throw $e;
        }
    }

    /**
     * Actualiza un cliente y su persona asociada.
     */
    public function actualizarClienteConPersona(int $idCliente, array $data): bool
    {
        $this->pdo->beginTransaction();
        try {
            // Obtener id_persona del cliente
            $cliente = $this->find($idCliente);
            if (!$cliente) {
                throw new \Exception('Cliente no encontrado.');
            }
            $idPersona = $cliente['id_persona'];

            // Actualizar persona
            $stmt = $this->pdo->prepare(
                "UPDATE persona SET nombre = :nombre, apellido = :apellido, ci = :ci,
                     correo = :correo, telefono = :telefono
                 WHERE id_persona = :id"
            );
            $stmt->execute([
                'nombre' => $data['nombre'],
                'apellido' => $data['apellido'],
                'ci' => $data['ci'],
                'correo' => $data['correo'] ?? null,
                'telefono' => $data['telefono'] ?? null,
                'id' => $idPersona,
            ]);

            // Actualizar cliente
            $stmt = $this->pdo->prepare(
                "UPDATE cliente SET direccion = :direccion, nacionalidad = :nacionalidad,
                     preferencias = :preferencias
                 WHERE id_cliente = :id"
            );
            $stmt->execute([
                'direccion' => $data['direccion'] ?? null,
                'nacionalidad' => $data['nacionalidad'] ?? null,
                'preferencias' => $data['preferencias'] ?? null,
                'id' => $idCliente,
            ]);

            $this->pdo->commit();
            return true;
        } catch (\Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    /**
     * Elimina un cliente y su persona asociada (si se desea).
     * Por ahora solo elimina el cliente; la persona queda.
     */
    public function eliminarCliente(int $id): bool
    {
        return $this->delete($id);
    }
}