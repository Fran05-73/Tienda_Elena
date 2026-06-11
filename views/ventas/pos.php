<div class="pos-layout">

    <!-- ─── Panel Izquierdo: Buscador + Carrito ─────────────────────────── -->
    <div class="pos-left">

        <!-- Buscador -->
        <div class="app-card">
            <label class="form-label fw-semibold mb-2">🔍 Buscar Producto</label>
            <input type="text" id="posBuscar" class="form-input" placeholder="Nombre, SKU o código de barras..."
                autocomplete="off">
            <div id="posResultados" class="pos-resultados"></div>
        </div>

        <!-- Carrito -->
        <div class="app-card" style="margin-top:16px">
            <div class="app-card-header">
                <span class="app-card-title">🛒 Carrito</span>
                <button class="btn-app-secondary" onclick="limpiarCarrito()" style="font-size:.8rem">
                    Limpiar
                </button>
            </div>

            <div id="carritoVacio" class="text-muted py-3" style="text-align:center">
                Agrega productos al carrito
            </div>

            <table class="app-table" id="carritoTabla" style="display:none">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th style="text-align:center">Cant.</th>
                        <th style="text-align:right">P.Unit</th>
                        <th style="text-align:right">Subtotal</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody id="carritoBody"></tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" style="text-align:right;font-weight:700;font-size:1.1rem">
                            TOTAL:
                        </td>
                        <td style="text-align:right;font-weight:700;font-size:1.1rem;color:var(--primary)"
                            id="totalDisplay">
                            Bs. 0.00
                        </td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>

    </div>

    <!-- ─── Panel Derecho: Datos de venta + Pago ────────────────────────── -->
    <div class="pos-right">
        <div class="app-card">
            <h3 class="app-card-title mb-3">💰 Registrar Venta</h3>

            <div class="form-group mb-3">
                <label class="form-label fw-semibold">
                    Cliente <span class="required">*</span>
                </label>
                <select id="posCliente" class="form-select" required>
                    <option value="">Seleccionar cliente...</option>
                    <?php foreach ($clientes as $c): ?>
                        <option value="<?= (int) $c['id_cliente'] ?>">
                            <?= Helpers::e($c['nombre_completo']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group mb-3">
                <label class="form-label fw-semibold">
                    Empleado <span class="required">*</span>
                </label>
                <select id="posEmpleado" class="form-select" required>
                    <option value="">Seleccionar empleado...</option>
                    <?php foreach ($empleados as $e): ?>
                        <option value="<?= (int) $e['id_empleado'] ?>">
                            <?= Helpers::e($e['nombre_completo']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group mb-3">
                <label class="form-label fw-semibold">
                    Método de Pago <span class="required">*</span>
                </label>
                <select id="posMetodoPago" class="form-select" required>
                    <option value="">Seleccionar...</option>
                    <option value="efectivo">Efectivo</option>
                    <option value="transferencia">Transferencia</option>
                    <option value="tarjeta">Tarjeta</option>
                </select>
            </div>

            <div class="form-group mb-4">
                <label class="form-label fw-semibold">Monto Pagado (Bs.)</label>
                <input type="number" id="posMontoPagado" class="form-input" min="0" step="0.01" placeholder="0.00">
                <div id="posVuelto" style="margin-top:6px;font-weight:600;color:var(--success)"></div>
            </div>

            <div style="background:var(--bg-light);border-radius:8px;padding:14px;margin-bottom:18px;text-align:right">
                <span style="font-size:.9rem;color:var(--text-muted)">Total a cobrar</span><br>
                <span id="posTotal" style="font-size:1.6rem;font-weight:700;color:var(--primary)">
                    Bs. 0.00
                </span>
            </div>

            <button id="btnConfirmarVenta" class="btn-app-primary" style="width:100%;padding:14px;font-size:1rem"
                onclick="confirmarVenta()">
                ✔ Confirmar Venta
            </button>

            <div id="posError" class="mt-3 p-3 rounded"
                style="display:none;background:#f8d7da;color:#842029;border:1px solid #f5c2c7"></div>
            <div id="posExito" class="mt-3 p-3 rounded"
                style="display:none;background:#d1e7dd;color:#0f5132;border:1px solid #badbcc"></div>
        </div>
    </div>

</div>
<!-- Modal para datos adicionales de pago -->
<div class="modal fade" id="modalPago" tabindex="-1" aria-labelledby="modalPagoLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalPagoLabel">Datos del Pago</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body" id="modalPagoBody">
                <!-- Los campos se inyectan dinámicamente según el método -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnConfirmarPago">Confirmar Pago</button>
            </div>
        </div>
    </div>
</div>

<script>
    // ── Estado del carrito ────────────────────────────────────────────────────────
    let carrito = [];
    let debounceTimer;

    // ── Buscador ──────────────────────────────────────────────────────────────────
    document.getElementById('posBuscar').addEventListener('input', function () {
        clearTimeout(debounceTimer);
        const q = this.value.trim();
        if (q.length < 1) {
            document.getElementById('posResultados').innerHTML = '';
            return;
        }
        debounceTimer = setTimeout(() => buscarProductos(q), 250);
    });

    async function buscarProductos(q) {
        try {
            const res = await fetch(`/productos/buscar?q=${encodeURIComponent(q)}`);
            const data = await res.json();
            renderResultados(data);
        } catch (e) {
            console.error('Error buscando productos', e);
        }
    }

    function renderResultados(productos) {
        const div = document.getElementById('posResultados');
        if (!productos.length) {
            div.innerHTML = '<p class="text-muted py-2" style="font-size:.9rem">Sin resultados.</p>';
            return;
        }
        div.innerHTML = productos.map(p => `
        <div class="pos-result-item" onclick="agregarAlCarrito(${JSON.stringify(p).replace(/"/g, '&quot;')})">
            <div>
                <strong>${escHtml(p.nombre)}</strong>
                <span class="sku-code" style="margin-left:6px">${escHtml(p.sku)}</span>
            </div>
            <div style="display:flex;gap:16px;font-size:.88rem;color:var(--text-muted)">
                <span>Bs. ${parseFloat(p.precio).toFixed(2)}</span>
                <span>Stock: ${parseInt(p.stock)}</span>
            </div>
        </div>
    `).join('');
    }

    // ── Carrito ───────────────────────────────────────────────────────────────────
    function agregarAlCarrito(p) {
        document.getElementById('posBuscar').value = '';
        document.getElementById('posResultados').innerHTML = '';

        const existente = carrito.find(i => i.id_producto === p.id_producto);
        if (existente) {
            if (existente.cantidad < parseInt(p.stock)) {
                existente.cantidad++;
            } else {
                mostrarError('Stock máximo alcanzado para «' + p.nombre + '».');
                return;
            }
        } else {
            if (parseInt(p.stock) < 1) {
                mostrarError('«' + p.nombre + '» no tiene stock disponible.');
                return;
            }
            carrito.push({
                id_producto: p.id_producto,
                nombre: p.nombre,
                precio: parseFloat(p.precio),
                cantidad: 1,
                stock_max: parseInt(p.stock),
            });
        }
        renderCarrito();
    }

    function cambiarCantidad(id, delta) {
        const item = carrito.find(i => i.id_producto == id);
        if (!item) return;
        item.cantidad += delta;
        if (item.cantidad < 1) {
            carrito = carrito.filter(i => i.id_producto != id);
        } else if (item.cantidad > item.stock_max) {
            item.cantidad = item.stock_max;
            mostrarError('Stock máximo para «' + item.nombre + '»: ' + item.stock_max);
        }
        renderCarrito();
    }

    function eliminarItem(id) {
        carrito = carrito.filter(i => i.id_producto != id);
        renderCarrito();
    }

    function limpiarCarrito() {
        carrito = [];
        renderCarrito();
    }

    function renderCarrito() {
        const tbody = document.getElementById('carritoBody');
        const tabla = document.getElementById('carritoTabla');
        const vacio = document.getElementById('carritoVacio');
        const total = carrito.reduce((s, i) => s + i.precio * i.cantidad, 0);

        if (carrito.length === 0) {
            tabla.style.display = 'none';
            vacio.style.display = 'block';
        } else {
            tabla.style.display = '';
            vacio.style.display = 'none';
        }

        tbody.innerHTML = carrito.map(item => `
        <tr>
            <td>${escHtml(item.nombre)}</td>
            <td style="text-align:center">
                <div style="display:flex;align-items:center;justify-content:center;gap:6px">
                    <button class="qty-btn" onclick="cambiarCantidad(${item.id_producto}, -1)">−</button>
                    <span style="min-width:24px;text-align:center">${item.cantidad}</span>
                    <button class="qty-btn" onclick="cambiarCantidad(${item.id_producto}, 1)">+</button>
                </div>
            </td>
            <td style="text-align:right">Bs. ${item.precio.toFixed(2)}</td>
            <td style="text-align:right;font-weight:600">
                Bs. ${(item.precio * item.cantidad).toFixed(2)}
            </td>
            <td>
                <button class="btn-icon danger" onclick="eliminarItem(${item.id_producto})">✕</button>
            </td>
        </tr>
    `).join('');

        const totalStr = 'Bs. ' + total.toFixed(2);
        document.getElementById('totalDisplay').textContent = totalStr;
        document.getElementById('posTotal').textContent = totalStr;
        calcularVuelto();
    }

    // ── Vuelto ────────────────────────────────────────────────────────────────────
    document.getElementById('posMontoPagado').addEventListener('input', calcularVuelto);

    function calcularVuelto() {
        const total = carrito.reduce((s, i) => s + i.precio * i.cantidad, 0);
        const pagado = parseFloat(document.getElementById('posMontoPagado').value) || 0;
        const divV = document.getElementById('posVuelto');
        const vuelto = pagado - total;
        divV.textContent = vuelto >= 0 ? `Cambio: Bs. ${vuelto.toFixed(2)}` : '';
    }

    // ── Confirmar venta ───────────────────────────────────────────────────────────
    // ── Confirmar venta (ahora abre el modal si se requiere) ─────────────────
    async function confirmarVenta() {
        limpiarMensajes();

        const clienteId = parseInt(document.getElementById('posCliente').value);
        const empleadoId = parseInt(document.getElementById('posEmpleado').value);
        const metodo = document.getElementById('posMetodoPago').value;
        const total = carrito.reduce((s, i) => s + i.precio * i.cantidad, 0);

        const montoRaw = document.getElementById('posMontoPagado').value.trim();
        const montoP = montoRaw !== '' ? parseFloat(montoRaw) : NaN;

        // ── Validaciones previas ─────────────────────────────────────
        if (!carrito.length) { mostrarError('El carrito está vacío.'); return; }
        if (!clienteId) { mostrarError('Selecciona un cliente.'); return; }
        if (!empleadoId) { mostrarError('Selecciona un empleado.'); return; }
        if (!metodo) { mostrarError('Selecciona el método de pago.'); return; }

        if (montoRaw === '') {
            mostrarError('Ingresa el monto pagado por el cliente.');
            document.getElementById('posMontoPagado').focus();
            return;
        }
        if (isNaN(montoP) || montoP <= 0) {
            mostrarError('El monto pagado debe ser un número mayor que 0.');
            document.getElementById('posMontoPagado').focus();
            return;
        }
        if (montoP < total - 0.001) {
            const falta = (total - montoP).toFixed(2);
            mostrarError(`Monto insuficiente. Faltan Bs. ${falta} para cubrir el total.`);
            document.getElementById('posMontoPagado').focus();
            return;
        }

        // ── Guardar datos para usar después en el modal ──────────────
        window._ventaPendiente = {
            clienteId, empleadoId, total, metodo, montoP
        };

        // ── Mostrar modal con campos según método ───────────────────
        abrirModalPago(metodo);
    }

    // ── Abre el modal y genera los campos dinámicos ─────────────────────────
    function abrirModalPago(metodo) {
        const body = document.getElementById('modalPagoBody');
        let html = '';

        // Mostrar monto a pagar
        html += `<p><strong>Total a cobrar:</strong> Bs. ${window._ventaPendiente.total.toFixed(2)}</p>`;
        html += `<p><strong>Monto entregado:</strong> Bs. ${window._ventaPendiente.montoP.toFixed(2)}</p>`;

        if (metodo === 'efectivo') {
            html += `
            <div class="mb-3">
                <label class="form-label">Recibido por (opcional)</label>
                <input type="text" id="modal_recibido_por" class="form-control" placeholder="Nombre de quien recibe">
            </div>`;
        } else if (metodo === 'transferencia') {
            html += `
            <div class="mb-3">
                <label class="form-label">Banco origen</label>
                <input type="text" id="modal_banco_origen" class="form-control" placeholder="Ej: Banco Mercantil">
            </div>
            <div class="mb-3">
                <label class="form-label">Número de operación</label>
                <input type="text" id="modal_numero_operacion" class="form-control" placeholder="N° de referencia">
            </div>`;
        } else if (metodo === 'tarjeta') {
            html += `
            <div class="mb-3">
                <label class="form-label">Nombre en tarjeta</label>
                <input type="text" id="modal_nombre_tarjeta" class="form-control" placeholder="Como aparece en la tarjeta">
            </div>
            <div class="mb-3">
                <label class="form-label">Número de tarjeta (últimos 4 dígitos)</label>
                <input type="text" id="modal_numero_tarjeta_mask" class="form-control" maxlength="4" placeholder="1234">
            </div>
            <div class="mb-3">
                <label class="form-label">Banco emisor</label>
                <input type="text" id="modal_banco_emisor" class="form-control" placeholder="Ej: Banco de Crédito">
            </div>`;
        }

        body.innerHTML = html;

        // Mostrar modal usando Bootstrap
        const modal = new bootstrap.Modal(document.getElementById('modalPago'));
        modal.show();
    }

    // ── Confirmar desde el modal ─────────────────────────────────────────────
    document.addEventListener('click', function (e) {
        if (e.target && e.target.id === 'btnConfirmarPago') {
            confirmarPagoModal();
        }
    });

    function confirmarPagoModal() {
        const data = window._ventaPendiente;
        if (!data) return;

        // Recoger campos adicionales
        let datosAdicionales = {};
        if (data.metodo === 'efectivo') {
            datosAdicionales.recibido_por = document.getElementById('modal_recibido_por')?.value?.trim() || null;
        } else if (data.metodo === 'transferencia') {
            datosAdicionales.banco_origen = document.getElementById('modal_banco_origen')?.value?.trim() || null;
            datosAdicionales.numero_operacion = document.getElementById('modal_numero_operacion')?.value?.trim() || null;
            if (!datosAdicionales.numero_operacion) {
                mostrarError('Debes ingresar el número de operación.');
                return;
            }
        } else if (data.metodo === 'tarjeta') {
            datosAdicionales.nombre_tarjeta = document.getElementById('modal_nombre_tarjeta')?.value?.trim() || null;
            datosAdicionales.numero_tarjeta_mask = document.getElementById('modal_numero_tarjeta_mask')?.value?.trim() || null;
            datosAdicionales.banco_emisor = document.getElementById('modal_banco_emisor')?.value?.trim() || null;
            if (!datosAdicionales.numero_tarjeta_mask) {
                mostrarError('Debes ingresar los últimos 4 dígitos de la tarjeta.');
                return;
            }
        }

        // Cerrar modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('modalPago'));
        modal.hide();

        // Construir payload final
        const payload = {
            cliente_id: data.clienteId,
            empleado_id: data.empleadoId,
            total: data.total,
            detalles: carrito.map(i => ({
                id_producto: i.id_producto,
                cantidad: i.cantidad,
                subtotal: i.precio * i.cantidad,
            })),
            pago: {
                metodo: data.metodo,
                monto: data.montoP,
                datos_adicionales: datosAdicionales
            }
        };

        // Enviar venta
        enviarVenta(payload);
    }

    // ── Envío al servidor ────────────────────────────────────────────────────
    async function enviarVenta(payload) {
        const btn = document.getElementById('btnConfirmarVenta');
        btn.disabled = true;
        btn.textContent = 'Procesando...';

        try {
            const res = await fetch('/ventas/guardarVenta', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload),
            });
            const data = await res.json();

            if (data.success) {
                const vuelto = payload.pago.monto - payload.total;
                let msg = `Venta #${data.id_venta} registrada. Total: Bs. ${payload.total.toFixed(2)}`;
                if (vuelto > 0.001) msg += ` · Cambio: Bs. ${vuelto.toFixed(2)}`;
                mostrarExito(msg);
                limpiarCarrito();
                document.getElementById('posCliente').value = '';
                document.getElementById('posEmpleado').value = '';
                document.getElementById('posMetodoPago').value = '';
                document.getElementById('posMontoPagado').value = '';
                document.getElementById('posVuelto').textContent = '';
            } else {
                mostrarError(data.message || 'Error desconocido al registrar la venta.');
            }
        } catch (e) {
            console.error('Error:', e);
            // Intenta leer la respuesta como texto para ver el error real
            try {
                const res = await fetch('/ventas/guardarVenta', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload),
                });
                const text = await res.text();
                console.log('Respuesta cruda:', text);
                mostrarError('Error del servidor. Ver consola.');
            } catch (e2) {
                mostrarError('Error de conexión.');
            }
        } finally {
            btn.disabled = false;
            btn.textContent = '✔ Confirmar Venta';
        }
    }

    // ── Utilidades ────────────────────────────────────────────────────────────────
    function escHtml(str) {
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    let _timerError = null;
    let _timerExito = null;

    function mostrarError(msg) {
        clearTimeout(_timerError);
        const el = document.getElementById('posError');
        el.textContent = msg;
        el.style.display = 'block';
        _timerError = setTimeout(() => el.style.display = 'none', 5000);
    }

    function mostrarExito(msg) {
        clearTimeout(_timerExito);
        const el = document.getElementById('posExito');
        el.textContent = msg;
        el.style.display = 'block';
        _timerExito = setTimeout(() => el.style.display = 'none', 6000);
    }

    function limpiarMensajes() {
        clearTimeout(_timerError);
        clearTimeout(_timerExito);
        document.getElementById('posError').style.display = 'none';
        document.getElementById('posExito').style.display = 'none';
    }
</script>