<?php
// Detectar la página actual
$pagina_actual = $_SERVER['PHP_SELF'];

// Definir qué páginas pertenecen a cada sección
$secciones = [
    'inicio' => ['/luminary/editor/editor.php'],
    'cursos' => ['/luminary/editor/cursos/index.php'],
    'notas' => ['/luminary/editor/notas.php'],
    'jefatura' => ['/luminary/editor/jefatura.php'],
    'configuracion' => ['/luminary/editor/configuracion.php']
];

// Función para verificar si la página actual está en una sección
function esSeleccionada($pagina, $seccion) {
    global $secciones;
    return in_array($pagina, $secciones[$seccion] ?? []);
}
?>
<aside class="nav-top">
    <nav>
        <ul>
            <li><img src="/assets/icon/logo-2.svg" alt=""></li>

            <li class="<?= esSeleccionada($pagina_actual, 'inicio') ? 'seleccionada' : '' ?>">
                <a href="/luminary/editor/editor.php"><img class="icon" src="/assets/icons/home.svg">Inicio</a>
            </li>

            <li class="<?= esSeleccionada($pagina_actual, 'cursos') ? 'seleccionada' : '' ?>">
                <a href="/luminary/editor/cursos"><img class="icon" src="/assets/icons/fa6-solid--list-ol.svg">Cursos</a>
            </li>

            <li class="<?= esSeleccionada($pagina_actual, 'notas') ? 'seleccionada' : '' ?>">
                <a href="/luminary/editor/notas.php"><img class="icon" src="/assets/icons/grade.svg">Notas</a>
            </li>
            <?php if ($primerCursoJefatura): ?>
                <li>
                    <a href="/luminary/editor/jefatura.php?curso_id=<?= $primerCursoJefatura['id'] ?>">
                        <img class="icon" src="/assets/icons/list.svg" title="Ver curso jefe">Jefatura
                    </a>
                </li>
            <?php else: ?>
                <li>
                    <img class="icon" style="filter: brightness(80%);" src="/assets/icons/list.svg" title="No tienes jefatura">
                </li>
            <?php endif; ?>
            <li class="<?= esSeleccionada($pagina_actual, 'configuracion') ? 'seleccionada' : '' ?>">
                <a href="/luminary/editor/configuracion.php"><img class="icon" src="/assets/icons/gear.svg">Configuración</a>
            </li>
        </ul>

        <a href="/luminary/logout.php" class="aside-logout"><img src="/assets/icons/tabler--logout.svg"></a>
    </nav>
</aside>
