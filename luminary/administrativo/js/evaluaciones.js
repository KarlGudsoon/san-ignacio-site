const ICONOS_EVALUACION = {
  Prueba: "/assets/icon/quiz.svg",
  Trabajo: "/assets/icon/assignment.svg",
  Disertación: "/assets/icon/presentation.svg",
  Guía: "/assets/icon/guia.svg",
  Proyecto: "/assets/icon/work.svg",
};

async function cargarEvaluaciones(cursoProfesorId, semestre = 1) {
  const res = await fetch(
    `/luminary/api/admin/evaluaciones/evaluaciones_lista.php?curso_profesor_id=${cursoProfesorId}&semestre=${semestre}`,
    { cache: "no-store" },
  );

  const data = await res.json();
  const contenedor = document.getElementById("listaEvaluaciones");
  const detallleEv = document.getElementById("detalleEvaluacion");
  detallleEv.innerHTML = "";
  const headerEv = document.getElementById("header-detalle");
  headerEv.classList.remove("header-detalle");
  headerEv.innerHTML = "";

  const cursoSelect = document.getElementById("cursoProfesorSelect");
  if (cursoSelect) cursoSelect.value = cursoProfesorId;

  const semestreSelect = document.getElementById("semestreEvaluacion");
  if (semestreSelect) semestreSelect.dataset.cursoProfesorId = cursoProfesorId;

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
    if (ev.activo === 0) return; // Solo mostrar activas
    contador++;
    const card = document.createElement("div");
    card.classList.add("card-evaluacion");
    card.onclick = () => seleccionarEvaluacion(card, ev.id);
    let iconEv;

    const icono = ICONOS_EVALUACION[ev.tipo];

    card.innerHTML = `
      <div class="icon-ev">
        ${icono ? `<img src="${icono}" alt="" />` : ""}
      </div>
      <div class="textoEv">
        <div class="infoCardEv">
          <span class="tipo">${ev.tipo}</span>
          <span class="numero">Ev. ${contador}</span>
        </div>
        <h4 class="titulo-ev">${ev.titulo}</h4>
        <small>${ev.fecha_aplicacion}</small>
        <button class="btn-eliminar" data-id="${ev.id}">Eliminar</button>
      </div>
    `;

    // Evitar que el click del botón propague a la card
    card.querySelector(".btn-eliminar").addEventListener("click", (e) => {
      e.stopPropagation();
      eliminarEvaluacion(ev.id, cursoProfesorId, card);
    });

    habilitarArrastre(".lista-evaluaciones");

    contenedor.append(card);
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
  const contenedor = document.getElementById("detalleEvaluacion");
  const headerEv = document.getElementById("header-detalle");

  headerEv.classList.remove("header-detalle");
  headerEv.innerHTML = "";
  contenedor.innerHTML = `
    <div class="detalle-loading">
      <div class="spinner"></div>
      <p>Cargando evaluación...</p>
    </div>
  `;
  contenedor.classList.add("seleccionado");

  try {
    const res = await fetch(
      `/luminary/api/admin/evaluaciones/evaluacion_detalle.php?evaluacion_id=${evaluacionId}`,
    );

    const data = await res.json();

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
          <span id="coefEv">${data.evaluacion.coeficiente2 == 1 ? "Coef. 2" : "Coef. 1"}</span>
          
        </div>
        <span id="tipoEv">${data.evaluacion.tipo_evaluacion}</span>
      </div>
      <div>
        <input type="date" id="fechaEv" value="${data.evaluacion.fecha_aplicacion}" >
        <input id="tituloEv" value="${data.evaluacion.titulo}" >
        <input id="descEv" value="${data.evaluacion.descripcion}" placeholder="Descripción (opcional)">
      </div>
      <button id="editarEv" class="btn-sencillo btn-afirmativo" onclick="editarEvaluacion(${data.evaluacion.id})">Guardar cambios</button>
      <button class="btn-rellenar" onclick="rellenarPendientes(${evaluacionId})">Rellenar pendientes</button>
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
            style="${est.nota >= 4.0 || ["L"].includes(est.nota) ? "color: #305bad;" : ""}${(est.nota < 4 && est.nota !== null) || ["NL"].includes(est.nota) ? "color: #f75353;" : ""} ${["ML"].includes(est.nota) ? "color: #0da761;" : ""} ${est.nota === "P" ? "background-color: #f8d03f;" : ""}"
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
  } catch (error) {
    console.error("Error cargando detalle de evaluación:", error);
    contenedor.innerHTML = `
      <div class="detalle-loading error">
        <p>No se pudo cargar la evaluación. Intenta de nuevo.</p>
      </div>
    `;
  }
}

