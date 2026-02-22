async function initAsignaturaDetalle(asignaturaId) {
  console.log("Cargando detalles de asignatura con ID:", asignaturaId);

  const asignatura = document.getElementById("asignatura-detalle");
  const colorGuardado = sessionStorage.getItem("asignaturaColor");
  asignatura.style.backgroundColor = colorGuardado;

  let asignaturaIcon = document.getElementById("asignatura-icon");
  const iconGuardado = sessionStorage.getItem("asignaturaIcon");
  asignaturaIcon.src = iconGuardado;

  const asignaturaWidget = document.getElementById("asignatura-widget");

  async function cargarWidgets(asignaturaId) {
    return fetch(
      `/luminary/api/estudiante/notas_asignatura.php?asignatura_id=${asignaturaId}`,
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
  cargarWidgets(asignaturaId);

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

  async function asignaturaInicio() {
    return fetch(`/luminary/api/estudiante/me`)
      .then((res) => res.json())
      .then((data) => {
        document.getElementById("asignatura-contenido").innerHTML = `
          <span>1</span>
          <span>${data.nombre}</span>

        `;
      });
  }

  async function asignaturaNotas() {
    return fetch(
      `/luminary/api/estudiante/notas_asignatura.php?asignatura_id=${asignaturaId}`,
    )
      .then((res) => res.json())
      .then((data) => {
        const contenedor = document.getElementById("asignatura-contenido");
        contenedor.className = "asignatura-contenido-notas";

        if (!data.success || !data.evaluaciones.length) {
          contenedor.innerHTML = "<p>Sin notas registradas</p>";
          return;
        }

        contenedor.innerHTML = "";

        let contenedorNotas = document.createElement("div");
        contenedorNotas.classList.add("contenedor-notas");
        
        data.evaluaciones.forEach((evaluacion) => {
          let colorNota;
          if (evaluacion.nota < 4) {
            colorNota = "#e24a4a";
          } else if (evaluacion.nota >= 4) {
            colorNota = "#2589df";
          }

          const ev = document.createElement("div");
          ev.classList.add("nota-item");
          ev.innerHTML = `
            <div style="display: flex; flex-direction: column; justify-content: center;">
              <h4 style="margin: 0;">${evaluacion.evaluacion}</h4>
              <div style="display: flex; gap: 0.5rem;">
                <span>${evaluacion.fecha_aplicacion}</span> 
                <span>${evaluacion.tipo_evaluacion}</span> 
              </div>
            </div>
            <div class="nota" style="background-color: ${colorNota};">${evaluacion.nota}</div>
            `;
          contenedorNotas.appendChild(ev);
        })

        contenedor.appendChild(contenedorNotas);
      });
  }

  document
    .getElementById("btn-inicio")
    .addEventListener("click", asignaturaInicio);
  document
    .getElementById("btn-notas")
    .addEventListener("click", asignaturaNotas);
  asignaturaInicio();

  return fetch(`/luminary/api/estudiante/asignatura.php?id=${asignaturaId}`)
    .then((res) => res.json())
    .then((data) => {
      document.getElementById("nombre-asignatura").textContent =
        data.asignatura;

      document.getElementById("profesor").textContent =
        "Profesor: " + data.profesor;
    });
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
