async function initMatriculaEditar(idMatricula) {
  document.getElementById("volver").addEventListener("click", () => {
    cargarView("docentes");
  });

  await cargarDatosMatricula(idMatricula);

  document
    .getElementById("guardar_editar_matricula")
    .addEventListener("click", () => {
      guardarCambiosMatricula();
    });
}

async function cargarDatosMatricula(idMatricula) {
  try {
    const res = await fetch(
      `/luminary/api/admin/matriculas/matricula_datos.php?id_matricula=${idMatricula}`,
    );

    const data = await res.json();

    if (!data.success) return;

    const matricula = data.matricula;

    document.querySelector('[name="nombre_estudiante"]').value = matricula.nombre_estudiante ?? "";
    document.querySelector('[name="apellidos_estudiante"]').value = matricula.apellidos_estudiante ?? "";
    document.querySelector('[name="fecha_nacimiento"]').value = matricula.fecha_nacimiento ?? "";
    document.querySelector('[name="rut_estudiante"]').value = matricula.rut_estudiante ?? "";
    document.querySelector('[name="serie_carnet_estudiante"]').value = matricula.serie_carnet_estudiante ?? "";
    document.querySelector('[name="etnia_estudiante"]').value = matricula.etnia_estudiante ?? "";
    document.querySelector('[name="direccion_estudiante"]').value = matricula.direccion_estudiante ?? "";
    document.querySelector('[name="correo_estudiante"]').value = matricula.correo_estudiante ?? "";
    document.querySelector('[name="curso_preferido"]').value = matricula.curso_preferido ?? "";
    document.querySelector('[name="telefono_estudiante"]').value = matricula.telefono_estudiante ?? "";
    document.querySelector('[name="hijos_estudiante"]').value = matricula.hijos_estudiante ?? "";
    document.querySelector('[name="situacion_especial_estudiante"]').value = matricula.situacion_especial_estudiante ?? "";
    document.querySelector('[name="programa_estudiante"]').value = matricula.programa_estudiante ?? "";
    document.querySelector('[name="nombre_apoderado"]').value = matricula.nombre_apoderado ?? "";
    document.querySelector('[name="rut_apoderado"]').value = matricula.rut_apoderado ?? "";
    document.querySelector('[name="parentezco_apoderado"]').value = matricula.parentezco_apoderado ?? "";
    document.querySelector('[name="direccion_apoderado"]').value = matricula.direccion_apoderado ?? "";
    document.querySelector('[name="telefono_apoderado"]').value = matricula.telefono_apoderado ?? "";
    document.querySelector('[name="situacion_especial_apoderado"]').value = matricula.situacion_especial_apoderado ?? "";

    // Guardar el id en el botón para usarlo al guardar
    document
      .getElementById("guardar_editar_matricula")
      .setAttribute("data-id", matricula.id);
  } catch (error) {
    console.log(error);
  }
}

async function guardarCambiosMatricula() {
  const matriculaId = document
    .getElementById("guardar_editar_matricula")
    .getAttribute("data-id");

  const formData = new FormData();

  formData.append("nombre_estudiante", document.querySelector('[name="nombre_estudiante"]').value);
  formData.append("apellidos_estudiante", document.querySelector('[name="apellidos_estudiante"]').value);
  formData.append("fecha_nacimiento", document.querySelector('[name="fecha_nacimiento"]').value);
  formData.append("rut_estudiante", document.querySelector('[name="rut_estudiante"]').value);
  formData.append("serie_carnet_estudiante", document.querySelector('[name="serie_carnet_estudiante"]').value);
  formData.append("etnia_estudiante", document.querySelector('[name="etnia_estudiante"]').value);
  formData.append("direccion_estudiante", document.querySelector('[name="direccion_estudiante"]').value);
  formData.append("correo_estudiante", document.querySelector('[name="correo_estudiante"]').value);
  formData.append("curso_preferido", document.querySelector('[name="curso_preferido"]').value);
  formData.append("telefono_estudiante", document.querySelector('[name="telefono_estudiante"]').value);
  formData.append("hijos_estudiante", document.querySelector('[name="hijos_estudiante"]').value);
  formData.append("situacion_especial_estudiante", document.querySelector('[name="situacion_especial_estudiante"]').value);
  formData.append("programa_estudiante", document.querySelector('[name="programa_estudiante"]').value);
  formData.append("nombre_apoderado", document.querySelector('[name="nombre_apoderado"]').value);
  formData.append("rut_apoderado", document.querySelector('[name="rut_apoderado"]').value);
  formData.append("parentezco_apoderado", document.querySelector('[name="parentezco_apoderado"]').value);
  formData.append("direccion_apoderado", document.querySelector('[name="direccion_apoderado"]').value);
  formData.append("telefono_apoderado", document.querySelector('[name="telefono_apoderado"]').value);
  formData.append("situacion_especial_apoderado", document.querySelector('[name="situacion_especial_apoderado"]').value);

  const res = await fetch("/luminary/api/admin/matriculas/matricula_editar.php", {
    method: "POST",
    body: formData,
  });

  const data = await res.json();

  if (data.success) {
    mostrarMensaje("Matricula editada correctamente", "green");

    cargarView("matriculas_pendientes");
  } else {
    alert(data.message);
  }
}
