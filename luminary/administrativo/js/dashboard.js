function initInicio() {
  fetch("/luminary/api/admin/me.php")
    .then((res) => {
      if (!res.ok) {
        window.location.href = "/luminary/";
        return;
      }
      return res.json();
    })

    .then((data) => {
      const nombreFormateado = capitalizarPalabras(data.nombre.toLowerCase());
      const primerNombre = nombreFormateado.trim().split(" ")[0];

      document
        .querySelectorAll('[data-admin="nombre"]')
        .forEach((el) => (el.textContent = primerNombre));
    });
}

function capitalizarPalabras(texto) {
  if (!texto) return texto;

  return texto
    .toLowerCase()
    .split(" ")
    .map((palabra) => palabra.charAt(0).toUpperCase() + palabra.slice(1))
    .join(" ");
}

function mostrarMensaje(mensaje, tipo = "red") {
  const msg = document.getElementById("mensaje");
  msg.textContent = mensaje;
  msg.className = `mostrar ${tipo}`;
  setTimeout(() => {
    msg.classList.remove("mostrar");
  }, 3000);
}