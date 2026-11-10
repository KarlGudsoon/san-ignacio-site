const COLORES_ASIGNATURAS = Object.freeze({
  matematicas: "#3891e9",
  lenguaje: "#f75353",
  historia: "#7ed321",
  ciencias: "#0da761",
  ingles: "#cdb51a",
  "ingles comunicativo": "#f1660f",
  "estudios sociales": "#f5a623",
  "artes visuales": "#23babf",
  tic: "#8544cf",
  "consumo y calidad de vida": "#8544cf",
  filosofia: "#cf58dcff",
  "instrumental 1": "#fb2b66",
  "instrumental 2": "#f16b3a",
  diferenciado: "#09dc84",
  jefatura: "#0c4d8e",
  "pensamiento computacional": "#8544cf",
  "educacion financiera": "#8544cf",
  "convivencia social": "#54328a",
  "insercion laboral": "#54328a",
  "responsabilidad personal y social": "#54328a",
  "emprendimiento y empleabilidad": "#54328a",
});

const DIAS_SEMANA = ["lunes", "martes", "miercoles", "jueves", "viernes"];
const COLOR_CURSO = {
  "1°": "#0da761",
  "2°": "#3891e9",
};

function capitalizarPalabras(texto = "") {
  return texto
    .toLowerCase()
    .trim()
    .split(/\s+/)
    .map((palabra) => palabra.charAt(0).toUpperCase() + palabra.slice(1))
    .join(" ");
}

function normalizar(texto = "") {
  return texto
    .toLowerCase()
    .normalize("NFD")
    .replace(/[\u0300-\u036f]/g, "")
    .replace(/\s+/g, " ")
    .trim();
}

async function obtenerJson(url, opciones = {}) {
  const res = await fetch(url, opciones);

  if (!res.ok) {
    throw new Error(`Error ${res.status} al cargar ${url}`);
  }

  return res.json();
}

function actualizarTexto(selector, texto) {
  document.querySelectorAll(selector).forEach((el) => {
    el.textContent = texto;
  });
}

