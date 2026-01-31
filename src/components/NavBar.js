export default function NavBar() {
  const container = document.getElementById("navbar");

  if (!container) return;

  container.innerHTML = `
  
    <button
      class="abrir-navegacion d-block d-md-none"
      type="button"
      data-bs-toggle="offcanvas"
      data-bs-target="#offcanvasExample"
      aria-controls="offcanvasExample"
    >
      <img src="/assets/icons/menu.svg" alt="" />
    </button>
    <div class="contenedor-logo-nav">
      <img
        class="logo"
        src="/assets/icons/logo-2.svg"
        alt="Logo Centro de Estudios San Ignacio"
      />
      <span>Centro de Estudios San Ignacio Villa Alemana</span>
    </div>

    <ul class="d-none d-md-flex">
      <li><a href="/">Inicio</a></li>
      <li><a href="/pages/estudiantes">Estudiantes</a></li>
      <li><a href="/pages/admision">Admisión</a></li>
      <div class="dropdown">
        <button
          class="btn-nav dropdown-toggle"
          type="button"
          data-bs-toggle="dropdown"
          aria-expanded="false"
        >
          Sobre nosotros
        </button>
        <ul class="dropdown-menu">
          <li><a href="/pages/reglamentos">Reglamentos</a></li>
          <li><a href="/pages/mision-vision">Misión y Visión</a></li>
          <li><a href="/pages/galeria">Galería</a></li>
          <li><a href="/pages/nosotros">Nosotros</a></li>
        </ul>
      </div>
      <li><a href="/#contacto">Contacto</a></li>
      <li><a class="boton-navegacion" href="/luminary/">Ingresar</a></li>
    </ul>
  `;
}
