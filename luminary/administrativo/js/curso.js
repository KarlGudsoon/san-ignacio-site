async function initCurso(cursoId) {
  await cargarInfo(cursoId);
  

  document.querySelectorAll(".asignatura-navegacion button").forEach((btn) => {
    btn.addEventListener("click", () => {
      let botones = document.querySelectorAll(".asignatura-navegacion button");

      botones.forEach((b) => {
        b.classList.remove("seleccionado");
      });

      btn.classList.add("seleccionado");
    });
  });

  document.addEventListener("click", function (e) {
    // ABRIR
    if (e.target.dataset.abrir) {
      const id = e.target.dataset.abrir;
      document.getElementById(id).classList.add("activo");
    }
    if (e.target.dataset.cerrar) {
      const id = e.target.dataset.cerrar;
      document.getElementById(id).classList.remove("activo");
    }
  });

  const form = document.getElementById("formEvaluacion");

  form.addEventListener("submit", async (e) => {
    e.preventDefault();
    await guardarEvaluacion();
  });

  document
    .getElementById("btn-estudiantes")
    .addEventListener("click", () => cargarEstudiantes(cursoId));
  document
    .getElementById("btn-notas")
    .addEventListener("click", () => cargarSeccionEv(cursoId));

  document.getElementById("volver").addEventListener("click", () => {
    cargarView("cursos");
  });
}

async function cargarInfo(cursoId) {
  try {
    const res = await fetch(
      `/luminary/api/admin/cursos/curso_detalle.php?id=${cursoId}`,
      {
        cache: "no-store",
      },
    );

    const data = await res.json();

    if (!data.success) return;

    console.log("Se cargaron los datos con exito");

    const curso = document.getElementById("curso-detalle");
    const colorGuardado = sessionStorage.getItem("cursoColor");
    curso.style.backgroundColor = colorGuardado;

    document.getElementById("curso-niveles").textContent =
      data.curso.nivel === "1°" ? "(1° y 2° medio)" : "(3° y 4° medio)";
    document.getElementById("nombre-curso").textContent = data.curso.curso_full;
  } catch (error) {
    console.error("Error cargando cursos:", error);
  }
}

async function cargarEstudiantes(cursoId) {
  try {
    const res = await fetch(
      `/luminary/api/admin/cursos/curso_estudiantes.php?curso_id=${cursoId}`,
      { cache: "no-store" }
    );

    const data = await res.json();

    if (!data.success) return;
    
    const contenedorPrincipal = document.getElementById("curso-contenido")
    contenedorPrincipal.innerHTML = "";

    // Crear tabla
    const tabla = document.createElement("table");
    tabla.classList.add("tabla-estudiantes");

    // Header
    tabla.innerHTML = `
      <thead>
        <tr>
          <th>#</th>
          <th>Nombre Completo</th>
          <th>RUT</th>
          <th>Edad</th>
          <th>Teléfono</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody></tbody>
    `;

    const tbody = tabla.querySelector("tbody");

    data.estudiantes.forEach((estudiante, index) => {
      const fila = document.createElement("tr");

      fila.innerHTML = `
        <td>${index + 1}</td>
        <td>${estudiante.nombre_estudiante} ${estudiante.apellidos_estudiante}</td>
        <td>${estudiante.rut_estudiante}</td>
        <td>${estudiante.edad ?? "-"}</td>
        <td>${estudiante.telefono_estudiante ?? "-"}</td>
        <td><button>Editar</button><button>Eliminar</button></td>
      `;

      tbody.appendChild(fila);
    });

    const contenedorTabla = document.createElement("div");
    contenedorTabla.classList.add("contenedor-tabla");

    contenedorTabla.appendChild(tabla)

    contenedorPrincipal.appendChild(contenedorTabla);

  } catch (error) {
    console.error("Error cargando estudiantes:", error);
  }
}

