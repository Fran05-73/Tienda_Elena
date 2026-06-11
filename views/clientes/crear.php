<div class="app-card" style="max-width:700px">
    <div class="app-card-header">
        <span class="app-card-title">⊕ Nuevo Cliente</span>
        <a href="/clientes" class="btn-app-secondary" style="font-size:.85rem">← Volver</a>
    </div>

    <form action="/clientes/guardar" method="POST" novalidate>

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

        <h5 class="mb-3">Datos del Cliente</h5>

        <div class="form-group mb-3">
            <label class="form-label">Dirección</label>
            <input type="text" name="direccion" class="form-input" placeholder="Av. Principal #123">
        </div>

        <div class="form-group mb-3">
            <label class="form-label">Nacionalidad</label>
            <input type="text" name="nacionalidad" class="form-input" placeholder="Ej: Boliviana">
        </div>

        <div class="form-group mb-4">
            <label class="form-label">Preferencias</label>
            <textarea name="preferencias" class="form-input" rows="2"
                placeholder="Productos de interés, notas..."></textarea>
        </div>

        <div class="form-actions">
            <a href="/clientes" class="btn-app-secondary">Cancelar</a>
            <button type="submit" class="btn-app-primary">Guardar Cliente</button>
        </div>

    </form>
</div>
<script>
    document.getElementById('ci').addEventListener('blur', async function () {
        const ci = this.value.trim();
        if (ci.length < 5) return;

        try {
            const res = await fetch(`/personas/verificar-ci?ci=${encodeURIComponent(ci)}`);
            const data = await res.json();

            const feedback = document.getElementById('ci-feedback');
            if (data.existe) {
                feedback.textContent = '⚠ Este CI ya está registrado. Usa "Persona existente".';
                feedback.style.color = 'var(--danger)';
            } else {
                feedback.textContent = '✓ CI disponible';
                feedback.style.color = 'var(--success)';
            }
        } catch (e) {
            // Silencioso
        }
    });
</script>