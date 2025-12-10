<aside class="nav-bottom">
    <nav>
        <ul>
            <li><img src="/assets/icon/logo-2.svg" alt=""></li>

            <li class="<?= esSeleccionada($pagina_actual, 'inicio') ? 'seleccionada' : '' ?>">
                <a href="/luminary/editor/editor.php"><img class="icon" src="/assets/icons/home.svg"></a>
            </li>

            <li class="<?= esSeleccionada($pagina_actual, 'cursos') ? 'seleccionada' : '' ?>">
                <a href="/luminary/editor/cursos"><img class="icon" src="/assets/icons/fa6-solid--list-ol.svg"></a>
            </li>

            <li class="<?= esSeleccionada($pagina_actual, 'notas') ? 'seleccionada' : '' ?>">
                <a href="/luminary/editor/notas.php"><img class="icon" src="/assets/icons/grade.svg"></a>
            </li>
            <?php if ($primerCursoJefatura): ?>
                <li>
                    <a href="/luminary/editor/jefatura.php?curso_id=<?= $primerCursoJefatura['id'] ?>">
                        <img class="icon" src="/assets/icons/list.svg" title="Ver curso jefe">
                    </a>
                </li>
            <?php else: ?>
                <li>
                    <img class="icon" style="filter: brightness(80%);" src="/assets/icons/list.svg" title="No tienes jefatura">
                </li>
            <?php endif; ?>
            <li class="<?= esSeleccionada($pagina_actual, 'configuracion') ? 'seleccionada' : '' ?>">
                <a href="/luminary/editor/configuracion.php"><img class="icon" src="/assets/icons/gear.svg"></a>
            </li>
        </ul>

        <a href="/luminary/logout.php" class="aside-logout"><img src="/assets/icons/tabler--logout.svg"></a>
    </nav>
</aside>

