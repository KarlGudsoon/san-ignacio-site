async function initAsignaturaDetalle(cursoProfesorId) {
  const asignatura = document.getElementById("asignatura-detalle");
  const colorGuardado = sessionStorage.getItem("asignaturaColor");
  asignatura.style.backgroundColor = colorGuardado;

  let asignaturaIcon = document.getElementById("asignatura-icon");
  const iconGuardado = sessionStorage.getItem("asignaturaIcon");
  asignaturaIcon.src = iconGuardado;

  cargarInfo(cursoProfesorId);

  cargarWidgets(cursoProfesorId);

  asignaturaNotas(cursoProfesorId);

  document.getElementById("volver").addEventListener("click", () => {
    cargarView("asignaturas");
  });

  document.querySelectorAll(".asignatura-navegacion button").forEach((btn) => {
    btn.addEventListener("click", () => {
      let botones = document.querySelectorAll(".asignatura-navegacion button");

      botones.forEach((b) => {
        b.classList.remove("seleccionado");
      });

      btn.classList.add("seleccionado");
    });
  });

  document
    .getElementById("btn-notas")
    .addEventListener("click", () => asignaturaNotas(cursoProfesorId));
  document
    .getElementById("btn-material")
    .addEventListener("click", () => asignaturaMaterial(cursoProfesorId));
}

async function cargarInfo(cursoProfesorId) {
  try {
    const res = await fetch(
      `/luminary/api/estudiante/asignatura/asignatura_detalle.php?curso_profesor_id=${cursoProfesorId}`,
      {
        cache: "no-store",
      },
    );
    const data = await res.json();
    if (!data.success) return;

    document
      .querySelectorAll('[data-asignatura="nombre"]')
      .forEach((el) => (el.textContent = data.asignatura.nombre_asignatura));
    document
      .querySelectorAll('[data-asignatura="profesor"]')
      .forEach((el) => (el.textContent = data.asignatura.profesor_asignatura));
  } catch (error) {
    console.error("Error cargando cursos:", error);
  }
}

async function cargarWidgets(cursoProfesorId) {
  return fetch(
    `/luminary/api/estudiante/asignatura/asignatura_notas.php?curso_profesor_id=${cursoProfesorId}`,
  )
    .then((res) => res.json())
    .then((data) => {
      const contenedorPromedio = document.querySelectorAll(
        ".contenedor-promedio",
      );
      contenedorPromedio.forEach((p) => {
        p.innerHTML = `
        <div class="promedio">
          <div class="header">
            <span>Promedio</span>
          </div>
          <div class="contenedor-promedio-porcentaje">
            <div class="promedio-porcentaje">
              <svg viewBox="0 0 120 120">
                <!-- fondo -->
                <circle class="ring-bg" cx="60" cy="60" r="54" />
                <!-- progreso -->
                <circle class="ring-progress" cx="60" cy="60" r="54" />
              </svg>
              <div class="promedio-nota">${data.promedio}</div>
            </div>
            <p class="progress-text"></p>
            <span class="text-extra" style="color: rgba(0,0,0,0.75); font-size: 0.875rem;">RENDIMIENTO ACADÉMICO</span>
          </div> 
        </div>
      `;
      });

      setProgress(((data.promedio / 7) * 100).toFixed(0));
    });
}

