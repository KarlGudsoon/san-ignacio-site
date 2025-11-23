const anioSiguiente = new Date().getFullYear() + 1;
document.getElementById("año-actual").textContent = anioSiguiente;

// FORMATEAR RUT EN TIEMPO REAL
const inputRut = document.querySelector(".formateador_rut");

inputRut.addEventListener("input", function () {
    let valor = this.value.replace(/\./g, "").replace(/-/g, "").replace(/[^0-9kK]/g, "");

    // Limitar a máximo 9 dígitos incluyendo DV
    if (valor.length > 9) {
        valor = valor.substring(0, 9);
    }

    // Aplicar formato
    if (valor.length > 1) {
        let cuerpo = valor.slice(0, -1);
        let dv = valor.slice(-1);

        // Poner puntos al cuerpo
        cuerpo = cuerpo.replace(/\B(?=(\d{3})+(?!\d))/g, ".");

        this.value = cuerpo + "-" + dv.toUpperCase();
    } else {
        this.value = valor;
    }
});

// IMPEDIR PEGAR FORMATO INCORRECTO
inputRut.addEventListener("paste", function (e) {
    e.preventDefault();
});