function formatearNota(input) {
  const allowedGrades = ["L", "M", "ML", "N", "NL", "P", "E"];
  let valor = input.value.toUpperCase().trim();

  if (allowedGrades.includes(valor)) {
    input.value = valor;
    return;
  }

  // Eliminar todo lo que no sea número
  valor = valor.replace(/[^0-9]/g, "");

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
  try {
    const response = await fetch(
      "/luminary/api/admin/evaluaciones/guardar_nota.php",
      {
        method: "POST",
        headers: {
          "Content-Type": "application/x-www-form-urlencoded",
        },
        body: new URLSearchParams({
          evaluacion_id: evaluacionId,
          estudiante_id: estudianteId,
          nota: nota,
        }),
      },
    );

    if (!response.ok) {
      throw new Error(`Error en el servidor: HTTP ${response.status}`);
    }

    const data = await response.json();
    return { success: true, data };
  } catch (error) {
    // 3. Capturar errores de red o excepciones lanzadas arriba
    console.error("Error al guardar la nota:", error.message);

    return { success: false, error: error.message };
  }
}

async function validarYGuardar(input, evaluacionId, estudianteId) {
  const allowedGrades = ["L", "ML", "NL", "P", "E"];
  let valor = input.value.toUpperCase().trim();

  input.classList.remove("disabled");
  input.style.outlineColor = "#00000054";
  input.disabled = true;

  let notaAGuardar = "";
  let esConceptual = false;

  if (valor === "") {
    notaAGuardar = "";
  } else if (allowedGrades.includes(valor)) {
    notaAGuardar = valor;
    esConceptual = true;
  } else {
    let numeric = parseFloat(valor);
    if (isNaN(numeric)) {
      restaurarInput(input, "3px solid #f75353");
      return;
    }
    numeric = Math.min(Math.max(numeric, 1.0), 7.0);
    notaAGuardar = numeric.toFixed(1);
  }

  if (notaAGuardar >= 4) {
    input.style.color = "#305bad";
    input.style.backgroundColor = "";
  } else {
    input.style.color = "#f75353";
    input.style.backgroundColor = "";
  }

  if (notaAGuardar === "P") {
    input.style.backgroundColor = "#e6ba1a";
    input.style.color = "";
  } else if (notaAGuardar === "L") {
    input.style.backgroundColor = "";
    input.style.color = "#305bad";
  } else if (notaAGuardar === "ML") {
    input.style.backgroundColor = "";
    input.style.color = "#0da761";
  } else if (notaAGuardar === "NL") {
    input.style.backgroundColor = "";
    input.style.color = "#f75353";
  } else if (notaAGuardar === "E") {
    input.style.backgroundColor = "";
    input.style.color = "";
  } else {
    input.style.backgroundColor = "";
  }

  input.value = notaAGuardar;

  const resultado = await guardarNota(evaluacionId, estudianteId, notaAGuardar);

  if (resultado && resultado.success) {
    restaurarInput(input, "#03ad77");
    mostrarMensaje("Nota guardada correctamente", "green", 750);
    return resultado;
  } else {
    restaurarInput(input, "#f75353");
    mostrarMensaje(resultado.error, "red");
  }
}

function restaurarInput(input, outlineColor) {
  input.style.outlineColor = outlineColor;

  setTimeout(() => {
    input.style.outlineColor = "transparent";
    input.disabled = false;
  }, 1000);
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
  formData.append(
    "semestre",
    document.getElementById("semestreSelect")?.value || "1",
  );
  formData.append(
    "coeficiente2",
    document.getElementById("coeficiente2").checked ? 1 : 0,
  );

  const res = await fetch(
    "/luminary/api/admin/evaluaciones/evaluacion_crear.php",
    {
      method: "POST",
      body: formData,
    },
  );

  const data = await res.json();

  if (data.success) {
    mostrarMensaje("Evaluación creada correctamente", "green");

    const form = document.getElementById("form-evaluacion");

    form.classList.remove("activo");

    const cursoProfesorId = document.getElementById(
      "cursoProfesorSelect",
    ).value;
    const semestre = Number(
      document.getElementById("semestreSelect")?.value || 1,
    );
    const semestreSelect = document.getElementById("semestreEvaluacion");
    if (semestreSelect) semestreSelect.value = String(semestre);

    cargarEvaluaciones(cursoProfesorId, semestre);
  } else {
    alert(data.message);
  }
}

