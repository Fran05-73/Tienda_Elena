<h2 class="app-card-title">✏️ Editar Magnitud</h2>

<div class="app-card">
    <form action="/magnitudes/actualizar/<?= (int) $magnitud['id_magnitud'] ?>" method="POST">
        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre <span class="text-danger">*</span></label>
            <input type="text" name="nombre" id="nombre"
                   class="form-control" required minlength="2"
                   value="<?= Helpers::e($magnitud['nombre']) ?>">
        </div>

        <div class="mb-3">
            <label for="abreviatura" class="form-label">Abreviatura <span class="text-danger">*</span></label>
            <input type="text" name="abreviatura" id="abreviatura"
                   class="form-control" required minlength="1"
                   value="<?= Helpers::e($magnitud['abreviatura']) ?>">
        </div>

        <div class="mb-3">
            <label for="tipo" class="form-label">Tipo</label>
            <select name="tipo" id="tipo" class="form-select">
                <option value="">— Seleccionar tipo —</option>
                <?php
                $tipos = ['peso', 'volumen', 'unidad', 'longitud', 'otros'];
                $tipoActual = $magnitud['tipo'] ?? '';
                foreach ($tipos as $t):
                    $selected = ($t === $tipoActual) ? 'selected' : '';
                ?>
                    <option value="<?= $t ?>" <?= $selected ?>><?= ucfirst($t) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="d-flex justify-content-between">
            <a href="/magnitudes" class="btn btn-secondary">Cancelar</a>
            <button type="submit" class="btn-app-primary">Actualizar Magnitud</button>
        </div>
    </form>
</div>