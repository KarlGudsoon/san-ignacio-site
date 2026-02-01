function capitalizarPalabras(texto) {
  if (!texto) return texto;

  return texto
    .toLowerCase()
    .split(" ")
    .map((palabra) => palabra.charAt(0).toUpperCase() + palabra.slice(1))
    .join(" ");
}

fetch("/luminary/api/estudiante/me.php")
  .then((res) => {
    if (!res.ok) {
      window.location.href = "/luminary/";
      return;
    }
    return res.json();
  })
  .then((data) => {
    const nombreFormateado = capitalizarPalabras(data.nombre.toLowerCase());
    const apellidosFormateado = capitalizarPalabras(
      data.apellidos.toLowerCase(),
    );
    document
      .querySelectorAll('[data-estudiante="nombre"]')
      .forEach((el) => (el.textContent = nombreFormateado));

    document
      .querySelectorAll('[data-estudiante="apellidos"]')
      .forEach((el) => (el.textContent = apellidosFormateado));
  })
  .catch(() => {
    window.location.href = "/luminary/";
  });
