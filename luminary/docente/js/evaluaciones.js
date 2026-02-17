async function initEvaluaciones() {
  await cargarTipos();
  await cargarCursosProfesorSelect();
  await cargarCursoProfesor();

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

async function cargarEvaluaciones(cursoProfesorId) {
  const res = await fetch(
    `/luminary/api/docente/evaluaciones/lista_evaluacion.php?curso_profesor_id=${cursoProfesorId}`,
  );

  const data = await res.json();
  const contenedor = document.getElementById("listaEvaluaciones");

  if (!data.success || data.evaluaciones.length === 0) {
    contenedor.innerHTML = "<p>No hay evaluaciones registradas</p>";
    return;
  }

  contenedor.innerHTML = "";

  data.evaluaciones.forEach((ev) => {
    contenedor.innerHTML += `
    <div class="card-evaluacion" onclick="cargarDetalleEvaluacion(${ev.id})">
        <h4>${ev.titulo}</h4>
        <p><strong>Tipo:</strong> ${ev.tipo}</p>
        <p><strong>Fecha:</strong> ${ev.fecha_aplicacion}</p>
    </div>
  `;
  });
}

async function cargarDetalleEvaluacion(evaluacionId) {
  const res = await fetch(
    `/luminary/api/docente/evaluaciones/detalle.php?evaluacion_id=${evaluacionId}`,
  );

  const data = await res.json();
  const contenedor = document.getElementById("detalleEvaluacion");

  contenedor.innerHTML = "";

  data.estudiantes.forEach((est) => {
    contenedor.innerHTML += `
      <div class="fila-estudiante">
        <span>${est.nombre_estudiante}</span>
        <input 
          type="text"
          inputmode="numeric"
          maxlength="2"
          value="${est.nota ?? ""}"
          oninput="formatearNota(this)"
          onchange="validarYGuardar(this, ${evaluacionId}, ${est.estudiante_id})"
        >

      </div>
    `;
  });
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
