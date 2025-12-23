document.getElementById("selector-fotos").addEventListener("change", function(event) {
    const selectedValue = event.target.value;
    
});

function cargarFotos(categoria) {
    const galeria = document.getElementById("galeria-fotos");

    for (let i = 1; i <= 392; i++) {
        const img = document.createElement("img");
        img.classList.add("foto-galeria");
        img.src = `/assets/img/gallery/${categoria}/${i}.jpg`;
        img.alt = `Foto ${i}`;
        img.setAttribute("data-bs-target", "#galeriaModal1");
        img.setAttribute("data-bs-toggle", "modal");
        galeria.appendChild(img);
        img.addEventListener("click", function() {
            ampliarFoto(img.src);
        });
    }

}

function ampliarFoto(src) {
    const modalImg = document.querySelector("#galeriaModal1 .modal-content img");
    modalImg.src = src;
    const modal = new bootstrap.Modal(document.getElementById('galeriaModal1'));
    modal.show();
}

document.addEventListener("DOMContentLoaded", function() {
    cargarFotos("L2025");
});