<div class="app-card" style="max-width:560px">
    <div class="app-card-header">
        <span class="app-card-title">⊕ Nueva Categoría</span>
        <a href="/categorias" class="btn-app-secondary" style="font-size:.85rem">← Volver</a>
    </div>

    <form action="/categorias/guardar" method="POST" novalidate>

        <div class="form-group mb-3">
            <label class="form-label">Nombre <span class="required">*</span></label>
            <input type="text" name="nombre" class="form-input"
                   placeholder="Ej: Lácteos" required>
        </div>

        <div class="form-group mb-3">
            <label class="form-label">Descripción</label>
            <textarea name="descripcion" class="form-input" rows="2"
                      placeholder="Descripción opcional..."></textarea>
        </div>

        <div class="form-group mb-4">
            <label class="form-label">Estado</label>
            <select name="estado" class="form-select">
                <option value="activa">Activa</option>
                <option value="inactiva">Inactiva</option>
            </select>
        </div>

        <div class="form-actions">
            <a href="/categorias" class="btn-app-secondary">Cancelar</a>
            <button type="submit" class="btn-app-primary">Guardar Categoría</button>
        </div>

    </form>
</div>