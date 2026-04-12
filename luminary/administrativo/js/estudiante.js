async function initEstudiante(estudianteId) {
  await infoEstudiante(estudianteId);
  await notasEstudiante(estudianteId);
  await cargarCursosSelectTraspaso();

  document.getElementById("volver").addEventListener("click", () => {
    history.back();
  });

  document.getElementById("btn-traspasar").addEventListener("click", () => {
    traspasarEstudiante(estudianteId);
  });

}

async function cargarCursosSelectTraspaso() {
  try {
    console.log("Cargando cursos para traspaso...");
    const res = await fetch("/luminary/api/admin/cursos/cursos.php");
    const data = await res.json();
    console.log("Datos de cursos:", data);

    if (!data.success) {
      console.error("Error en respuesta de cursos:", data);
      return;
    }

    const select = document.getElementById("selectCursoTraspaso");
    if (!select) {
      console.error("Select de cursos de traspaso no encontrado");
      return;
    }

    select.innerHTML = '<option value="" disabled selected>Selecciona un curso</option>';

    data.cursos.forEach((curso) => {
      const option = document.createElement("option");
      option.value = curso.id;
      option.textContent = `${curso.curso}`;
      select.appendChild(option);
    });

    console.log("Cursos cargados correctamente para traspaso");
  } catch (error) {
    console.error("Error cargando cursos para traspaso:", error);
  }
}

async function infoEstudiante(estudianteId) {
  try {
    const res = await fetch(
      `/luminary/api/admin/estudiantes/estudiante_ficha.php?estudiante_id=${estudianteId}`,
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
      
    });
    document
      .querySelectorAll('[data-estudiante="edad"]')
      .forEach((el) => (el.textContent = data.estudiante.edad));
    document
      .querySelectorAll('[data-estudiante="correo"]')
      .forEach((el) => (el.textContent = data.estudiante.correo));

    document.querySelectorAll('.curso-1').forEach((el) => {
      el.classList.add(
        `curso-${data.estudiante.curso.toLowerCase().split(" ")[0]}`,
      );

    document.getElementById("inputCursoActual").value = data.estudiante.curso_id;

    });
  } catch (error) {
    console.error("Error cargando estudiantes:", error);
  }
}

async function notasEstudiante(estudianteId) {
  try {
    const res = await fetch(
      `/luminary/api/admin/estudiantes/estudiante_notas.php?estudiante_id=${estudianteId}`,
      { cache: "no-store" },
    );

    const data = await res.json();

    if (!data.success) return;

    const tabla = document.getElementById("tabla-notas");
    tabla.classList.add("tabla-notas-estudiante");
    tabla.innerHTML = "";

    const notas = data.notas;

    let maxNotas = 0;

    // 🔹 Para promedio general
    let sumaGeneral = 0;
    let cantidadGeneral = 0;

    for (const asignatura in notas) {
      if (notas[asignatura].length > maxNotas) {
        maxNotas = notas[asignatura].length;
      }
    }

    // ---------- THEAD ----------
    const thead = document.createElement("thead");
    const headerRow = document.createElement("tr");

    const thAsignatura = document.createElement("th");
    thAsignatura.textContent = "Asignatura";
    headerRow.appendChild(thAsignatura);

    for (let i = 1; i <= maxNotas; i++) {
      const th = document.createElement("th");
      th.textContent = `Nota ${i}`;
      headerRow.appendChild(th);
    }

    const thPromedio = document.createElement("th");
    thPromedio.textContent = "Promedio";
    headerRow.appendChild(thPromedio);

    thead.appendChild(headerRow);
    tabla.appendChild(thead);

    // ---------- TBODY ----------
    const tbody = document.createElement("tbody");

    for (const asignatura in notas) {
      const row = document.createElement("tr");
      const notasAsignatura = notas[asignatura];

      const tdAsignatura = document.createElement("td");
      tdAsignatura.innerHTML = `<div class="asignatura-td asignatura-${asignatura.toLowerCase().replace(/\s+/g, "-")}">${asignatura}</div>`;
      row.appendChild(tdAsignatura);

      let suma = 0;

      for (let i = 0; i < maxNotas; i++) {
        const td = document.createElement("td");

        if (notasAsignatura[i]) {
          const nota = notasAsignatura[i].nota;
          suma += nota;

          // 🔹 acumulamos para promedio general
          sumaGeneral += nota;
          cantidadGeneral++;

          td.textContent = nota.toFixed(1);
        } else {
          td.textContent = "-";
        }

        row.appendChild(td);
      }

      const promedio =
        notasAsignatura.length > 0
          ? (suma / notasAsignatura.length).toFixed(1)
          : "-";

      const tdPromedio = document.createElement("td");
      tdPromedio.textContent = promedio;

      row.appendChild(tdPromedio);
      tbody.appendChild(row);
    }

    // ---------- FILA PROMEDIO GENERAL ----------
    const promedioGeneral =
      cantidadGeneral > 0 ? (sumaGeneral / cantidadGeneral).toFixed(1) : "-";

    const rowFinal = document.createElement("tr");

    const tdTexto = document.createElement("td");
    tdTexto.textContent = "Promedio General";
    rowFinal.appendChild(tdTexto);

    for (let i = 0; i < maxNotas; i++) {
      const tdVacio = document.createElement("td");
      tdVacio.textContent = "";
      rowFinal.appendChild(tdVacio);
    }

    const tdPromedioGeneral = document.createElement("td");
    tdPromedioGeneral.textContent = promedioGeneral;
    tdPromedioGeneral.style.color =
      promedioGeneral !== "-" && promedioGeneral < 4.0 ? "red" : "green";

    rowFinal.appendChild(tdPromedioGeneral);

    tbody.appendChild(rowFinal);
    tabla.appendChild(tbody);

    // ---------- CONTENEDOR APARTE ----------
    const contenedorPromedio = document.getElementById("promedio-general");
    contenedorPromedio.textContent = promedioGeneral;
  } catch (error) {
    console.error("Error cargando estudiantes:", error);
  }
}
