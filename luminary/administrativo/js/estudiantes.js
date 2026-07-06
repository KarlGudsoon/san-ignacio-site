async function initEstudiantes() {
  todosEstudiantes();

  await cargarNumeroSolicitudes();

  document
    .getElementById("btn-agregar-estudiante")
    .addEventListener("click", () => {
      cargarView("matricula_nueva");
    });

  document
    .getElementById("btn-matriculas-pendientes")
    .addEventListener("click", () => {
      cargarView("matriculas_pendientes");
    });
}

async function todosEstudiantes() {
  try {
    const response = await fetch("/luminary/api/admin/estudiantes/estudiantes_lista.php");
    const data = await response.json();

    if (!data.success) return;

    const contenedorPrincipal = document.getElementById("estudiantes-contenido");
    contenedorPrincipal.innerHTML = "";

    // Buscador
    const buscadorEst = document.createElement("div");
    buscadorEst.classList.add("buscador");
    buscadorEst.innerHTML = `<input type="text" id="buscadorEstudiantes" placeholder="Buscar por nombre">`;
    contenedorPrincipal.appendChild(buscadorEst);
    document.getElementById("buscadorEstudiantes").addEventListener("input", buscarEstudiantes);

    // Estado del ordenamiento
    let ordenActual = { columna: null, direccion: "asc" };

    const tabla = document.createElement("table");
    tabla.classList.add("tabla-estudiantes");
    tabla.id = "tablaEstudiantes";

    tabla.innerHTML = `
      <thead>
        <tr>
          <th>#</th>
          <th data-orden="nombre_estudiante" class="th-ordenable">Nombre Completo <span class="icono-orden">↕</span></th>
          <th>RUT</th>
          <th>Edad</th>
          <th>Curso</th>
          <th data-orden="fecha_registro" class="th-ordenable activo">Fecha de matrícula <span class="icono-orden">↓</span></th>
        </tr>
      </thead>
      <tbody></tbody>
    `;

    const tbody = tabla.querySelector("tbody");

    // Función para renderizar filas
    function renderizarFilas(lista) {
      tbody.innerHTML = "";
      lista.forEach((estudiante, index) => {
        const fila = document.createElement("tr");
        fila.innerHTML = `
          <td>${index + 1}</td>
          <td><span class="estudiante-tabla" data-estudiante-id="${estudiante.id_estudiante}">${estudiante.nombre_estudiante} ${estudiante.apellidos_estudiante}</span></td>
          <td>${estudiante.rut_estudiante}</td>
          <td>${estudiante.edad ?? "-"}</td>
          <td>${estudiante.curso ?? "-"}</td>
          <td>${estudiante.fecha_registro ?? "-"}</td>
        `;
        tbody.appendChild(fila);
      });
    }

    // Función para ordenar
    function ordenarEstudiantes(columna) {
      // Si es la misma columna, invertir dirección
      if (ordenActual.columna === columna) {
        ordenActual.direccion = ordenActual.direccion === "asc" ? "desc" : "asc";
      } else {
        ordenActual.columna = columna;
        ordenActual.direccion = "asc";
      }

      // Actualizar íconos en los headers
      tabla.querySelectorAll(".th-ordenable").forEach(th => {
        const icono = th.querySelector(".icono-orden");
        if (th.dataset.orden === columna) {
          icono.textContent = ordenActual.direccion === "asc" ? "↑" : "↓";
          th.classList.add("activo");
        } else {
          icono.textContent = "↕";
          th.classList.remove("activo");
        }
      });

      // Ordenar el array
      const ordenado = [...data.estudiantes].sort((a, b) => {
        let valA = a[columna] ?? "";
        let valB = b[columna] ?? "";

        // Ordenamiento especial para fechas en formato d-m-Y H:i
        if (columna === "fecha_registro") {
          // Convertir "28-06-2026 14:35" → Date comparable
          const parsearFecha = (str) => {
            if (!str || str === "-") return new Date(0);
            const [fecha, hora] = str.split(" ");
            const [dia, mes, anio] = fecha.split("-");
            return new Date(`${anio}-${mes}-${dia}T${hora ?? "00:00"}`);
          };
          valA = parsearFecha(a[columna]);
          valB = parsearFecha(b[columna]);
        }

        if (valA < valB) return ordenActual.direccion === "asc" ? -1 : 1;
        if (valA > valB) return ordenActual.direccion === "asc" ? 1 : -1;
        return 0;
      });

      renderizarFilas(ordenado);
    }

    // Evento click en headers ordenables
    tabla.querySelectorAll(".th-ordenable").forEach(th => {
      th.addEventListener("click", () => ordenarEstudiantes(th.dataset.orden));
    });


    renderizarFilas(data.estudiantes);

    const contenedorTabla = document.createElement("div");
    contenedorTabla.classList.add("contenedor-tabla");
    contenedorTabla.appendChild(tabla);
    contenedorPrincipal.appendChild(contenedorTabla);

    // Click en fila de estudiante
    tabla.addEventListener("click", (e) => {
      const fila = e.target.closest(".estudiante-tabla");
      if (!fila) return;
      cargarEstudiante(fila.dataset.estudianteId);
    });

  } catch (error) {
    console.error("Error cargando estudiantes:", error);
  }
}

