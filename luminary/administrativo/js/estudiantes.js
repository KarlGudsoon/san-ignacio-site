async function initEstudiantes() {
    todosEstudiantes();
}

async function todosEstudiantes() {
    try {
        const response = await fetch("/luminary/api/admin/estudiantes/estudiantes_lista.php");
        const data = await response.json();

        if (data.success) {
            const estudiantes = data.estudiantes;
            const contenedorPrincipal = document.getElementById("estudiantes-contenido");
            contenedorPrincipal.innerHTML = "";

            const buscadorEst = document.createElement("div");
            buscadorEst.classList.add("buscador")
            buscadorEst.innerHTML = `
            <input type="text" id="buscadorEstudiantes" placeholder="Buscar por nombre">
            `;

            contenedorPrincipal.appendChild(buscadorEst);

            const buscadorInput = document.getElementById("buscadorEstudiantes");
            buscadorInput.addEventListener("input", buscarEstudiantes);

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
                <th>Curso</th>
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
                <td>${estudiante.curso ?? "-"}</td>
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
            });}
        } catch (error) {
            console.error("Error cargando estudiantes:", error);
        }
}

function buscarEstudiantes() {
    const input = document.getElementById("buscadorEstudiantes");
    const filter = input.value.toLowerCase();
    const tabla = document.getElementById("tablaEstudiantes");
    const filas = tabla.getElementsByTagName("tr");
    for (let i = 1; i < filas.length; i++) {
        const nombreCompleto = filas[i].getElementsByTagName("td")[1].textContent.toLowerCase();
        if (nombreCompleto.includes(filter)) {
            filas[i].style.display = "";
        } else {            
            filas[i].style.display = "none";
        }
    }
}

function traspasarEstudiante(estudianteId) {
  const selectCurso = document.getElementById("selectCursoTraspaso");
  const nuevoCursoId = selectCurso.value;

  if (!nuevoCursoId) {
    alert("Por favor, selecciona un curso.");
    return;
  }

  if (!confirm("¿Estás seguro de traspasar al estudiante a este curso? Se copiarán las evaluaciones existentes y se eliminarán las del curso anterior.")) {
    return;
  }

  // Mostrar loading
  const btnTraspasar = document.getElementById("btn-traspasar");
  const textoOriginal = btnTraspasar.textContent;
  btnTraspasar.textContent = "Procesando...";
  btnTraspasar.disabled = true;

  fetch("/luminary/api/admin/estudiantes/estudiante_traspasar_curso.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded",
    },
    body: new URLSearchParams({
      estudiante_id: estudianteId,
      nuevo_curso_id: nuevoCursoId,
    }),
  })
  .then(res => res.json())
  .then(data => {
    if (data.success) {
      alert(`Estudiante traspasado exitosamente.\nNotas traspasadas: ${data.notas_traspasadas}\nEvaluaciones creadas: ${data.evaluaciones_creadas}\nNotas eliminadas del curso anterior: ${data.notas_eliminadas}`);
      // Recargar la información del estudiante
      infoEstudiante(estudianteId);
      notasEstudiante(estudianteId);
    } else {
      alert("Error: " + data.message);
    }
  })
  .catch(error => {
    console.error("Error traspasando estudiante:", error);
    alert("Error al traspasar estudiante.");
  })
  .finally(() => {
    // Restaurar botón
    btnTraspasar.textContent = textoOriginal;
    btnTraspasar.disabled = false;
  });
}