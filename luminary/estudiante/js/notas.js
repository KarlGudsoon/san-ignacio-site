async function initNotas() {
  const res = await fetch(`/luminary/api/estudiante/notas.php`);
  const data = await res.json();

  const contenedor = document.getElementById("tabla-notas");
  contenedor.innerHTML = "";

  if (!data.length) {
    contenedor.textContent = "No hay notas registradas";
    return;
  }

  const table = document.createElement("table");
  table.classList.add("tabla-notas");

  // ---- HEADER ----
  const thead = document.createElement("thead");
  const headerRow = document.createElement("tr");

  const headers = ["Asignatura"];

  for (let i = 1; i <= 9; i++) {
    headers.push(`N${i}`);
  }

  headers.push("Promedio");

  headers.forEach(text => {
    const th = document.createElement("th");
    th.textContent = text;
    headerRow.appendChild(th);
  });

  thead.appendChild(headerRow);
  table.appendChild(thead);

  // ---- BODY ----
  const tbody = document.createElement("tbody");

  data.forEach(asig => {
    const row = document.createElement("tr");

    // Asignatura (por ahora solo ID)
    const tdAsignatura = document.createElement("td");
    tdAsignatura.textContent = `Asignatura ${asig.asignatura_id}`;
    row.appendChild(tdAsignatura);

    // Notas
    for (let i = 1; i <= 9; i++) {
      const td = document.createElement("td");
      const valor = asig[`nota${i}`];

      td.textContent = valor ?? "-";

      if (valor) {
        td.classList.add(Number(valor) >= 4 ? "aprobada" : "reprobada");
      }

      row.appendChild(td);
    }

    // Promedio
    const tdPromedio = document.createElement("td");
    tdPromedio.textContent = asig["x̄"] ?? "-";
    tdPromedio.classList.add("promedio-tabla");

    row.appendChild(tdPromedio);

    tbody.appendChild(row);
  });

  table.appendChild(tbody);
  contenedor.appendChild(table);
}
