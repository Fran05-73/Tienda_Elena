<?php
class EmailPersona extends Model
{
    protected string $table      = 'email_persona';
    protected string $primaryKey = 'id_email_persona';

    public function crearEmailPersona(array $data): int
    {
        return $this->create($data);
    }

    public function actualizarEmailPersona(int $id, array $data): bool
    {
        return $this->update($id, $data);
    }

    public function eliminarEmailPersona(int $id): bool
    {
        return $this->delete($id);
    }
}