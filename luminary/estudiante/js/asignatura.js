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
        const notas = data[0];
        const contenedorPromedio = document.createElement("div");
        asignaturaWidget.appendChild(contenedorPromedio);
        contenedorPromedio.classList.add("contenedor-promedio");
        contenedorPromedio.innerHTML = `
                <div class="promedio">
                  <div class="header">
                   <span>Promedio asignatura</span>
                  </div>
                  <div class="promedio-porcentaje">
                    <svg viewBox="0 0 120 120">
                      <!-- fondo -->
                      <circle class="ring-bg" cx="60" cy="60" r="54" />
                      <!-- progreso -->
                      <circle class="ring-progress" cx="60" cy="60" r="54" />
                    </svg>
                    <div class="promedio-nota">${notas.x̄}</div>
                  </div>
                  <p id="progress-text"></p>
                  <span style="color: rgba(0,0,0,0.75); font-size: 0.875rem;">RENDIMIENTO ACADÉMICO</span>
                </div>
                <div class="promedio"></div>
              `;
        setProgress(((notas.x̄ / 7) * 100).toFixed(0));
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

        if (!data.length) {
          contenedor.innerHTML = "<p>Sin notas registradas</p>";
          return;
        }

        contenedor.innerHTML = "";

        const notas = data[0];

        let contenedorNotas = document.createElement("div");
        contenedorNotas.classList.add("contenedor-notas");

        for (let i = 1; i <= 9; i++) {
          const valor = notas[`nota${i}`];
          if (valor == null) continue;
          let colorNota;
          if (valor < 4) {
            colorNota = "#e24a4a";
          } else if (valor >= 4) {
            colorNota = "#2589df";
          }

          const nota = document.createElement("div");
          nota.classList.add("nota-item");
          nota.innerHTML = `
            <div style="display: flex; flex-direction: column; justify-content: center;">
              <h4 style="margin: 0;">Nota ${i}</h4>
              <div style="display: flex; gap: 0.5rem;">
                <span>Fecha de evaluación</span> 
                <span>Tipo de evaluación</span> 
              </div>
            </div>
            <div class="nota" style="background-color: ${colorNota};">${valor}</div>
            `;

          contenedorNotas.appendChild(nota);
        }

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
  const circle = document.querySelector(".ring-progress");
  const text = document.getElementById("progress-text");

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
  circle.style.stroke = color;
  circle.style.strokeDasharray = circumference;
  setTimeout(() => {
    circle.style.strokeDashoffset =
      circumference - (percent / 100) * circumference;
  }, 50);
  if (percent >= 85) {
    text.textContent = "EXCELENTE";
  } else if (percent >= 71) {
    text.textContent = "BIEN";
  } else if (percent >= 57) {
    text.textContent = "REGULAR";
  } else {
    text.textContent = "NECESITA MEJORAR";
  }
}
