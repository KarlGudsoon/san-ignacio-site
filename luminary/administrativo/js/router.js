import NavBarAdmin from '../components/NavBarDashboard.js';
import NavBarAdminMobile from '../components/NavBarMobile.js';

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

  const vistaActiva = { curso: 'cursos', estudiante: 'estudiantes' }[nombre] ?? nombre;
  NavBarAdmin(vistaActiva);
  NavBarAdminMobile(vistaActiva);

  const resSesion = await verificarSesion();

  if (!resSesion) {
    window.location.href = "/luminary/";
    return;
  }

  if (!document.startViewTransition) {
    const res = await fetch(`/luminary/administrativo/views/${nombre}.html`, {
      cache: "no-store",
    });
    contenedor.innerHTML = await res.text();
    iniciarView(nombre, param);
    return;
  }

  document.startViewTransition(async () => {
    const res = await fetch(`/luminary/administrativo/views/${nombre}.html`, {
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
    curso: () => initCurso(param),
    horario: () => initHorario(),
    estudiante: () => initEstudiante(param),
    estudiantes: () => initEstudiantes(),
    docentes: () => initDocentes(),
    docente_editar: () => initEditarDocente(param),
    matricula_nueva: () => initMatriculaNueva(),
    matricula_editar: () => initMatriculaEditar(param),
    matriculas_pendientes: () => initMatriculasPendientes(),
    matricula_pendiente_nueva: () => initMatriculaPendienteNueva(),
    pendientes: () => initPendientes(param),
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

async function verificarSesion() {
  try {
    const res = await fetch("/luminary/api/admin/me.php");
    const data = await res.json();

    if (!res.ok) {
      window.location.href = "/luminary/";
      return;
    }
    
    return data;
    
  } catch (error) {
    console.log(error)
  }
}