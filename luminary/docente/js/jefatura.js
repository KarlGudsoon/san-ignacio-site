async function initJefatura() {
     try {
    const res = await fetch(
      `/luminary/api/docente/jefatura/jefatura_detalle.php`,
      {
        cache: "no-store",
      },
    );

    const data = await res.json();

    if (data.message === "No tienes jefatura asignada") {
      const contenedorPrincipal = document.getElementById("jefatura-contenido");
      contenedorPrincipal.innerHTML = "";
      contenedorPrincipal.innerHTML = "<h3>No tienes jefatura asignada</h3>";
      return
    }

    const cursoId = data.jefatura.id;

    if (!data.success) return;

    if (data.jefatura.curso_nivel === "1°") {
      document.getElementById("curso-asignatura").classList.add("curso-1°");
    } else if (data.jefatura.curso_nivel === "2°") {
      document.getElementById("curso-asignatura").classList.add("curso-2°");
    }

    document.getElementById("curso-asignatura").textContent =
      data.jefatura.curso;
    document.getElementById("nombre-asignatura").textContent = "Jefatura";

    // NAVEGACIÓN 
    
    cargarEstudiantes(cursoId);

    document
        .getElementById("btn-inicio")
        .addEventListener("click", () => cargarEstudiantes(cursoId));


  } catch (error) {
    console.error("Error:", error);
  }
}

async function cargarEstudiantes(cursoId) {
  try {
    const res = await fetch(
      `/luminary/api/docente/jefatura/jefatura_estudiantes?curso_id=${cursoId}.php`,
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