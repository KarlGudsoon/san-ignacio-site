export default function NavBarMobile() {
  const container = document.getElementById("navbar-mobile");

  if (!container) return;

  container.innerHTML = `
    <div
  class="offcanvas offcanvas-start bg-blue"
  tabindex="19999"
  id="offcanvasExample"
  aria-labelledby="offcanvasExampleLabel"
>
  <div class="offcanvas-header">
    <h5 class="offcanvas-title" id="offcanvasExampleLabel">Navegación</h5>
    <button
      type="button"
      class="btn-close dark"
      data-bs-theme="dark"
      data-bs-dismiss="offcanvas"
      aria-label="Close"
    ></button>
  </div>
  <div class="offcanvas-body">
    <div class="contenedor-logo-nav">
      <img
        class="logo"
        src="/assets/icons/logo-2.svg"
        alt="Logo Centro de Estudios San Ignacio"
      />
      <span>Centro de Estudios San Ignacio Villa Alemana</span>
    </div>
    <ul class="lista-navegacion">
      <li><a href="/">Inicio</a></li>
      <li><a href="/pages/admision">Admisión</a></li>
      <li><a href="/pages/estudiantes">Estudiantes</a></li>
      <div class="dropdown">
        <li
          class="dropdown-toggle"
          type="button"
          data-bs-toggle="dropdown"
          aria-expanded="false"
        >
          Sobre nosotros
        </li>
        <ul class="dropdown-menu">
          <li><a href="/pages/reglamentos">Reglamentos</a></li>
          <li><a href="/pages/mision-vision">Misión y Visión</a></li>
          <li><a href="/pages/nosotros">Nosotros</a></li>
        </ul>
      </div>
      <li><a href="/#contacto">Contacto</a></li>
      <a class="boton-navegacion" href="/luminary/">Ingresar</a>
    </ul>
  </div>
</div>
  `;
}
