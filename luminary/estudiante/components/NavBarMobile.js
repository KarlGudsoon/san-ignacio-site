export default function NavBarEstudianteMobile() {
  const container = document.getElementById("navbarmobile");
  if (!container) return;
  container.innerHTML = `
    <nav>
        <ul>
            <li class="logo"><img src="/assets/icon/logo-2.svg" alt=""></li>

            <li>
                <button onClick="cargarView('inicio')"><img class="icon" src="/assets/icons/home.svg"><span>Inicio</span></button>
            </li>

            <li>
                <button onClick="cargarView('asignaturas')"><img class="icon" src="/assets/icons/teacher.svg"><span>Asignaturas</span></button>
            </li>

            <li>
                <button onClick="cargarView('notas')"><img class="icon" src="/assets/icons/fa6-solid--list-ol.svg"><span>Notas</span></button>
            </li>
        </ul>

        <a href="../logout.php" class="aside-logout"><img src="/assets/icons/tabler--logout.svg"></a>
    </nav>


    `;
}
