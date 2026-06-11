<div class="app-card" style="max-width:500px">
    <div class="app-card-header">
        <span class="app-card-title">✏ Editar Rol</span>
        <a href="/roles" class="btn-app-secondary" style="font-size:.85rem">← Volver</a>
    </div>

    <form action="/roles/actualizar/<?= (int) $rol['id_rol'] ?>" method="POST" novalidate>

        <div class="form-group mb-4">
            <label class="form-label">Nombre del Rol <span class="required">*</span></label>
            <input type="text" name="nombre" class="form-input"
                   value="<?= Helpers::e($rol['nombre']) ?>" required>
        </div>

        <div class="form-actions">
            <a href="/roles" class="btn-app-secondary">Cancelar</a>
            <button type="submit" class="btn-app-primary">Actualizar Rol</button>
        </div>

    </form>
</div>