async function cargarAsig(cursoId) {
  try {
    const res = await fetch(
      `/luminary/api/admin/cursos/curso_asignaturas.php?curso_id=${cursoId}`,
      { cache: "no-store" }
    );

    const data = await res.json();

    if (!data.success) return;

    const colores = {
        matematicas: "#3891e9",
        lenguaje: "#f75353",
        historia: "#7ed321",
        ciencias: "#0da761",
        ingles: "#cdb51a",
        "estudios sociales": "#f5a623",
        "artes visuales": "#23babf",
        tic: "#8544cf",
        filosofia: "#cf58dcff",
        "instrumental 1": "#fb2b66",
        "instrumental 2": "#f16b3a",
        diferenciado: "#09dc84",
        jefatura: "#0c4d8e",
    };
    
    const contenedorPrincipal = document.getElementById("curso-contenido")
    contenedorPrincipal.innerHTML = "";

    const contenedorAsignaturas = document.createElement("div");
    contenedorAsignaturas.classList.add("contenedor-asignaturas")
    
    data.asignaturas.forEach((asignatura) => {
      const cardAsignatura = document.createElement("div");
      const key = asignatura.asignatura
          .toLowerCase()
          .normalize("NFD")
          .replace(/[\u0300-\u036f]/g, "")
          .replace(/\s+/g, " ")
          .trim();
      const color = colores[key] ?? "#e0e0e0";
      cardAsignatura.style.backgroundColor = `${color}`;
      cardAsignatura.style.setProperty("--backgroundColor", `${color}`);
      cardAsignatura.style.setProperty("--backgroundColor2", `${color}40`);
      cardAsignatura.style.setProperty("--backgroundColor3", `${color}80`);
      cardAsignatura.classList.add("asignatura-item");  
      cardAsignatura.setAttribute("data-curso-profesor-id", asignatura.curso_profesor_id);
      cardAsignatura.innerHTML = `
        <h4>${asignatura.asignatura}</h4>
      `;
      contenedorAsignaturas.appendChild(cardAsignatura);
    });

    contenedorPrincipal.appendChild(contenedorAsignaturas);

  } catch (error) {
    console.error("Error cargando estudiantes:", error);
  }
  
}


async function cargarSeccionEv(cursoId) {
  try {
    const res = await fetch(
      `/luminary/api/admin/cursos/curso_asignaturas.php?curso_id=${cursoId}`,
      { cache: "no-store" }
    );

    const data = await res.json();

    if (!data.success) return;

    await cargarTipos()

    const colores = {
        matematicas: "#3891e9",
        lenguaje: "#f75353",
        historia: "#7ed321",
        ciencias: "#0da761",
        ingles: "#cdb51a",
        "estudios sociales": "#f5a623",
        "artes visuales": "#23babf",
        tic: "#8544cf",
        filosofia: "#cf58dcff",
        "instrumental 1": "#fb2b66",
        "instrumental 2": "#f16b3a",
        diferenciado: "#09dc84",
        jefatura: "#0c4d8e",
    };

    const contenedorPrincipal = document.getElementById("curso-contenido")
    contenedorPrincipal.innerHTML = "";

    const contenedorAsignaturas = document.createElement("div");
    contenedorAsignaturas.classList.add("contenedor-asignaturas")
    
    data.asignaturas.forEach((asignatura, index) => {
      const cardAsignatura = document.createElement("div");
      const key = asignatura.asignatura
          .toLowerCase()
          .normalize("NFD")
          .replace(/[\u0300-\u036f]/g, "")
          .replace(/\s+/g, " ")
          .trim();
      const color = colores[key] ?? "#e0e0e0";
      cardAsignatura.style.backgroundColor = `${color}`;
      cardAsignatura.style.setProperty("--backgroundColor", `${color}`);
      cardAsignatura.style.setProperty("--backgroundColor2", `${color}40`);
      cardAsignatura.style.setProperty("--backgroundColor3", `${color}80`);
      cardAsignatura.classList.add("asignatura-item");  
      cardAsignatura.classList.add("deseleccionado");
      cardAsignatura.setAttribute("data-curso-profesor-id", asignatura.curso_profesor_id);
      cardAsignatura.innerHTML = `
        <h4>${asignatura.asignatura}</h4>
      `;
      if (index === 0) {
        cardAsignatura.classList.add("seleccionado");
        cardAsignatura.classList.remove("deseleccionado")

        // Cargar evaluaciones automáticamente
        cargarEvaluaciones(asignatura.curso_profesor_id);
      }
      cardAsignatura.addEventListener("click", () => {
        cargarEvaluaciones(cardAsignatura.getAttribute("data-curso-profesor-id"));
        document.querySelectorAll(".asignatura-item").forEach(card => {
          card.classList.remove("seleccionado");
          card.classList.add("deseleccionado")
        });
        cardAsignatura.classList.add("seleccionado");
        cardAsignatura.classList.remove("deseleccionado");

        contenedorEv.style.backgroundColor = `${color}`;
      });


      contenedorAsignaturas.appendChild(cardAsignatura);
    });

    contenedorPrincipal.appendChild(contenedorAsignaturas);

    const contenedorEv = document.createElement("div");
    contenedorEv.classList.add("contenedor-evaluaciones");

    const listaEv = document.createElement("div");
    listaEv.classList.add("lista-evaluaciones");
    listaEv.id = "listaEvaluaciones";
    const btnCrearEv = document.createElement("button");
    btnCrearEv.textContent = "+ Crear evaluación";
    btnCrearEv.disabled;
    btnCrearEv.setAttribute("data-abrir", "form-evaluacion");
    const contenedorLista = document.createElement("div");
    contenedorLista.classList.add("contenedor-lista-ev");
    contenedorLista.appendChild(btnCrearEv);
    contenedorLista.appendChild(listaEv);
    const detalleEv = document.createElement("div");
    detalleEv.id = "detalleEvaluacion";
    const headerDetalle = document.createElement("div");
    headerDetalle.id = "header-detalle"
    const contenedorDetalle = document.createElement("div");
    contenedorDetalle.classList.add("contenedor-detalle")
    contenedorDetalle.appendChild(headerDetalle);
    contenedorDetalle.appendChild(detalleEv);

    
    
    contenedorEv.appendChild(contenedorLista);
    contenedorEv.appendChild(contenedorDetalle);

    contenedorPrincipal.appendChild(contenedorEv);


  } catch (error) {
    console.error("Error cargando estudiantes:", error);
  }
}

