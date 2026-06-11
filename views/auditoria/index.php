<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px">
    <h2 class="app-card-title mb-0">📋 Auditoría del Sistema</h2>
</div>

<!-- Filtros -->
<div class="app-card" style="margin-bottom:20px">
    <form method="GET" action="/auditoria" style="display:flex;gap:16px;align-items:end;flex-wrap:wrap">
        <div class="form-group" style="min-width:200px">
            <label class="form-label fw-semibold">Tabla afectada</label>
            <select name="tabla" class="form-select">
                <option value="">— Todas las tablas —</option>
                <?php foreach ($tablas as $t): ?>
                    <option value="<?= Helpers::e($t['nombre']) ?>" <?= $tabla === $t['nombre'] ? 'selected' : '' ?>>
                        <?= Helpers::e($t['nombre']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group" style="min-width:160px">
            <label class="form-label fw-semibold">Acción</label>
            <select name="accion" class="form-select">
                <option value="">— Todas —</option>
                <option value="INSERT" <?= $accion === 'INSERT' ? 'selected' : '' ?>>INSERT</option>
                <option value="UPDATE" <?= $accion === 'UPDATE' ? 'selected' : '' ?>>UPDATE</option>
                <option value="DELETE" <?= $accion === 'DELETE' ? 'selected' : '' ?>>DELETE</option>
            </select>
        </div>

        <div class="form-group">
            <button type="submit" class="btn-app-primary">🔍 Filtrar</button>
            <a href="/auditoria" class="btn-app-secondary" style="margin-left:8px">Limpiar</a>
        </div>
    </form>
</div>

<!-- Tabla de auditoría -->
<div class="app-card">
    <?php if (empty($auditorias)): ?>
        <p class="text-muted py-3">No hay registros de auditoría.</p>
    <?php else: ?>
    <div style="overflow-x:auto">
        <table class="app-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Fecha y Hora</th>
                    <th>Tabla</th>
                    <th>Acción</th>
                    <th>Usuario BD</th>
                    <th>ID Registro</th>
                    <th>Datos Anteriores</th>
                    <th>Datos Nuevos</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($auditorias as $a): ?>
                <tr>
                    <td><?= (int) $a['id'] ?></td>
                    <td style="white-space:nowrap;font-size:0.85rem">
                        <?= Helpers::dateFormat($a['fecha']) ?><br>
                        <small class="text-muted"><?= date('H:i:s', strtotime($a['fecha'])) ?></small>
                    </td>
                    <td>
                        <span class="badge bg-info" style="font-size:0.8rem">
                            <?= Helpers::e($a['tabla_afectada']) ?>
                        </span>
                    </td>
                    <td>
                        <span class="badge bg-<?= 
                            $a['accion'] === 'INSERT' ? 'success' : 
                            ($a['accion'] === 'UPDATE' ? 'warning' : 
                            ($a['accion'] === 'DELETE' ? 'danger' : 'secondary')) 
                        ?>">
                            <?= Helpers::e($a['accion']) ?>
                        </span>
                    </td>
                    <td><?= Helpers::e($a['usuario_bd']) ?></td>
                    <td><?= Helpers::e($a['id_registro'] ?? '—') ?></td>
                    <td style="max-width:250px">
                        <?php if (!empty($a['datos_anteriores'])): ?>
                            <button class="btn-icon" onclick="verDetalle('anteriores_<?= (int) $a['id'] ?>')">
                                📄 Ver
                            </button>
                            <pre id="anteriores_<?= (int) $a['id'] ?>" 
                                 style="display:none;max-height:200px;overflow-y:auto;font-size:0.75rem;margin-top:4px;background:var(--bg-light);padding:8px;border-radius:4px"><?= Helpers::e(json_encode(json_decode($a['datos_anteriores']), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) ?></pre>
                        <?php else: ?>
                            <span class="text-muted">—</span>
                        <?php endif; ?>
                    </td>
                    <td style="max-width:250px">
                        <?php if (!empty($a['datos_nuevos'])): ?>
                            <button class="btn-icon" onclick="verDetalle('nuevos_<?= (int) $a['id'] ?>')">
                                📄 Ver
                            </button>
                            <pre id="nuevos_<?= (int) $a['id'] ?>" 
                                 style="display:none;max-height:200px;overflow-y:auto;font-size:0.75rem;margin-top:4px;background:var(--bg-light);padding:8px;border-radius:4px"><?= Helpers::e(json_encode(json_decode($a['datos_nuevos']), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) ?></pre>
                        <?php else: ?>
                            <span class="text-muted">—</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

<script>
function verDetalle(id) {
    const el = document.getElementById(id);
    if (el.style.display === 'none' || el.style.display === '') {
        el.style.display = 'block';
    } else {
        el.style.display = 'none';
    }
}
</script>