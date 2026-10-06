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

  document.getElementById("btn-eliminar-estudiante").addEventListener("click", () => {
    eliminarMatricula(estudianteId);
  });

  const btnInforme = document.getElementById("informe-notas");

  btnInforme.addEventListener("click", () => {
    window.open(`/luminary/api/admin/estudiantes/generar_informe_notas.php?id=${estudianteId}`, '_blank');
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

    document.querySelectorAll('[data-estudiante="rut-estudiante"]').forEach((el) => {
      el.textContent = data.estudiante.rut_estudiante;
    });
    document.querySelectorAll('[data-estudiante="curso"]').forEach((el) => {
      el.textContent = data.estudiante.curso;
      
    });
    document
      .querySelectorAll('[data-estudiante="edad"]')
      .forEach((el) => (el.textContent = data.estudiante.edad));
    document
      .querySelectorAll('[data-estudiante="correo"]')
      .forEach((el) => (el.textContent = data.estudiante.correo_estudiante));
    document
      .querySelectorAll('[data-estudiante="fecha-nacimiento"]')
      .forEach((el) => (el.textContent = data.estudiante.fecha_nacimiento === '0000-00-00' ? 'Sin información' : data.estudiante.fecha_nacimiento));
    document
      .querySelectorAll('[data-estudiante="direccion"]')
      .forEach((el) => (el.textContent = data.estudiante.direccion_estudiante || 'Sin información'));
    document
      .querySelectorAll('[data-estudiante="telefono"]')
      .forEach((el) => (el.textContent = data.estudiante.telefono_estudiante || 'Sin información'));
    document
      .querySelectorAll('[data-estudiante="especial-estudiante"]')
      .forEach((el) => (el.textContent = data.estudiante.situacion_especial_estudiante || 'Sin información'));
    document
      .querySelectorAll('[data-estudiante="modalidad"]')
      .forEach((el) => {
        el.textContent = data.estudiante.tipo_estudiante;
        el.style.textTransform = "capitalize";
      });

    document.querySelectorAll('.curso-1').forEach((el) => {
      el.classList.add(
        `curso-${data.estudiante.curso.toLowerCase().split(" ")[0]}`,
      );

    document.getElementById("inputCursoActual").value = data.estudiante.curso_id;

    const btnFichaPdf = document.getElementById("btn-ficha-pdf");
    btnFichaPdf.addEventListener("click", () => {
      window.open(`/luminary/api/admin/matriculas/generar_ficha_matricula.php?id=${data.estudiante.id_matricula}&estado=activo`, '_blank');
    })

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

    const SEMESTRES = [1, 2];
    const NOMBRES = { 1: "Primer semestre", 2: "Segundo semestre" };

    // ---------- Helpers ----------
    const valores = (lista) =>
      lista.map((n) => parseFloat(n.nota)).filter((v) => !isNaN(v));
    const promedio = (vals) =>
      vals.length ? vals.reduce((a, b) => a + b, 0) / vals.length : null;
    const fmt = (p) => (p === null ? "-" : p.toFixed(1));
    const celdaPromedio = (p, clases = "") => {
      const td = document.createElement("td");
      td.className = clases;
      td.textContent = fmt(p);
      if (p !== null) td.style.color = p < 4.0 ? "red" : "#035bad";
      return td;
    };

    // Agrupa las notas de cada asignatura por semestre
    const datos = {};
    const maxPorSem = { 1: 1, 2: 1 };

    for (const asignatura in data.notas) {
      datos[asignatura] = {};
      for (const s of SEMESTRES) {
        datos[asignatura][s] = data.notas[asignatura].filter(
          (n) => Number(n.semestre) === s,
        );
        maxPorSem[s] = Math.max(maxPorSem[s], datos[asignatura][s].length);
      }
    }

    // ---------- THEAD (dos filas) ----------
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

    // ---------- TBODY ----------
    const tbody = document.createElement("tbody");
    const todasPorSem = { 1: [], 2: [] };

    for (const asignatura in datos) {
      const row = document.createElement("tr");

      const tdAsig = document.createElement("td");
      tdAsig.innerHTML = `<div class="asignatura-td asignatura-${asignatura.toLowerCase().replace(/\s+/g, "-")}">${asignatura}</div>`;
      row.appendChild(tdAsig);

      const valsPorSem = {};

      // Notas de ambos semestres
      for (const s of SEMESTRES) {
        const lista = datos[asignatura][s];
        valsPorSem[s] = valores(lista);
        todasPorSem[s].push(...valsPorSem[s]);

        for (let i = 0; i < maxPorSem[s]; i++) {
          const td = document.createElement("td");
          td.classList.add(`nota-semestre`);
          if (i === 0) td.classList.add("inicio-bloque");
          td.textContent = lista[i] ? lista[i].nota : "-";
          row.appendChild(td);
        }
      }

      // Tres columnas de promedio
      row.appendChild(celdaPromedio(promedio(valsPorSem[1]), "inicio-bloque promedio-semestre"));
      row.appendChild(celdaPromedio(promedio(valsPorSem[2]), "promedio-semestre"));
      row.appendChild(
        celdaPromedio(promedio([...valsPorSem[1], ...valsPorSem[2]]), "promedio-general"),
      );

      tbody.appendChild(row);
    }

    // ---------- FILA PROMEDIO GENERAL ----------
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

    const pS1 = promedio(todasPorSem[1]);
    const pS2 = promedio(todasPorSem[2]);
    const pGeneral = promedio([...todasPorSem[1], ...todasPorSem[2]]);

    rowFinal.appendChild(celdaPromedio(pS1, "inicio-bloque promedio-semestre"));
    rowFinal.appendChild(celdaPromedio(pS2, "promedio-semestre"));
    rowFinal.appendChild(celdaPromedio(pGeneral, "promedio-general"));

    tbody.appendChild(rowFinal);
    tabla.appendChild(tbody);

    // ---------- CONTENEDOR APARTE ----------
    const contenedor = document.getElementById("promedio-general");
    if (contenedor) {
      contenedor.textContent = fmt(pGeneral);
      contenedor.style.color =
        pGeneral !== null && pGeneral < 4.0 ? "red" : "#035bad";
    }
  } catch (error) {
    console.error("Error cargando notas:", error);
  }
}