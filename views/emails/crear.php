<h2 class="app-card-title">➕ Nuevo Correo</h2>

<div class="app-card">
    <form action="/emails/guardar" method="POST" id="emailForm">
        <!-- Selección de tipo de propietario -->
        <div class="mb-3">
            <label class="form-label">¿A quién pertenece el correo? <span class="text-danger">*</span></label>
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

        <!-- Dropdown para Persona (oculto inicialmente) -->
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

        <!-- Dropdown para Empresa (oculto inicialmente) -->
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

        <!-- Correo y tipo -->
        <div class="mb-3">
            <label for="correo" class="form-label">Correo electrónico <span class="text-danger">*</span></label>
            <input type="email" name="correo" id="correo" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="tipo" class="form-label">Tipo (opcional)</label>
            <input type="text" name="tipo" id="tipo" class="form-control" placeholder="Ej: trabajo, personal">
        </div>

        <div class="d-flex justify-content-between">
            <a href="/emails" class="btn btn-secondary">Cancelar</a>
            <button type="submit" class="btn-app-primary">Guardar Correo</button>
        </div>
    </form>
</div>

<script>
// Mostrar el selector correspondiente según el tipo de entidad seleccionado
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