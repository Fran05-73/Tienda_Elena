<div class="app-card" style="max-width:520px">
    <div class="app-card-header">
        <span class="app-card-title">✏ Editar Usuario: <?= Helpers::e($usuario['username']) ?></span>
        <a href="/usuarios" class="btn-app-secondary" style="font-size:.85rem">← Volver</a>
    </div>

    <form method="POST"
          action="/usuarios/actualizar/<?= (int) $usuario['id_usuario'] ?>"
          novalidate>

        <div class="form-group mb-3">
            <label class="form-label fw-semibold">
                Usuario <span class="required">*</span>
            </label>
            <input type="text" name="username" class="form-control"
                   value="<?= Helpers::e($usuario['username']) ?>" required>
        </div>

        <div class="form-group mb-3">
            <label class="form-label fw-semibold">
                Nueva Contraseña
                <small class="text-muted">(dejar vacío para no cambiar)</small>
            </label>
            <input type="password" name="password" class="form-control"
                   placeholder="Mín. 6 caracteres" autocomplete="new-password">
        </div>

        <div class="form-group mb-4">
            <label class="form-label fw-semibold">
                Empleado vinculado <span class="required">*</span>
            </label>
            <select name="id_empleado" class="form-select" required>
                <option value="">Seleccionar empleado...</option>
                <?php foreach ($empleados as $emp): ?>
                    <option value="<?= (int) $emp['id_empleado'] ?>"
                        <?= (int) $emp['id_empleado'] === (int) ($usuario['id_empleado'] ?? 0) ? 'selected' : '' ?>>
                        <?= Helpers::e($emp['nombre_completo']) ?>
                        (<?= Helpers::e($emp['nombre_rol'] ?? 'Sin rol') ?>)
                    </option>
                <?php endforeach; ?>
            </select>
            <small class="text-muted">El rol se hereda del empleado seleccionado.</small>
        </div>

        <div class="form-actions">
            <a href="/usuarios" class="btn-app-secondary">Cancelar</a>
            <button type="submit" class="btn-app-primary">Actualizar Usuario</button>
        </div>

    </form>
</div>