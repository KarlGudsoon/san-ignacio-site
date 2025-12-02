<?php
// Detectar la página actual
$pagina_actual = basename($_SERVER['PHP_SELF']);
?>

<aside class="nav-top">
    <nav>
        <ul>
            <li><img src="/assets/icon/logo-2.svg" alt=""></li>

            <li class="<?= $pagina_actual === 'admin.php' ? 'seleccionada' : '' ?>">
                <a href="admin.php"><img class="icon" src="/assets/icons/home.svg">Inicio</a>
            </li>

            <li class="<?= $pagina_actual === 'admin_cursos.php' ? 'seleccionada' : '' ?>">
                <a href="admin_cursos.php"><img class="icon" src="/assets/icons/fa6-solid--list-ol.svg">Cursos</a>
            </li>

            <li class="<?= $pagina_actual === 'admin_profesores.php' ? 'seleccionada' : '' ?>">
                <a href="admin_profesores.php"><img class="icon" src="/assets/icons/teacher.svg">Profesores</a>
            </li>

            <li class="<?= $pagina_actual === 'matriculas.php' ? 'seleccionada' : '' ?>">
                <a href="matriculas.php"><img class="icon" src="/assets/icons/school.svg">Matrículas</a>
            </li>
            <li class="<?= $pagina_actual === 'configuracion.php' ? 'seleccionada' : '' ?>">
                <a href="configuracion.php"><img class="icon" src="/assets/icons/gear.svg">Configuración</a>
            </li>
        </ul>

        <a href="../logout.php" class="aside-logout"><img src="/assets/icons/tabler--logout.svg"></a>
    </nav>
</aside>
