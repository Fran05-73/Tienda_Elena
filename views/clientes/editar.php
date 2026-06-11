<div class="app-card" style="max-width:700px">
    <div class="app-card-header">
        <span class="app-card-title">✏ Editar Cliente: <?= Helpers::e($cliente['nombre'] . ' ' . $cliente['apellido']) ?></span>
        <a href="/clientes" class="btn-app-secondary" style="font-size:.85rem">← Volver</a>
    </div>

    <form action="/clientes/actualizar/<?= (int) $cliente['id_cliente'] ?>" method="POST" novalidate>

        <h5 class="mb-3">Datos Personales</h5>

        <div class="form-grid-2">
            <div class="form-group">
                <label class="form-label">Nombre <span class="required">*</span></label>
                <input type="text" name="nombre" class="form-input"
                       value="<?= Helpers::e($cliente['nombre']) ?>" required>
            </div>
            <div class="form-group">
                <label class="form-label">Apellido <span class="required">*</span></label>
                <input type="text" name="apellido" class="form-input"
                       value="<?= Helpers::e($cliente['apellido']) ?>" required>
            </div>
            <div class="form-group">
                <label class="form-label">CI <span class="required">*</span></label>
                <input type="text" name="ci" class="form-input"
                       value="<?= Helpers::e($cliente['ci']) ?>" required>
            </div>
            <div class="form-group">
                <label class="form-label">Correo</label>
                <input type="email" name="correo" class="form-input"
                       value="<?= Helpers::e($cliente['correo'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label class="form-label">Teléfono</label>
                <input type="text" name="telefono" class="form-input"
                       value="<?= Helpers::e($cliente['telefono'] ?? '') ?>">
            </div>
        </div>

        <hr class="my-4">

        <h5 class="mb-3">Datos del Cliente</h5>

        <div class="form-group mb-3">
            <label class="form-label">Dirección</label>
            <input type="text" name="direccion" class="form-input"
                   value="<?= Helpers::e($cliente['direccion'] ?? '') ?>">
        </div>

        <div class="form-group mb-3">
            <label class="form-label">Nacionalidad</label>
            <input type="text" name="nacionalidad" class="form-input"
                   value="<?= Helpers::e($cliente['nacionalidad'] ?? '') ?>">
        </div>

        <div class="form-group mb-4">
            <label class="form-label">Preferencias</label>
            <textarea name="preferencias" class="form-input" rows="2"><?= Helpers::e($cliente['preferencias'] ?? '') ?></textarea>
        </div>

        <div class="form-actions">
            <a href="/clientes" class="btn-app-secondary">Cancelar</a>
            <button type="submit" class="btn-app-primary">Actualizar Cliente</button>
        </div>

    </form>
</div>