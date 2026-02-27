async function initCursos() {
  await cargarCursos();
}

async function cargarCursos() {
  try {
    const res = await fetch("/luminary/api/docente/cursos/cursos.php", {
      cache: "no-store",
    });

    const data = await res.json();

    if (!data.success) return;

    const contenedor = document.getElementById("contenedor-cursos")

    const colores = {
        matematicas: "#3891e9",
        lenguaje: "#f75353",
        historia: "#7ed321",
        ciencias: "#0da761",
        ingles: "#cdb51a",
        "estudios sociales": "#f5a623",
        "artes visuales": "#23babf",
        tic: "#8544cf",
        filosofia: "#cf58dcff",
        "instrumental 1": "#ea507c",
        "instrumental 2": "#e6662a",
        diferenciado: "#21568b",
    };

    data.cursos.forEach((curso) => {
        const cardCurso = document.createElement("div");
        const key = curso.asignatura
          .toLowerCase()
          .normalize("NFD")
          .replace(/[\u0300-\u036f]/g, "")
          .replace(/\s+/g, " ")
          .trim();
        const color = colores[key] ?? "#e0e0e0";
        let icon = "";
        if (curso.asignatura === "Matemáticas") {
          icon = "/assets/icon/math.svg";
        } else if (curso.asignatura === "Lenguaje") {
          icon = "/assets/icon/books.svg";
        } else if (curso.asignatura === "Ciencias") {
          icon = "/assets/icon/science.svg";
        } else if (curso.asignatura === "Inglés") {
          icon = "/assets/icon/english.svg";
        } else if (curso.asignatura === "Estudios Sociales") {
          icon = "/assets/icon/history.svg";
        } else if (curso.asignatura === "Artes Visuales") {
          icon = "/assets/icon/photo.svg";
        } else if (curso.asignatura === "TIC") {
          icon = "/assets/icon/computer.svg";
        } else if (curso.asignatura === "Filosofía") {
          icon = "/assets/icon/thinking.svg";
        }
        let colorCurso;
        if (curso.curso_nivel === "1°") {
            colorCurso = "#0da761";
        } else if (curso.curso_nivel === "2°") {
            colorCurso = "#3891e9";
        }
        cardCurso.style.backgroundColor = `${color}`;
        cardCurso.style.setProperty("--backgroundColor", `${color}`);
        cardCurso.style.setProperty("--backgroundColor2", `${color}40`);
        cardCurso.style.setProperty("--backgroundColor3", `${color}80`);
        cardCurso.setAttribute("data-curso-profesor", curso.id);
        cardCurso.classList.add("asignatura-item")
        cardCurso.textContent = `${curso.asignatura}`;
        cardCurso.innerHTML = `
        <span class="curso" style="--color: ${colorCurso}">${curso.curso_nivel} Nivel ${curso.curso_letra}</span>
        <h3>${curso.asignatura}</h3>
        <div class="asignatura-icon">
            <img src="${icon}" alt="${curso.asignatura} icon" >
        </div> 
        `;

        cardCurso.addEventListener("click", () => {
          sessionStorage.setItem("asignaturaColor", color);
          sessionStorage.setItem("asignaturaoIcon", icon)
          cardCurso.style.setProperty("view-transition-name", `asignatura`);
          cargarView("asignatura", cardCurso.getAttribute("data-curso-profesor"));
        });
        contenedor.appendChild(cardCurso);
    });
  } catch (error) {
    console.error("Error cargando cursos:", error);
  }
}