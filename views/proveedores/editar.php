<div class="app-card" style="max-width:700px">
    <div class="app-card-header">
        <span class="app-card-title">✏ Editar Proveedor</span>
        <a href="/proveedores" class="btn-app-secondary" style="font-size:.85rem">← Volver</a>
    </div>

    <form action="/proveedores/actualizar/<?= (int) $proveedor['id_proveedor'] ?>" method="POST" novalidate>

        <div class="form-group mb-3">
            <label class="form-label">Persona <span class="required">*</span></label>
            <select name="id_persona" class="form-select" required>
                <option value="">Seleccionar persona...</option>
                <?php foreach ($personas as $per): ?>
                    <option value="<?= (int) $per['id_persona'] ?>" <?= (int) $per['id_persona'] === (int) $proveedor['id_persona'] ? 'selected' : '' ?>>
                        <?= Helpers::e($per['apellido'] . ' ' . $per['nombre']) ?> (CI: <?= Helpers::e($per['ci']) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group mb-3">
            <label class="form-label">Empresa <span class="required">*</span></label>
            <select name="id_empresa" class="form-select" required>
                <option value="">Seleccionar empresa...</option>
                <?php foreach ($empresas as $emp): ?>
                    <option value="<?= (int) $emp['id_empresa'] ?>" <?= (int) $emp['id_empresa'] === (int) $proveedor['id_empresa'] ? 'selected' : '' ?>>
                        <?= Helpers::e($emp['nombre']) ?> (NIT: <?= Helpers::e($emp['nit']) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group mb-3">
            <label class="form-label">Cargo</label>
            <input type="text" name="cargo" class="form-input" value="<?= Helpers::e($proveedor['cargo'] ?? '') ?>">
        </div>

        <div class="form-group mb-4">
            <label class="form-label">Estado</label>
            <select name="estado" class="form-select">
                <option value="activo" <?= $proveedor['estado'] === 'activo' ? 'selected' : '' ?>>Activo</option>
                <option value="inactivo" <?= $proveedor['estado'] === 'inactivo' ? 'selected' : '' ?>>Inactivo</option>
            </select>
        </div>

        <div class="form-actions">
            <a href="/proveedores" class="btn-app-secondary">Cancelar</a>
            <button type="submit" class="btn-app-primary">Actualizar Proveedor</button>
        </div>

    </form>
</div>