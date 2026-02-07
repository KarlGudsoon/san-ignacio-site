function initAsignaturaDetalle(asignaturaId) {
  console.log("Cargando detalles de asignatura con ID:", asignaturaId);

  fetch(`/luminary/api/estudiante/asignatura.php?id=${asignaturaId}`)
    .then((res) => res.json())
    .then((data) => {
      document.getElementById("nombre-asignatura").textContent =
        data.asignatura;

      document.getElementById("profesor").textContent =
        "Profesor: " + data.profesor;
    });

  document.getElementById("volver").addEventListener("click", () => {
    cargarView("asignaturas");
  });
}