async function initInicio() {
  cargarPendientes();

  try {
    const data = await obtenerJson("/luminary/api/estudiante/me.php");
    if (!data || !data.nombre || !data.apellidos) {
      throw new Error("La respuesta del estudiante no es válida");
    }

    if (data.tipo !== "presencial") {
      cargarView("asignaturas");
      mostrarInformacionDistancia(data.nombre);
    }

    const nombreFormateado = capitalizarPalabras(data.nombre);
    const apellidosFormateado = capitalizarPalabras(data.apellidos);

    const primerNombre = nombreFormateado.trim().split(" ")[0];
    const primerApellido = apellidosFormateado.trim().split(" ")[0];

    actualizarTexto('[data-estudiante="nombre"]', nombreFormateado);
    actualizarTexto(
      '[data-estudiante="nombre-apellido"]',
      `${primerNombre} ${primerApellido}`,
    );
    actualizarTexto('[data-estudiante="apellidos"]', apellidosFormateado);
    actualizarTexto('[data-estudiante="curso"]', data.curso ?? "N/A");
    document.querySelectorAll('[data-estudiante="curso"]').forEach((el) => {
      el.style.backgroundColor = COLOR_CURSO[data.curso_nivel] ?? "";
    });

    cargarPromedioGeneral();

    cargarVistaPromedioAnual(data.nombre);

    const jornadas = {
      1: "Mañana",
      4: "Mañana",
      5: "Mañana",
      2: "Tarde",
      6: "Tarde",
      7: "Tarde",
      3: "Noche",
      8: "Noche",
      9: "Noche",
    };
    actualizarTexto(
      '[data-estudiante="jornada"]',
      jornadas[data.curso_id] ?? "N/A",
    );
  } catch (error) {
    console.error("Error cargando los datos del estudiante:", error);
    window.location.href = "/luminary/";
    return;
  }

  const anioActual = new Date().getFullYear();
  const PERIODOS = [
    {
      inicio: new Date(`${anioActual}-03-01`),
      fin: new Date(`${anioActual}-06-30`),
    },
    {
      inicio: new Date(`${anioActual}-07-01`),
      fin: new Date(`${anioActual}-12-31`),
    },
  ];

  function obtenerPeriodoActual(periodos) {
    const hoy = new Date();
    hoy.setHours(0, 0, 0, 0);

    return (
      periodos.find((p) => hoy >= p.inicio && hoy <= p.fin) ??
      periodos[periodos.length - 1]
    );
  }

  function calcularProgresoPorFecha(inicio, fin) {
    const hoy = new Date();
    const inicioDia = new Date(inicio);
    const finDia = new Date(fin);

    inicioDia.setHours(0, 0, 0, 0);
    finDia.setHours(0, 0, 0, 0);
    hoy.setHours(0, 0, 0, 0);

    if (hoy <= inicioDia) return 0;
    if (hoy >= finDia) return 100;

    const totalDias = (finDia - inicioDia) / (1000 * 60 * 60 * 24);
    const diasTranscurridos = (hoy - inicioDia) / (1000 * 60 * 60 * 24);

    return Math.round((diasTranscurridos / totalDias) * 100);
  }

  function setProgress(percent) {
    const circle = document.querySelector(".ring-progress");
    const text = document.getElementById("progress-text");
    if (!circle || !text) return;

    const radius = 54;
    const circumference = 2 * Math.PI * radius;

    circle.style.strokeDasharray = circumference;
    circle.style.strokeDashoffset =
      circumference - (percent / 100) * circumference;

    text.textContent = `${percent}%`;
  }

  const periodoActual = obtenerPeriodoActual(PERIODOS);
  const progreso = calcularProgresoPorFecha(
    periodoActual.inicio,
    periodoActual.fin,
  );

  setProgress(progreso);

  try {
    renderHorario(await obtenerJson("/luminary/api/estudiante/horario.php"));
  } catch (error) {
    console.error("Error cargando el horario:", error);
  }

  function renderHorario(data) {
    const tbody = document.querySelector("#horario tbody");
    if (!tbody || !Array.isArray(data)) return;
    tbody.replaceChildren();
    const bloques = {};

    data.forEach((item) => {
      if (!item || item.orden == null) return;
      if (!bloques[item.orden]) {
        bloques[item.orden] = {
          hora: `${item.hora_inicio?.slice(0, 5) ?? "--:--"} - ${item.hora_fin?.slice(0, 5) ?? "--:--"}`,
        };
      }
      bloques[item.orden][item.dia] = item.asignatura;
    });

    Object.values(bloques).forEach((bloque) => {
      const tr = document.createElement("tr");
      const hora = document.createElement("td");
      hora.textContent = bloque.hora;
      tr.appendChild(hora);

      DIAS_SEMANA.forEach((dia) => {
        const celda = document.createElement("td");
        const asignatura = bloque[dia];
        if (asignatura) {
          celda.textContent = asignatura;
          celda.style.backgroundColor =
            COLORES_ASIGNATURAS[normalizar(asignatura)] ?? "#e0e0e0";
          celda.style.color = "#fff";
        }
        tr.appendChild(celda);
      });
      tbody.appendChild(tr);
    });
  }

  document
    .getElementById("pendientes-container")
    ?.addEventListener("click", () => {
      cargarView("pendientes");
    });

  document.getElementById("notas-container")?.addEventListener("click", () => {
    cargarView("notas");
  });
}

function mostrarInformacion({
  titulo,
  mensaje,
  importante = "",
  tipo = "white",
  complemento = "",
  tabla = null,
} = {}) {
  const contenedor = document.getElementById("informaciones");
  const contenido = contenedor?.querySelector(".modal-content");
  if (!contenedor || !contenido) {
    console.error("No se encontró la estructura del modal de información.");
    return;
  }
  if (!window.bootstrap?.Modal) {
    console.error("Bootstrap Modal no está disponible.");
    return;
  }

  contenido.replaceChildren();
  const encabezado = document.createElement("div");
  encabezado.className = "modal-header";
  const tituloModal = document.createElement("h2");
  tituloModal.id = "informacionesTitulo";
  tituloModal.className = "modal-title fs-5";
  tituloModal.textContent = titulo || "Información";
  const cerrar = document.createElement("button");
  cerrar.type = "button";
  cerrar.className = "btn-close";
  cerrar.setAttribute("data-bs-dismiss", "modal");
  cerrar.setAttribute("aria-label", "Cerrar");
  encabezado.append(tituloModal, cerrar);

  const cuerpo = document.createElement("div");
  cuerpo.className = "modal-body d-flex flex-column items-center text-center";
  const mensajeModal = document.createElement("p");
  mensajeModal.className = "mensaje-modal mb-0";

  mensajeModal.textContent = mensaje || "";

  if (complemento) {
    const complementoModal = document.createElement("div");
    complementoModal.className = "complemento-modal mb-2 bg-success ";
    complementoModal.textContent = complemento || "";
    if (complemento === "P") {
      complementoModal.classList.add("bg-warning", "text-dark");
    }
    cuerpo.appendChild(complementoModal);
  }

  cuerpo.appendChild(mensajeModal);

  if (tabla) {
    const tablaModal = document.createElement("div");
    tablaModal.className = "tabla-modal mt-3 mb-3 flex flex-wrap justify-content-center gap-2";
    console.log("Tabla a mostrar en el modal:", tabla);
    tabla.forEach(({nombre, promedio_anual}) => {
      const fila = document.createElement("div");
      fila.className = "fila-modal";
      const nombreCelda = document.createElement("div");
      nombreCelda.className = "celda-modal";
      nombreCelda.textContent = nombre;
      fila.style.backgroundColor = COLORES_ASIGNATURAS[normalizar(nombre)] ?? "#e0e0e0";
      const promedioCelda = document.createElement("div");
      promedioCelda.className = "promedio-modal";
      promedioCelda.textContent = promedio_anual;
      fila.appendChild(nombreCelda);
      fila.appendChild(promedioCelda);
      tablaModal.appendChild(fila);
    });
    cuerpo.appendChild(tablaModal);
  }

  if (importante) {
    const importanteModal = document.createElement("strong");
    importanteModal.className = `text-${tipo}`;
    importanteModal.textContent = importante;
    cuerpo.appendChild(importanteModal);
  }

  contenido.append(encabezado, cuerpo);
  window.bootstrap.Modal.getOrCreateInstance(contenedor).show();
}

