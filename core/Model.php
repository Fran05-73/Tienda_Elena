<?php
/**
 * Model — Tienda Doña Elena
 *
 * Clase base abstracta para todos los modelos.
 * Centraliza PDO y provee métodos reutilizables:
 *   fetch / fetchAll / execute / all / find / create / update / delete
 *   + soporte explícito de transacciones PostgreSQL.
 */
abstract class Model
{
    protected PDO    $pdo;
    protected string $table;
    protected string $primaryKey = 'id';

    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

    // ── Helpers de consulta ──────────────────────────────────────────────────

    /**
     * Ejecuta una consulta preparada y devuelve el PDOStatement.
     */
    protected function query(string $sql, array $params = []): \PDOStatement
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    /** Devuelve una sola fila o false. */
    public function fetch(string $sql, array $params = []): array|false
    {
        return $this->query($sql, $params)->fetch();
    }

    /** Devuelve todas las filas. */
    public function fetchAll(string $sql, array $params = []): array
    {
        return $this->query($sql, $params)->fetchAll();
    }

    /** Ejecuta INSERT/UPDATE/DELETE y devuelve bool. */
    public function execute(string $sql, array $params = []): bool
    {
        return $this->query($sql, $params)->rowCount() > 0;
    }

    // ── CRUD genérico ────────────────────────────────────────────────────────

    /** Todos los registros de la tabla. */
    public function all(): array
    {
        return $this->fetchAll("SELECT * FROM {$this->table}");
    }

    /** Busca por clave primaria. */
    public function find(int $id): array|false
    {
        return $this->fetch(
            "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :id",
            ['id' => $id]
        );
    }

    /**
     * Inserta una fila. Devuelve el ID generado.
     * Usa RETURNING para compatibilidad con PostgreSQL.
     */
    public function create(array $data): int
    {
        $columns      = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));

        $sql  = "INSERT INTO {$this->table} ($columns) VALUES ($placeholders)";
        $sql .= " RETURNING {$this->primaryKey}";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($data);
        $row = $stmt->fetch();

        return $row ? (int) $row[$this->primaryKey] : 0;
    }

    /**
     * Actualiza una fila por clave primaria.
     */
    public function update(int $id, array $data): bool
    {
        $setParts = [];
        foreach (array_keys($data) as $key) {
            $setParts[] = "$key = :$key";
        }
        $set        = implode(', ', $setParts);
        $data['id'] = $id;

        return $this->execute(
            "UPDATE {$this->table} SET $set WHERE {$this->primaryKey} = :id",
            $data
        );
    }

    /** Elimina una fila por clave primaria. */
    public function delete(int $id): bool
    {
        return $this->execute(
            "DELETE FROM {$this->table} WHERE {$this->primaryKey} = :id",
            ['id' => $id]
        );
    }

    // ── Transacciones PostgreSQL ─────────────────────────────────────────────

    public function beginTransaction(): void
    {
        $this->pdo->beginTransaction();
    }

    public function commit(): void
    {
        $this->pdo->commit();
    }

    public function rollback(): void
    {
        if ($this->pdo->inTransaction()) {
            $this->pdo->rollBack();
        }
    }
}
