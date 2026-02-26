const params = new URLSearchParams(window.location.search);
const nivel = params.get("id");

function initInicio(cursoId) {
  fetch(`/luminary/api/public/curso.php?id=${cursoId}`)
    .then((res) => {
      if (!res.ok) {
        window.location.href = "/pages/estudiantes";
        return;
      }
      return res.json();
    })

    .then((data) => {
      document
        .querySelectorAll('[data-curso="nombre"]')
        .forEach((el) => (el.textContent = data.curso.curso_full));
      document
        .querySelectorAll('[data-curso="cursos-media"]')
        .forEach(
          (el) =>
            (el.textContent =
              data.curso.nivel === "1°"
                ? "(1° y 2° medio)"
                : "(3° y 4° medio)"),
        );
      let modalidadIcon = "";
      if ((data.curso.jornada = "mañana")) {
        modalidadIcon = "/assets/icon/meteocons--sunset-fill.svg";
      } else if ((data.curso.jornada = "tarde")) {
        modalidadIcon = "/assets/icon/meteocons--sunset-fill.svg";
      } else if ((data.curso.jornada = "tarde")) {
        modalidadIcon = "/assets/icon/meteocons--sunset-fill.svg";
      }
      document
        .querySelectorAll('[data-curso="icon-modalidad"]')
        .forEach((el) => (el.src = modalidadIcon));

      const modalidad = capitalizarPalabras(data.curso.jornada.toLowerCase());

      document
        .querySelectorAll('[data-curso="modalidad"]')
        .forEach((el) => (el.textContent = modalidad));
    });
}

function capitalizarPalabras(texto) {
  if (!texto) return texto;

  return texto
    .toLowerCase()
    .split(" ")
    .map((palabra) => palabra.charAt(0).toUpperCase() + palabra.slice(1))
    .join(" ");
}

initInicio(nivel);