async function cargarEvaluaciones(cursoProfesorId) {
  const res = await fetch(
    `/luminary/api/admin/evaluaciones/evaluaciones_lista.php?curso_profesor_id=${cursoProfesorId}`,
  );

  const data = await res.json();
  const contenedor = document.getElementById("listaEvaluaciones");
  const detallleEv = document.getElementById("detalleEvaluacion");
  detallleEv.innerHTML = "";
  const headerEv = document.getElementById("header-detalle");
  headerEv.classList.remove("header-detalle");
  headerEv.innerHTML = "";

  document.getElementById("cursoProfesorSelect").value = cursoProfesorId;

  if (!data.success || data.evaluaciones.length === 0) {
    contenedor.innerHTML = "<p>No hay evaluaciones registradas</p>";
    detallleEv.innerHTML = "";
    headerEv.innerHTML = "";
    headerEv.classList.remove("header-detalle");
    return;
  }

  contenedor.innerHTML = "";
  let contador = 0;

  data.evaluaciones.forEach((ev) => {
    contador++;
    contenedor.innerHTML += `
    <div class="card-evaluacion" onclick="seleccionarEvaluacion(this, ${ev.id})">
      <div class="infoCardEv">
        <span class="tipo">${ev.tipo}</span>
        <span class="numero">${contador}</span>
      </div>
      <h4>${ev.titulo}</h4>
      <p><strong>Fecha:</strong> ${ev.fecha_aplicacion}</p>
    </div>
  `;
  });
}

function seleccionarEvaluacion(card, evaluacionId) {
  // Quitar seleccionado de todas
  document
    .querySelectorAll(".card-evaluacion")
    .forEach((c) => c.classList.remove("seleccionado"));

  // Agregar a la clickeada
  card.classList.add("seleccionado");

  // Cargar detalle
  cargarDetalleEvaluacion(evaluacionId);
}

