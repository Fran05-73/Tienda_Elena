<div class="app-card" style="max-width:720px">
    <div class="app-card-header">
        <span class="app-card-title">➕ Crear Nuevo Usuario</span>
        <a href="/usuarios" class="btn-app-secondary" style="font-size:.85rem">← Volver</a>
    </div>

    <form method="POST" action="/usuarios/guardar" novalidate>
        <!-- ── Credenciales del usuario ──────────────────────────── -->
        <div class="form-grid-2">
            <div class="form-group">
                <label class="form-label fw-semibold">
                    Usuario <span class="required">*</span>
                </label>
                <input type="text" name="username" class="form-control"
                       placeholder="Mín. 4 caracteres" required>
            </div>
            <div class="form-group">
                <label class="form-label fw-semibold">
                    Contraseña <span class="required">*</span>
                </label>
                <input type="password" name="password" class="form-control"
                       placeholder="Mín. 6 caracteres" required>
            </div>
        </div>

        <hr>

        <!-- ── Selección del empleado ──────────────────────────────── -->
        <div class="mb-3">
            <label class="form-label fw-semibold">Tipo de vinculación</label>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="tipo_empleado" id="tipoExistente" value="existente" checked>
                <label class="form-check-label" for="tipoExistente">Seleccionar empleado existente</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="tipo_empleado" id="tipoNuevo" value="nuevo">
                <label class="form-check-label" for="tipoNuevo">Crear nueva persona y empleado</label>
            </div>
        </div>

        <!-- Panel: empleado existente -->
        <div id="panelExistente">
            <div class="form-group mb-4">
                <label class="form-label fw-semibold">Empleado <span class="required">*</span></label>
                <select name="id_empleado" class="form-select">
                    <option value="">Seleccionar empleado...</option>
                    <?php foreach ($empleados as $emp): ?>
                        <option value="<?= (int) $emp['id_empleado'] ?>">
                            <?= Helpers::e($emp['nombre_completo']) ?>
                            (<?= Helpers::e($emp['nombre_rol'] ?? 'Sin rol') ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <!-- Panel: nueva persona + empleado -->
        <div id="panelNuevo" style="display:none;">
            <div class="form-grid-2">
                <div class="form-group">
                    <label class="form-label">Nombre <span class="required">*</span></label>
                    <input type="text" name="nombre" class="form-control" placeholder="Nombre(s)">
                </div>
                <div class="form-group">
                    <label class="form-label">Apellido <span class="required">*</span></label>
                    <input type="text" name="apellido" class="form-control" placeholder="Apellido(s)">
                </div>
                <div class="form-group">
                    <label class="form-label">CI <span class="required">*</span></label>
                    <input type="text" name="ci" class="form-control" placeholder="Ej. 1234567">
                </div>
                <div class="form-group">
                    <label class="form-label">Correo</label>
                    <input type="email" name="correo" class="form-control" placeholder="correo@ejemplo.com">
                </div>
                <div class="form-group">
                    <label class="form-label">Teléfono</label>
                    <input type="text" name="telefono" class="form-control" placeholder="Ej. 77712345">
                </div>
                <div class="form-group">
                    <label class="form-label">Salario <span class="required">*</span></label>
                    <input type="number" name="salario" step="0.01" class="form-control" placeholder="2500.00">
                </div>
                <div class="form-group">
                    <label class="form-label">Rol <span class="required">*</span></label>
                    <select name="id_rol" class="form-select">
                        <option value="">Seleccionar rol...</option>
                        <?php foreach ($roles as $rol): ?>
                            <option value="<?= (int) $rol['id_rol'] ?>">
                                <?= Helpers::e($rol['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>

        <div class="form-actions mt-4">
            <a href="/usuarios" class="btn-app-secondary">Cancelar</a>
            <button type="submit" class="btn-app-primary">Crear Usuario</button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const radioExistente = document.getElementById('tipoExistente');
    const radioNuevo = document.getElementById('tipoNuevo');
    const panelExistente = document.getElementById('panelExistente');
    const panelNuevo = document.getElementById('panelNuevo');

    function togglePanels() {
        if (radioNuevo.checked) {
            panelExistente.style.display = 'none';
            panelNuevo.style.display = 'block';
            // Deshabilitar select de empleado para que no se envíe
            document.querySelector('select[name="id_empleado"]').disabled = true;
        } else {
            panelExistente.style.display = 'block';
            panelNuevo.style.display = 'none';
            document.querySelector('select[name="id_empleado"]').disabled = false;
            // Limpiar campos nuevos (opcional)
        }
    }

    radioExistente.addEventListener('change', togglePanels);
    radioNuevo.addEventListener('change', togglePanels);
    togglePanels(); // estado inicial
});
</script>