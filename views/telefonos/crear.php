<h2 class="app-card-title">➕ Nuevo Teléfono</h2>

<div class="app-card">
    <form action="/telefonos/guardar" method="POST" id="telefonoForm">
        <!-- Selección tipo de propietario -->
        <div class="mb-3">
            <label class="form-label">¿A quién pertenece el teléfono? <span class="text-danger">*</span></label>
            <div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="tipo_entidad" id="tipo_persona" value="persona" required>
                    <label class="form-check-label" for="tipo_persona">Persona</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="tipo_entidad" id="tipo_empresa" value="empresa">
                    <label class="form-check-label" for="tipo_empresa">Empresa</label>
                </div>
            </div>
        </div>

        <!-- Selector de persona (oculto) -->
        <div class="mb-3" id="selector-persona" style="display:none;">
            <label for="id_persona" class="form-label">Persona <span class="text-danger">*</span></label>
            <select name="id_persona" id="id_persona" class="form-select">
                <option value="">— Seleccionar persona —</option>
                <?php foreach ($personas as $p): ?>
                    <option value="<?= (int) $p['id_persona'] ?>">
                        <?= Helpers::e($p['nombre_completo']) ?> (CI: <?= Helpers::e($p['ci']) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Selector de empresa (oculto) -->
        <div class="mb-3" id="selector-empresa" style="display:none;">
            <label for="id_empresa" class="form-label">Empresa <span class="text-danger">*</span></label>
            <select name="id_empresa" id="id_empresa" class="form-select">
                <option value="">— Seleccionar empresa —</option>
                <?php foreach ($empresas as $e): ?>
                    <option value="<?= (int) $e['id_empresa'] ?>">
                        <?= Helpers::e($e['nombre']) ?> (NIT: <?= Helpers::e($e['nit']) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Número y tipo -->
        <div class="mb-3">
            <label for="numero" class="form-label">Número <span class="text-danger">*</span></label>
            <input type="text" name="numero" id="numero" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="tipo" class="form-label">Tipo (opcional)</label>
            <input type="text" name="tipo" id="tipo" class="form-control" placeholder="Ej: móvil, oficina">
        </div>

        <div class="d-flex justify-content-between">
            <a href="/telefonos" class="btn btn-secondary">Cancelar</a>
            <button type="submit" class="btn-app-primary">Guardar Teléfono</button>
        </div>
    </form>
</div>

<script>
document.querySelectorAll('input[name="tipo_entidad"]').forEach(radio => {
    radio.addEventListener('change', function() {
        const personaDiv = document.getElementById('selector-persona');
        const empresaDiv = document.getElementById('selector-empresa');
        if (this.value === 'persona') {
            personaDiv.style.display = 'block';
            empresaDiv.style.display = 'none';
            document.getElementById('id_empresa').value = '';
        } else {
            personaDiv.style.display = 'none';
            empresaDiv.style.display = 'block';
            document.getElementById('id_persona').value = '';
        }
    });
});
</script>