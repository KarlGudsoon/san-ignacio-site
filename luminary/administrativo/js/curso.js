async function initCurso(cursoId) {
  await cargarInfo(cursoId);

  document.querySelectorAll(".asignatura-navegacion button").forEach((btn) => {
    btn.addEventListener("click", () => {
      let botones = document.querySelectorAll(".asignatura-navegacion button");

      botones.forEach((b) => {
        b.classList.remove("seleccionado");
      });

      btn.classList.add("seleccionado");
    });
  });

  document.addEventListener("click", function (e) {
    // ABRIR
    if (e.target.dataset.abrir) {
      const id = e.target.dataset.abrir;
      document.getElementById(id).classList.add("activo");
    }
    if (e.target.dataset.cerrar) {
      const id = e.target.dataset.cerrar;
      document.getElementById(id).classList.remove("activo");
    }
  });

  const form = document.getElementById("formEvaluacion");

  form.addEventListener("submit", async (e) => {
    e.preventDefault();
    await guardarEvaluacion();
  });

  document
    .getElementById("btn-estudiantes")
    .addEventListener("click", () => cargarEstudiantes(cursoId));
  document
    .getElementById("btn-evaluaciones")
    .addEventListener("click", () => cargarSeccionEv(cursoId));

  document.getElementById("volver").addEventListener("click", () => {
    cargarView("cursos");
  });
}

async function cargarInfo(cursoId) {
  try {
    const res = await fetch(
      `/luminary/api/admin/cursos/curso_detalle.php?id=${cursoId}`,
      {
        cache: "no-store",
      },
    );

    const data = await res.json();

    if (!data.success) return;

    console.log("Se cargaron los datos con exito");

    const curso = document.getElementById("curso-detalle");
    const colorGuardado = sessionStorage.getItem("cursoColor");
    curso.style.backgroundColor = colorGuardado;

    document.getElementById("curso-niveles").textContent =
      data.curso.nivel === "1°" ? "(1° y 2° medio)" : "(3° y 4° medio)";
    document.getElementById("nombre-curso").textContent = data.curso.curso_full;
  } catch (error) {
    console.error("Error cargando cursos:", error);
  }
}

async function cargarEstudiantes(cursoId) {
  try {
    const res = await fetch(
      `/luminary/api/admin/cursos/curso_estudiantes.php?curso_id=${cursoId}`,
      { cache: "no-store" },
    );

    const data = await res.json();

    if (!data.success) return;

    const contenedorPrincipal = document.getElementById("curso-contenido");
    contenedorPrincipal.innerHTML = "";

    // Crear tabla
    const tabla = document.createElement("table");
    tabla.classList.add("tabla-estudiantes");
    tabla.id = "tablaEstudiantes";

    // Header
    tabla.innerHTML = `
      <thead>
        <tr>
          <th>#</th>
          <th>Nombre Completo</th>
          <th>RUT</th>
          <th>Edad</th>
          <th>Teléfono</th>
        </tr>
      </thead>
      <tbody></tbody>
    `;

    const tbody = tabla.querySelector("tbody");

    data.estudiantes.forEach((estudiante, index) => {
      const fila = document.createElement("tr");

      fila.innerHTML = `
        <td>${index + 1}</td>
        <td><span class="estudiante-tabla" data-estudiante-id="${estudiante.id_estudiante}">${estudiante.nombre_estudiante} ${estudiante.apellidos_estudiante}</span></td>
        <td>${estudiante.rut_estudiante}</td>
        <td>${estudiante.edad ?? "-"}</td>
        <td>${estudiante.telefono_estudiante ?? "-"}</td>
      `;

      tbody.appendChild(fila);
    });

    const contenedorTabla = document.createElement("div");
    contenedorTabla.classList.add("contenedor-tabla");

    contenedorTabla.appendChild(tabla);

    contenedorPrincipal.appendChild(contenedorTabla);

    document
      .getElementById("tablaEstudiantes")
      .addEventListener("click", (e) => {
        const fila = e.target.closest(".estudiante-tabla");

        if (!fila) return;

        const estudianteId = fila.dataset.estudianteId;

        cargarEstudiante(estudianteId);
      });
  } catch (error) {
    console.error("Error cargando estudiantes:", error);
  }
}

