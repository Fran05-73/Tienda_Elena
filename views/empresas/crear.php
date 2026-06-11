<h2 class="app-card-title">➕ Nueva Empresa</h2>

<div class="app-card">
    <form action="/empresas/guardar" method="POST">
        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre <span class="text-danger">*</span></label>
            <input type="text" name="nombre" id="nombre"
                   class="form-control" required minlength="2"
                   value="">
        </div>

        <div class="mb-3">
            <label for="nit" class="form-label">NIT <span class="text-danger">*</span></label>
            <input type="text" name="nit" id="nit"
                   class="form-control" required minlength="5"
                   value="">
        </div>

        <div class="mb-3">
            <label for="direccion" class="form-label">Dirección</label>
            <input type="text" name="direccion" id="direccion"
                   class="form-control"
                   value="">
        </div>

        <div class="mb-3">
            <label for="telefono_principal" class="form-label">Teléfono principal</label>
            <input type="text" name="telefono_principal" id="telefono_principal"
                   class="form-control"
                   value="">
        </div>

        <div class="mb-3">
            <label for="email_principal" class="form-label">Email principal</label>
            <input type="email" name="email_principal" id="email_principal"
                   class="form-control"
                   value="">
        </div>

        <div class="d-flex justify-content-between">
            <a href="/empresas" class="btn btn-secondary">Cancelar</a>
            <button type="submit" class="btn-app-primary">Guardar Empresa</button>
        </div>
    </form>
</div>