function mostrarInformacionDistancia(nombre) {
  mostrarInformacion({
    titulo: `¡Hola ${capitalizarPalabras(nombre)}!`,
    mensaje:
      "Actualmente estás registrado como estudiante a distancia. A continuación encontrarás el material a trabajar durante el primer semestre. Debes desarrollar cada actividad usando el material de estudio. Tus respuestas pueden ser enviadas indicando tu curso y nombre al correo cesi.utp2014@gmail.com",
    importante:
      "Importante: Recuerda que debes enviar tus respuestas al correo indicado para que puedan ser evaluadas.",
    tipo: "white",
  });
}

async function cargarVistaPromedioAnual(nombre) {
  try {
    const data = await obtenerJson(
      `/luminary/api/estudiante/notas/promedio_anual.php`,
      { cache: "no-store" },
    );
    if (!data?.success) return;

    if (data.promedio_anual === null) {
      mostrarInformacion({
        titulo: `¡Hola, ${capitalizarPalabras(nombre)}!`,
        mensaje:
          "Actualmente no se ha registrado un promedio anual para ti. Esto puede deberse a que aún no se han ingresado todas tus calificaciones o a que tu desempeño académico aún está en proceso de evaluación.",
        tipo: "white",
      });
      return;
    }
    if (data.promedio_anual === "P") {
      mostrarInformacion({
        titulo: `¡Hola, ${capitalizarPalabras(nombre)}!`,
        mensaje:
          "Tu promedio anual actualmente está pendiente de cálculo. Esto se debe a que el promedio esta aún esta en revisión o estas pendiente en alguna asignatura.",
        tipo: "white",
        complemento: data.promedio_anual,
        importante:
          "Te contactaremos para resolver tu situación académica",
        tabla: data.asignaturas,
      });
      return;
    }
    mostrarInformacion({
      titulo: `¡Hola, ${capitalizarPalabras(nombre)}!`,
      mensaje:
        "Este es tu promedio anual. Esto refleja tu desempeño académico durante el año, felicitaciones.",
      tipo: "white",
      complemento: data.promedio_anual,
      importante:
        "Si ves esta información, es por que estas al día con tus evaluaciones.",
    });
  } catch (error) {
    console.error("Error cargando promedios:", error);
  }
}

async function cargarPromedioGeneral() {
  try {
    const data = await obtenerJson(
      `/luminary/api/estudiante/notas/notas_asignatura.php`,
      { cache: "no-store" },
    );

    if (!data?.success || typeof data.promedio_general_anio !== "number")
      return;

    document.querySelectorAll('[data-estudiante="promedioG"]').forEach((el) => {
      el.textContent =
        data.promedio_general_anio >= 6.0
          ? "Excelente"
          : data.promedio_general_anio >= 5.0
            ? "Bueno"
            : data.promedio_general_anio >= 4.0
              ? "Regular"
              : "Necesitas mejorar";
      el.className = data.promedio_general_anio >= 4.0 ? "verde" : "rojo";
    });
  } catch (error) {
    console.error("Error cargando estudiantes:", error);
  }
}

async function cargarPendientes() {
  try {
    const data = await obtenerJson(
      `/luminary/api/estudiante/notas/notas_pendientes.php`,
      { cache: "no-store" },
    );

    if (!data?.success) return;

    actualizarTexto('[data-estudiante="pendientes"]', data.cantidad ?? "N/A");
  } catch (error) {
    console.error("Error cargando notas pendientes:", error);
  }
}
