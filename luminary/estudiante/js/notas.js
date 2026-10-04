let notasData = null;

async function initNotas() {
  try {
    const res = await fetch(
      `/luminary/api/estudiante/notas/notas_asignatura.php`,
      { cache: "no-store" },
    );
    const data = await res.json();
    if (!data.success) return;

    notasData = data;

    const selector = document.getElementById("selector-semestre");
    selector.addEventListener("change", () => renderNotas(selector.value));
    renderNotas(selector.value);
  } catch (error) {
    console.error("Error cargando notas:", error);
  }
}

function renderNotas(semestre) {
  const tabla = document.getElementById("tabla-notas");
  tabla.classList.add("tabla-notas-estudiante");
  tabla.innerHTML = "";

  // Filtra las notas del semestre elegido
  const notas = {};
  for (const asignatura in notasData.notas) {
    const original = notasData.notas[asignatura];
    notas[asignatura] = {
      pendientes: original.pendientes,
      notas: original.notas.filter((n) => String(n.semestre) === semestre),
    };
  }

  let maxNotas = 0;
  for (const a in notas) {
    maxNotas = Math.max(maxNotas, notas[a].notas.length);
  }

  // ---------- THEAD ----------
  const thead = document.createElement("thead");
  const headerRow = document.createElement("tr");
  headerRow.innerHTML = "<th>Asignatura</th>";
  for (let i = 1; i <= maxNotas; i++) {
    headerRow.innerHTML += `<th>Nota ${i}</th>`;
  }
  headerRow.innerHTML += "<th>Promedio</th>";
  thead.appendChild(headerRow);
  tabla.appendChild(thead);

  // ---------- TBODY ----------
  const tbody = document.createElement("tbody");
  const promediosAsignaturas = [];

  for (const asignatura in notas) {
    const { notas: notasAsig, pendientes } = notas[asignatura];
    const row = document.createElement("tr");

    const tdAsignatura = document.createElement("td");
    tdAsignatura.innerHTML = `
      <div class="asignatura-td asignatura-${asignatura.toLowerCase().replace(/\s+/g, "-")}">
        <p>${asignatura}</p>
        ${pendientes > 0 ? `<span class="badge-pendiente">${pendientes} P</span>` : ""}
      </div>`;
    row.appendChild(tdAsignatura);

    for (let i = 0; i < maxNotas; i++) {
      const td = document.createElement("td");
      td.textContent = notasAsig[i] ? notasAsig[i].nota : "-";
      row.appendChild(td);
    }

    const numericas = notasAsig
      .map((n) => parseFloat(n.nota))
      .filter((n) => !isNaN(n));

    let promedio = "N/A";
    if (numericas.length > 0) {
      const prom = numericas.reduce((a, b) => a + b, 0) / numericas.length;
      promedio = prom.toFixed(1);
      promediosAsignaturas.push(prom);
    }

    const tdPromedio = document.createElement("td");
    tdPromedio.innerHTML = `<div class="td-central"><span class="${pendientes > 0 ? "pendiente" : ""}">${promedio}</span></div>`;
    row.appendChild(tdPromedio);
    tbody.appendChild(row);
  }

  // ---------- PROMEDIO GENERAL DEL SEMESTRE ----------
  const promedioGeneral = promediosAsignaturas.length
    ? (
        promediosAsignaturas.reduce((a, b) => a + b, 0) /
        promediosAsignaturas.length
      ).toFixed(1)
    : "N/A";

  const rowFinal = document.createElement("tr");
  rowFinal.innerHTML = "<td>Promedio General</td>" + "<td></td>".repeat(maxNotas);

  const tdGeneral = document.createElement("td");
  const color =
    promedioGeneral === "N/A"
      ? ""
      : parseFloat(promedioGeneral) < 4.0
        ? "red"
        : "green";
  tdGeneral.innerHTML = `<div class="td-central"><span style="color:${color}">${promedioGeneral}</span></div>`;
  rowFinal.appendChild(tdGeneral);
  tbody.appendChild(rowFinal);
  tabla.appendChild(tbody);

  const contenedor = document.getElementById("promedio-general");
  if (contenedor) contenedor.textContent = promedioGeneral;
}