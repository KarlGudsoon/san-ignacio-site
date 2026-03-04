async function crearUnidad(e) {
  e.preventDefault();

  const formData = new FormData(e.target);

  const res = await fetch("/luminary/api/docente/unidades/unidad_crear.php", {
    method: "POST",
    body: formData,
  });

  const data = await res.json();

  if (data.success) {
    document.getElementById("mensaje").textContent =
      "Unidad creada correctamente";
    document.getElementById("mensaje").classList.add("mostrar", "green");
    setTimeout(() => {
      document.getElementById("mensaje").classList.remove("mostrar", "green");
    }, 5000);
    seccionMaterial(formData.get("curso_profesor_id"));
    e.target.reset();
  } else {
    document.getElementById("mensaje").textContent = data.message;
    document.getElementById("mensaje").classList.add("mostrar", "red");
    setTimeout(() => {
      document.getElementById("mensaje").classList.remove("mostrar", "red");
    }, 5000);
  }
}

async function cargarUnidades(cursoProfesorId) {
  const res = await fetch(
    `/luminary/api/docente/unidades/unidades_listar.php?curso_profesor_id=${cursoProfesorId}`,
  );
  const data = await res.json();

  const select = document.getElementById("material_unidad_id");
  select.innerHTML = '<option value="">Seleccione unidad</option>';

  data.unidades.forEach((unidad) => {
    select.innerHTML += `
      <option value="${unidad.id}">${unidad.nombre}</option>
    `;
  });
}

async function eliminarUnidad(unidadId) {
  if (!confirm("¿Eliminar esta unidad y todo su material?")) {
    return;
  }

  const formData = new FormData();
  formData.append("unidad_id", unidadId);

  const res = await fetch(
    "/luminary/api/docente/unidades/unidad_eliminar.php",
    {
      method: "POST",
      body: formData,
    },
  );

  const data = await res.json();

  if (data.success) {
    document.getElementById("mensaje").textContent =
      "Unidad y material eliminados correctamente";
    document.getElementById("mensaje").classList.add("mostrar", "green");
    setTimeout(() => {
      document.getElementById("mensaje").classList.remove("mostrar");
    }, 5000);
    cargarMaterial(document.getElementById("material_curso_profesor_id").value);
  } else {
    document.getElementById("mensaje").textContent = data.message;
    document.getElementById("mensaje").classList.add("mostrar", "red");
    setTimeout(() => {
      document.getElementById("mensaje").classList.remove("mostrar");
    }, 5000);
  }
}
