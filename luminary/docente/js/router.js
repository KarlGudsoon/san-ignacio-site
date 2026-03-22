import NavBarDocente from '../components/NavBarDashboard.js';
import NavBarDocenteMobile from '../components/NavBarMobile.js';

const view = document.getElementById("dashboard-content");

window.cargarView = cargarView;

document.addEventListener("click", (e) => {
  const btn = e.target.closest("[data-view]");
  if (!btn) return;

  const view = btn.dataset.view;
  cargarView(view);
});

async function cargarView(nombre, param = null, push = true) {
  const contenedor = document.getElementById("dashboard-content");

  const vistaActiva = { asignatura: 'cursos', estudiante: 'jefatura' }[nombre] ?? nombre;
  NavBarDocente(vistaActiva);
  NavBarDocenteMobile(vistaActiva);

  if (!document.startViewTransition) {
    const res = await fetch(`/luminary/docente/views/${nombre}.html`, {
      cache: "no-store",
    });
    contenedor.innerHTML = await res.text();
    iniciarView(nombre, param);
    return;
  }

  document.startViewTransition(async () => {
    const res = await fetch(`/luminary/docente/views/${nombre}.html`, {
      cache: "no-store",
    });
    contenedor.innerHTML = await res.text();

    // 👇 inicializa inmediatamente
    iniciarView(nombre, param);
  });

  if (push) {
    const url = param ? `#/${nombre}/${param}` : `#/${nombre}`;
    history.pushState({ nombre, param }, "", url);
  }
}

// vista inicial
cargarView("inicio");

function iniciarView(nombre, param = null) {
  const views = {
    inicio: () => initInicio(),
    cursos: () => initCursos(),
    asignatura: () => initAsignatura(param),
    evaluaciones: () => initEvaluaciones(),
    jefatura: () => initJefatura(),
    estudiante: () => initEstudiante(param),
  };

  views[nombre]?.();
}

window.addEventListener("popstate", (e) => {
  if (!e.state) {
    cargarView("inicio", null, false);
    return;
  }

  cargarView(e.state.nombre, e.state.param, false);
});
