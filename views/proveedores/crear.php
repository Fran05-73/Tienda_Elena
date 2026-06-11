<div class="app-card" style="max-width:800px">
    <div class="app-card-header">
        <span class="app-card-title">⊕ Nuevo Proveedor</span>
        <a href="/proveedores" class="btn-app-secondary" style="font-size:.85rem">← Volver</a>
    </div>

    <form action="/proveedores/guardar" method="POST" novalidate>

        <!-- ── PERSONA ──────────────────────────────────────────────── -->
        <div class="mb-3">
            <label class="form-label fw-semibold">Vincular Persona</label>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="tipo_persona" id="personaExistente" value="existente" checked>
                <label class="form-check-label" for="personaExistente">Seleccionar existente</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="tipo_persona" id="personaNueva" value="nueva">
                <label class="form-check-label" for="personaNueva">Crear nueva</label>
            </div>
        </div>

        <!-- Panel persona existente -->
        <div id="panelPersonaExistente">
            <div class="form-group mb-3">
                <select name="id_persona" class="form-select">
                    <option value="">Seleccionar persona...</option>
                    <?php foreach ($personas as $per): ?>
                        <option value="<?= (int) $per['id_persona'] ?>">
                            <?= Helpers::e($per['apellido'] . ' ' . $per['nombre']) ?> (CI: <?= Helpers::e($per['ci']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <!-- Panel persona nueva -->
        <div id="panelPersonaNueva" style="display:none;">
            <div class="form-grid-2">
                <div class="form-group">
                    <input type="text" name="persona_nombre" class="form-input" placeholder="Nombre(s)">
                </div>
                <div class="form-group">
                    <input type="text" name="persona_apellido" class="form-input" placeholder="Apellido(s)">
                </div>
                <div class="form-group">
                    <input type="text" name="persona_ci" class="form-input" placeholder="CI">
                </div>
                <div class="form-group">
                    <input type="email" name="persona_correo" class="form-input" placeholder="Correo">
                </div>
                <div class="form-group">
                    <input type="text" name="persona_telefono" class="form-input" placeholder="Teléfono">
                </div>
            </div>
        </div>

        <hr>

        <!-- ── EMPRESA ──────────────────────────────────────────────── -->
        <div class="mb-3">
            <label class="form-label fw-semibold">Vincular Empresa</label>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="tipo_empresa" id="empresaExistente" value="existente" checked>
                <label class="form-check-label" for="empresaExistente">Seleccionar existente</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="tipo_empresa" id="empresaNueva" value="nueva">
                <label class="form-check-label" for="empresaNueva">Crear nueva</label>
            </div>
        </div>

        <!-- Panel empresa existente -->
        <div id="panelEmpresaExistente">
            <div class="form-group mb-3">
                <select name="id_empresa" class="form-select">
                    <option value="">Seleccionar empresa...</option>
                    <?php foreach ($empresas as $emp): ?>
                        <option value="<?= (int) $emp['id_empresa'] ?>">
                            <?= Helpers::e($emp['nombre']) ?> (NIT: <?= Helpers::e($emp['nit']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <!-- Panel empresa nueva -->
        <div id="panelEmpresaNueva" style="display:none;">
            <div class="form-grid-2">
                <div class="form-group">
                    <input type="text" name="empresa_nombre" class="form-input" placeholder="Nombre de empresa">
                </div>
                <div class="form-group">
                    <input type="text" name="empresa_nit" class="form-input" placeholder="NIT">
                </div>
                <div class="form-group">
                    <input type="text" name="empresa_direccion" class="form-input" placeholder="Dirección">
                </div>
                <div class="form-group">
                    <input type="text" name="empresa_telefono" class="form-input" placeholder="Teléfono">
                </div>
                <div class="form-group">
                    <input type="email" name="empresa_email" class="form-input" placeholder="Email">
                </div>
            </div>
        </div>

        <hr>

        <!-- ── Datos del proveedor ──────────────────────────────────── -->
        <div class="form-grid-2">
            <div class="form-group">
                <label class="form-label">Cargo</label>
                <input type="text" name="cargo" class="form-input" placeholder="Ej: Representante">
            </div>
            <div class="form-group">
                <label class="form-label">Estado</label>
                <select name="estado" class="form-select">
                    <option value="activo">Activo</option>
                    <option value="inactivo">Inactivo</option>
                </select>
            </div>
        </div>

        <div class="form-actions">
            <a href="/proveedores" class="btn-app-secondary">Cancelar</a>
            <button type="submit" class="btn-app-primary">Guardar Proveedor</button>
        </div>

    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle persona
    document.querySelectorAll('input[name="tipo_persona"]').forEach(radio => {
        radio.addEventListener('change', function() {
            document.getElementById('panelPersonaExistente').style.display = this.value === 'existente' ? 'block' : 'none';
            document.getElementById('panelPersonaNueva').style.display = this.value === 'nueva' ? 'block' : 'none';
        });
    });
    // Toggle empresa
    document.querySelectorAll('input[name="tipo_empresa"]').forEach(radio => {
        radio.addEventListener('change', function() {
            document.getElementById('panelEmpresaExistente').style.display = this.value === 'existente' ? 'block' : 'none';
            document.getElementById('panelEmpresaNueva').style.display = this.value === 'nueva' ? 'block' : 'none';
        });
    });
});
</script>