function habilitarArrastre(
  elemento,
  { umbral = 5, selector, alineacion = "start" } = {},
) {
  const el =
    typeof elemento === "string" ? document.querySelector(elemento) : elemento;

  // Si no existe o ya fue activado, no hace nada (evita listeners duplicados)
  if (!el || el.dataset.arrastre === "1") return;
  el.dataset.arrastre = "1";
  el.classList.add("arrastrable");

  let presionado = false;
  let arrastrando = false;
  let inicioX = 0;
  let inicioScroll = 0;

  el.addEventListener("pointerdown", (e) => {
    if (e.pointerType !== "mouse" || e.button !== 0) return;
    if (e.target.closest("input, select, textarea")) return;

    presionado = true;
    arrastrando = false;
    inicioX = e.clientX;
    inicioScroll = el.scrollLeft;
  });

  el.addEventListener("pointermove", (e) => {
    if (!presionado) return;

    const dx = e.clientX - inicioX;

    // Hasta superar el umbral se considera un clic normal
    if (!arrastrando && Math.abs(dx) > umbral) {
      arrastrando = true;
      el.classList.add("arrastrando");
      el.setPointerCapture(e.pointerId);
    }

    if (arrastrando) {
      el.scrollLeft = inicioScroll - dx;
    }
  });

  const terminar = (e) => {
    if (!presionado) return;
    presionado = false;

    if (arrastrando) {
      el.classList.remove("arrastrando");
      if (el.hasPointerCapture(e.pointerId)) {
        el.releasePointerCapture(e.pointerId);
      }
      // Evita que el arrastre dispare un clic en lo que había debajo
      el.addEventListener("click", (ev) => ev.stopPropagation(), {
        capture: true,
        once: true,
      });
    }
    arrastrando = false;
  };

  el.addEventListener("pointerup", terminar);
  el.addEventListener("pointercancel", terminar);

  // Click en un elemento: posicionar el scroll en él con animación suave
  el.addEventListener("click", (e) => {
    let item;

    if (selector) {
      item = e.target.closest(selector);
      if (!item || !el.contains(item)) return;
    } else {
      // Hijo directo del contenedor que contiene el clic
      item = e.target;
      while (item && item.parentElement !== el) item = item.parentElement;
      if (!item) return;
    }

    item.scrollIntoView({
      behavior: "smooth",
      inline: alineacion, // "start" | "center" | "end" | "nearest"
      block: "nearest", // no mueve la página en vertical
    });
  });

  // Evita que el navegador arrastre imágenes o enlaces como "fantasma"
  el.addEventListener("dragstart", (e) => e.preventDefault());
}
