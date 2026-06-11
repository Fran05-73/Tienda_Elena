<?php
/**
 * Auditoria — Modelo para la tabla auditoria
 */
class Auditoria extends Model
{
    protected string $table      = 'auditoria';
    protected string $primaryKey = 'id';

    /**
     * Obtiene todos los registros de auditoría con filtros opcionales.
     */
    public function obtenerTodas(string $tabla = '', string $accion = ''): array
    {
        $sql = "SELECT id, tabla_afectada, accion, usuario_bd, id_registro,
                       datos_anteriores, datos_nuevos, fecha
                FROM auditoria
                WHERE 1=1";
        
        $params = [];

        if (!empty($tabla)) {
            $sql .= " AND tabla_afectada = :tabla";
            $params['tabla'] = $tabla;
        }

        if (!empty($accion)) {
            $sql .= " AND accion = :accion";
            $params['accion'] = $accion;
        }

        $sql .= " ORDER BY fecha DESC LIMIT 500";

        return $this->fetchAll($sql, $params);
    }

    /**
     * Obtiene las tablas únicas registradas en auditoría para los filtros.
     */
    public function obtenerTablasUnicas(): array
    {
        return $this->fetchAll(
            "SELECT DISTINCT tabla_afectada AS nombre
             FROM auditoria
             ORDER BY tabla_afectada"
        );
    }
}