const API = "/luminary/api/public/galeria.php";

const NOMBRES = {
    L2025: "Licenciatura 2025",
};

const selector = document.getElementById("selector-fotos");
const paginador = document.getElementById("secciones");
const galeria = document.getElementById("galeria-fotos");
const modalEl = document.getElementById("galeriaModal1");
const modalImg = modalEl.querySelector(".modal-content img");

async function cargarCategorias() {
    const res = await fetch(API);
    const categorias = await res.json();

    categorias.forEach(cat => {
        const opt = document.createElement("option");
        opt.value = cat;
        opt.textContent = NOMBRES[cat] ?? cat;
        selector.appendChild(opt);
    });

    if (categorias.length) cargarFotos(categorias[0], 1);
}

async function cargarFotos(categoria, pagina) {
    const res = await fetch(
        `${API}?categoria=${encodeURIComponent(categoria)}&pagina=${pagina}`
    );
    const data = await res.json();
    if (data.error) return;

    galeria.innerHTML = "";
    const fragment = document.createDocumentFragment();
    data.fotos.forEach((url, i) => {
        const img = document.createElement("img");
        img.className = "foto-galeria";
        img.src = url;
        img.alt = `Foto ${(data.pagina - 1) * 48 + i + 1}`;
        img.loading = "lazy";
        img.addEventListener("click", () => ampliarFoto(url));
        fragment.appendChild(img);
    });
    galeria.appendChild(fragment);

    renderPaginador(categoria, data.pagina, data.paginas);
}

function renderPaginador(categoria, actual, total) {
    paginador.innerHTML = "";
    if (total <= 1) return;

    for (let p = 1; p <= total; p++) {
        const btn = document.createElement("button");
        btn.className = "btn-seccion-galeria" + (p === actual ? " active" : "");
        btn.textContent = p;
        btn.addEventListener("click", () => {
            cargarFotos(categoria, p);
            window.scrollTo({ top: 0, behavior: "smooth" });
        });
        paginador.appendChild(btn);
    }
}

function ampliarFoto(src) {
    modalImg.src = src;
    bootstrap.Modal.getOrCreateInstance(modalEl).show();
}

selector.addEventListener("change", e => cargarFotos(e.target.value, 1));
document.addEventListener("DOMContentLoaded", cargarCategorias);