async function eliminarEvaluacion(evaluacionId, cursoProfesorId, cardElement) {
  const confirmar = confirm(
    "¿Seguro que deseas eliminar esta evaluación? Se eliminarán todas las notas asociadas.",
  );
  if (!confirmar) return;

  try {
    const formData = new FormData();
    formData.append("evaluacion_id", evaluacionId);

    const res = await fetch(
      `/luminary/api/admin/evaluaciones/evaluacion_eliminar.php`,
      {
        method: "POST",
        body: formData,
      },
    );

    const data = await res.json();

    if (!data.success) {
      alert("Error: " + data.message);
      return;
    }

    mostrarMensaje("Evaluación eliminada correctamente", "green");

    // Remover la card del DOM sin recargar todo
    cardElement.remove();

    // Limpiar el panel de detalle si estaba mostrando esa evaluación
    document.getElementById("header-detalle").innerHTML = "";
    document.getElementById("detalleEvaluacion").innerHTML = "";
  } catch (error) {
    console.error("Error eliminando evaluación:", error);
  }
}

async function cargarTipos() {
  try {
    const res = await fetch(
      "/luminary/api/admin/evaluaciones/evaluaciones_tipo.php",
      {
        cache: "no-store",
        method: "POST",
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

async function rellenarPendientes(evaluacionId) {
  const confirmar = confirm(
    "¿Seguro que deseas marcar como pendientes a todos los estudiantes sin nota?",
  );
  if (!confirmar) return;

  try {
    const res = await fetch(
      "/luminary/api/admin/evaluaciones/rellenar_pendientes.php",
      {
        method: "POST",
        body: new URLSearchParams({ evaluacion_id: evaluacionId }),
      },
    );

    const data = await res.json();

    if (!data.success) {
      alert("Error: " + data.message);
      return;
    }

    mostrarMensaje(
      `${data.actualizados} estudiantes marcados como pendientes`,
      "green",
    );
    cargarDetalleEvaluacion(evaluacionId);
  } catch (error) {
    console.error("Error rellenando pendientes:", error);
  }
}

function cambiarSemestreEv() {
  const select = document.getElementById("semestreEvaluacion");
  if (!select || select.dataset.listener === "1") return;

  select.dataset.listener = "1";

  select.addEventListener("change", () => {
    const cursoProfesorId = select.dataset.cursoProfesorId;
    if (!cursoProfesorId) return;

    cargarEvaluaciones(cursoProfesorId, Number(select.value));
  });
}

async function editarEvaluacion(evaluacionId) {
  const titulo = document.getElementById("tituloEv").value;
  const descripcion = document.getElementById("descEv").value;
  const fecha_aplicacion = document.getElementById("fechaEv").value;

  const semestreGuardado = document.getElementById("semestreEvaluacion").value;
  const cursoProfesorIdGuardado = document
    .getElementById("semestreEvaluacion")
    .getAttribute("data-curso-profesor-id");

  try {
    const formData = new FormData();
    formData.append("evaluacion_id", evaluacionId);
    formData.append("titulo", titulo);
    formData.append("descripcion", descripcion);
    formData.append("fecha_aplicacion", fecha_aplicacion);

    const res = await fetch(
      `/luminary/api/admin/evaluaciones/evaluacion_editar.php`,
      {
        method: "POST",
        body: formData,
      },
    );

    const data = await res.json();

    if (!data.success) {
      alert("Error: " + data.message);
      return;
    } else {
      mostrarMensaje("Evaluación actualizada correctamente", "green");
      cargarEvaluaciones(cursoProfesorIdGuardado, semestreGuardado);
    }
  } catch (error) {
    console.error("Error editando evaluación:", error);
  }
}
