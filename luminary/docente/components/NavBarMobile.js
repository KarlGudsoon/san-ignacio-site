export default function NavBarDocenteMobile(vistaActiva = 'inicio') {
  const container = document.getElementById("navbarmobile");
  if (!container) return;

  const links = [
    { view: 'inicio',       icon: '/assets/icons/home.svg',                        label: 'Inicio' },
    { view: 'cursos',       icon: '/assets/icons/teacher.svg',                     label: 'Cursos' },
    { view: 'evaluaciones', icon: '/assets/icons/fa6-solid--list-ol.svg',          label: 'Evaluaciones' },
    { view: 'jefatura',     icon: '/assets/icon/hugeicons--students.svg',          label: 'Jefatura' },
  ];

  const items = links.map(({ view, icon, label }) => `
    <li>
      <button class="${vistaActiva === view ? 'active' : ''}" onClick="cargarView('${view}')">
        <img class="icon" src="${icon}">
        <span>${label}</span>
      </button>
    </li>
  `).join('');

  container.innerHTML = `
    <nav>
      <ul>
        <li class="logo"><img src="/assets/icon/logo-2.svg" alt=""></li>
        ${items}
      </ul>
      <a href="../logout.php" class="aside-logout"><img src="/assets/icons/tabler--logout.svg"></a>
    </nav>
  `;
}