<aside class="nav-bottom">
    <nav>
        <ul>
            <li style="background: white;">
                <img class="icon" src="/assets/img/logo.svg" alt="">
            </li>

            <li class="<?= $pagina_actual === 'admin.php' ? 'seleccionada' : '' ?>">
                <a href="admin.php"><img class="icon" src="/assets/icons/home.svg"></a>
            </li>

            <li class="<?= $pagina_actual === 'admin_cursos.php' ? 'seleccionada' : '' ?>">
                <a href="admin_cursos.php"><img class="icon" src="/assets/icons/fa6-solid--list-ol.svg"></a>
            </li>

            <li class="<?= $pagina_actual === 'admin_profesores.php' ? 'seleccionada' : '' ?>">
                <a href="admin_profesores.php"><img class="icon" src="/assets/icons/teacher.svg"></a>
            </li>

            <li class="<?= $pagina_actual === 'matriculas.php' ? 'seleccionada' : '' ?>">
                <a href="matriculas.php"><img class="icon" src="/assets/icons/school.svg"></a>
            </li>
        </ul>

        <a href="../logout.php"><img class="icon" src="/assets/icons/tabler--logout.svg"></a>
    </nav>
</aside>
