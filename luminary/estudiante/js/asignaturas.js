function initAsignaturas() {
  const contenedor = document.getElementById("asignaturas-lista");
  if (!contenedor) return;

  fetch("/luminary/api/estudiante/asignaturas.php")
    .then((res) => {
      if (!res.ok) throw new Error("Error al cargar asignaturas");
      return res.json();
    })
    .then((data) => {
      if (!data.length) {
        contenedor.innerHTML = "<p>No hay asignaturas asignadas</p>";
        return;
      }

      const colores = {
        matematicas: "#3891e9",
        lenguaje: "#f75353",
        historia: "#7ed321",
        ciencias: "#0da761",
        ingles: "#cdb51a",
        "estudios sociales": "#f5a623",
        "artes visuales": "#23babf",
        tic: "#8544cf",
        filosofia: "#ce57db",
      };

      contenedor.innerHTML = "";

      data.forEach((asignatura) => {
        const asignaturaDiv = document.createElement("div");
        asignaturaDiv.classList.add("asignatura-item");
        const key = asignatura.nombre
          .toLowerCase()
          .normalize("NFD")
          .replace(/[\u0300-\u036f]/g, "")
          .replace(/\s+/g, " ")
          .trim();
        const color = colores[key] ?? "#e0e0e0";
        let icon = "";
        if (asignatura.nombre === "Matemáticas") {
          icon = "/assets/icon/math.svg";
        } else if (asignatura.nombre === "Lenguaje") {
          icon = "/assets/icon/books.svg";
        } else if (asignatura.nombre === "Ciencias") {
          icon = "/assets/icon/science.svg";
        } else if (asignatura.nombre === "Inglés") {
          icon = "/assets/icon/english.svg";
        } else if (asignatura.nombre === "Estudios Sociales") {
          icon = "/assets/icon/history.svg";
        } else if (asignatura.nombre === "Artes Visuales") {
          icon = "/assets/icon/photo.svg";
        } else if (asignatura.nombre === "TIC") {
          icon = "/assets/icon/computer.svg";
        } else if (asignatura.nombre === "Filosofía") {
          icon = "/assets/icon/thinking.svg";
        }
        asignaturaDiv.style.backgroundColor = `${color}`;
        asignaturaDiv.style.setProperty("--backgroundColor", `${color}`);
        asignaturaDiv.style.setProperty("--backgroundColor2", `${color}50`);
        asignaturaDiv.style.setProperty("--backgroundColor3", `${color}00`);
        asignaturaDiv.innerHTML = `
          <h3>${asignatura.nombre}</h3>
          <span>${asignatura.profesor}</span>
          <div class="asignatura-icon">
            <img src="${icon}" alt="${asignatura.nombre} icon" >
          </div>      
        `;
        contenedor.appendChild(asignaturaDiv);
      });
    })
    .catch((err) => {
      console.error(err);
      contenedor.innerHTML = "<p>Error al cargar las asignaturas</p>";
    });
}
