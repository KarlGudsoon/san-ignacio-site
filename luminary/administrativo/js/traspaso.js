async function traspasarEstudiante(estudianteId) {
  const formData = new FormData();

  formData.append("estudianteId", estudianteId);
  formData.append("cursoActual", document.getElementById("inputCursoActual").value);
  formData.append("cursoNuevo", document.getElementById("selectCursoTraspaso").value);

  const res = await fetch(
    "/luminary/api/admin/estudiantes/estudiante_traspasar.php",
    {
      method: "POST",
      body: formData,
    },
  );

  const data = await res.json();

  if (data.success) {
    
    mostrarMensaje("Estudiante traspasado correctamente", "green");
    
    cargarView("estudiante", estudianteId);

  } else {
    alert(data.message);
  }
}