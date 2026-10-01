async function initInicio() {
  const res = await verificarSesion();

  document
    .querySelectorAll('[data-admin="nombre"]')
    .forEach((el) => (el.textContent = capitalizarPalabras(res.nombre)));
}

async function verificarSesion() {
  try {
    const res = await fetch("/luminary/api/admin/me.php");
    const data = await res.json();

    if (!res.ok) {
      window.location.href = "/luminary/";
      return;
    }
    
    return data;
    
  } catch (error) {
    console.log(error)
  }
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