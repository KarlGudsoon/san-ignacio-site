// =====================================================
// notas-comun.js
// Lógica compartida para editar notas (asignatura.js y evaluaciones.js).
// Debe cargarse ANTES de los otros dos archivos.
// =====================================================

const NOTAS_LETRAS = ["L", "ML", "NL", "P", "E"];
const CLASES_COLOR = ["nota-azul", "nota-verde", "nota-roja", "nota-p"];

// ---------- Utilidades ----------
function escaparHTML(texto) {
  return String(texto ?? "").replace(
    /[&<>"']/g,
    (c) =>
      ({ "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#39;" })[
        c
      ],
  );
}

function debounce(fn, ms) {
  let t;
  return (...args) => {
    clearTimeout(t);
    t = setTimeout(() => fn(...args), ms);
  };
}

// ---------- Estado global de guardado ----------
const estadoGuardado = {
  pendientes: 0,
  conError: new Set(),
  elemento: null,

  // Cada vista llama a esto con su propio indicador (<span class="estado-guardado">)
  vincular(elemento) {
    this.elemento = elemento;
    this.render();
  },
  iniciar() {
    this.pendientes++;
    this.render();
  },
  terminar() {
    this.pendientes = Math.max(0, this.pendientes - 1);
    this.render();
  },
  marcarError(input, hayError) {
    if (hayError) this.conError.add(input);
    else this.conError.delete(input);
    this.render();
  },
  render() {
    // Ignora inputs que ya no están en el DOM
    this.conError.forEach((i) => {
      if (!i.isConnected) this.conError.delete(i);
    });
    if (!this.elemento) return;

    let estado, texto;
    if (this.pendientes > 0) {
      estado = "guardando";
      texto = "Guardando…";
    } else if (this.conError.size > 0) {
      const n = this.conError.size;
      estado = "error";
      texto = `⚠ ${n} nota${n > 1 ? "s" : ""} sin guardar`;
    } else {
      estado = "ok";
      texto = "✓ Todos los cambios guardados";
    }
    this.elemento.dataset.estado = estado;
    this.elemento.textContent = texto;
  },
  hayTrabajoPendiente() {
    return this.pendientes > 0 || this.conError.size > 0;
  },
};

window.addEventListener("beforeunload", (e) => {
  if (estadoGuardado.hayTrabajoPendiente()) {
    e.preventDefault();
    e.returnValue = "";
  }
});

// ---------- Formato, validación y estilos ----------
function formatearNota(input) {
  const permitidas = ["L", "M", "ML", "N", "NL", "P", "E"]; // M y N: letras intermedias al escribir ML / NL
  let valor = input.value.toUpperCase().trim();

  if (permitidas.includes(valor)) {
    input.value = valor;
    return;
  }

  valor = valor.replace(/[^0-9]/g, "").slice(0, 2);
  input.value = valor.length === 2 ? valor[0] + "." + valor[1] : valor;
}

// Devuelve { ok, valor }. Si no es válida: { ok: false }
function normalizarNota(texto) {
  const v = String(texto).toUpperCase().trim();
  if (v === "") return { ok: true, valor: "" };
  if (NOTAS_LETRAS.includes(v)) return { ok: true, valor: v };

  const n = parseFloat(v);
  if (isNaN(n)) return { ok: false };
  return { ok: true, valor: Math.min(7, Math.max(1, n)).toFixed(1) };
}

function clasePorValor(valor) {
  const v = String(valor ?? "").toUpperCase();
  if (v === "P") return "nota-p";
  if (v === "L") return "nota-azul";
  if (v === "ML") return "nota-verde";
  if (v === "NL") return "nota-roja";
  const n = parseFloat(v);
  if (isNaN(n)) return "";
  return n >= 4 ? "nota-azul" : "nota-roja";
}

function aplicarClaseNota(input, valor) {
  input.classList.remove(...CLASES_COLOR);
  const clase = clasePorValor(valor);
  if (clase) input.classList.add(clase);
}

// Estado visual de la celda: "guardando" | "guardada" | "error" | null
function setEstadoCelda(input, estado, mensaje = "") {
  input.classList.remove("nota-guardando", "nota-guardada", "nota-error");
  if (estado) input.classList.add(`nota-${estado}`);
  input.title = estado === "error" ? mensaje : "";
}

// ---------- Guardado ----------
async function guardarNota(evaluacionId, estudianteId, nota) {
  const controller = new AbortController();
  const timeout = setTimeout(() => controller.abort(), 15000);

  try {
    const res = await fetch(
      "/luminary/api/docente/evaluaciones/guardar_nota.php",
      {
        method: "POST",
        body: new URLSearchParams({
          evaluacion_id: evaluacionId,
          estudiante_id: estudianteId,
          nota: nota,
        }),
        signal: controller.signal,
      },
    );
    if (!res.ok) throw new Error(`Error ${res.status}`);

    // Si responde JSON con success:false es error; si no es JSON, basta el 2xx.
    const texto = await res.text();
    try {
      const data = JSON.parse(texto);
      if (data && data.success === false) {
        throw new Error(data.message || "El servidor rechazó la nota");
      }
    } catch (e) {
      if (!(e instanceof SyntaxError)) throw e;
    }
    return { ok: true };
  } catch (err) {
    return {
      ok: false,
      error:
        err.name === "AbortError" ? "Tiempo de espera agotado" : err.message,
    };
  } finally {
    clearTimeout(timeout);
  }
}

async function validarYGuardar(input, evaluacionId, estudianteId, onGuardado) {
  const guardado = input.dataset.valorGuardado ?? "";
  const resultado = normalizarNota(input.value);

  // 1) Valor inválido: volver al último valor guardado y avisar
  if (!resultado.ok) {
    input.value = guardado;
    aplicarClaseNota(input, guardado);
    setEstadoCelda(input, "error", "Nota no válida");
    setTimeout(() => {
      if (!estadoGuardado.conError.has(input) && !input._enCurso) {
        setEstadoCelda(input, null);
      }
    }, 1500);
    return;
  }

  const valor = resultado.valor;
  input.value = valor;
  aplicarClaseNota(input, valor);

  // 2) Evitar guardados innecesarios o duplicados
  const enCurso = input._enCurso > 0;
  const base = enCurso ? input._ultimoEnviado : guardado;
  if (valor === base) {
    if (!enCurso && estadoGuardado.conError.has(input)) {
      estadoGuardado.marcarError(input, false); // volvió al valor guardado
      setEstadoCelda(input, null);
    }
    return;
  }

  // 3) Guardar, serializando por celda para que lleguen en orden
  const seq = (input._seq = (input._seq || 0) + 1);
  input._enCurso = (input._enCurso || 0) + 1;
  input._ultimoEnviado = valor;

  setEstadoCelda(input, "guardando");
  estadoGuardado.iniciar();

  const cola = (input._cola || Promise.resolve()).then(() =>
    guardarNota(evaluacionId, estudianteId, valor),
  );
  input._cola = cola;
  const res = await cola;

  input._enCurso--;
  estadoGuardado.terminar();

  if (res.ok) {
    input.dataset.valorGuardado = valor;
    if (seq !== input._seq) return; // hay un guardado más reciente
    estadoGuardado.marcarError(input, false);
    setEstadoCelda(input, "guardada");
    setTimeout(() => {
      if (input.classList.contains("nota-guardada"))
        setEstadoCelda(input, null);
    }, 1000);
    onGuardado?.();
  } else {
    if (seq !== input._seq) return;
    estadoGuardado.marcarError(input, true);
    setEstadoCelda(
      input,
      "error",
      `No se pudo guardar (${res.error}). Sal de la celda para reintentar.`,
    );
  }
}

// ---------- Piezas reutilizables para las vistas ----------

// HTML del input de nota (mismo markup en todas las vistas)
function htmlInputNota({ valor, evaluacionId, estudianteId }) {
  const v = valor === null || valor === undefined ? "" : String(valor);
  const norm = normalizarNota(v);
  const guardado = norm.ok ? norm.valor : v;

  return `<input type="text" class="nota-input ${clasePorValor(v)}"
    value="${escaparHTML(v)}"
    autocomplete="off"
    data-evaluacion-id="${evaluacionId}"
    data-estudiante-id="${estudianteId}"
    data-valor-guardado="${escaparHTML(guardado)}">`;
}

// Registra los eventos UNA sola vez sobre un contenedor (delegación).
// Sirve aunque el contenido interno se vuelva a renderizar.
function activarEdicionNotas(contenedor, onGuardado) {
  if (contenedor._notasActivas) return;
  contenedor._notasActivas = true;

  const esInputNota = (el) =>
    el instanceof HTMLInputElement && el.classList.contains("nota-input");

  contenedor.addEventListener("input", (e) => {
    if (esInputNota(e.target)) formatearNota(e.target);
  });

  // focusout (en vez de change): también permite reintentar tras un error
  contenedor.addEventListener("focusout", (e) => {
    const input = e.target;
    if (!esInputNota(input)) return;
    validarYGuardar(
      input,
      input.dataset.evaluacionId,
      input.dataset.estudianteId,
      onGuardado,
    );
  });

  // Enter baja a la nota del siguiente estudiante
  contenedor.addEventListener("keydown", (e) => {
    if (e.key !== "Enter" || !esInputNota(e.target)) return;
    e.preventDefault();
    const input = e.target;
    const idx = input.closest("td").cellIndex;
    const siguiente = input
      .closest("tr")
      .nextElementSibling?.cells[idx]?.querySelector(".nota-input");
    if (siguiente) siguiente.focus();
    else input.blur();
  });
}
