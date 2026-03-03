async function initAsignatura(cursoProfesorId) {
  await cargarInfo(cursoProfesorId);
  await cargarTipos();
  await cargarCursosProfesorSelect();
  await cargarCursoProfesor();

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

  // document
  // .getElementById("btn-inicio")
  // .addEventListener("click", asignaturaInicio);

  document.getElementById("volver").addEventListener("click", () => {
    cargarView("cursos");
  });

  document
    .getElementById("btn-notas")
    .addEventListener("click", () => asigNotas(cursoProfesorId));
  document
    .getElementById("btn-material")
    .addEventListener("click", () => seccionMaterial(cursoProfesorId));
}

async function cargarInfo(cursoProfesorId) {
  try {
    const res = await fetch(
      `/luminary/api/docente/asignaturas/asignatura_detalle.php?id=${cursoProfesorId}`,
      {
        cache: "no-store",
      },
    );

    const data = await res.json();

    if (!data.success) return;

    console.log("Se cargaron los datos con exito");

    const asignatura = document.getElementById("asignatura-detalle");
    const colorGuardado = sessionStorage.getItem("asignaturaColor");
    asignatura.style.backgroundColor = colorGuardado;

    let asignaturaIcon = document.getElementById("asignatura-icon");
    const iconGuardado = sessionStorage.getItem("asignaturaIcon");
    asignaturaIcon.src = iconGuardado;

    document.getElementById("curso-asignatura").textContent =
      data.asignatura.curso;
    document.getElementById("nombre-asignatura").textContent =
      data.asignatura.nombre;
  } catch (error) {
    console.error("Error cargando cursos:", error);
  }
}

async function asigNotas(cursoProfesorId) {
  try {
    const res = await fetch(
      `/luminary/api/docente/evaluaciones/lista_evaluacion.php?curso_profesor_id=${cursoProfesorId}`,
      {
        cache: "no-store",
      },
    );

    const data = await res.json();

    if (!data.success) return;

    console.log("Se cargaron las evaluaciones");

    const contenedorPrincipal = document.getElementById("asignatura-contenido");
    contenedorPrincipal.innerHTML = "";
    const contenedorEv = document.createElement("div");
    contenedorEv.classList.add("lista-evaluaciones");
    const btnCrearEv = document.createElement("button");
    btnCrearEv.textContent = "+ Crear evaluación";
    btnCrearEv.setAttribute("data-abrir", "form-evaluacion");
    const contenedorDetalle = document.createElement("div");
    contenedorDetalle.classList.add("contenedor-detalle");
    const headerDetalle = document.createElement("div");
    headerDetalle.id = "header-detalle";
    const detalleEvaluacion = document.createElement("div");
    detalleEvaluacion.id = "detalleEvaluacion";
    detalleEvaluacion.classList.add("detalle-evaluacion");

    contenedorEv.append(btnCrearEv);

    contenedorDetalle.append(headerDetalle);
    contenedorDetalle.append(detalleEvaluacion);

    contenedorPrincipal.append(contenedorEv);
    contenedorPrincipal.append(contenedorDetalle);

    let contador = 0;

    data.evaluaciones.forEach((ev) => {
      contador++;
      contenedorEv.innerHTML += `
        <div class="card-evaluacion" onclick="seleccionarEvaluacion(this, ${ev.id})">
        <div class="infoCardEv">
            <span class="tipo">${ev.tipo}</span>
            <span class="numero">Evaluación ${contador}</span>
        </div>
        <h4>${ev.titulo}</h4>
        <p><strong>Fecha:</strong> ${ev.fecha_aplicacion}</p>
        </div>
    `;
    });
  } catch (error) {
    console.error("Error cargando notas:", error);
  }
}

async function seccionMaterial(cursoProfesorId) {
  try {
    const contenedorPrincipal = document.getElementById("asignatura-contenido");
    contenedorPrincipal.innerHTML = "";
    const formMaterial = document.createElement("div");
    formMaterial.classList.add("contenedor-form-material");

    formMaterial.innerHTML = `
      <h2>Subir Material</h2>
      <form id="form-subir-material" class="form-material">
        <input type="hidden" id="material_curso_profesor_id" name="material_curso_profesor_id" value="${cursoProfesorId}">
        <div class="campo">
          <label>Título</label>
          <input type="text" id="material_titulo" name="material_titulo" required>
        </div>
        <div class="campo">
          <label>Descripción</label>
          <textarea id="material_descripcion" name="material_descripcion"></textarea>
        </div>
        <div class="campo">
          <label>Categoría</label>
          <select id="material_categoria_id" name="material_categoria_id" required>
            <option value="">Cargando categorías...</option>
          </select>
        </div>
        <div class="campo">
          <label>Archivo</label>
          <input type="file" id="material_archivo" name="material_archivo" required>
        </div>
        <button type="submit">Subir Material</button>
      </form>
    `;

    const listaMateriales = document.createElement("div");
    listaMateriales.id = "material-curso";
    listaMateriales.classList.add("material-curso");
    listaMateriales.innerHTML = `
      <div class="lista-materiales">
        Cargando material...
      </div>
    `;

    cargarCategoriasMaterial();
    cargarMaterial(cursoProfesorId);

    contenedorPrincipal.append(formMaterial);
    contenedorPrincipal.append(listaMateriales);

    document
      .getElementById("form-subir-material")
      .addEventListener("submit", subirMaterial);
  } catch (error) {
    console.error("Error cargando material:", error);
  }
}
