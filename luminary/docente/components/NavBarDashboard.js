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
                <button onClick="cargarView('cursos')"><img class="icon" src="/assets/icons/teacher.svg"><span>Cursos</span></button>
            </li>

            <li>
                <button onClick="cargarView('evaluaciones')"><img class="icon" src="/assets/icons/fa6-solid--list-ol.svg"><span>Evaluaciones</span></button>
            </li>

            <li>
                <a href="matriculas.php"><img class="icon" src="/assets/icons/school.svg"><span>Matrículas</span></a>
            </li>

            <li>
                <a href="configuracion.php"><img class="icon" src="/assets/icons/gear.svg"><span>Configuración</span></a>
            </li>
        </ul>

        <a href="../logout.php" class="aside-logout"><img src="/assets/icons/tabler--logout.svg"></a>
    </nav>


    `;
}