function buscarEstudiantes() {
  const input = document.getElementById("buscadorEstudiantes");
  const filter = input.value.toLowerCase();
  const tabla = document.getElementById("tablaEstudiantes");
  const filas = tabla.getElementsByTagName("tr");
  for (let i = 1; i < filas.length; i++) {
    const nombreCompleto = filas[i]
      .getElementsByTagName("td")[1]
      .textContent.toLowerCase();
    if (nombreCompleto.includes(filter)) {
      filas[i].style.display = "";
    } else {
      filas[i].style.display = "none";
    }
  }
}

function traspasarEstudiante(estudianteId) {
  const selectCurso = document.getElementById("selectCursoTraspaso");
  const nuevoCursoId = selectCurso.value;

  if (!nuevoCursoId) {
    alert("Por favor, selecciona un curso.");
    return;
  }

  if (
    !confirm(
      "¿Estás seguro de traspasar al estudiante a este curso? Se copiarán las evaluaciones existentes y se eliminarán las del curso anterior.",
    )
  ) {
    return;
  }

  // Mostrar loading
  const btnTraspasar = document.getElementById("btn-traspasar");
  const textoOriginal = btnTraspasar.textContent;
  btnTraspasar.textContent = "Procesando...";
  btnTraspasar.disabled = true;

  fetch("/luminary/api/admin/estudiantes/estudiante_traspasar_curso.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded",
    },
    body: new URLSearchParams({
      estudiante_id: estudianteId,
      nuevo_curso_id: nuevoCursoId,
    }),
  })
    .then((res) => res.json())
    .then((data) => {
      if (data.success) {
        alert(
          `Estudiante traspasado exitosamente.\nNotas traspasadas: ${data.notas_traspasadas}\nEvaluaciones creadas: ${data.evaluaciones_creadas}\nNotas eliminadas del curso anterior: ${data.notas_eliminadas}`,
        );
        // Recargar la información del estudiante
        infoEstudiante(estudianteId);
        notasEstudiante(estudianteId);
      } else {
        alert("Error: " + data.message);
      }
    })
    .catch((error) => {
      console.error("Error traspasando estudiante:", error);
      alert("Error al traspasar estudiante.");
    })
    .finally(() => {
      // Restaurar botón
      btnTraspasar.textContent = textoOriginal;
      btnTraspasar.disabled = false;
    });
}

async function cargarNumeroSolicitudes() {
  try {
    const response = await fetch(
      "/luminary/api/admin/matriculas/matriculas_pendientes.php",
    );
    const data = await response.json();

    if (data.success) {
      const numeroSolicitudes = data.cantidad;

      if (numeroSolicitudes > 9) {
        document.getElementById("numeroSolicitudes").textContent = "9+";
      } else {
        document.getElementById("numeroSolicitudes").textContent =
          numeroSolicitudes;
      }
    } else {
      document.getElementById("numeroSolicitudes").remove();
      console.error("Error cargando número de solicitudes:", data.message);
    }
  } catch (error) {
    console.error("Error traspasando estudiante:", error);
  }
}