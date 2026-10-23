export default function NavBarEstudianteMobile(vistaActiva = "inicio") {
  const container = document.getElementById("navbarmobile");
  if (!container) return;

  const links = [
    { view: "inicio", icon: "/assets/icons/home.svg", label: "Inicio" },
    {
      view: "asignaturas",
      icon: "/assets/icons/teacher.svg",
      label: "Asignaturas",
    },
    {
      view: "notas",
      icon: "/assets/icons/fa6-solid--list-ol.svg",
      label: "Notas",
    },
  ];

  const items = links
    .map(
      ({ view, icon, label }) => `
    <li>
      <button class="${vistaActiva === view ? "active" : ""}" onClick="cargarView('${view}')">
        <img class="icon" src="${icon}">
        <small>${label}</small>
      </button>
    </li>
  `,
    )
    .join("");

  container.innerHTML = `
    <nav>
      <ul>
        <li class="logo"><img src="/assets/icon/logo-2.svg" alt=""></li>
        ${items}
      </ul>
      <a href="/luminary/api/auth/estudiante/logout.php" class="aside-logout"><img src="/assets/icons/tabler--logout.svg"></a>
    </nav>
  `;
}