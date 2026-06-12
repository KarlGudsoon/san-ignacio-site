async function initPendientes(estudianteId) {
  cargarListaPendientes(estudianteId);

  const datosEstudiante = await cargarDatosEstudiante(estudianteId);

  document.getElementById("volver").addEventListener("click", () => {
    cargarView("curso", datosEstudiante.curso_id);
  });
}

async function cargarDatosEstudiante(estudianteId) {
  try {
    const res = await fetch(
      `/luminary/api/admin/estudiantes/estudiante_ficha.php?estudiante_id=${estudianteId}`,
      { cache: "no-store" },
    );
    const data = await res.json();
    const estudiante = data.estudiante;
  
    return data.estudiante;

  } catch (error) {
    console.error("Error al cargar datos del estudiante:", error);
  }
}

async function cargarListaPendientes(estudianteId) {
  try {
    const res = await fetch(
      `/luminary/api/admin/evaluaciones/evaluaciones_pendientes.php?estudiante_id=${estudianteId}`,
      { cache: "no-store" },
    );

    const data = await res.json();

    if (!data.success) return;

    const tabla = document.getElementById("tabla-pendientes");
    tabla.innerHTML = "";

    if (data.cantidad === 0) {
      tabla.innerHTML =
        "<p>No tienes evaluaciones pendientes. ¡Buen trabajo!</p>";
      return;
    }

    Object.entries(data.notas).forEach(([asignatura, notas]) => {
      // Cápsula por asignatura
      const capsula = document.createElement("div");
      capsula.className = "";

      // Header de la cápsula
      const capsulaTitulo = document.createElement("h3");
      capsulaTitulo.className = "asignatura-item pendiente-capsula";
      capsulaTitulo.textContent = asignatura;
      capsula.appendChild(capsulaTitulo);

      const key = asignatura
        .toLowerCase()
        .normalize("NFD")
        .replace(/[\u0300-\u036f]/g, "")
        .replace(/\s+/g, " ")
        .trim();
      const color = colores[key] ?? "#e0e0e0";
      let icon = "";
      if (asignatura === "Matemáticas") {
        icon = "/assets/icon/math.svg";
      } else if (asignatura === "Lenguaje") {
        icon = "/assets/icon/books.svg";
      } else if (asignatura === "Ciencias") {
        icon = "/assets/icon/science.svg";
      } else if (asignatura === "Inglés") {
        icon = "/assets/icon/english.svg";
      } else if (asignatura === "Estudios Sociales") {
        icon = "/assets/icon/history.svg";
      } else if (asignatura === "Artes Visuales") {
        icon = "/assets/icon/photo.svg";
      } else if (asignatura === "TIC") {
        icon = "/assets/icon/computer.svg";
      } else if (asignatura === "Filosofía") {
        icon = "/assets/icon/thinking.svg";
      }
      capsulaTitulo.style.backgroundColor = `${color}`;
      capsulaTitulo.style.setProperty("--backgroundColor", `${color}`);
      capsulaTitulo.style.setProperty("--backgroundColor2", `${color}40`);
      capsulaTitulo.style.setProperty("--backgroundColor3", `${color}80`);

      let contadorNotas = 0;

      // Lista de notas dentro de la cápsula
      notas.forEach((nota) => {
        let colorNota;
        if (nota.nota < 4) {
          colorNota = "#e24a4a";
        } else if (nota.nota >= 4) {
          colorNota = "#2589df";
        } else if (nota.nota == "L") {
          colorNota = "#2589df";
        } else if (nota.nota == "ML") {
          colorNota = "#0da761";
        } else if (nota.nota == "NL") {
          colorNota = "#e24a4a";
        } else if (nota.nota == "P") {
          colorNota = "#f2a400";
        }

        contadorNotas++;
        const item = document.createElement("div");
        item.className = "nota-item";
        item.innerHTML = `
          <div style="display: flex; gap: 1rem; align-items: center;">
          <div><span>${contadorNotas}.</span></div>
          <div style="display: flex; flex-direction: column; justify-content: center;">
            <h4 style="margin: 0;">${nota.titulo}</h4>
            <div style="display: flex; gap: 0.5rem;">
              <span>${nota.fecha_aplicacion}</span> 
              
            </div>

          </div>
        </div>
        <div class="nota" style="background-color: ${colorNota};">${nota.nota}</div>
        `;
        capsula.appendChild(item);
      });

      tabla.appendChild(capsula);
    });
  } catch (error) {
    console.error("Error cargando pendientes:", error);
  }
}
