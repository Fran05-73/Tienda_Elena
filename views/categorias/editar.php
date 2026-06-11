<div class="app-card" style="max-width:560px">
    <div class="app-card-header">
        <span class="app-card-title">✏ Editar Categoría: <?= Helpers::e($categoria['nombre']) ?></span>
        <a href="/categorias" class="btn-app-secondary" style="font-size:.85rem">← Volver</a>
    </div>

    <form action="/categorias/actualizar/<?= (int) $categoria['id_categoria'] ?>"
          method="POST" novalidate>

        <div class="form-group mb-3">
            <label class="form-label">Nombre <span class="required">*</span></label>
            <input type="text" name="nombre" class="form-input"
                   value="<?= Helpers::e($categoria['nombre']) ?>" required>
        </div>

        <div class="form-group mb-3">
            <label class="form-label">Descripción</label>
            <textarea name="descripcion" class="form-input" rows="2"
                      placeholder="Descripción opcional..."><?= Helpers::e($categoria['descripcion'] ?? '') ?></textarea>
        </div>

        <div class="form-group mb-4">
            <label class="form-label">Estado</label>
            <select name="estado" class="form-select">
                <option value="activa" <?= $categoria['estado'] === 'activa' ? 'selected' : '' ?>>Activa</option>
                <option value="inactiva" <?= $categoria['estado'] === 'inactiva' ? 'selected' : '' ?>>Inactiva</option>
            </select>
        </div>

        <div class="form-actions">
            <a href="/categorias" class="btn-app-secondary">Cancelar</a>
            <button type="submit" class="btn-app-primary">Actualizar Categoría</button>
        </div>

    </form>
</div>