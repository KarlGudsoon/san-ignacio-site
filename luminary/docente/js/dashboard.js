function initInicio() {
  fetch("/luminary/api/docente/me.php")
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
        .querySelectorAll('[data-docente="nombre"]')
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
