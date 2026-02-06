const view = document.getElementById("dashboard-content");

document.addEventListener("click", (e) => {
  const btn = e.target.closest("[data-view]");
  if (!btn) return;

  const view = btn.dataset.view;
  cargarView(view);
});

async function cargarView(nombre) {
  const contenedor = document.getElementById("dashboard-content");

  const res = await fetch(`/luminary/estudiante/views/${nombre}.html`);
  contenedor.innerHTML = await res.text();

  iniciarView(nombre);
}


// vista inicial
cargarView("inicio");

function iniciarView(nombre) {
  const views = {
    inicio: initInicio,
    asignaturas: initAsignaturas
  };

  views[nombre]?.();
}