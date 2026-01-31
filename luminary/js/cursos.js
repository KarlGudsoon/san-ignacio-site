const params = new URLSearchParams(window.location.search);
const id = params.get("id");

fetch(`../api/cursos/show.php?id=${id}`)
  .then((r) => r.json())
  .then((data) => {
    document.getElementById("curso").textContent =
      `${data.curso.nivel} Nivel ${data.curso.letra}`;

    document.getElementById("jornada").textContent = data.jornada;
    document.getElementById("horario").textContent = data.horario;

    const contenedor = document.getElementById("asignaturas");

    data.asignaturas.forEach((a) => {
      contenedor.innerHTML += `
        <div class="col-md-4 p-2">
          <div class="p-3 rounded asignatura">
            <h3>${a.asignatura}</h3>
            <p>${a.profesor ?? "Sin información"}</p>
          </div>
        </div>
      `;
    });
  });
