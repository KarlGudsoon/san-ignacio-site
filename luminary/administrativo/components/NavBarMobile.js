export default function NavBarAdminMobile(vistaActiva = "inicio") {
  const container = document.getElementById("navbarmobile");
  if (!container) return;

  const links = [
    { view: "inicio", icon: "/assets/icons/home.svg", label: "Inicio" },
    { view: "cursos", icon: "/assets/icons/teacher.svg", label: "Cursos" },
    {
      view: "horario",
      icon: "/assets/icon/material-symbols--schedule-outline-rounded.svg",
      label: "Horarios",
    },
    {
      view: "estudiantes",
      icon: "/assets/icon/list.svg",
      label: "Estudiantes",
    },
    {
      view: "matriculas_pendientes",
      icon: "/assets/icon/fluent-mdl2--open-enrollment.svg",
      label: "Matrículas",
    },
    {
      view: "docentes",
      icon: "/assets/icons/hugeicons--teacher.svg",
      label: "Docentes",
    },
  ];

  const items = links
    .map(
      ({ view, icon, label }) => `
    <li>
      <button class="${vistaActiva === view ? "active" : ""}" onClick="cargarView('${view}')">
        <img class="icon" src="${icon}" alt="">
        <span>${label}</span>
      </button>
    </li>
  `,
    )
    .join("");

  container.innerHTML = `
    <nav>
      <ul class="contenedor-botones-navbar">
        <li class="logo"><img src="/assets/icon/logo-2.svg" alt=""></li>
        ${items}
      </ul>
      <a href="../logout.php" class="aside-logout"><img src="/assets/icons/tabler--logout.svg" alt="Cerrar sesión"></a>
    </nav>
  `;
}
