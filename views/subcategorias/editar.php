<div class="app-card" style="max-width:560px">
    <div class="app-card-header">
        <span class="app-card-title">✏ Editar Subcategoría: <?= Helpers::e($subcategoria['nombre']) ?></span>
        <a href="/subcategorias" class="btn-app-secondary" style="font-size:.85rem">← Volver</a>
    </div>

    <form action="/subcategorias/actualizar/<?= (int) $subcategoria['id_subcategoria'] ?>"
          method="POST" novalidate>

        <div class="form-group mb-3">
            <label class="form-label">Nombre <span class="required">*</span></label>
            <input type="text" name="nombre" class="form-input"
                   value="<?= Helpers::e($subcategoria['nombre']) ?>" required>
        </div>

        <div class="form-group mb-3">
            <label class="form-label">Categoría Padre <span class="required">*</span></label>
            <select name="id_categoria" class="form-select" required>
                <option value="">Seleccionar categoría...</option>
                <?php foreach ($categorias as $cat): ?>
                    <option value="<?= (int) $cat['id_categoria'] ?>"
                        <?= (int) $cat['id_categoria'] === (int) $subcategoria['id_categoria'] ? 'selected' : '' ?>>
                        <?= Helpers::e($cat['nombre']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group mb-4">
            <label class="form-label">Descripción</label>
            <textarea name="descripcion" class="form-input" rows="2"
                      placeholder="Descripción opcional..."><?= Helpers::e($subcategoria['descripcion'] ?? '') ?></textarea>
        </div>

        <div class="form-actions">
            <a href="/subcategorias" class="btn-app-secondary">Cancelar</a>
            <button type="submit" class="btn-app-primary">Actualizar Subcategoría</button>
        </div>

    </form>
</div>