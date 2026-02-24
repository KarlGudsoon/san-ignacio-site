export default function NavBarEstudiante() {
  const container = document.getElementById("navbar");
  if (!container) return;
  container.innerHTML = `
    <nav>
        <ul>
            <li class="logo"><img src="/assets/icon/logo-2.svg" alt=""></li>

            <li>
                <button onClick="cargarView('inicio')"><img class="icon" src="/assets/icons/home.svg"><span>Inicio</span></button>
            </li>

            <li>
                <button onClick="cargarView('horario')"><img class="icon" src="/assets/icons/teacher.svg"><span>Horarios</span></button>
            </li>
            
        </ul>

        <a href="../logout.php" class="aside-logout"><img src="/assets/icons/tabler--logout.svg"></a>
    </nav>


    `;
}
