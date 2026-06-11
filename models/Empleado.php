<?php
/**
 * Empleado — Tienda Doña Elena
 *
 * Modelo para la tabla empleado.
 * Gestiona la creación y actualización conjunta con persona.
 */
class Empleado extends Model
{
    protected string $table = 'empleado';
    protected string $primaryKey = 'id_empleado';

    /**
     * Obtiene todos los empleados con datos de persona y rol.
     */
    public function obtenerTodosConDetalles(): array
    {
        $sql = "SELECT e.id_empleado, e.salario, e.fecha_contratacion,
                       p.nombre, p.apellido, p.ci, p.correo, p.telefono,
                       r.nombre AS rol
                FROM empleado e
                JOIN persona p ON e.id_persona = p.id_persona
                JOIN roles r ON e.id_rol = r.id_rol
                ORDER BY p.apellido, p.nombre";
        return $this->fetchAll($sql);
    }

    /**
     * Busca un empleado por ID con datos completos.
     */
    public function obtenerPorId(int $id): array|false
    {
        $sql = "SELECT e.*, p.nombre, p.apellido, p.ci, p.correo, p.telefono,
                       r.nombre AS rol
                FROM empleado e
                JOIN persona p ON e.id_persona = p.id_persona
                JOIN roles r ON e.id_rol = r.id_rol
                WHERE e.id_empleado = :id";
        return $this->fetch($sql, ['id' => $id]);
    }

    /**
     * Crea un nuevo empleado junto con su persona.
     * Retorna el id_empleado generado.
     */
    public function crearEmpleadoConPersona(array $data): int
    {
        $this->beginTransaction();

        try {
            // 1. Insertar persona
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

            // 2. Insertar empleado
            $stmt = $this->pdo->prepare(
                "INSERT INTO empleado (id_persona, id_rol, salario, fecha_contratacion)
             VALUES (:id_persona, :id_rol, :salario, :fecha_contratacion)
             RETURNING id_empleado"
            );
            $stmt->execute([
                'id_persona' => $id_persona,
                'id_rol' => $data['id_rol'],
                'salario' => $data['salario'],
                'fecha_contratacion' => $data['fecha_contratacion'] ?? date('Y-m-d'),
            ]);
            $id_empleado = (int) $stmt->fetchColumn();

            $this->commit();
            return $id_empleado;

        } catch (\Exception $e) {
            $this->rollback();
            throw $e;
        }
    }

    /**
     * Actualiza un empleado y su persona asociada.
     */
    public function actualizarEmpleadoConPersona(int $idEmpleado, array $data): bool
    {
        $this->pdo->beginTransaction();
        try {
            $empleado = $this->find($idEmpleado);
            if (!$empleado) {
                throw new \Exception('Empleado no encontrado.');
            }
            $idPersona = $empleado['id_persona'];

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

            // Actualizar empleado
            $stmt = $this->pdo->prepare(
                "UPDATE empleado SET salario = :salario, fecha_contratacion = :fecha_contratacion,
                     id_rol = :id_rol
                 WHERE id_empleado = :id"
            );
            $stmt->execute([
                'salario' => $data['salario'],
                'fecha_contratacion' => $data['fecha_contratacion'],
                'id_rol' => $data['id_rol'],
                'id' => $idEmpleado,
            ]);

            $this->pdo->commit();
            return true;
        } catch (\Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    /**
     * Elimina un empleado (solo el registro de empleado, no la persona).
     */
    public function eliminarEmpleado(int $id): bool
    {
        return $this->delete($id);
    }
}