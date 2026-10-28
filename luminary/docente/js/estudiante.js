async function initEstudiante(estudianteId) {
  await infoEstudiante(estudianteId);
  await notasEstudiante(estudianteId);

  document.getElementById("volver").addEventListener("click", () => {
    history.back();
  });

  const btnInforme = document.getElementById("informe-notas");
  const btnInformeSemestral = document.querySelectorAll(
    ".informe-notas-semestral",
  );

  btnInformeSemestral.forEach((btn) => {
    const semestre = btn.getAttribute("data-semestre");
    btn.addEventListener("click", () => {
      window.open(
        `/luminary/api/docente/jefatura/generar_informe_notas.php?id=${estudianteId}&semestre=${semestre}`,
        "_blank",
      );
    });
  });

  btnInforme.addEventListener("click", () => {
    window.open(
      `/luminary/api/docente/jefatura/generar_informe_notas.php?id=${estudianteId}`,
      "_blank",
    );
  });

  btnInforme.addEventListener("click", () => {
    window.open(
      `/luminary/api/docente/jefatura/generar_informe_notas.php?id=${estudianteId}`,
      "_blank",
    );
  });
}

async function infoEstudiante(estudianteId) {
  try {
    const res = await fetch(
      `/luminary/api/docente/jefatura/estudiante_ficha.php?estudiante_id=${estudianteId}`,
      { cache: "no-store" },
    );

    const data = await res.json();

    if (!data.success) return;

    const nombresFormateado = capitalizarPalabras(
      data.estudiante.nombre_estudiante.toLowerCase(),
    );
    const apellidosFormateado = capitalizarPalabras(
      data.estudiante.apellidos_estudiante.toLowerCase(),
    );
    const primerNombre = nombresFormateado.trim().split(" ")[0];

    document
      .querySelectorAll('[data-estudiante="nombre-completo"]')
      .forEach(
        (el) =>
          (el.textContent = nombresFormateado + " " + apellidosFormateado),
      );
    document.querySelectorAll('[data-estudiante="curso"]').forEach((el) => {
      el.textContent = data.estudiante.curso;
      el.classList.add(
        `curso-${data.estudiante.curso.toLowerCase().split(" ")[0]}`,
      );
    });
    document
      .querySelectorAll('[data-estudiante="edad"]')
      .forEach((el) => (el.textContent = data.estudiante.edad));
    document
      .querySelectorAll('[data-estudiante="correo"]')
      .forEach((el) => (el.textContent = data.estudiante.correo));
  } catch (error) {
    console.error("Error cargando estudiantes:", error);
  }
}

async function notasEstudiante(estudianteId) {
  try {
    const res = await fetch(
      `/luminary/api/docente/jefatura/estudiante_notas.php?estudiante_id=${estudianteId}`,
      { cache: "no-store" },
    );
    const data = await res.json();
    if (!data.success) return;

    const tabla = document.getElementById("tabla-notas");
    tabla.classList.add("tabla-notas-estudiante");
    tabla.innerHTML = "";

    const SEMESTRES = [1, 2];
    const NOMBRES = { 1: "Primer semestre", 2: "Segundo semestre" };

    const fmt = (p) =>
      p === null || p === undefined ? "-" : Number(p).toFixed(1);

    const fmtNota = (nota) =>
      nota !== null && nota !== undefined && !isNaN(parseFloat(nota))
        ? parseFloat(nota).toFixed(1)
        : (nota ?? "-");

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
    const datos = {};
    const maxPorSem = { 1: 1, 2: 1 };

    for (const asignatura in asignaturas) {
      datos[asignatura] = {};
      for (const s of SEMESTRES) {
        const notasLista = asignaturas[asignatura].notas || [];
        datos[asignatura][s] = notasLista.filter(
          (n) => Number(n.semestre) === s,
        );
        maxPorSem[s] = Math.max(maxPorSem[s], datos[asignatura][s].length);
      }
    }

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

    for (const asignatura in datos) {
      const info = asignaturas[asignatura];
      const row = document.createElement("tr");

      const tdAsig = document.createElement("td");
      tdAsig.innerHTML = `<div class="asignatura-td asignatura-${asignatura.toLowerCase().replace(/\s+/g, "-")}">${asignatura}</div>`;
      row.appendChild(tdAsig);

      for (const s of SEMESTRES) {
        const lista = datos[asignatura][s];

        for (let i = 0; i < maxPorSem[s]; i++) {
          const td = document.createElement("td");
          td.classList.add("nota-semestre");
          if (i === 0) td.classList.add("inicio-bloque");
          td.textContent = lista[i] ? fmtNota(lista[i].nota) : "-";
          row.appendChild(td);
        }
      }

      const pS1Asig = info.promedio_semestre_1;
      const pS2Asig = info.promedio_semestre_2;

      row.appendChild(
        celdaPromedio(pS1Asig, "inicio-bloque promedio-semestre"),
      );
      row.appendChild(celdaPromedio(pS2Asig, "promedio-semestre"));
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

    const pGeneral = data.promedio_general_anio;

    const tdS1Vacio = document.createElement("td");
    tdS1Vacio.className = "inicio-bloque promedio-semestre";
    rowFinal.appendChild(tdS1Vacio);

    const tdS2Vacio = document.createElement("td");
    tdS2Vacio.className = "promedio-semestre";
    rowFinal.appendChild(tdS2Vacio);

    rowFinal.appendChild(celdaPromedio(pGeneral, "promedio-general"));

    tbody.appendChild(rowFinal);
    tabla.appendChild(tbody);

    const contenedorPromedio = document.getElementById("promedio-general");
    if (contenedorPromedio) {
      contenedorPromedio.textContent = fmt(pGeneral);
      contenedorPromedio.style.color =
        pGeneral !== null && pGeneral < 4.0 ? "red" : "#035bad";
    }
  } catch (error) {
    console.error("Error cargando notas:", error);
  }
}
