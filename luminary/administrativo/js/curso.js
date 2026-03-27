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
    .getElementById("btn-evaluaciones")
    .addEventListener("click", () => cargarSeccionEv(cursoId));
  document
    .getElementById("btn-distancia")
    .addEventListener("click", () => cargarSeccionMaterial(cursoId));

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
      { cache: "no-store" },
    );

    const data = await res.json();

    if (!data.success) return;

    const contenedorPrincipal = document.getElementById("curso-contenido");
    contenedorPrincipal.innerHTML = "";

    // Crear tabla
    const tabla = document.createElement("table");
    tabla.classList.add("tabla-estudiantes");
    tabla.id = "tablaEstudiantes";

    // Header
    tabla.innerHTML = `
      <thead>
        <tr>
          <th>#</th>
          <th>Nombre Completo</th>
          <th>RUT</th>
          <th>Edad</th>
          <th>Teléfono</th>
        </tr>
      </thead>
      <tbody></tbody>
    `;

    const tbody = tabla.querySelector("tbody");

    data.estudiantes.forEach((estudiante, index) => {
      const fila = document.createElement("tr");

      fila.innerHTML = `
        <td>${index + 1}</td>
        <td><span class="estudiante-tabla" data-estudiante-id="${estudiante.id_estudiante}">${estudiante.nombre_estudiante} ${estudiante.apellidos_estudiante}</span></td>
        <td>${estudiante.rut_estudiante}</td>
        <td>${estudiante.edad ?? "-"}</td>
        <td>${estudiante.telefono_estudiante ?? "-"}</td>
      `;

      tbody.appendChild(fila);
    });

    const contenedorTabla = document.createElement("div");
    contenedorTabla.classList.add("contenedor-tabla");

    contenedorTabla.appendChild(tabla);

    contenedorPrincipal.appendChild(contenedorTabla);

    document
      .getElementById("tablaEstudiantes")
      .addEventListener("click", (e) => {
        const fila = e.target.closest(".estudiante-tabla");

        if (!fila) return;

        const estudianteId = fila.dataset.estudianteId;

        cargarEstudiante(estudianteId);
      });
  } catch (error) {
    console.error("Error cargando estudiantes:", error);
  }
}

function cargarEstudiante(estudianteId) {
  cargarView("estudiante", estudianteId);
}

async function cargarSeccionEv(cursoId) {
  try {
    const res = await fetch(
      `/luminary/api/admin/cursos/curso_asignaturas.php?curso_id=${cursoId}`,
      { cache: "no-store" },
    );

    const data = await res.json();

    if (!data.success) return;

    await cargarTipos();

    const colores = {
      matematicas: "#3891e9",
      lenguaje: "#f75353",
      historia: "#7ed321",
      ciencias: "#0da761",
      ingles: "#cdb51a",
      "ingles comunicativo": "#f1660f",
      "estudios sociales": "#f5a623",
      "artes visuales": "#23babf",
      tic: "#8544cf",
      "consumo y calidad de vida": "#8544cf",
      filosofia: "#cf58dcff",
      "instrumental 1": "#fb2b66",
      "instrumental 2": "#f16b3a",
      diferenciado: "#09dc84",
      jefatura: "#0c4d8e",
      "pensamiento computacional": "#8544cf",
      "educacion financiera": "#8544cf",
      "convivencia social": "#54328a",
      "insercion laboral": "#54328a",
      "responsabilidad personal y social": "#54328a",
      "emprendimiento y empleabilidad": "#54328a",
    };

    const contenedorPrincipal = document.getElementById("curso-contenido");
    contenedorPrincipal.innerHTML = "";

    const contenedorAsignaturas = document.createElement("div");
    contenedorAsignaturas.classList.add("contenedor-asignaturas");

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
    headerDetalle.id = "header-detalle";
    const contenedorDetalle = document.createElement("div");
    contenedorDetalle.classList.add("contenedor-detalle");
    contenedorDetalle.appendChild(headerDetalle);
    contenedorDetalle.appendChild(detalleEv);

    contenedorEv.appendChild(contenedorLista);
    contenedorEv.appendChild(contenedorDetalle);

    contenedorPrincipal.appendChild(contenedorEv);

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
      cardAsignatura.setAttribute(
        "data-curso-profesor-id",
        asignatura.curso_profesor_id,
      );
      cardAsignatura.innerHTML = `
        <h4>${asignatura.asignatura}</h4>
      `;
      if (index === 0) {
        cardAsignatura.classList.add("seleccionado");
        cardAsignatura.classList.remove("deseleccionado");

        contenedorEv.style.backgroundColor = `${color}75`;

        // Cargar evaluaciones automáticamente
        cargarEvaluaciones(asignatura.curso_profesor_id);
      }
      cardAsignatura.addEventListener("click", () => {
        cargarEvaluaciones(
          cardAsignatura.getAttribute("data-curso-profesor-id"),
        );
        document.querySelectorAll(".asignatura-item").forEach((card) => {
          card.classList.remove("seleccionado");
          card.classList.add("deseleccionado");
        });
        cardAsignatura.classList.add("seleccionado");
        cardAsignatura.classList.remove("deseleccionado");

        contenedorEv.style.backgroundColor = `${color}75`;
      });

      contenedorAsignaturas.appendChild(cardAsignatura);
    });
  } catch (error) {
    console.error("Error cargando estudiantes:", error);
  }
}