async function asignaturaNotas(cursoProfesorId) {
  try {
    const res = await fetch(
      `/luminary/api/estudiante/asignatura/asignatura_notas.php?curso_profesor_id=${cursoProfesorId}`,
      {
        cache: "no-store",
      },
    );
    const data = await res.json();
    if (!data.success) return;

    const contenedor = document.getElementById("asignatura-contenido");
    contenedor.className = "asignatura-contenido-notas";

    if (!data.success || !data.evaluaciones.length) {
      contenedor.innerHTML = "<p>Sin notas registradas</p>";
      return;
    }

    contenedor.innerHTML = "";

    window._asignaturaNotasData = data.evaluaciones;

    const contenedorBotones = document.createElement("div");
    contenedorBotones.classList.add("contenedor-botones-semestre");
    contenedorBotones.style.display = "flex";
    contenedorBotones.style.gap = "0.5rem";
    contenedorBotones.style.marginBottom = "1rem";

    const btnSem1 = document.createElement("button");
    btnSem1.classList.add("btn-semestre", "btn-sencillo", "active");
    btnSem1.dataset.semestre = "1";
    btnSem1.textContent = "Primer semestre";

    const btnSem2 = document.createElement("button");
    btnSem2.classList.add("btn-semestre", "btn-sencillo");
    btnSem2.dataset.semestre = "2";
    btnSem2.textContent = "Segundo semestre";

    contenedorBotones.appendChild(btnSem1);
    contenedorBotones.appendChild(btnSem2);
    contenedor.appendChild(contenedorBotones);

    const tituloSemestre = document.createElement("h3");
    tituloSemestre.id = "titulo-semestre-asignatura";
    tituloSemestre.textContent = "Notas - Primer semestre";
    contenedor.appendChild(tituloSemestre);

    const contenedorNotas = document.createElement("div");
    contenedorNotas.classList.add("contenedor-notas");
    contenedorNotas.style.borderRadius = "0 0 1rem 1rem";
    contenedorNotas.id = "contenedor-notas-asignatura";
    contenedor.appendChild(contenedorNotas);

    function renderPorSemestre(semestre) {
      contenedorNotas.innerHTML = "";

      const evaluacionesFiltradas = (window._asignaturaNotasData || []).filter(
        (ev) => String(ev.semestre) === String(semestre),
      );

      if (evaluacionesFiltradas.length === 0) {
        contenedorNotas.innerHTML =
          "<p>Sin notas registradas para este semestre</p>";
        return;
      }

      let contadorNotas = 0;
      evaluacionesFiltradas.forEach((evaluacion) => {
        let colorNota;
        const notaVal = evaluacion.nota;
        const num = parseFloat(notaVal);

        if (!isNaN(num)) {
          if (num < 4) {
            colorNota = "#e24a4a";
          } else {
            colorNota = "#2589df";
          }
        } else if (notaVal == "L") {
          colorNota = "#2589df";
        } else if (notaVal == "ML") {
          colorNota = "#0da761";
        } else if (notaVal == "NL") {
          colorNota = "#e24a4a";
        } else if (notaVal == "P") {
          colorNota = "#f2a400";
        } else {
          colorNota = "#2589df";
        }

        const ev = document.createElement("div");
        contadorNotas++;
        ev.classList.add("nota-item");
        ev.innerHTML = `
          <div style="display: flex; gap: 1rem; align-items: center;">
            <div><span>${contadorNotas}.</span></div>
            <div style="display: flex; flex-direction: column; justify-content: center;">
              <h4 style="margin: 0;">${evaluacion.titulo}</h4>
              <div style="display: flex; gap: 0.5rem;">
                <span>${evaluacion.fecha_aplicacion}</span> 
                <span>${evaluacion.tipo_evaluacion}</span> 
              </div>
            </div>
          </div>
          <div class="nota" style="background-color: ${colorNota};">${evaluacion.nota}</div>
        `;
        contenedorNotas.appendChild(ev);
      });
    }

    btnSem1.addEventListener("click", () => {
      btnSem1.classList.add("active");
      btnSem2.classList.remove("active");
      tituloSemestre.textContent = "Notas - Primer semestre";
      renderPorSemestre(1);
    });

    btnSem2.addEventListener("click", () => {
      btnSem2.classList.add("active");
      btnSem1.classList.remove("active");
      tituloSemestre.textContent = "Notas - Segundo semestre";
      renderPorSemestre(2);
    });

    renderPorSemestre(1);
  } catch (error) {
    console.error("Error cargando cursos:", error);
  }
}

async function asignaturaMaterial(cursoProfesorId) {
  try {
    const contenedorPrincipal = document.getElementById("asignatura-contenido");
    contenedorPrincipal.innerHTML = "";

    const contenedorUnidades = document.createElement("div");
    contenedorUnidades.classList.add("contenedor-unidades");

    const listaMateriales = document.createElement("div");
    listaMateriales.id = "material-curso";
    listaMateriales.classList.add("material-curso");
    listaMateriales.innerHTML = `
      <div class="lista-materiales">
        Cargando material...
      </div>
    `;

    contenedorUnidades.append(listaMateriales);
    contenedorPrincipal.append(contenedorUnidades);

    cargarMaterial(cursoProfesorId);
  } catch (error) {
    console.error("Error cargando material:", error);
  }
}

function setProgress(percent) {
  const circle = document.querySelectorAll(".ring-progress");
  const text = document.querySelectorAll(".progress-text");

  const radius = 54;
  const circumference = 2 * Math.PI * radius;

  let color;
  if (percent >= 71) {
    color = "#0da761";
  } else if (percent >= 57) {
    color = "#f2a400"; // Amarillo
  } else {
    color = "#eb3b3b"; // Rojo
  }
  circle.forEach((c) => {
    c.style.stroke = color;
    c.style.strokeDasharray = circumference;
    setTimeout(() => {
      c.style.strokeDashoffset =
        circumference - (percent / 100) * circumference;
    }, 50);
  });

  if (percent >= 85) {
    text.forEach((t) => {
      t.textContent = "EXCELENTE";
    });
  } else if (percent >= 71) {
    text.forEach((t) => {
      t.textContent = "BIEN";
    });
  } else if (percent >= 57) {
    text.forEach((t) => {
      t.textContent = "REGULAR";
    });
  } else {
    text.forEach((t) => {
      t.textContent = "NECESITAS MEJORAR";
    });
  }
}

async function initMaterial() {
  try {
    const response = await fetch(
      `/luminary/api/estudiante/material_asignatura.php?asignatura_id=${sessionStorage.getItem("asignaturaId")}`,
    );
    const data = await response.json();
  } catch (error) {
    console.error("Error fetching material:", error);
  }
}
