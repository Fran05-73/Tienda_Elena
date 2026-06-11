<?php
class TelefonoPersona extends Model
{
    protected string $table      = 'telefono_persona';
    protected string $primaryKey = 'id_telefono_persona';

    public function crearTelefonoPersona(array $data): int
    {
        return $this->create($data);
    }

    public function actualizarTelefonoPersona(int $id, array $data): bool
    {
        return $this->update($id, $data);
    }

    public function eliminarTelefonoPersona(int $id): bool
    {
        return $this->delete($id);
    }
}