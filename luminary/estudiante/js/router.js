const view = document.getElementById("dashboard-content");

document.addEventListener("click", (e) => {
  const btn = e.target.closest("[data-view]");
  if (!btn) return;

  const view = btn.dataset.view;
  cargarView(view);
});

async function cargarView(nombre, param = null, push = true) {
  const contenedor = document.getElementById("dashboard-content");

  if (!document.startViewTransition) {
    // fallback
    const res = await fetch(`/luminary/estudiante/views/${nombre}.html`);
    contenedor.innerHTML = await res.text();
    iniciarView(nombre, param);
    return;
  }

  const transition = document.startViewTransition(async () => {
    const res = await fetch(`/luminary/estudiante/views/${nombre}.html`);
    contenedor.innerHTML = await res.text();
  });

  if (push) {
    const url = param ? `#/${nombre}/${param}` : `#/${nombre}`;

    history.pushState({ nombre, param }, "", url);
  }

  await transition.finished;
  iniciarView(nombre, param);
}

// vista inicial
cargarView("inicio");

function iniciarView(nombre, param = null) {
  const views = {
    inicio: () => initInicio(),
    asignaturas: () => initAsignaturas(),
    asignatura: () => initAsignaturaDetalle(param),
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
