async function initCursos() {
  await cargarCursos();
}

async function cargarCursos() {
  try {
    const res = await fetch("/luminary/api/admin/cursos/cursos.php", {
      cache: "no-store",
    });

    const data = await res.json();

    if (!data.success) return;

    const contenedor = document.getElementById("contenedor-cursos");

    data.cursos.forEach((curso) => {
      const cardCurso = document.createElement("div");
      let colorCurso;

      if (curso.curso_nivel === "1°") {
        colorCurso = "#0da761";
      } else if (curso.curso_nivel === "2°") {
        colorCurso = "#3891e9";
      }
      cardCurso.style.backgroundColor = `${colorCurso}`;
      cardCurso.setAttribute("data-curso-id", curso.id);
      cardCurso.style.setProperty("--backgroundColor", `${colorCurso}`);
      cardCurso.style.setProperty("--backgroundColor2", `${colorCurso}40`);
      cardCurso.style.setProperty("--backgroundColor3", `${colorCurso}80`);
      cardCurso.classList.add("asignatura-item");
      cardCurso.innerHTML = `
        <h3>${curso.curso}</h3>
        <span>${curso.curso_nivel === "1°" ? "(1° y 2° medio)" : "(3° y 4° medio)"}</span>
        `;

      cardCurso.addEventListener("click", () => {
        sessionStorage.setItem("cursoColor", colorCurso);
        cardCurso.style.setProperty("view-transition-name", `asignatura`);
        cargarView("curso", cardCurso.getAttribute("data-curso-id"));
      });
      contenedor.appendChild(cardCurso);
    });
  } catch (error) {
    console.error("Error cargando cursos:", error);
  }
}
