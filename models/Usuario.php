<?php
/**
 * Usuario — Tienda Doña Elena
 *
 * Modelo adaptado a la nueva estructura:
 *   usuarios.id_empleado → empleado.id_empleado
 *   empleado.id_rol → roles.id_rol
 */
class Usuario extends Model
{
    protected string $table = 'usuarios';
    protected string $primaryKey = 'id_usuario';

    // ── Consultas de autenticación ──────────────────────────────────────────

    public function findByUsername(string $username): array|false
    {
        return $this->fetch(
            "SELECT * FROM usuarios WHERE username = :username",
            ['username' => $username]
        );
    }

    /**
     * Obtiene el rol del usuario usando la relación usuarios → empleado → roles.
     */
    public function getRoleByUserId(int $userId): ?string
    {
        $row = $this->fetch(
            "SELECT r.nombre
             FROM usuarios u
             JOIN empleado e ON u.id_empleado = e.id_empleado
             JOIN roles r ON e.id_rol = r.id_rol
             WHERE u.id_usuario = :id",
            ['id' => $userId]
        );
        return $row ? $row['nombre'] : null;
    }

    // ── Listado con nombre del empleado y rol ──────────────────────────────

    public function allWithRoles(): array
    {
        return $this->fetchAll(
            "SELECT u.id_usuario, u.username,
                    CONCAT(p.nombre, ' ', p.apellido) AS empleado_nombre,
                    r.nombre AS rol
             FROM usuarios u
             LEFT JOIN empleado e ON u.id_empleado = e.id_empleado
             LEFT JOIN persona p ON e.id_persona = p.id_persona
             LEFT JOIN roles r ON e.id_rol = r.id_rol
             ORDER BY u.username"
        );
    }

    public function getWithRole(int $id): array|false
    {
        return $this->fetch(
            "SELECT u.*,
                    CONCAT(p.nombre, ' ', p.apellido) AS empleado_nombre,
                    r.nombre AS rol,
                    e.id_empleado, e.id_rol
             FROM usuarios u
             LEFT JOIN empleado e ON u.id_empleado = e.id_empleado
             LEFT JOIN persona p ON e.id_persona = p.id_persona
             LEFT JOIN roles r ON e.id_rol = r.id_rol
             WHERE u.id_usuario = :id",
            ['id' => $id]
        );
    }

    // ── Verificación de existencia ─────────────────────────────────────────

    public function existsByUsername(string $username): bool
    {
        $row = $this->fetch(
            "SELECT id_usuario FROM usuarios WHERE username = :username",
            ['username' => $username]
        );
        return $row !== false;
    }

    // ── Escritura ───────────────────────────────────────────────────────────

    /**
     * Crea un usuario vinculado a un empleado.
     * $data debe contener: username, password_hash (texto plano), id_empleado.
     */
    public function createUser(array $data): int
    {
        if (isset($data['password_hash'])) {
            $data['password_hash'] = password_hash($data['password_hash'], PASSWORD_DEFAULT);
        }
        return $this->create($data);
    }

    /**
     * Actualiza datos del usuario (username, password, id_empleado).
     */
    public function updateUser(int $id, array $data): bool
    {
        if (isset($data['password_hash'])) {
            if ($data['password_hash'] !== '') {
                $data['password_hash'] = password_hash($data['password_hash'], PASSWORD_DEFAULT);
            } else {
                unset($data['password_hash']);
            }
        }
        return $this->update($id, $data);
    }

    public function updatePassword(int $id, string $newPassword): bool
    {
        $hash = password_hash($newPassword, PASSWORD_DEFAULT);
        return $this->execute(
            "UPDATE usuarios SET password_hash = :hash WHERE id_usuario = :id",
            ['hash' => $hash, 'id' => $id]
        );
    }
}