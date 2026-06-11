<h2 class="app-card-title">✏️ Editar Teléfono</h2>

<div class="app-card">
    <form action="/telefonos/actualizar/<?= (int) $telefonoData['id'] ?>" method="POST">
        <!-- Propietario actual (solo lectura) -->
        <div class="mb-3">
            <label class="form-label">Propietario</label>
            <?php if ($telefonoData['origen'] === 'persona'): ?>
                <?php
                $nombrePersona = '';
                foreach ($personas as $p) {
                    if ((int)$p['id_persona'] === (int)$telefonoData['owner_id']) {
                        $nombrePersona = $p['nombre_completo'] . ' (CI: ' . $p['ci'] . ')';
                        break;
                    }
                }
                ?>
                <input type="text" class="form-control" value="<?= Helpers::e($nombrePersona) ?>" disabled>
            <?php else: ?>
                <?php
                $nombreEmpresa = '';
                foreach ($empresas as $e) {
                    if ((int)$e['id_empresa'] === (int)$telefonoData['id_empresa']) {
                        $nombreEmpresa = $e['nombre'] . ' (NIT: ' . $e['nit'] . ')';
                        break;
                    }
                }
                ?>
                <input type="text" class="form-control" value="<?= Helpers::e($nombreEmpresa) ?>" disabled>
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <label for="numero" class="form-label">Número <span class="text-danger">*</span></label>
            <input type="text" name="numero" id="numero" class="form-control" required
                   value="<?= Helpers::e($telefonoData['numero']) ?>">
        </div>

        <div class="mb-3">
            <label for="tipo" class="form-label">Tipo (opcional)</label>
            <input type="text" name="tipo" id="tipo" class="form-control"
                   value="<?= Helpers::e($telefonoData['tipo'] ?? '') ?>">
        </div>

        <div class="d-flex justify-content-between">
            <a href="/telefonos" class="btn btn-secondary">Cancelar</a>
            <button type="submit" class="btn-app-primary">Actualizar Teléfono</button>
        </div>
    </form>
</div>