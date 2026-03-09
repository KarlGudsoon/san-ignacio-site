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

    if (data.asignatura.curso_nivel === "1°") {
      document.getElementById("curso-asignatura").classList.add("curso-1°");
    } else if (data.asignatura.curso_nivel === "2°") {
      document.getElementById("curso-asignatura").classList.add("curso-2°");
    }

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

    const formCursoProfesor = document.getElementById("cursoProfesorSelect");
    formCursoProfesor.value = cursoProfesorId;

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
      const card = document.createElement("div");
      card.classList.add("card-evaluacion");
      card.onclick = () => seleccionarEvaluacion(card, ev.id);
      card.innerHTML = `
        <div class="infoCardEv">
          <span class="tipo">${ev.tipo}</span>
          <span class="numero">Evaluación ${contador}</span>
        </div>
        <h4>${ev.titulo}</h4>
        <p><strong>Fecha:</strong> ${ev.fecha_aplicacion}</p>
        <button class="btn-eliminar" data-id="${ev.id}">Eliminar</button>
      `;

      // Evitar que el click del botón propague a la card
      card.querySelector(".btn-eliminar").addEventListener("click", (e) => {
        e.stopPropagation();
        eliminarEvaluacion(ev.id, cursoProfesorId, card);
      });

      contenedorEv.append(card);
    });
  } catch (error) {
    console.error("Error cargando notas:", error);
  }
}

async function seccionMaterial(cursoProfesorId) {
  try {
    const contenedorPrincipal = document.getElementById("asignatura-contenido");
    contenedorPrincipal.innerHTML = "";
    const contenedorFormMaterial = document.createElement("div");
    contenedorFormMaterial.classList.add("contenedor-form-material");

    const formMaterial = document.createElement("div");
    formMaterial.classList.add("form-material");

    formMaterial.innerHTML = `
      <form id="form-subir-material">
        <h2>Subir Material</h2>
        <input type="hidden" id="material_curso_profesor_id" name="material_curso_profesor_id" value="${cursoProfesorId}">
        <div class="campo">
          <label>Unidad</label>
          <select class="select-material" id="material_unidad_id" name="material_unidad_id" required>
            <option value="">Cargando unidades...</option>
          </select>
        </div>
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
          <select class="select-material" id="material_categoria_id" name="material_categoria_id" required>
            <option value="">Cargando categorías...</option>
          </select>
        </div>
        <div class="asignatura-navegacion">
          <div id="btn-material-archivo" class="btn seleccionado">Archivo</div>
          <div id="btn-material-enlace" class="btn">Enlace</div>
        </div>
        <div class="campo">
          <label>Material</label>
          <div class="campo-archivo seleccionado" id="campo-archivo">
            <div class="contenido-campo-archivo">
              <img src="/assets/icon/icons8--upload-2.svg">
              <span>Sube tu archivo aquí</span>
              <span id="file-name">Ningún archivo seleccionado</span>
            </div>
            <input type="file" id="material_archivo" name="material_archivo" required>
          </div>
          <div class="campo-archivo" id="campo-enlace">
            <input type="text" id="material_enlace" name="material_enlace" placeholder="https://ejemplo.com">
          </div>
        </div>
        
        <button type="submit">Subir Material</button>
      </form>
    `;
    contenedorFormMaterial.append(formMaterial);

    const contenedorUnidades = document.createElement("div");
    contenedorUnidades.classList.add("contenedor-unidades");

    const formUnidad = document.createElement("div");
    formUnidad.classList.add("contenedor-form-unidad");

    formUnidad.innerHTML = `
      <form id="form-crear-unidad" class="form-unidad">
        <h2>Crear Unidad</h2>
        <input type="hidden" name="curso_profesor_id" value="${cursoProfesorId}">
        
        <div class="campo">
          <input type="text" placeholder="Unidad I..." name="unidad_nombre" required>
        </div>
        <button type="submit">Crear Unidad</button>
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

    contenedorUnidades.append(listaMateriales);
    contenedorUnidades.append(formUnidad);

    cargarUnidades(cursoProfesorId);

    cargarCategoriasMaterial();
    cargarMaterial(cursoProfesorId);

    contenedorPrincipal.append(contenedorFormMaterial);
    contenedorPrincipal.append(contenedorUnidades);

    const input = document.getElementById("material_archivo");
    const fileName = document.getElementById("file-name");

    input.addEventListener("change", function () {
      if (this.files.length > 0) {
        fileName.textContent = this.files[0].name;
      } else {
        fileName.textContent = "Ningún archivo seleccionado";
      }
    });

    document.querySelectorAll(".asignatura-navegacion .btn").forEach((btn) => {
      btn.addEventListener("click", () => {
        let botones = document.querySelectorAll(".asignatura-navegacion .btn");

        botones.forEach((b) => {
          b.classList.remove("seleccionado");
        });

        btn.classList.add("seleccionado");
      });
    });

    document
      .getElementById("btn-material-archivo")
      .addEventListener("click", () => {
        document.getElementById("campo-archivo").classList.add("seleccionado");
        document
          .getElementById("campo-enlace")
          .classList.remove("seleccionado");
        document.getElementById("material_archivo").required = true;
        document.getElementById("material_enlace").required = false;
      });

    document
      .getElementById("btn-material-enlace")
      .addEventListener("click", () => {
        document
          .getElementById("campo-archivo")
          .classList.remove("seleccionado");
        document.getElementById("campo-enlace").classList.add("seleccionado");
        document.getElementById("material_archivo").required = false;
        document.getElementById("material_enlace").required = true;
      });

    document
      .getElementById("form-crear-unidad")
      .addEventListener("submit", crearUnidad);

    document
      .getElementById("form-subir-material")
      .addEventListener("submit", subirMaterial);
  } catch (error) {
    console.error("Error cargando material:", error);
  }
}
