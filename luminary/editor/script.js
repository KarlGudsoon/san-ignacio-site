const promedio = document.querySelectorAll(".promedio"); 

promedio.forEach(elemento => {
    const valor = parseFloat(elemento.textContent);

    if (valor < 4) {
        elemento.style.backgroundColor = "#e24a4a";
    } else if (valor >= 4) {
        elemento.style.backgroundColor = "#2589df"; 
    }
});