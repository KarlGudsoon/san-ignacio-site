async function initMatriculasPendientes() {
  document.getElementById("volver").addEventListener("click", () => {
    cargarView("estudiantes");
  });

  await cargarMatriculasPendientes();
}

async function cargarMatriculasPendientes() {
  try {
    const res = await fetch(
      `/luminary/api/admin/matriculas/matriculas_pendientes.php`,
      { cache: "no-store" },
    );
    const data = await res.json();

    if (data.success) {
      const estudiantes = data.matriculas;
      const contenedorPrincipal = document.getElementById(
        "contenido-principal",
      );
      contenedorPrincipal.innerHTML = "";

      const buscadorEst = document.createElement("div");
      buscadorEst.classList.add("buscador");
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
                <th>Fecha de registro</th>
                <th class="td-central">Acciones</th>
                </tr>
            </thead>
            <tbody></tbody>
            `;

      const tbody = tabla.querySelector("tbody");

      data.matriculas.forEach((matricula, index) => {
        const fila = document.createElement("tr");

        fila.innerHTML = `
                <td>${index + 1}</td>
                <td><div class="flex-align"><span class="estudiante-tabla" data-estudiante-id="${matricula.id_estudiante}">${matricula.nombre_estudiante} ${matricula.apellidos_estudiante}</span><button class="btn-mini btn-pdf" onclick="generarFichaMatricula(${matricula.id}, 'solicitud')" style="margin-bottom: 0;"><img src="/assets/icon/pdf-red.svg"></button></div></td>
                <td>${matricula.rut_estudiante}</td>
                <td>${matricula.edad ?? "-"}</td>
                <td>${matricula.curso ?? "-"}</td>
                <td>${matricula.fecha_registro}</td>
                <td><div class="td-central contenedor-botones"><button class="btn-mini" onclick="cargarView('matricula_editar', ${matricula.id})"><img src="/assets/icon/editar.svg"></button><button class="btn-mini btn-negativo" onclick="eliminarMatriculaPendiente(${matricula.id})"><img src="/assets/icon/delete.svg"></button><button class="btn-mini btn-afirmativo"><img src="/assets/icon/listo-white.svg"></button></div></td>
            `;

        tbody.appendChild(fila);
      });

      const contenedorTabla = document.createElement("div");
      contenedorTabla.classList.add("contenedor-tabla");

      contenedorTabla.appendChild(tabla);

      contenedorPrincipal.appendChild(contenedorTabla);
    }
  } catch (error) {
    console.log(error);
  }
}

async function eliminarMatriculaPendiente(idMatricula) {
  if (
    !confirm(
      "¿Estás seguro de eliminar la solicitud de matrícula?",
    )
  ) {
    return;
  }

  const formData = new FormData();

  formData.append("id_matricula", idMatricula);

  const res = await fetch("/luminary/api/admin/matriculas/matricula_pendiente_eliminar.php", {
    method: "POST",
    body: formData,
  });

  const data = await res.json();

  if (data.success) {
    mostrarMensaje("Matricula eliminada correctamente", "green");

    cargarView("matriculas_pendientes");
  } else {
     mostrarMensaje(data.message, "red");
  }
  
}

function generarFichaMatricula(idMatricula, estado) {
  window.open(`/luminary/api/admin/matriculas/generar_ficha_matricula.php?id=${idMatricula}&estado=${estado}`, '_blank');
}