async function cargarDetalleEvaluacion(evaluacionId) {
  const res = await fetch(
    `/luminary/api/admin/evaluaciones/evaluacion_detalle.php?evaluacion_id=${evaluacionId}`,
  );

  const data = await res.json();
  const contenedor = document.getElementById("detalleEvaluacion");
  const headerEv = document.getElementById("header-detalle");

  contenedor.innerHTML = "";
  contenedor.classList.add("seleccionado");

  const colores = {
    matematicas: "#3891e9",
    lenguaje: "#f75353",
    historia: "#7ed321",
    ciencias: "#0da761",
    ingles: "#cdb51a",
    "estudios sociales": "#f5a623",
    "artes visuales": "#23babf",
    tic: "#8544cf",
    filosofia: "#ce57db",
  };

  const key = data.evaluacion.asignatura
    .toLowerCase()
    .normalize("NFD")
    .replace(/[\u0300-\u036f]/g, "")
    .replace(/\s+/g, " ")
    .trim();
  const color = colores[key] ?? "#e0e0e0";
  let colorCurso = "";

  if (data.evaluacion.nivel === "1°") {
    colorCurso = "#0da761";
  } else if (data.evaluacion.nivel === "2°") {
    colorCurso = "#3891e9";
  }

  headerEv.innerHTML = `
    <div class="infoEvaluacion" id="infoEv">
      <div>
        <span id="cursoEv" style="--color: ${colorCurso}">${data.evaluacion.curso}</span>
        <span id="asigEv" style="--color: ${color}">${data.evaluacion.asignatura}</span>
      </div>
      <span id="tipoEv">${data.evaluacion.tipo_evaluacion}</span>
    </div>
    <h3 id="tituloEv">${data.evaluacion.titulo}</h3>
    <p id="descEv">${data.evaluacion.descripcion}</p>
  `;
  headerEv.classList.add("header-detalle");

  let html = `
  <div class="header-tabla">
    <span>#</span>
    <span>Nombre</span>
    <span>Nota</span>
  </div>
  <table class="tabla-notas">
    <tbody>
`;

  data.estudiantes.forEach((est, index) => {
    html += `
    <tr>
      <td>${index + 1}</td>
      <td>${est.nombre_estudiante}</td>
      <td>
        <input 
          type="text"
          inputmode="numeric"
          maxlength="2"
          value="${est.nota ?? ""}"
          oninput="formatearNota(this)"
          onchange="validarYGuardar(this, ${evaluacionId}, ${est.estudiante_id})"
        >
      </td>
    </tr>
  `;
  });

  html += `
    </tbody>
  </table>
`;

  contenedor.innerHTML = html;
}

function formatearNota(input) {
  // Eliminar todo lo que no sea número
  let valor = input.value.replace(/\D/g, "");

  // Limitar a 2 dígitos
  if (valor.length > 2) {
    valor = valor.slice(0, 2);
  }

  if (valor.length === 2) {
    input.value = valor[0] + "." + valor[1];
  } else {
    input.value = valor;
  }
}

async function guardarNota(evaluacionId, estudianteId, nota) {
  await fetch("/luminary/api/admin/evaluaciones/guardar_nota.php", {
    method: "POST",
    body: new URLSearchParams({
      evaluacion_id: evaluacionId,
      estudiante_id: estudianteId,
      nota: nota,
    }),
  });
}

function validarYGuardar(input, evaluacionId, estudianteId) {
  let valor = parseFloat(input.value);

  if (isNaN(valor)) return;

  if (valor < 1) valor = 1;
  if (valor > 7) valor = 7;

  input.value = valor.toFixed(1);

  guardarNota(evaluacionId, estudianteId, valor);
}

async function guardarEvaluacion() {
  const formData = new FormData();

  formData.append("titulo", document.getElementById("titulo").value);
  formData.append("descripcion", document.getElementById("descripcion").value);
  formData.append(
    "curso_profesor_id",
    document.getElementById("cursoProfesorSelect").value,
  );
  formData.append("tipo_id", document.getElementById("tipoSelect").value);
  formData.append("fecha_aplicacion", document.getElementById("fecha").value);

  const res = await fetch(
    "/luminary/api/admin/evaluaciones/evaluacion_crear.php",
    {
      method: "POST",
      body: formData,
    },
  );

  const data = await res.json();

  if (data.success) {
    alert("Evaluación creada correctamente");

    // 👉 aquí puedes abrir automáticamente la vista de carga de notas
    cargarView("cursos");
  } else {
    alert(data.message);
  }
}

async function cargarTipos() {
  try {
    const res = await fetch(
      "/luminary/api/admin/evaluaciones/evaluaciones_tipo.php",
      {
        cache: "no-store",
      },
    );

    const data = await res.json();

    if (!data.success) return;

    const select = document.getElementById("tipoSelect");
    if (!select) return; // seguridad

    select.innerHTML = '<option value="">Seleccionar tipo</option>';

    data.tipos.forEach((tipo) => {
      const option = document.createElement("option");
      option.value = tipo.id;
      option.textContent = tipo.nombre;
      select.appendChild(option);
    });
  } catch (error) {
    console.error("Error cargando tipos:", error);
  }
}