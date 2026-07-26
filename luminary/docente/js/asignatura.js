async function initAsignatura(cursoProfesorId) {
  await cargarInfo(cursoProfesorId);
  await cargarTipos();
  await cargarCursosProfesorSelect();
  await cargarCursoProfesor();

  document.querySelectorAll(".asignatura-navegacion button").forEach((btn) => {
    btn.addEventListener("click", () => {
      document
        .querySelectorAll(".asignatura-navegacion button")
        .forEach((b) => {
          b.classList.remove("seleccionado");
          b.removeAttribute("disabled");
        });

      btn.classList.add("seleccionado");
      btn.setAttribute("disabled", "true");
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

  asigNotas(cursoProfesorId);

  document
    .getElementById("btn-notas")
    .addEventListener("click", () => asigNotas(cursoProfesorId));
  document
    .getElementById("btn-material")
    .addEventListener("click", () => seccionMaterial(cursoProfesorId));
  document
    .getElementById("btn-estudiantes")
    .addEventListener("click", () => seccionEstudiantes(cursoProfesorId));
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
    verificarSesion();

    const formCursoProfesor = document.getElementById("cursoProfesorSelect");
    formCursoProfesor.value = cursoProfesorId;

    const contenedorPrincipal = document.getElementById("asignatura-contenido");
    contenedorPrincipal.innerHTML = "";
    const contenedorEvaluaciones = document.createElement("div");
    contenedorEvaluaciones.classList.add("evaluaciones-container");

    const contenedorEv = document.createElement("div");
    contenedorEv.classList.add("lista-evaluaciones");
    contenedorEv.id = "listaEvaluaciones";
    const contenedorBtns = document.createElement("div");
    contenedorBtns.classList.add("contenedor-botones");
    const btnCrearEv = document.createElement("button");
    btnCrearEv.textContent = "+";
    btnCrearEv.setAttribute("data-abrir", "form-evaluacion");
    btnCrearEv.className = "btn-sencillo btn-afirmativo"

    const semestreSelect = document.createElement("select");
    semestreSelect.id = "semestreEvaluacion";
    semestreSelect.classList.add("select-1");
    semestreSelect.name = "semestre";
    semestreSelect.innerHTML = `
      <option value="1">1° Semestre</option>
      <option value="2">2° Semestre</option>
    `;

    const contenedorDetalle = document.createElement("div");
    contenedorDetalle.classList.add("contenedor-detalle");
    const headerDetalle = document.createElement("div");
    headerDetalle.id = "header-detalle";
    const detalleEvaluacion = document.createElement("div");
    detalleEvaluacion.id = "detalleEvaluacion";
    detalleEvaluacion.classList.add("detalle-evaluacion");

    contenedorBtns.append(semestreSelect);
    contenedorBtns.append(btnCrearEv);
    contenedorEvaluaciones.append(contenedorBtns);
    contenedorEvaluaciones.append(contenedorEv);

    contenedorDetalle.append(headerDetalle);
    contenedorDetalle.append(detalleEvaluacion);

    contenedorPrincipal.append(contenedorEvaluaciones);
    contenedorPrincipal.append(contenedorDetalle);

    await cargarEvaluaciones(cursoProfesorId, 1);

    cambiarSemestreEv(cursoProfesorId);
  } catch (error) {
    console.error("Error cargando notas:", error);
  }
}

async function seccionMaterial(cursoProfesorId) {
  try {
    verificarSesion();
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

async function seccionEstudiantes(cursoProfesorId) {
  try {
    verificarSesion();

    const contenedorPrincipal = document.getElementById("asignatura-contenido");
    contenedorPrincipal.innerHTML = "";

    // ✅ Select de semestre
    const filtroSemestre = document.createElement("div");
    filtroSemestre.classList.add("filtro-semestre");
    filtroSemestre.innerHTML = `
      <select id="selectSemestre" class="select-1">
        <option value="1">1° Semestre</option>
        <option value="2">2° Semestre</option>
      </select>
    `;

    const contenedorSeccion = document.createElement("div");
    contenedorSeccion.classList.add("contenedor-seccion")
    

    const contenedorTabla = document.createElement("div");
    contenedorTabla.classList.add("contenedor-tabla");
    contenedorSeccion.appendChild(filtroSemestre);
    contenedorSeccion.appendChild(contenedorTabla);
    contenedorPrincipal.appendChild(contenedorSeccion);
    

    // ✅ Fetch con semestre
    async function cargarDatos(semestre) {
      const res = await fetch(
        `/luminary/api/docente/asignaturas/asignatura_estudiantes.php?id_curso_profesor=${cursoProfesorId}&semestre=${semestre}`,
        { cache: "no-store" },
      );
      const data = await res.json();
      if (!data.success) return;
      renderizarTabla(data);
    }

    function generarHeaderEvaluaciones(evaluaciones) {
      let contador = 0;
      return evaluaciones
        .flatMap((ev) => {
          const repeticiones = ev.coeficiente2 == 1 ? 2 : 1;
          return Array.from({ length: repeticiones }, () => {
            contador++;
            return `<th>
              <span class="info-evaluacion" data-tippy-content="${ev.titulo} ${ev.coeficiente2 == 1 ? "Coef. 2" : ""}">
                Nota ${contador}
              </span>
              ${ev.coeficiente2 == 1 ? '<span class="badge-coef"></span>' : ""}
            </th>`;
          });
        })
        .join("");
    }

    function generarCeldasNotas(estudiante, evaluaciones) {
      return evaluaciones
        .flatMap((ev) => {
          const notasEv = estudiante.notas.filter((n) => n.evaluacion_id == ev.id);
          const repeticiones = ev.coeficiente2 == 1 ? 2 : 1;

          return Array.from({ length: repeticiones }, (_, i) => {
            const notaObj = notasEv[i];
            const valor = notaObj && notaObj.nota !== null ? notaObj.nota : "-";

            let color = "";
            if (valor === "P") color = "background-color: #e6ba1a;";
            else if (valor === "L" || valor === "ML") color = "color: #305bad;";
            else if (valor === "NL") color = "color: #f75353;";
            else if (parseFloat(valor) >= 4) color = "color: #305bad;";
            else if (parseFloat(valor) < 4 && valor !== "-") color = "color: #f75353;";

            return `<td><input type="text" readonly="true" value="${valor}" oninput="formatearNota(this)" onchange="validarYGuardarYRecargar(this, ${ev.id}, ${estudiante.estudiante_id}, ${cursoProfesorId})" class="nota-input" style="${color}"></input></td>`;
          });
        })
        .join("");
    }

    // ✅ Renderiza con los datos ya filtrados desde el backend
    function renderizarTabla(data) {
      contenedorTabla.innerHTML = "";

      const tabla = document.createElement("table");
      tabla.className = "tabla-estudiantes tabla-notas";
      tabla.id = "tablaEstudiantes";

      tabla.innerHTML = `
        <thead>
          <tr>
            <th>#</th>
            <th>Nombre Completo</th>
            ${generarHeaderEvaluaciones(data.evaluaciones)}
            <th>N°</th>
            <th>Suma</th>
            <th>XA</th>
            <th>X</th>
          </tr>
        </thead>
        <tbody></tbody>
      `;

      const tbody = tabla.querySelector("tbody");

      data.estudiantes.forEach((estudiante, index) => {
        const fila = document.createElement("tr");
        fila.innerHTML = `
          <td>${index + 1}</td>
          <td><span class="estudiante-tabla" data-estudiante-id="${estudiante.id_matricula}">${estudiante.nombre_estudiante} ${estudiante.apellidos_estudiante}</span></td>
          ${generarCeldasNotas(estudiante, data.evaluaciones)}
          <td>${estudiante.cantidad_notas}</td>
          <td>${estudiante.suma_notas}</td>
          <td>${estudiante.promedio_aproximado}</td>
          <td>${estudiante.promedio !== null ? estudiante.promedio : "-"}</td>
        `;
        tbody.appendChild(fila);
      });

      contenedorTabla.appendChild(tabla);
      
      tippy("[data-tippy-content]");
    }

    // Cargar semestre 1 por defecto
    await cargarDatos(1);

    // ✅ Recargar al cambiar semestre
    document.getElementById("selectSemestre").addEventListener("change", (e) => {
      cargarDatos(e.target.value);
    });

  } catch (error) {
    console.error("Error al cargar sección estudiantes:", error);
  }
}
async function validarYGuardarYRecargar(
  input,
  evaluacionId,
  estudianteId,
  cursoProfesorId,
) {
  await validarYGuardar(input, evaluacionId, estudianteId);
  seccionEstudiantes(cursoProfesorId);
}
