let listaAsignaturas = [];
const colores = {
  matematicas: "#3891e9",
  lenguaje: "#f75353",
  historia: "#7ed321",
  ciencias: "#0da761",
  ingles: "#cdb51a",
  "estudios sociales": "#f5a623",
  "artes visuales": "#23babf",
  tic: "#8544cf",
  filosofia: "#cf58dcff",
  "instrumental 1": "#fb2b66",
  "instrumental 2": "#f16b3a",
  diferenciado: "#09dc84",
  jefatura: "#0c4d8e",
};

async function initHorario() {
  await cargarAsignaturas();
  await cargarCursosSelect();

  document
    .getElementById("selectCursoHorario")
    .addEventListener("change", function () {
      const cursoId = this.value;
      if (!cursoId) return;

      cargarHorario(cursoId);
    });
}

async function cargarCursosSelect() {
  try {
    const res = await fetch("/luminary/api/admin/cursos/cursos.php");
    const data = await res.json();

    if (!data.success) return;

    const select = document.getElementById("selectCursoHorario");

    data.cursos.forEach((curso) => {
      const option = document.createElement("option");
      option.value = curso.id;
      option.textContent = `${curso.curso}`;
      select.appendChild(option);
    });
  } catch (error) {
    console.error("Error cargando cursos:", error);
  }
}

function normalizar(texto) {
  if (!texto || typeof texto !== "string") return "";

  return texto
    .toLowerCase()
    .normalize("NFD")
    .replace(/[\u0300-\u036f]/g, "")
    .replace(/\s+/g, " ")
    .trim();
}

document.addEventListener("change", (e) => {
  if (e.target.classList.contains("select-horario")) {
    const select = e.target;

    const curso_id = document.getElementById("selectCursoHorario").value;
    const dia = select.dataset.dia;
    const bloque_id = select.dataset.bloque;

    // ✅ Si no hay valor → null
    const asignatura_id = select.value !== "" ? select.value : null;

    // ✅ Obtener nombre solo si existe
    const nombreAsignatura =
      select.value !== "" ? select.options[select.selectedIndex].text : null;

    let color = "#e0e0e0"; // 🔘 color por defecto (ninguna)

    if (nombreAsignatura) {
      const key = normalizar(nombreAsignatura);
      color = colores[key] ?? "#e0e0e0";
    }

    // ✅ Cambiar fondo del td
    select.parentElement.style.background = color;

    // ✅ Guardar en backend
    fetch("/luminary/api/admin/horario/guardar_horario.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({
        curso_id,
        dia,
        bloque_id,
        asignatura_id,
      }),
    })
      .then((res) => res.json())
      .then(() => {
        console.log("Guardado correctamente");
      })
      .catch((err) => console.error(err));
  }
});

async function cargarHorario(cursoId) {
  fetch(`/luminary/api/admin/horario/horario.php?curso_id=${cursoId}`)
    .then((res) => res.json())
    .then((data) => renderHorario(data))
    .catch((err) => console.error(err));

  function renderHorario(data) {
    const tbody = document.querySelector("#horario tbody");
    const dias = ["lunes", "martes", "miercoles", "jueves", "viernes"];

    const bloques = {};
    tbody.innerHTML = "";

    // Agrupar por bloque
    data.forEach((item) => {
      if (!bloques[item.bloque_id]) {
        bloques[item.bloque_id] = {
          hora: `${item.hora_inicio.slice(0, 5)} - ${item.hora_fin.slice(0, 5)}`,
          orden: item.orden,
        };
      }

      bloques[item.bloque_id][item.dia] = {
        nombre: item.asignatura,
        id: item.asignatura_id,
      };
    });

    // Pintar tabla
    Object.entries(bloques)
      .sort((a, b) => a[1].orden - b[1].orden)
      .forEach(([bloque_id, bloque]) => {
        const tr = document.createElement("tr");

        tr.innerHTML = `
    <td>${bloque.hora}</td>
    ${dias
      .map((d) => {
        const asignaturaData = bloque[d] || {};
        const asignaturaNombre = asignaturaData.nombre || "";
        const asignaturaId = asignaturaData.id || "";

        const key = asignaturaNombre ? normalizar(asignaturaNombre) : "";

        const color = colores[key] ?? "#e0e0e0";

        return `
    <td style="background:${color};">
      <select 
        data-dia="${d}"
        data-bloque="${bloque_id}"
        class="select-horario"
        style="
          background:transparent;
          border:none;
          color:#fff;
          text-align:center;
          font-weight:600;
        "
      >
        <option value="">Ninguna</option>
        ${listaAsignaturas
          .map((asig) => {
            const selected = asig.id == asignaturaId ? "selected" : "";

            return `<option value="${asig.id}" ${selected}>
                      ${asig.nombre}
                    </option>`;
          })
          .join("")}
      </select>
    </td>
  `;
      })
      .join("")}
  `;

        tbody.appendChild(tr);
      });
  }
}

async function cargarAsignaturas() {
  try {
    const res = await fetch("/luminary/api/admin/asignaturas/listar.php");
    listaAsignaturas = await res.json();
  } catch (error) {
    console.error("Error cargando asignaturas:", error);
  }
}
