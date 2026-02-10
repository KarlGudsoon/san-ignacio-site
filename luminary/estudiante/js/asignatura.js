async function initAsignaturaDetalle(asignaturaId) {
  console.log("Cargando detalles de asignatura con ID:", asignaturaId);

  const asignatura = document.getElementById("asignatura-detalle");
  const colorGuardado = sessionStorage.getItem("asignaturaColor");
  asignatura.style.backgroundColor = colorGuardado;

  let asignaturaIcon = document.getElementById("asignatura-icon");
  const iconGuardado = sessionStorage.getItem("asignaturaIcon");
  asignaturaIcon.src = iconGuardado;

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
    return fetch(`/luminary/api/estudiante/notas?asignatura_id=${asignaturaId}`)
      .then((res) => res.json())
      .then((data) => {
        const contenedor = document.getElementById("asignatura-contenido");

        if (!data.length) {
          contenedor.innerHTML = "<p>Sin notas registradas</p>";
          return;
        }

        const notas = data[0];

        let html = "";

        for (let i = 1; i <= 9; i++) {
          const valor = notas[`nota${i}`];
          if (valor == null) continue;

          html += `
          <div class="nota-item">
            <span>Nota ${i}</span>
            <strong>${valor}</strong>
          </div>
        `;
        }

        html += `
          <div class="nota-item">
            <span>Promedio</span>
            <strong>${notas.x̄}</strong>
          </div>
          `;

        contenedor.innerHTML = html || "<p>Sin notas</p>";
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
