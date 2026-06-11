<div class="app-card" style="max-width:700px">
    <div class="app-card-header">
        <span class="app-card-title">✏ Editar Empleado: <?= Helpers::e($empleado['nombre'] . ' ' . $empleado['apellido']) ?></span>
        <a href="/empleados" class="btn-app-secondary" style="font-size:.85rem">← Volver</a>
    </div>

    <form action="/empleados/actualizar/<?= (int) $empleado['id_empleado'] ?>" method="POST" novalidate>

        <h5 class="mb-3">Datos Personales</h5>

        <div class="form-grid-2">
            <div class="form-group">
                <label class="form-label">Nombre <span class="required">*</span></label>
                <input type="text" name="nombre" class="form-input"
                       value="<?= Helpers::e($empleado['nombre']) ?>" required>
            </div>
            <div class="form-group">
                <label class="form-label">Apellido <span class="required">*</span></label>
                <input type="text" name="apellido" class="form-input"
                       value="<?= Helpers::e($empleado['apellido']) ?>" required>
            </div>
            <div class="form-group">
                <label class="form-label">CI <span class="required">*</span></label>
                <input type="text" name="ci" class="form-input"
                       value="<?= Helpers::e($empleado['ci']) ?>" required>
            </div>
            <div class="form-group">
                <label class="form-label">Correo</label>
                <input type="email" name="correo" class="form-input"
                       value="<?= Helpers::e($empleado['correo'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label class="form-label">Teléfono</label>
                <input type="text" name="telefono" class="form-input"
                       value="<?= Helpers::e($empleado['telefono'] ?? '') ?>">
            </div>
        </div>

        <hr class="my-4">

        <h5 class="mb-3">Datos Laborales</h5>

        <div class="form-grid-2">
            <div class="form-group">
                <label class="form-label">Salario (Bs.) <span class="required">*</span></label>
                <input type="number" name="salario" step="0.01" min="0.01"
                       class="form-input"
                       value="<?= number_format((float) $empleado['salario'], 2, '.', '') ?>" required>
            </div>
            <div class="form-group">
                <label class="form-label">Fecha de Contratación</label>
                <input type="date" name="fecha_contratacion" class="form-input"
                       value="<?= Helpers::e($empleado['fecha_contratacion'] ?? date('Y-m-d')) ?>">
            </div>
            <div class="form-group">
                <label class="form-label">Rol <span class="required">*</span></label>
                <select name="id_rol" class="form-select" required>
                    <option value="">Seleccionar rol...</option>
                    <?php foreach ($roles as $rol): ?>
                        <option value="<?= (int) $rol['id_rol'] ?>"
                            <?= (int) $rol['id_rol'] === (int) $empleado['id_rol'] ? 'selected' : '' ?>>
                            <?= Helpers::e($rol['nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="form-actions">
            <a href="/empleados" class="btn-app-secondary">Cancelar</a>
            <button type="submit" class="btn-app-primary">Actualizar Empleado</button>
        </div>

    </form>
</div>