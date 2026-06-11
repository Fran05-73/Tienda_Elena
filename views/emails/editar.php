<h2 class="app-card-title">✏️ Editar Correo</h2>

<div class="app-card">
    <form action="/emails/actualizar/<?= (int) $emailData['id'] ?>" method="POST">
        <!-- Propietario actual (no editable) -->
        <div class="mb-3">
            <label class="form-label">Propietario</label>
            <?php if ($emailData['origen'] === 'persona'): ?>
                <?php
                // Buscamos el nombre de la persona (ya teníamos la lista en $personas)
                $nombrePersona = '';
                foreach ($personas as $p) {
                    if ((int)$p['id_persona'] === (int)$emailData['owner_id']) {
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
                    if ((int)$e['id_empresa'] === (int)$emailData['id_empresa']) {
                        $nombreEmpresa = $e['nombre'] . ' (NIT: ' . $e['nit'] . ')';
                        break;
                    }
                }
                ?>
                <input type="text" class="form-control" value="<?= Helpers::e($nombreEmpresa) ?>" disabled>
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <label for="correo" class="form-label">Correo electrónico <span class="text-danger">*</span></label>
            <input type="email" name="correo" id="correo" class="form-control" required
                   value="<?= Helpers::e($emailData['correo']) ?>">
        </div>

        <div class="mb-3">
            <label for="tipo" class="form-label">Tipo (opcional)</label>
            <input type="text" name="tipo" id="tipo" class="form-control"
                   value="<?= Helpers::e($emailData['tipo'] ?? '') ?>">
        </div>

        <div class="d-flex justify-content-between">
            <a href="/emails" class="btn btn-secondary">Cancelar</a>
            <button type="submit" class="btn-app-primary">Actualizar Correo</button>
        </div>
    </form>
</div>