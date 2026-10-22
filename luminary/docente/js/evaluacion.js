async function initEvaluacion(cursoProfesorId) {
  await cargarTipos();
  await cargarCursosProfesorSelect();
  await cargarCursoProfesor();

  document.getElementById("volver").addEventListener("click", () => {
    cargarView("asignatura", cursoProfesorId);
  });

  document.getElementById("cursoProfesorSelect").value = cursoProfesorId;

  const btn = document.getElementById("btn-guardar-evaluacion");

  btn.onclick = async (e) => {
    e.preventDefault();

    if (!document.getElementById("titulo").value.trim()) {
      mostrarMensaje("El título es obligatorio", "red");
      return;
    }

    btn.disabled = true;
    const { ok, mensaje } = await guardarEvaluacion();
    btn.disabled = false;

    if (ok) {
      cargarView("asignatura", cursoProfesorId);
      mostrarMensaje("Evaluación creada correctamente", "green");
    } else {
      mostrarMensaje(mensaje || "Error al crear la evaluación", "red");
    }
  };
}
