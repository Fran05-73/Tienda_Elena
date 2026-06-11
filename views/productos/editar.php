<div class="app-card" style="max-width:860px">
    <div class="app-card-header">
        <span class="app-card-title">✏ Editar Producto: <?= Helpers::e($producto['nombre']) ?></span>
        <a href="/productos" class="btn-app-secondary" style="font-size:.85rem">← Volver</a>
    </div>

    <form action="/productos/actualizar/<?= (int) $producto['id_producto'] ?>"
          method="POST" novalidate>

        <div class="form-grid-2">
            <div class="form-group">
                <label class="form-label">SKU <span class="required">*</span></label>
                <input type="text" name="sku" class="form-input"
                       value="<?= Helpers::e($producto['sku']) ?>" required>
            </div>
            <div class="form-group">
                <label class="form-label">Código de Barras</label>
                <input type="text" name="codigo_barras" class="form-input"
                       value="<?= Helpers::e($producto['codigo_barras'] ?? '') ?>">
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Nombre <span class="required">*</span></label>
            <input type="text" name="nombre" class="form-input"
                   value="<?= Helpers::e($producto['nombre']) ?>" required>
        </div>

        <div class="form-group">
            <label class="form-label">Descripción</label>
            <textarea name="descripcion" class="form-input" rows="2"><?= Helpers::e($producto['descripcion'] ?? '') ?></textarea>
        </div>

        <div class="form-group">
            <label class="form-label">Precio (Bs.) <span class="required">*</span></label>
            <input type="number" name="precio" step="0.01" min="0.01"
                   class="form-input"
                   value="<?= number_format((float) $producto['precio'], 2, '.', '') ?>"
                   required>
        </div>

        <div class="form-grid-2">
            <div class="form-group">
                <label class="form-label">Subcategoría <span class="required">*</span></label>
                <select name="id_subcategoria" class="form-select" required>
                    <option value="">Seleccionar...</option>
                    <?php foreach ($subcategorias as $s): ?>
                        <option value="<?= (int) $s['id_subcategoria'] ?>"
                            <?= (int) $s['id_subcategoria'] === (int) $producto['id_subcategoria'] ? 'selected' : '' ?>>
                            <?= Helpers::e($s['categoria_nombre']) ?> → <?= Helpers::e($s['nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Proveedor <span class="required">*</span></label>
                <select name="id_proveedor" class="form-select" required>
                    <option value="">Seleccionar...</option>
                    <?php foreach ($proveedores as $prov): ?>
                        <option value="<?= (int) $prov['id_proveedor'] ?>"
                            <?= (int) $prov['id_proveedor'] === (int) $producto['id_proveedor'] ? 'selected' : '' ?>>
                            <?= Helpers::e($prov['empresa_nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="form-grid-2">
            <div class="form-group">
                <label class="form-label">Magnitud</label>
                <select name="id_magnitud" class="form-select">
                    <option value="">Sin magnitud</option>
                    <?php foreach ($magnitudes as $m): ?>
                        <option value="<?= (int) $m['id_magnitud'] ?>"
                            <?= (int) $m['id_magnitud'] === (int) ($producto['id_magnitud'] ?? 0) ? 'selected' : '' ?>>
                            <?= Helpers::e($m['nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Valor Magnitud</label>
                <input type="number" name="valor_magnitud" step="0.01" min="0"
                       class="form-input"
                       value="<?= Helpers::e($producto['valor_magnitud'] ?? '') ?>">
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Fecha de Vencimiento</label>
            <input type="date" name="fecha_vencimiento" class="form-input"
                   value="<?= Helpers::e($producto['fecha_vencimiento'] ?? '') ?>">
        </div>

        <div class="form-actions">
            <a href="/productos" class="btn-app-secondary">Cancelar</a>
            <button type="submit" class="btn-app-primary">Actualizar Producto</button>
        </div>

    </form>
</div>