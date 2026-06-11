<h2 class="app-card-title">➕ Nueva Magnitud</h2>

<div class="app-card">
    <form action="/magnitudes/guardar" method="POST">
        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre <span class="text-danger">*</span></label>
            <input type="text" name="nombre" id="nombre"
                   class="form-control" required minlength="2"
                   value="">
        </div>

        <div class="mb-3">
            <label for="abreviatura" class="form-label">Abreviatura <span class="text-danger">*</span></label>
            <input type="text" name="abreviatura" id="abreviatura"
                   class="form-control" required minlength="1"
                   value="">
        </div>

        <div class="mb-3">
            <label for="tipo" class="form-label">Tipo</label>
            <select name="tipo" id="tipo" class="form-select">
                <option value="">— Seleccionar tipo —</option>
                <option value="peso">Peso</option>
                <option value="volumen">Volumen</option>
                <option value="unidad">Unidad</option>
                <option value="longitud">Longitud</option>
                <option value="otros">Otros</option>
            </select>
        </div>

        <div class="d-flex justify-content-between">
            <a href="/magnitudes" class="btn btn-secondary">Cancelar</a>
            <button type="submit" class="btn-app-primary">Guardar Magnitud</button>
        </div>
    </form>
</div>