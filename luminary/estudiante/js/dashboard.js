function capitalizarPalabras(texto) {
  if (!texto) return texto;

  return texto
    .toLowerCase()
    .split(" ")
    .map((palabra) => palabra.charAt(0).toUpperCase() + palabra.slice(1))
    .join(" ");
}

fetch("/luminary/api/estudiante/me.php")
  .then((res) => {
    if (!res.ok) {
      window.location.href = "/luminary/";
      return;
    }
    return res.json();
  })

  .then((data) => {
    const nombreFormateado = capitalizarPalabras(data.nombre.toLowerCase());
    const apellidosFormateado = capitalizarPalabras(
      data.apellidos.toLowerCase(),
    );

    const primerNombre = nombreFormateado.trim().split(" ")[0];
    const primerApellido = apellidosFormateado.trim().split(" ")[0];

    if (data.curso_nivel === "1°") {
      document.querySelectorAll('[data-estudiante="curso"]').forEach((el) => {
        el.style.backgroundColor = "#0da761";
      });
    } else if (data.curso_nivel === "2°") {
      document.querySelectorAll('[data-estudiante="curso"]').forEach((el) => {
        el.style.backgroundColor = "#3891e9";
      });
    }

    document
      .querySelectorAll('[data-estudiante="nombre"]')
      .forEach((el) => (el.textContent = nombreFormateado));

    document
      .querySelectorAll('[data-estudiante="nombre-apellido"]')
      .forEach((el) => (el.textContent = `${primerNombre} ${primerApellido}`));

    document
      .querySelectorAll('[data-estudiante="apellidos"]')
      .forEach((el) => (el.textContent = apellidosFormateado));
    document
      .querySelectorAll('[data-estudiante="curso"]')
      .forEach((el) => (el.textContent = data.curso));

    let jornada = "";

    if (data.curso_id == 1 || data.curso_id == 4 || data.curso_id == 5) {
      jornada = "Mañana";
    } else if (data.curso_id == 2 || data.curso_id == 6 || data.curso_id == 7) {
      jornada = "Tarde";
    } else if (data.curso_id == 3 || data.curso_id == 8 || data.curso_id == 9) {
      jornada = "Noche";
    }
    document
      .querySelectorAll('[data-estudiante="jornada"]')
      .forEach((el) => (el.textContent = jornada));
  })

  .catch(() => {
    window.location.href = "/luminary/";
  });

const PERIODOS = [
  {
    inicio: new Date("2026-03-01"),
    fin: new Date("2026-06-30"),
  },
  {
    inicio: new Date("2026-07-01"),
    fin: new Date("2026-12-31"),
  },
];

function obtenerPeriodoActual(periodos) {
  const hoy = new Date();
  hoy.setHours(0, 0, 0, 0);

  return (
    periodos.find((p) => hoy >= p.inicio && hoy <= p.fin) ??
    periodos[periodos.length - 1]
  );
}

function calcularProgresoPorFecha(inicio, fin) {
  const hoy = new Date();

  inicio.setHours(0, 0, 0, 0);
  fin.setHours(0, 0, 0, 0);
  hoy.setHours(0, 0, 0, 0);

  if (hoy <= inicio) return 0;
  if (hoy >= fin) return 100;

  const totalDias = (fin - inicio) / (1000 * 60 * 60 * 24);
  const diasTranscurridos = (hoy - inicio) / (1000 * 60 * 60 * 24);

  return Math.round((diasTranscurridos / totalDias) * 100);
}

function setProgress(percent) {
  const circle = document.querySelector(".ring-progress");
  const text = document.getElementById("progress-text");

  const radius = 54;
  const circumference = 2 * Math.PI * radius;

  circle.style.strokeDasharray = circumference;
  circle.style.strokeDashoffset =
    circumference - (percent / 100) * circumference;

  text.textContent = `${percent}%`;
}

const periodoActual = obtenerPeriodoActual(PERIODOS);
const progreso = calcularProgresoPorFecha(
  periodoActual.inicio,
  periodoActual.fin,
);

setProgress(progreso);

console.log(
  `Período actual: ${periodoActual.inicio.toLocaleDateString()} - ${periodoActual.fin.toLocaleDateString()}`,
);