function cargarEstudiante(estudianteId) {
  cargarView("estudiante", estudianteId);
}

async function cargarSeccionEv(cursoId) {
  try {
    const res = await fetch(
      `/luminary/api/admin/cursos/curso_asignaturas.php?curso_id=${cursoId}`,
      { cache: "no-store" },
    );

    const data = await res.json();

    if (!data.success) return;

    await cargarTipos();

    const colores = {
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
    };

    const contenedorPrincipal = document.getElementById("curso-contenido");
    contenedorPrincipal.innerHTML = "";

    const contenedorAsignaturas = document.createElement("div");
    contenedorAsignaturas.classList.add("contenedor-asignaturas");

    contenedorPrincipal.appendChild(contenedorAsignaturas);

    const contenedorEv = document.createElement("div");
    contenedorEv.classList.add("contenedor-evaluaciones");

    const listaEv = document.createElement("div");
    listaEv.classList.add("lista-evaluaciones");
    listaEv.id = "listaEvaluaciones";
    const btnCrearEv = document.createElement("button");
    btnCrearEv.textContent = "+ Crear evaluación";
    btnCrearEv.disabled;
    btnCrearEv.setAttribute("data-abrir", "form-evaluacion");
    const contenedorLista = document.createElement("div");
    contenedorLista.classList.add("contenedor-lista-ev");
    contenedorLista.appendChild(btnCrearEv);
    contenedorLista.appendChild(listaEv);
    const detalleEv = document.createElement("div");
    detalleEv.id = "detalleEvaluacion";
    const headerDetalle = document.createElement("div");
    headerDetalle.id = "header-detalle";
    const contenedorDetalle = document.createElement("div");
    contenedorDetalle.classList.add("contenedor-detalle");
    contenedorDetalle.appendChild(headerDetalle);
    contenedorDetalle.appendChild(detalleEv);

    contenedorEv.appendChild(contenedorLista);
    contenedorEv.appendChild(contenedorDetalle);

    contenedorPrincipal.appendChild(contenedorEv);

    data.asignaturas.forEach((asignatura, index) => {
      const cardAsignatura = document.createElement("div");
      const key = asignatura.asignatura
        .toLowerCase()
        .normalize("NFD")
        .replace(/[\u0300-\u036f]/g, "")
        .replace(/\s+/g, " ")
        .trim();
      const color = colores[key] ?? "#e0e0e0";
      cardAsignatura.style.backgroundColor = `${color}`;
      cardAsignatura.style.setProperty("--backgroundColor", `${color}`);
      cardAsignatura.style.setProperty("--backgroundColor2", `${color}40`);
      cardAsignatura.style.setProperty("--backgroundColor3", `${color}80`);
      cardAsignatura.classList.add("asignatura-item");
      cardAsignatura.classList.add("deseleccionado");
      cardAsignatura.setAttribute(
        "data-curso-profesor-id",
        asignatura.curso_profesor_id,
      );
      cardAsignatura.innerHTML = `
        <h4>${asignatura.asignatura}</h4>
      `;
      if (index === 0) {
        cardAsignatura.classList.add("seleccionado");
        cardAsignatura.classList.remove("deseleccionado");

        contenedorEv.style.backgroundColor = `${color}75`;

        // Cargar evaluaciones automáticamente
        cargarEvaluaciones(asignatura.curso_profesor_id);
      }
      cardAsignatura.addEventListener("click", () => {
        cargarEvaluaciones(
          cardAsignatura.getAttribute("data-curso-profesor-id"),
        );
        document.querySelectorAll(".asignatura-item").forEach((card) => {
          card.classList.remove("seleccionado");
          card.classList.add("deseleccionado");
        });
        cardAsignatura.classList.add("seleccionado");
        cardAsignatura.classList.remove("deseleccionado");

        contenedorEv.style.backgroundColor = `${color}75`;
      });

      contenedorAsignaturas.appendChild(cardAsignatura);
    });
  } catch (error) {
    console.error("Error cargando estudiantes:", error);
  }
}
