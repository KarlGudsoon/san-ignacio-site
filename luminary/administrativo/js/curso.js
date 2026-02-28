async function initCurso(cursoId) {
  await cargarInfo(cursoId);

  document.querySelectorAll(".asignatura-navegacion button").forEach((btn) => {
    btn.addEventListener("click", () => {
      let botones = document.querySelectorAll(".asignatura-navegacion button");

      botones.forEach((b) => {
        b.classList.remove("seleccionado");
      });

      btn.classList.add("seleccionado");
    });
  });

  document.getElementById("volver").addEventListener("click", () => {
    cargarView("cursos");
  });
}

async function cargarInfo(cursoId) {
  try {
    const res = await fetch(
      `/luminary/api/admin/cursos/curso_detalle.php?id=${cursoId}`,
      {
        cache: "no-store",
      },
    );

    const data = await res.json();

    if (!data.success) return;

    console.log("Se cargaron los datos con exito");

    const curso = document.getElementById("curso-detalle");
    const colorGuardado = sessionStorage.getItem("cursoColor");
    curso.style.backgroundColor = colorGuardado;

    document.getElementById("curso-niveles").textContent =
      data.curso.curso_nivel === "1°" ? "(1° y 2° medio)" : "(3° y 4° medio)";
    document.getElementById("nombre-curso").textContent = data.curso.curso_full;
  } catch (error) {
    console.error("Error cargando cursos:", error);
  }
}
