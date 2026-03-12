async function initAsignaturaDetalle(cursoProfesorId) {
  const asignatura = document.getElementById("asignatura-detalle");
  const colorGuardado = sessionStorage.getItem("asignaturaColor");
  asignatura.style.backgroundColor = colorGuardado;

  let asignaturaIcon = document.getElementById("asignatura-icon");
  const iconGuardado = sessionStorage.getItem("asignaturaIcon");
  asignaturaIcon.src = iconGuardado;

  cargarInfo(cursoProfesorId);

  cargarWidgets(cursoProfesorId);

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
          <div>
            <div>
              <span>Mejor Nota</span>
            </div>
            <div></div>
            <div></div>
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

    let contenedorNotas = document.createElement("div");
    contenedorNotas.classList.add("contenedor-notas");
    let contadorNotas = 0;

    data.evaluaciones.forEach((evaluacion) => {
      let colorNota;
      if (evaluacion.nota < 4) {
        colorNota = "#e24a4a";
      } else if (evaluacion.nota >= 4) {
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

    contenedor.appendChild(contenedorNotas);
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
