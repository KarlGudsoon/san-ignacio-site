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
      if ((data.curso.jornada === "mañana")) {
        modalidadIcon = "/assets/icon/meteocons--sunset-fill.svg";
      } else if ((data.curso.jornada === "tarde")) {
        modalidadIcon = "/assets/icon/line-md--sunny-filled-loop.svg";
      } else if ((data.curso.jornada === "noche")) {
        modalidadIcon = "/assets/icon/line-md--moon-filled-alt-loop.svg";
      }
      document
        .querySelectorAll('[data-curso="icon-modalidad"]')
        .forEach((el) => (el.src = modalidadIcon));

      const modalidad = capitalizarPalabras(data.curso.jornada.toLowerCase());

      document
        .querySelectorAll('[data-curso="modalidad"]')
        .forEach((el) => (el.textContent = modalidad));
      document
        .querySelectorAll('[data-curso="horas"]')
        .forEach((el) => (el.textContent = data.horario));
      document
        .querySelectorAll('[data-curso="profesor-jefe"]')
        .forEach((el) => (el.textContent = data.curso.profesor_jefe || "Sin información"));
      document
        .querySelectorAll('[data-curso="correo-profesor-jefe"]')
        .forEach((el) => {
          el.textContent = data.curso.correo_profesor_jefe
          el.href = `mailto:${data.curso.correo_profesor_jefe}`
        });
      let cursos = document.querySelectorAll('.curso');

      cursos.forEach((element) => {
          const texto = element.textContent.trim();

          if (texto.includes("1° Nivel")) {
              element.style.backgroundColor = "#0da761"; // Verde
          } else if (texto.includes("2° Nivel")) {
              element.style.backgroundColor = "#3891e9"; // Azul
          }
      });

        cargarHorario(cursoId);
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

async function cargarHorario(cursoId) {
  fetch(`/luminary/api/public/horario.php?curso_id=${cursoId}`)
  .then((res) => res.json())
  .then((data) => renderHorario(data))
  .catch((err) => console.error(err));

function normalizar(texto) {
  return texto
    .toLowerCase()
    .normalize("NFD")
    .replace(/[\u0300-\u036f]/g, "")
    .replace(/\s+/g, " ")
    .trim();
}

function renderHorario(data) {
  const table = document.getElementById("horario");
  table.querySelectorAll("th").forEach((e) => {
    e.style.border = "1px solid black";
  })

  const tbody = document.querySelector("#horario tbody");
  const dias = ["lunes", "martes", "miercoles", "jueves", "viernes"];

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
  };

  const bloques = {};

  // Agrupar por bloque
  data.forEach((item) => {
    if (!bloques[item.orden]) {
      bloques[item.orden] = {
        hora: `${item.hora_inicio.slice(0, 5)} - ${item.hora_fin.slice(0, 5)}`,
      };
    }
    bloques[item.orden][item.dia] = item.asignatura;
  });

  // Pintar tabla
  Object.values(bloques).forEach((bloque) => {
    const tr = document.createElement("tr");

    tr.innerHTML = `
      <td style="color: black; border: 1px solid black;">${bloque.hora}</td>
      ${dias
        .map((d) => {
          const asignatura = bloque[d];

          if (!asignatura) {
            return `<td style="border: 1px solid black;"></td>`;
          }

          const key = normalizar(asignatura);
          const color = colores[key] ?? "#e0e0e0";

          return `
            <td style="background:${color}; color: #fff; padding: 12px; text-align:center; border: 1px solid black;">
                ${asignatura}
            </td>
            `;
        })
        .join("")}
    `;

    tbody.appendChild(tr);
  });
}
  
}

function descargarHorario() {
  const elemento = document.getElementById("pdf-area"); 

  html2pdf().set({
    margin: 10,
    filename: "horario.pdf",
    html2canvas: {
      scale: 3,
      scrollY: 0,
      backgroundColor: null 
    },
    jsPDF: {
      unit: "mm",
      format: "a4",
      orientation: "landscape"
    }
  }).from(elemento).save();
}


initInicio(nivel);
