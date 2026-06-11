<div class="app-card" style="max-width:560px">
    <div class="app-card-header">
        <span class="app-card-title">⊕ Nueva Subcategoría</span>
        <a href="/subcategorias" class="btn-app-secondary" style="font-size:.85rem">← Volver</a>
    </div>

    <form action="/subcategorias/guardar" method="POST" novalidate>

        <div class="form-group mb-3">
            <label class="form-label">Nombre <span class="required">*</span></label>
            <input type="text" name="nombre" class="form-input"
                   placeholder="Ej: Lácteos" required>
        </div>

        <div class="form-group mb-3">
            <label class="form-label">Categoría Padre <span class="required">*</span></label>
            <select name="id_categoria" class="form-select" required>
                <option value="">Seleccionar categoría...</option>
                <?php foreach ($categorias as $cat): ?>
                    <option value="<?= (int) $cat['id_categoria'] ?>">
                        <?= Helpers::e($cat['nombre']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group mb-4">
            <label class="form-label">Descripción</label>
            <textarea name="descripcion" class="form-input" rows="2"
                      placeholder="Descripción opcional..."></textarea>
        </div>

        <div class="form-actions">
            <a href="/subcategorias" class="btn-app-secondary">Cancelar</a>
            <button type="submit" class="btn-app-primary">Guardar Subcategoría</button>
        </div>

    </form>
</div>