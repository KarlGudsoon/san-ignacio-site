async function initEstudiante(estudianteId) {
    await infoEstudiante(estudianteId)
    await notasEstudiante(estudianteId)

    document.getElementById("volver").addEventListener("click", () => {
        history.back();
    });
    
}

async function infoEstudiante(estudianteId) {
     try {
    const res = await fetch(
      `/luminary/api/admin/estudiantes/estudiante_ficha.php?estudiante_id=${estudianteId}`,
      { cache: "no-store" }
    );

    const data = await res.json();

    if (!data.success) return;
    
    const nombresFormateado = capitalizarPalabras(data.estudiante.nombre_estudiante.toLowerCase());
    const apellidosFormateado = capitalizarPalabras(data.estudiante.apellidos_estudiante.toLowerCase());
    const primerNombre = nombresFormateado.trim().split(" ")[0];

    document.querySelectorAll('[data-estudiante="nombre-completo"]').forEach((el) => (el.textContent = nombresFormateado + " " + apellidosFormateado));

  } catch (error) {
    console.error("Error cargando estudiantes:", error);
  }
}

async function notasEstudiante(estudianteId) {
    try {
    const res = await fetch(
      `/luminary/api/admin/estudiantes/estudiante_notas.php?estudiante_id=${estudianteId}`,
      { cache: "no-store" }
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
        tdAsignatura.textContent = asignatura;
        tdAsignatura.classList.add(`asignatura-${asignatura.toLowerCase().replace(/\s+/g, "-")}`); 
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

        const promedio = notasAsignatura.length > 0
        ? (suma / notasAsignatura.length).toFixed(1)
        : "-";

        const tdPromedio = document.createElement("td");
        tdPromedio.textContent = promedio;

        row.appendChild(tdPromedio);
        tbody.appendChild(row);
    }

    // ---------- FILA PROMEDIO GENERAL ----------
    const promedioGeneral = cantidadGeneral > 0
        ? (sumaGeneral / cantidadGeneral).toFixed(1)
        : "-";

    const rowFinal = document.createElement("tr");
    rowFinal.style.fontWeight = "bold";
    rowFinal.style.backgroundColor = "#f3f3f3";

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
        promedioGeneral !== "-" && promedioGeneral < 4.0
        ? "red"
        : "green";

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