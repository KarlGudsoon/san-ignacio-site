async function initInicio() {
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

async function verificarSesion() {
  try {
    const res = await fetch("/luminary/api/docente/me.php");
    const data = await res.json();

    if (!res.ok) {
      window.location.href = "/luminary/";
      return;
    }
    console.log(data);
  } catch (error) {
    console.log(error);
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

// Variable para almacenar el ID del timer activo
let timerMensaje = null;

function mostrarMensaje(mensaje, tipo = "green", time = 3000) {
  const msg = document.getElementById("mensaje");
  if (!msg) return; // Evitar error si el elemento no existe en la página

  // 1. Cancelar el temporizador anterior si existía uno en ejecución
  if (timerMensaje) {
    clearTimeout(timerMensaje);
  }

  // 2. Establecer texto y clases (conservando clases base si existen)
  msg.textContent = mensaje;

  // Limpiar tipos anteriores para no acumular (ej: 'green', 'red')
  msg.classList.remove("red", "green", "blue", "yellow");
  msg.classList.add("mostrar", tipo);

  // 3. Programar el ocultamiento del mensaje
  timerMensaje = setTimeout(() => {
    msg.classList.remove("mostrar");
    timerMensaje = null;
  }, time);
}
