export default function Footer() {
  const container = document.getElementById("footer");
  if (!container) return;
  container.innerHTML = `
    <div class="contenedor-footer flex-wrap">
        <div
          class="seccion-footer d-flex flex-column gap-3 align-items-start"
          style="min-width: 200px"
        >
          <img src="/assets/icons/logo-2.svg" alt="" />
          <p>
            El Centro de Estudios San Ignacio de Villa Alemana es una comunidad
            educativa para jóvenes y adultos, orientada al desarrollo integral,
            la formación humanista-cristiana y el acompañamiento académico hacia
            nuevas oportunidades educativas y laborales.
          </p>
          <div class="redes-sociales d-flex gap-3">
            <a href="https://wa.me/56996116669" target="_blank"
              ><img class="h-100" src="/assets/icons/whatsapp.svg" alt=""
            /></a>
            <a href="https://www.instagram.com/sanignaciova/" target="_blank"
              ><img class="h-100" src="/assets/icons/instagram.svg" alt=""
            /></a>
          </div>
        </div>
        <div class="d-flex flex-fill">
          <div
            class="seccion-footer d-flex flex-column gap-3 justify-content-center"
          >
            <a class="link-simple" href="/">Inicio</a>
            <a class="link-simple" href="/pages/estudiantes"
              >Estudiantes</a
            >
            <a class="link-simple" href="/pages/nosotros"
              >Sobre nosotros</a
            >
            <a class="link-simple" href="/#contacto">Contacto</a>
          </div>
          <div
            class="seccion-footer d-flex flex-column gap-3 justify-content-center"
          >
            <a class="link-simple" href="/pages/reglamentos"
              >Reglamentos</a
            >
            <a class="link-simple" href="">PIE</a>
            <a class="link-simple" href="/pages/mision-vision"
              >Misión y visión</a
            >
          </div>
        </div>
      </div>
      <div class="text-center p-3" style="background-color: rgba(0, 0, 0, 15%)">
        <span
          >&copy; 2025 Centro de Estudios San Ignacio. Todos los derechos
          reservados.</span
        >
        <span
          >Página Web desarrollada por
          <a
            target="_blank"
            href="https://www.linkedin.com/in/adri%C3%A1n-maturana-mu%C3%B1oz-3a7b501aa/"
            >Adrián Maturana</a
          ></span
        >
      </div>
  
  `;
}
