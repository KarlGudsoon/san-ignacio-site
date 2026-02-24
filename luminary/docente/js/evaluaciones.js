async function initEvaluaciones() {
  await cargarTipos();
  await cargarCursosProfesorSelect();
  await cargarCursoProfesor();

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

  const select = document.getElementById("filtroCurso");

  select.addEventListener("change", async () => {
    const cursoProfesorId = select.value;

    if (!cursoProfesorId) {
      document.getElementById("listaEvaluaciones").innerHTML =
        "<p>Selecciona un curso para ver las evaluaciones</p>";
      return;
    }

    await cargarEvaluaciones(cursoProfesorId);
  });
}

async function cargarTipos() {
  try {
    const res = await fetch(
      "/luminary/api/docente/evaluaciones/tipo_evaluacion.php",
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

async function cargarCursosProfesorSelect() {
  try {
    const res = await fetch("/luminary/api/docente/cursos/cursos.php", {
      cache: "no-store",
    });

    const data = await res.json();

    if (!data.success) return;

    const select = document.getElementById("cursoProfesorSelect");
    if (!select) return; // seguridad

    select.innerHTML = '<option value="">Seleccionar tipo</option>';

    data.cursos.forEach((curso) => {
      const option = document.createElement("option");
      option.value = curso.id;
      option.textContent = `${curso.curso_nivel} Nivel ${curso.curso_letra} - ${curso.asignatura}`;
      select.appendChild(option);
    });
  } catch (error) {
    console.error("Error cargando cursos:", error);
  }
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
    "/luminary/api/docente/evaluaciones/crear_evaluacion.php",
    {
      method: "POST",
      body: formData,
    },
  );

  const data = await res.json();

  if (data.success) {
    alert("Evaluación creada correctamente");

    // 👉 aquí puedes abrir automáticamente la vista de carga de notas
    cargarView("evaluaciones");
  } else {
    alert(data.message);
  }
}

async function cargarCursoProfesor() {
  try {
    const res = await fetch("/luminary/api/docente/cursos/cursos.php", {
      cache: "no-store",
    });

    const data = await res.json();

    if (!data.success) return;

    const select = document.getElementById("filtroCurso");
    if (!select) return; // seguridad

    select.innerHTML = '<option value="">Seleccionar asignatura</option>';

    data.cursos.forEach((curso) => {
      const option = document.createElement("option");
      option.value = curso.id;
      option.textContent = `${curso.curso_nivel} Nivel ${curso.curso_letra} - ${curso.asignatura}`;
      select.appendChild(option);
    });
  } catch (error) {
    console.error("Error cargando cursos:", error);
  }
}

async function cargarEvaluaciones(cursoProfesorId) {
  const res = await fetch(
    `/luminary/api/docente/evaluaciones/lista_evaluacion.php?curso_profesor_id=${cursoProfesorId}`,
  );

  const data = await res.json();
  const contenedor = document.getElementById("listaEvaluaciones");
  const detallleEv = document.getElementById("detalleEvaluacion");
  detallleEv.innerHTML = "";
  const headerEv = document.getElementById("header-detalle");
  headerEv.classList.remove("header-detalle");
  headerEv.innerHTML = "";

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
    `/luminary/api/docente/evaluaciones/detalle.php?evaluacion_id=${evaluacionId}`,
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
  await fetch("/luminary/api/docente/evaluaciones/guardar_nota.php", {
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
