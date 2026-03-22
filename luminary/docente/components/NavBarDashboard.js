export default function NavBarDocente(vistaActiva = 'inicio') {
  const container = document.getElementById("navbar");
  if (!container) return;

  const activo = (view) => vistaActiva === view ? 'active' : '';

  container.innerHTML = `
    <nav>
        <ul>
            <li class="logo"><img src="/assets/icon/logo-2.svg" alt=""></li>

            <li>
                <button class="${activo('inicio')}" onClick="cargarView('inicio')">
                  <img class="icon" src="/assets/icons/home.svg"><span>Inicio</span>
                </button>
            </li>

            <li>
                <button class="${activo('cursos')}" onClick="cargarView('cursos')">
                  <img class="icon" src="/assets/icons/teacher.svg"><span>Cursos</span>
                </button>
            </li>

            <li>
                <button class="${activo('evaluaciones')}" onClick="cargarView('evaluaciones')">
                  <img class="icon" src="/assets/icons/fa6-solid--list-ol.svg"><span>Evaluaciones</span>
                </button>
            </li>

            <li>
                <button class="${activo('jefatura')}" onClick="cargarView('jefatura')">
                  <img class="icon" src="/assets/icon/hugeicons--students.svg"><span>Jefatura</span>
                </button>
            </li>
        </ul>

        <a href="../logout.php" class="aside-logout"><img src="/assets/icons/tabler--logout.svg"></a>
    </nav>
  `;
}