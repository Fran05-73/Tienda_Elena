<?php
class EmailEmpresa extends Model
{
    protected string $table      = 'email_empresa';
    protected string $primaryKey = 'id_email_empresa';

    public function crearEmailEmpresa(array $data): int
    {
        // El dato debe incluir 'id_email_empresa' generado con la secuencia
        return $this->create($data);
    }

    public function actualizarEmailEmpresa(int $id, array $data): bool
    {
        return $this->update($id, $data);
    }

    public function eliminarEmailEmpresa(int $id): bool
    {
        return $this->delete($id);
    }
}