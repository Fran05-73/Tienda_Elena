<div class="app-card" style="max-width:700px">
    <div class="app-card-header">
        <span class="app-card-title">⊕ Nuevo Empleado</span>
        <a href="/empleados" class="btn-app-secondary" style="font-size:.85rem">← Volver</a>
    </div>

    <form action="/empleados/guardar" method="POST" novalidate>

        <h5 class="mb-3">Datos Personales</h5>

        <div class="form-grid-2">
            <div class="form-group">
                <label class="form-label">Nombre <span class="required">*</span></label>
                <input type="text" name="nombre" class="form-input" placeholder="Nombre(s)" required>
            </div>
            <div class="form-group">
                <label class="form-label">Apellido <span class="required">*</span></label>
                <input type="text" name="apellido" class="form-input" placeholder="Apellido(s)" required>
            </div>
            <div class="form-group">
                <label class="form-label">CI <span class="required">*</span></label>
                <input type="text" name="ci" class="form-input" placeholder="Número de cédula" required>
            </div>
            <div class="form-group">
                <label class="form-label">Correo</label>
                <input type="email" name="correo" class="form-input" placeholder="correo@ejemplo.com">
            </div>
            <div class="form-group">
                <label class="form-label">Teléfono</label>
                <input type="text" name="telefono" class="form-input" placeholder="Ej: 77712345">
            </div>
        </div>

        <hr class="my-4">

        <h5 class="mb-3">Datos Laborales</h5>

        <div class="form-grid-2">
            <div class="form-group">
                <label class="form-label">Salario (Bs.) <span class="required">*</span></label>
                <input type="number" name="salario" step="0.01" min="0.01"
                       class="form-input" placeholder="Ej: 2500.00" required>
            </div>
            <div class="form-group">
                <label class="form-label">Fecha de Contratación</label>
                <input type="date" name="fecha_contratacion" class="form-input"
                       value="<?= date('Y-m-d') ?>">
            </div>
            <div class="form-group">
                <label class="form-label">Rol <span class="required">*</span></label>
                <select name="id_rol" class="form-select" required>
                    <option value="">Seleccionar rol...</option>
                    <?php foreach ($roles as $rol): ?>
                        <option value="<?= (int) $rol['id_rol'] ?>">
                            <?= Helpers::e($rol['nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="form-actions">
            <a href="/empleados" class="btn-app-secondary">Cancelar</a>
            <button type="submit" class="btn-app-primary">Guardar Empleado</button>
        </div>

    </form>
</div>