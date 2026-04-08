

document.body.addEventListener("click", function(e) {
  if (e.target.classList.contains("cerrar-contenedor")) {
    e.target.parentElement.classList.remove("active");
    document.body.classList.remove("no-scroll");
  }
});

document.body.addEventListener("click", function(e) {
  if (e.target.classList.contains("contenedor-modal") && e.target.classList.contains("active")) {
    e.target.classList.remove("active");
    document.body.classList.remove("no-scroll");
  }
});

function AbrirContenedor() {
    const idContenedor = this.getAttribute("data-modal");
    const modal = document.getElementById(idContenedor);
  
    if (modal) {
      modal.classList.add("active");
      document.body.classList.add("no-scroll");
  
      modal.addEventListener("click", function cerrarFuera(e) {
        const contenido = modal.querySelector(".modal-contenido");
        if (!contenido.contains(e.target)) {
          modal.classList.remove("active");
          document.body.classList.remove("no-scroll");
          modal.removeEventListener("click", cerrarFuera); 
        }
      });
    }
  }

document.querySelectorAll("[data-modal]").forEach(boton => {
    boton.addEventListener("click", AbrirContenedor);
});


