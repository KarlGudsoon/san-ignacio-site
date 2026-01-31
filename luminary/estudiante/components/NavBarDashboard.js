export default function NavBarEstudiante() {
  const container = document.getElementById("navbar");
  if (!container) return;
  container.innerHTML = `
    <nav>
        <ul>
            <li><img src="/assets/icon/logo-2.svg" alt=""></li>

            <li class="<?= esSeleccionada($pagina_actual, 'inicio') ? 'seleccionada' : '' ?>">
                <a href="admin.php"><img class="icon" src="/assets/icons/home.svg">Inicio</a>
            </li>

            <li class="<?= esSeleccionada($pagina_actual, 'cursos') ? 'seleccionada' : '' ?>">
                <a href="admin_cursos.php"><img class="icon" src="/assets/icons/fa6-solid--list-ol.svg">Cursos</a>
            </li>

            <li class="<?= esSeleccionada($pagina_actual, 'profesores') ? 'seleccionada' : '' ?>">
                <a href="admin_profesores.php"><img class="icon" src="/assets/icons/teacher.svg">Profesores</a>
            </li>

            <li class="<?= esSeleccionada($pagina_actual, 'matriculas') ? 'seleccionada' : '' ?>">
                <a href="matriculas.php"><img class="icon" src="/assets/icons/school.svg">Matrículas</a>
            </li>

            <li class="<?= esSeleccionada($pagina_actual, 'configuracion') ? 'seleccionada' : '' ?>">
                <a href="configuracion.php"><img class="icon" src="/assets/icons/gear.svg">Configuración</a>
            </li>
        </ul>

        <a href="../logout.php" class="aside-logout"><img src="/assets/icons/tabler--logout.svg"></a>
    </nav>


    `;
}
