<?php
class TelefonoEmpresa extends Model
{
    protected string $table      = 'telefono_empresa';
    protected string $primaryKey = 'id_telefono_empresa';

    public function crearTelefonoEmpresa(array $data): int
    {
        // $data debe incluir 'id_telefono_empresa' generado manualmente
        return $this->create($data);
    }

    public function actualizarTelefonoEmpresa(int $id, array $data): bool
    {
        return $this->update($id, $data);
    }

    public function eliminarTelefonoEmpresa(int $id): bool
    {
        return $this->delete($id);
    }
}