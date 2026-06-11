/* ═══════════════════════════════════════════════════════════════════════════
   main.js — Tienda Doña Elena
   Utilidades globales: sidebar toggle, fecha en topbar, toast helper.
   ═══════════════════════════════════════════════════════════════════════════ */

document.addEventListener("DOMContentLoaded", function () {
  // ── Fecha en topbar ───────────────────────────────────────────────────────
  const dateEl = document.getElementById("topbarDate");
  if (dateEl) {
    const now = new Date();
    dateEl.textContent = now.toLocaleDateString("es-BO", {
      weekday: "short",
      year: "numeric",
      month: "short",
      day: "numeric",
    });
  }

  // ── Título dinámico del topbar ────────────────────────────────────────────
  const titleEl = document.getElementById("pageTitle");
  if (titleEl) {
    const path = window.location.pathname.split("/").filter(Boolean);
    const names = {
      dashboard: "Dashboard",
      productos: "Productos",
      inventario: "Inventario",
      ventas: "Punto de Venta",
      usuarios: "Usuarios",
      pedidos: "Pedidos",
      prediccion: "Predicción de Demanda",
      auditoria:"Auditoría",
      categorias: "Categorías",
      clientes: "Clientes",
      empleados: "Empleados",
      empresas: "Empresas",
      facturas: "Facturas",
      inventario:"Inventario",
      proveedores: "Proveedores",
      reportes: "Reportes",
      roles: "Roles",
      emails: "Emails",
      telefonos: "Teléfonos",
      subcategorias: "Subcategorías",
      magnitudes: "Magnitudes",
      
    };
    titleEl.textContent = names[path[0]] || "Panel";
  }

  // ── Sidebar toggle (móvil) ────────────────────────────────────────────────
  const toggleBtn = document.getElementById("sidebarToggle");
  const sidebar = document.querySelector(".app-sidebar");

  if (toggleBtn && sidebar) {
    toggleBtn.addEventListener("click", function () {
      sidebar.classList.toggle("open");
    });

    // Cerrar al hacer clic fuera (móvil)
    document.addEventListener("click", function (e) {
      if (!sidebar.contains(e.target) && e.target !== toggleBtn) {
        sidebar.classList.remove("open");
      }
    });
  }

  // ── Auto-dismiss alertas de Bootstrap ────────────────────────────────────
  document.querySelectorAll(".alert").forEach(function (alert) {
    setTimeout(function () {
      const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
      if (bsAlert) bsAlert.close();
    }, 5000);
  });
});

// ── Toast global (llamable desde cualquier vista) ─────────────────────────────
/**
 * Muestra un toast Bootstrap en la esquina inferior derecha.
 * @param {string} message
 * @param {'success'|'danger'|'warning'|'info'} type
 */
function showToast(message, type = "info") {
  const toastEl = document.getElementById("appToast");
  const msgEl = document.getElementById("toastMsg");
  const titleEl = document.getElementById("toastTitle");

  if (!toastEl || !msgEl) return;

  const titles = {
    success: "Éxito",
    danger: "Error",
    warning: "Atención",
    info: "Aviso",
  };
  titleEl.textContent = titles[type] || "Aviso";
  msgEl.textContent = message;

  // Limpiar clases anteriores
  toastEl.className = "toast";
  toastEl.classList.add(
    `bg-${type === "danger" ? "danger" : type}`,
    "text-white",
  );

  const toast = bootstrap.Toast.getOrCreateInstance(toastEl, { delay: 4000 });
  toast.show();
}

document.addEventListener("DOMContentLoaded", () => {
  const btn = document.getElementById("btn-confirmar");
  if (!btn) return;

  btn.addEventListener("click", async () => {
    const filas = document.querySelectorAll("#tabla-sugeridos tbody tr");
    const items = [];

    filas.forEach((fila) => {
      const idPedido = parseInt(fila.getAttribute("data-id"));
      const idProducto = parseInt(fila.getAttribute("data-producto"));
      const input = fila.querySelector(".input-sugerido");
      const cant = parseInt(input?.value) || 0;

      if (idPedido && idProducto && cant > 0) {
        items.push({
          id_pedido: idPedido,
          id_producto: idProducto,
          cantidad: cant,
        });
      }
    });

    if (items.length === 0) {
      alert("No hay cantidades positivas para pedir.");
      return;
    }

    if (!confirm("¿Confirma el abastecimiento de estos productos?")) return;

    try {
      const res = await fetch("/pedidos/confirmarPedido", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ items }),
      });

      const data = await res.json();
      if (data.status === "success") {
        alert(data.message);
        location.reload();
      } else {
        alert("Error: " + data.message);
      }
    } catch (err) {
      console.error(err);
      alert("Error de conexión");
    }
  });
});

document.addEventListener("DOMContentLoaded", function () {
  const card = document.getElementById("cardPrediccion");
  if (!card) return;

  const btnLista = document.getElementById("btnLista");
  const btnGrafico = document.getElementById("btnGrafico");
  const tablaDiv = document.getElementById("tablaPredicciones");
  const contenedorGrafico = document.getElementById("contenedorGrafico");
  const canvas = document.getElementById("graficoDemanda");

  let predicciones = [];
  try {
    predicciones = JSON.parse(card.getAttribute("data-predicciones") || "[]");
  } catch (e) {
    console.error("Error al leer predicciones:", e);
  }

  let chartInstance = null;

  function activarPestana(activo) {
    if (activo === "lista") {
      btnLista.classList.add("tab-active");
      btnGrafico.classList.remove("tab-active");
      tablaDiv.style.display = "";
      contenedorGrafico.style.display = "none";
      if (chartInstance) {
        chartInstance.destroy();
        chartInstance = null;
      }
    } else {
      btnGrafico.classList.add("tab-active");
      btnLista.classList.remove("tab-active");
      tablaDiv.style.display = "none";
      contenedorGrafico.style.display = "";
      dibujarGrafico();
    }
  }

  function dibujarGrafico() {
    if (chartInstance) return;
    if (predicciones.length === 0) return;

    const nombres = predicciones.map((p) => p.nombre);
    const demandas = predicciones.map((p) => parseFloat(p.cantidad_predicha));

    chartInstance = new Chart(canvas, {
      type: "bar",
      data: {
        labels: nombres,
        datasets: [
          {
            label: "Demanda Esperada",
            data: demandas,
            backgroundColor: "rgba(54, 162, 235, 0.6)",
            borderColor: "rgba(54, 162, 235, 1)",
            borderWidth: 1,
            barThickness: 8,
          },
        ],
      },
      options: {
        indexAxis: "y",
        responsive: true,
        maintainAspectRatio: false,
        categoryPercentage: 0.9,
        barPercentage: 0.9,
        scales: {
          x: { beginAtZero: true, title: { display: true, text: "Unidades" } },
        },
        plugins: { legend: { display: false } },
      },
    });
  }

  // Estado inicial
  activarPestana("lista");

  btnLista.addEventListener("click", () => activarPestana("lista"));
  btnGrafico.addEventListener("click", () => activarPestana("grafico"));
});
