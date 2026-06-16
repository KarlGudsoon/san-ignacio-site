async function initDocentes() {
  await cargarListaDocentes();
}

async function cargarListaDocentes() {
  try {
    const res = await fetch(`/luminary/api/admin/docentes/docentes_lista.php`, {
      cache: "no-store",
    });

    const data = await res.json();

    if (data.success) {
      const docentes = data.docentes;
      const contenedorPrincipal = document.getElementById(
        "contenedor-principal",
      );
      contenedorPrincipal.innerHTML = "";

      const tabla = document.createElement("table");
      tabla.classList.add("tabla-estudiantes");
      tabla.id = "tablaDocentes";

      // Header
      tabla.innerHTML = `
            <thead>
                <tr>
                <th>#</th>
                <th>Nombre Completo</th>
                <th>Correo</th>
                <th>Asignatura</th>
                <th>Acciones</th>
                </tr>
            </thead>
            <tbody></tbody>
            `;

      const tbody = tabla.querySelector("tbody");

      data.docentes.forEach((docente, index) => {
        const fila = document.createElement("tr");

        fila.innerHTML = `
                <td>${index + 1}</td>
                <td>${docente.nombre}</td>
                <td>${docente.correo}</td>
                <td>${docente.asignatura}</td>
                <td><button class="btn-mini" onclick="cargarView('docente_editar',${docente.id})"><img src="/assets/icon/ic--round-password.svg"></button></td>
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