async function cargarSeccionMaterial(cursoId) {
  try {
    const res = await fetch(
      `/luminary/api/admin/cursos/curso_asignaturas.php?curso_id=${cursoId}`,
      { cache: "no-store" },
    );

    const data = await res.json();

    if (!data.success) return;

    await cargarTipos();

    const colores = {
      matematicas: "#3891e9",
      lenguaje: "#f75353",
      historia: "#7ed321",
      ciencias: "#0da761",
      ingles: "#cdb51a",
      "ingles comunicativo": "#f1660f",
      "estudios sociales": "#f5a623",
      "artes visuales": "#23babf",
      tic: "#8544cf",
      "consumo y calidad de vida": "#8544cf",
      filosofia: "#cf58dcff",
      "instrumental 1": "#fb2b66",
      "instrumental 2": "#f16b3a",
      diferenciado: "#09dc84",
      jefatura: "#0c4d8e",
      "pensamiento computacional": "#8544cf",
      "educacion financiera": "#8544cf",
      "convivencia social": "#54328a",
      "insercion laboral": "#54328a",
      "responsabilidad personal y social": "#54328a",
      "emprendimiento y empleabilidad": "#54328a",
    };

    const contenedorPrincipal = document.getElementById("curso-contenido");
    contenedorPrincipal.innerHTML = "";

    const contenedorAsignaturas = document.createElement("div");
    contenedorAsignaturas.classList.add("contenedor-asignaturas");
    const contenedorMaterial = document.createElement("div");
    contenedorMaterial.classList.add("contenedor-material");
    contenedorMaterial.id = "contenedorMaterial"

    contenedorPrincipal.appendChild(contenedorAsignaturas);
    contenedorPrincipal.appendChild(contenedorMaterial);

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
      cardAsignatura.setAttribute(
        "data-curso-profesor-id",
        asignatura.curso_profesor_id,
      );
      cardAsignatura.innerHTML = `
        <h4>${asignatura.asignatura}</h4>
      `;
      if (index === 0) {
        cardAsignatura.classList.add("seleccionado");
        cardAsignatura.classList.remove("deseleccionado");

        // Cargar evaluaciones automáticamente
        cargarMaterialAsignatura(asignatura.curso_profesor_id);
      }
      cardAsignatura.addEventListener("click", () => {
        cargarEvaluaciones(
          cardAsignatura.getAttribute("data-curso-profesor-id"),
        );
        document.querySelectorAll(".asignatura-item").forEach((card) => {
          card.classList.remove("seleccionado");
          card.classList.add("deseleccionado");
        });
        cardAsignatura.classList.add("seleccionado");
        cardAsignatura.classList.remove("deseleccionado");

      });

      contenedorAsignaturas.appendChild(cardAsignatura);
    });
    
    } catch (error) {
    console.error("Error cargando estudiantes:", error);
  }
  
}

async function cargarMaterialAsignatura(cursoProfesorId) {
  try {
    const contenedorPrincipal = document.getElementById("contenedorMaterial");
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
