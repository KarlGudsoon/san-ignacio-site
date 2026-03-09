fetch("/luminary/api/estudiante/horario.php")
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
    "consumo y calidad de vida": "#8544cf",
    filosofia: "#cf58dcff",
    "instrumental 1": "#fb2b66",
    "instrumental 2": "#f16b3a",
    diferenciado: "#09dc84",
    jefatura: "#0c4d8e",
    "pensamiento computacional": "#8544cf",
    "educación financiera": "#8544cf",
    "convivencia social": "#54328a",
    "inserción laboral": "#54328a",
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
      <td>${bloque.hora}</td>
      ${dias
        .map((d) => {
          const asignatura = bloque[d];

          if (!asignatura) {
            return `<td></td>`;
          }

          const key = normalizar(asignatura);
          const color = colores[key] ?? "#e0e0e0";

          return `
            <td style="background:${color}; color: #fff;">
                ${asignatura}
            </td>
            `;
        })
        .join("")}
    `;

    tbody.appendChild(tr);
  });
}
