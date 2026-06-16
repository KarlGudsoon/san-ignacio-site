async function initEditarDocente(idDocente) {
  document.getElementById("volver").addEventListener("click", () => {
    cargarView("docentes");
  });

  await cargarDatosDocente(idDocente);

  document
    .getElementById("guardar_editar_docente")
    .addEventListener("click", () => {
      guardarCambiosDocente();
    });
}

async function cargarDatosDocente(idDocente) {
  try {
    const res = await fetch(
      `/luminary/api/admin/docentes/docente_datos.php?id_docente=${idDocente}`,
    );

    const data = await res.json();

    if (!data.success) return;

    const docente = data.docente;

    document.querySelector('[name="docente_nombre"]').value =
      docente.nombre ?? "";
    document.querySelector('[name="docente_correo"]').value =
      docente.correo ?? "";
    document.querySelector('[name="docente_asignatura"]').value =
      docente.asignatura ?? "";

    // Guardar el id en el botón para usarlo al guardar
    document
      .getElementById("guardar_editar_docente")
      .setAttribute("data-id", docente.id);
  } catch (error) {
    console.log(error);
  }
}

async function guardarCambiosDocente() {
  const docenteId = document
    .getElementById("guardar_editar_docente")
    .getAttribute("data-id");

  const formData = new FormData();

  formData.append("docente_id", docenteId);
  formData.append(
    "docente_nombre",
    document.querySelector('[name="docente_nombre"]').value,
  );
  formData.append(
    "docente_correo",
    document.querySelector('[name="docente_correo"]').value,
  );
  formData.append(
    "docente_contrasena",
    document.querySelector('[name="docente_contrasena"]').value,
  );
  formData.append(
    "docente_asignatura",
    document.querySelector('[name="docente_asignatura"]').value,
  );

  const res = await fetch("/luminary/api/admin/docentes/docente_editar.php", {
    method: "POST",
    body: formData,
  });

  const data = await res.json();

  if (data.success) {
    mostrarMensaje("Docente editado correctamente", "green");

    cargarView("docentes");
  } else {
    alert(data.message);
  }
}
