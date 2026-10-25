async function initNotas() {
  try {
    const res = await fetch(
      `/luminary/api/estudiante/notas/notas_asignatura.php`,
      { cache: "no-store" },
    );
    const data = await res.json();
    if (!data.success) return;

    await notasEstudianteVista(data);
  } catch (error) {
    console.error("Error cargando notas:", error);
  }
}

async function notasEstudianteVista(data) {
  try {
    const tabla = document.getElementById("tabla-notas");
    if (!tabla) return;
    tabla.classList.add("tabla-notas-estudiante");
    tabla.innerHTML = "";

    const SEMESTRES = [1, 2];
    const NOMBRES = { 1: "Primer semestre", 2: "Segundo semestre" };

    const fmt = (p) =>
      p === null || p === undefined ? "-" : Number(p).toFixed(1);

    const fmtNota = (nota) => {
      if (nota === null || nota === undefined) return "-";
      const num = parseFloat(nota);
      if (!isNaN(num)) {
        return num.toFixed(1);
      }
      return nota ?? "-";
    };

    const celdaPromedio = (p, clases = "") => {
      const td = document.createElement("td");
      td.className = clases;
      td.textContent = fmt(p);
      if (p !== null && p !== undefined) {
        td.style.color = p < 4.0 ? "red" : "#035bad";
      }
      return td;
    };

    const asignaturas = data.asignaturas || {};
    const maxPorSem = { 1: 1, 2: 1 };

    for (const asignatura in asignaturas) {
      const notas = asignaturas[asignatura].notas || [];
      for (const s of SEMESTRES) {
        const lista = notas.filter((n) => Number(n.semestre) === s);
        if (lista.length > maxPorSem[s]) {
          maxPorSem[s] = lista.length;
        }
      }
    }

    const promedioGeneralAnio = data.promedio_general_anio;

    const thead = document.createElement("thead");

    const fila1 = document.createElement("tr");
    fila1.innerHTML = `<th rowspan="2">Asignatura</th>`;
    for (const s of SEMESTRES) {
      fila1.innerHTML += `<th class="semestre-${s} inicio-bloque" colspan="${maxPorSem[s]}"><div>${NOMBRES[s]}</div></th>`;
    }
    fila1.innerHTML += `<th class="promedios inicio-bloque" colspan="3"><div>Promedios</div></th>`;

    const fila2 = document.createElement("tr");
    for (const s of SEMESTRES) {
      for (let i = 1; i <= maxPorSem[s]; i++) {
        fila2.innerHTML += `<th class="nota-semestre ${i === 1 ? "inicio-bloque" : ""}"><div>N ${i}</div></th>`;
      }
    }
    fila2.innerHTML += `
      <th class="inicio-bloque promedio"><div>1° Sem.</div></th>
      <th class="promedio"><div>2° Sem.</div></th>
      <th class="promedio"><div>General</div></th>`;

    thead.append(fila1, fila2);
    tabla.appendChild(thead);

    const tbody = document.createElement("tbody");
    for (const asignatura in asignaturas) {
      const info = asignaturas[asignatura];
      const row = document.createElement("tr");

      const tdAsig = document.createElement("td");
      tdAsig.innerHTML = `<div class="asignatura-td asignatura-${asignatura.toLowerCase().replace(/\s+/g, "-")}">${asignatura}</div>`;
      row.appendChild(tdAsig);

      const notas = info.notas || [];
      for (const s of SEMESTRES) {
        const lista = notas.filter((n) => Number(n.semestre) === s);
        for (let i = 0; i < maxPorSem[s]; i++) {
          const td = document.createElement("td");
          const span = document.createElement("span");
          td.appendChild(span);
          td.classList.add("nota-semestre");

          if (i === 0) td.classList.add("inicio-bloque");
          if (
            lista[i] &&
            lista[i].nota !== undefined &&
            lista[i].nota !== null
          ) {
            span.textContent = fmtNota(lista[i].nota);
            span.setAttribute("data-tippy-content", lista[i].evaluacion_nombre);
          } else {
            span.textContent = "-";
          }
          row.appendChild(td);
        }
      }

      row.appendChild(
        celdaPromedio(
          info.promedio_semestre_1,
          "inicio-bloque promedio-semestre",
        ),
      );
      row.appendChild(
        celdaPromedio(info.promedio_semestre_2, "promedio-semestre"),
      );
      row.appendChild(celdaPromedio(info.promedio_general, "promedio-general"));

      tbody.appendChild(row);
    }

    const rowFinal = document.createElement("tr");
    rowFinal.classList.add("fila-promedio-general");

    const tdTexto = document.createElement("td");
    tdTexto.textContent = "Promedio General";
    rowFinal.appendChild(tdTexto);

    for (const s of SEMESTRES) {
      for (let i = 0; i < maxPorSem[s]; i++) {
        const td = document.createElement("td");
        if (i === 0) td.classList.add("inicio-bloque");
        rowFinal.appendChild(td);
      }
    }

    const tdS1Vacio = document.createElement("td");
    tdS1Vacio.className = "inicio-bloque promedio-semestre";
    rowFinal.appendChild(tdS1Vacio);

    const tdS2Vacio = document.createElement("td");
    tdS2Vacio.className = "promedio-semestre";
    rowFinal.appendChild(tdS2Vacio);

    rowFinal.appendChild(
      celdaPromedio(promedioGeneralAnio, "promedio-general"),
    );

    tbody.appendChild(rowFinal);
    tabla.appendChild(tbody);

    const contenedor = document.getElementById("promedio-general");
    if (contenedor) {
      contenedor.textContent = fmt(promedioGeneralAnio);
      if (promedioGeneralAnio !== null && promedioGeneralAnio < 4.0) {
        contenedor.style.color = "red";
      } else {
        contenedor.style.color = "#035bad";
      }
    }

    const promedio1 = data.promedio_general_semestre_1;
    const promedio2 = data.promedio_general_semestre_2;

    // ---------- CONTENEDOR APARTE ----------
    const contenedor1 = document.getElementById("promedio-1");
    const contenedor2 = document.getElementById("promedio-2");
    if (contenedor1) {
      contenedor1.textContent = promedio1 ? fmt(promedio1) : "N/A";
      contenedor1.style.color =
        promedio1 !== null && promedio1 < 4.0 ? "red" : "#035bad";
    }
    if (contenedor2) {
      contenedor2.textContent = promedio2 != null ? fmt(promedio2) : "N/A";
      contenedor2.style.color =
        promedio2 !== null && promedio2 < 4.0 ? "red" : "#035bad";
    }

    tippy("[data-tippy-content]");
  } catch (error) {
    console.error("Error cargando notas:", error);
  }
}
