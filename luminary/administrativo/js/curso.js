async function initCurso(cursoId) {
  await cargarInfo(cursoId);

  await cargarEstudiantes(cursoId);

  document.querySelectorAll(".asignatura-navegacion button").forEach((btn) => {
    btn.addEventListener("click", () => {
      let botones = document.querySelectorAll(".asignatura-navegacion button");

      botones.forEach((b) => {
        b.classList.remove("seleccionado");
      });

      btn.classList.add("seleccionado");
    });
  });

  const btnInformeSemestral = document.querySelectorAll(
    ".informe-notas-semestral",
  );

  btnInformeSemestral.forEach((btn) => {
    const semestre = btn.getAttribute("data-semestre");
    btn.addEventListener("click", () => {
      window.open(
        `/luminary/api/admin/cursos/curso_generar_informe_notas_estudiantes.php?id_curso=${cursoId}&semestre=${semestre}`,
        "_blank",
      );
    });
  });

  // document
  //   .getElementById("btnInformeNotas")
  //   .addEventListener("click", () =>
  //     window.open(
  //       `/luminary/api/admin/cursos/curso_generar_informe_notas_estudiantes.php?id_curso=${cursoId}`,
  //     ),
  //   );

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
    .getElementById("btn-notas")
    .addEventListener("click", () => cargarSeccionNotas(cursoId));
  document
    .getElementById("btn-distancia")
    .addEventListener("click", () => cargarSeccionMaterial(cursoId));
  // document
  //   .getElementById("btn-promedios")
  //   .addEventListener("click", () => cargarSeccionPromedios(cursoId));

  document.getElementById("volver").addEventListener("click", () => {
    cargarView("cursos");
  });

  habilitarArrastre(".asignatura-navegacion");
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
    document.getElementById("profesor_jefe").textContent =
      data.curso.profesor_jefe;
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
          <th>Tipo</th>
          <th>Pend.</th>
          <th>X</th>
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
        <td><span class="tipo-estudiante"><img class="icon-estudiante" src="${estudiante.tipo_estudiante === "distancia" ? "/assets/icon/distancia.svg" : estudiante.tipo_estudiante === "tutoria" ? "/assets/icon/tutoria.svg" : "/assets/icon/presencial.svg"}"></span></td>
        <td><span class="tipo-estudiante link-pendiente" style="background: ${estudiante.notas_pendientes > 0 ? "#f8d03f" : "rgba(0, 0, 0, 0.1)"};" onclick="cargarView('pendientes', ${estudiante.id_estudiante})"><img class="icon-pendiente" src=${estudiante.notas_pendientes > 0 ? "/assets/icon/ion--warning.svg" : ""}>${estudiante.notas_pendientes > 0 ? estudiante.notas_pendientes : "-"}</span></td>
        <td></td>
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

    const contenedorBtns = document.createElement("div");
    contenedorBtns.classList.add("contenedor-botones");

    const btnCrearEv = document.createElement("button");
    btnCrearEv.innerHTML = `<img src="/assets/icon/fa7-solid--add.svg">`;
    btnCrearEv.setAttribute("data-abrir", "form-evaluacion");
    btnCrearEv.className = "btn-sencillo btn-afirmativo btn-crear-evaluacion";

    const semestreActual = new Date().getMonth() < 6 ? 1 : 2;

    const semestreSelect = document.createElement("select");
    semestreSelect.id = "semestreEvaluacion";
    semestreSelect.classList.add("select-1");
    semestreSelect.innerHTML = `
      <option value="1">1° Semestre</option>
      <option value="2">2° Semestre</option>
    `;
    semestreSelect.value = String(semestreActual);

    const semestreForm = document.getElementById("semestreSelect");
    if (semestreForm) semestreForm.value = String(semestreActual);

    semestreSelect.addEventListener("change", () => {
      const formSelect = document.getElementById("semestreSelect");
      if (formSelect) formSelect.value = semestreSelect.value;
    });

    const contenedorLista = document.createElement("div");
    contenedorLista.classList.add("contenedor-lista-ev");

    const detalleEv = document.createElement("div");
    detalleEv.id = "detalleEvaluacion";
    const headerDetalle = document.createElement("div");
    headerDetalle.id = "header-detalle";
    const contenedorDetalle = document.createElement("div");
    contenedorDetalle.classList.add("contenedor-detalle");

    contenedorBtns.append(semestreSelect);
    contenedorBtns.append(btnCrearEv);
    contenedorLista.appendChild(contenedorBtns);
    contenedorLista.appendChild(listaEv);

    contenedorDetalle.appendChild(headerDetalle);
    contenedorDetalle.appendChild(detalleEv);

    contenedorEv.appendChild(contenedorLista);
    contenedorEv.appendChild(contenedorDetalle);

    contenedorPrincipal.appendChild(contenedorEv);

    cambiarSemestreEv();

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
        <span>${asignatura.asignatura}</span>
      `;
      if (index === 0) {
        cardAsignatura.classList.add("seleccionado");
        cardAsignatura.classList.remove("deseleccionado");

        contenedorEv.style.backgroundColor = `${color}75`;

        // Cargar evaluaciones automáticamente
        semestreSelect.dataset.cursoProfesorId = asignatura.curso_profesor_id;
        cargarEvaluaciones(asignatura.curso_profesor_id, semestreActual);
      }
      cardAsignatura.addEventListener("click", () => {
        semestreSelect.dataset.cursoProfesorId = cardAsignatura.getAttribute(
          "data-curso-profesor-id",
        );
        cargarEvaluaciones(
          cardAsignatura.getAttribute("data-curso-profesor-id"),
          Number(semestreSelect.value),
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

    habilitarArrastre(".contenedor-asignaturas");
  } catch (error) {
    console.error("Error cargando estudiantes:", error);
  }
}

async function cargarSeccionNotas(cursoId) {
  try {
    verificarSesion();

    // 1. Estado local centralizado
    let semestreActual = new Date().getMonth() < 6 ? 1 : 2;
    let cursoProfesorIdActual = null;
    let cargando = false; // Prevención de peticiones concurrentes
    let requestId = 0; // Identifica la última petición lanzada

    // Caché de notas por (cursoProfesorId, semestre)
    const notasCache = new Map();
    const cacheKey = (cpId, sem) => `${cpId}-${sem}`;
    window.notasCacheNotas = notasCache;

    // Helper reutilizable para normalizar cadenas
    const normalizarTexto = (texto) =>
      texto
        .toLowerCase()
        .normalize("NFD")
        .replace(/[\u0300-\u036f]/g, "")
        .replace(/\s+/g, " ")
        .trim();

    // 2. Cargar lista de asignaturas
    const res = await fetch(
      `/luminary/api/admin/cursos/curso_asignaturas.php?curso_id=${cursoId}`,
      { cache: "no-store" },
    );
    const dataAsignaturas = await res.json();

    if (!dataAsignaturas.success || !dataAsignaturas.asignaturas.length) return;

    await cargarTipos();

    // 3. Montar la estructura base en el DOM
    const contenedorPrincipal = document.getElementById("curso-contenido");
    contenedorPrincipal.innerHTML = "";

    const contenedorAsignaturas = document.createElement("div");
    contenedorAsignaturas.classList.add("contenedor-asignaturas");

    // Construcción de tarjetas de asignaturas
    dataAsignaturas.asignaturas.forEach((asignatura, index) => {
      const card = document.createElement("div");
      const key = normalizarTexto(asignatura.asignatura);
      const color = colores[key] ?? "#e0e0e0";

      card.className = "asignatura-item deseleccionado";
      card.style.backgroundColor = color;
      card.style.setProperty("--backgroundColor", color);
      card.style.setProperty("--backgroundColor2", `${color}40`);
      card.style.setProperty("--backgroundColor3", `${color}80`);
      card.dataset.id = asignatura.curso_profesor_id;
      card.innerHTML = `<span>${asignatura.asignatura}</span>`;

      if (index === 0) {
        card.classList.replace("deseleccionado", "seleccionado");
        cursoProfesorIdActual = asignatura.curso_profesor_id;
      }

      card.addEventListener("click", () => {
        const idSeleccionado = Number(card.dataset.id);
        if (idSeleccionado === cursoProfesorIdActual || cargando) return;

        // Cambiar selección visual de tarjetas
        contenedorAsignaturas
          .querySelectorAll(".asignatura-item")
          .forEach((c) => {
            c.classList.replace("seleccionado", "deseleccionado");
          });
        card.classList.replace("deseleccionado", "seleccionado");

        cursoProfesorIdActual = idSeleccionado;
        window.cursoProfesorIdActualNotas = idSeleccionado;
        cargarDatos(cursoProfesorIdActual, semestreActual);
      });

      contenedorAsignaturas.appendChild(card);
    });

    // Filtros de semestre
    const filtroSemestre = document.createElement("div");
    filtroSemestre.classList.add("filtro-semestre");
    filtroSemestre.innerHTML = `
      <button type="button" class="btn-semestre btn-sencillo" data-semestre="1" aria-pressed="false">1° Semestre</button>
      <button type="button" class="btn-semestre btn-sencillo" data-semestre="2" aria-pressed="false">2° Semestre</button>
    `;

    const contenedorSeccion = document.createElement("div");
    contenedorSeccion.classList.add("contenedor-seccion");
    contenedorSeccion.id = "contenedorTablaNotas";

    const contenedorTabla = document.createElement("div");
    contenedorTabla.classList.add("contenedor-tabla");

    const headerTabla = document.createElement("div");
    headerTabla.classList.add("header-tabla");
    const headerSpan = document.createElement("span");
    headerTabla.appendChild(headerSpan);
    headerTabla.appendChild(filtroSemestre);

    const cuerpoTabla = document.createElement("div");
    cuerpoTabla.classList.add("cuerpo-tabla");

    contenedorTabla.appendChild(cuerpoTabla);
    contenedorSeccion.appendChild(headerTabla);
    contenedorSeccion.appendChild(contenedorTabla);

    contenedorPrincipal.appendChild(contenedorAsignaturas);
    contenedorPrincipal.appendChild(contenedorSeccion);

    const botonesSemestre = filtroSemestre.querySelectorAll(".btn-semestre");

    // Actualiza la cabecera e indicadores visuales
    function marcarSemestre(cursoProfesorId, semestre) {
      botonesSemestre.forEach((btn) => {
        const activo = Number(btn.dataset.semestre) === semestre;
        btn.classList.toggle("active", activo);
        btn.setAttribute("aria-pressed", activo);
      });

      const asignaturaActual = dataAsignaturas.asignaturas.find(
        (a) => a.curso_profesor_id == cursoProfesorId,
      );

      if (asignaturaActual) {
        const key = normalizarTexto(asignaturaActual.asignatura);
        headerTabla.style.backgroundColor = colores[key] ?? "#e0e0e0";
        headerSpan.textContent = `${asignaturaActual.asignatura} - ${semestre}° Semestre`;
      }
    }

    funcionMarcarSemestre = marcarSemestre;

    // Petición al backend
    async function cargarDatos(cursoProfesorId, semestre) {
      if (!cursoProfesorId || cargando) return;

      const key = cacheKey(cursoProfesorId, semestre);

      // 0. Si ya está en caché, pintar directo sin skeleton ni fetch
      if (notasCache.has(key)) {
        const cached = notasCache.get(key);
        cuerpoTabla.classList.remove("skeleton");
        renderizarTabla(cached, cursoProfesorId);
        marcarSemestre(cursoProfesorId, semestre);
        return;
      }

      const currentId = ++requestId;

      // 1. Mostrar skeleton solo si la petición tarda más de 500 ms
      const timerCargando = setTimeout(() => {
        cuerpoTabla.innerHTML = ``;
        cuerpoTabla.classList.add("skeleton");
      }, 500);

      cargando = true;

      try {
        const res = await fetch(
          `/luminary/api/admin/cursos/curso_estudiante_notas.php?id_curso_profesor=${cursoProfesorId}&semestre=${semestre}`,
          { cache: "no-store" },
        );
        const data = await res.json();

        // 2. Si ya existe una petición más reciente, ignorar esta respuesta
        if (currentId !== requestId) return;

        if (data.success) {
          notasCache.set(key, data);
          cuerpoTabla.classList.remove("skeleton");
          renderizarTabla(data, cursoProfesorId);
          marcarSemestre(cursoProfesorId, semestre);
        } else {
          cuerpoTabla.classList.remove("skeleton");
          cuerpoTabla.innerHTML = `<p class="error-mensaje">No se pudieron obtener las notas.</p>`;
        }
      } catch (err) {
        console.error("Error al obtener notas:", err);
        if (currentId === requestId) {
          cuerpoTabla.classList.remove("skeleton");
          cuerpoTabla.innerHTML = `<p class="error-mensaje">Error de conexión con el servidor.</p>`;
        }
      } finally {
        // 3. Cancelar siempre el temporizador al finalizar la petición
        clearTimeout(timerCargando);
        if (currentId === requestId) {
          cargando = false;
        }
      }
    }

    funcionCargarDatos = cargarDatos;

    function generarHeaderEvaluaciones(evaluaciones) {
      let contador = 0;
      return evaluaciones
        .flatMap((ev) => {
          const repeticiones = ev.coeficiente2 == 1 ? 2 : 1;
          return Array.from({ length: repeticiones }, () => {
            contador++;
            const badgeClass = ev.coeficiente2 == 1 ? "badge-coef" : "";
            const tooltip = `${ev.titulo} ${ev.coeficiente2 == 1 ? "Coef. 2" : ""}`;
            return `<th>
              <span class="info-evaluacion ${badgeClass}" data-tippy-content="${tooltip}">
                Nota ${contador}
              </span>
            </th>`;
          });
        })
        .join("");
    }

    function generarCeldasNotas(estudiante, evaluaciones, cursoProfesorId) {
      return evaluaciones
        .flatMap((ev) => {
          const notasEv = estudiante.notas.filter(
            (n) => n.evaluacion_id == ev.id,
          );
          const repeticiones = ev.coeficiente2 == 1 ? 2 : 1;

          return Array.from({ length: repeticiones }, (_, i) => {
            const notaObj = notasEv[i];
            const valor = notaObj && notaObj.nota !== null ? notaObj.nota : "-";

            let color = "";
            if (valor === "P") color = "background-color: #e6ba1a;";
            else if (valor === "L" || valor === "ML") color = "color: #305bad;";
            else if (valor === "NL") color = "color: #f75353;";
            else if (parseFloat(valor) >= 4) color = "color: #305bad;";
            else if (parseFloat(valor) < 4 && valor !== "-")
              color = "color: #f75353;";

            return `<td>
              <input type="text" 
                     value="${valor}" 
                     oninput="formatearNota(this)" 
                     onchange="validarYGuardarYRecargar(this, ${ev.id}, ${estudiante.estudiante_id}, ${cursoProfesorId})" 
                     class="nota-input" 
                     style="${color}" />
            </td>`;
          });
        })
        .join("");
    }

    function renderizarTabla(data, cursoProfesorId) {
      cuerpoTabla.innerHTML = "";

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
          ${generarCeldasNotas(estudiante, data.evaluaciones, cursoProfesorId)}
          <td>${estudiante.cantidad_notas}</td>
          <td>${estudiante.suma_notas}</td>
          <td>${estudiante.promedio_aproximado}</td>
          <td>${estudiante.promedio !== null ? estudiante.promedio : "-"}</td>
        `;
        tbody.appendChild(fila);
      });

      cuerpoTabla.appendChild(tabla);
      tippy("[data-tippy-content]");
    }

    habilitarArrastre(".contenedor-asignaturas");

    // Event listeners para los botones de semestre
    botonesSemestre.forEach((btn) => {
      btn.addEventListener("click", () => {
        const semestreSeleccionado = Number(btn.dataset.semestre);
        if (semestreSeleccionado === semestreActual || cargando) return;

        semestreActual = semestreSeleccionado;
        window.semestreActualNotas = semestreSeleccionado;
        cargarDatos(cursoProfesorIdActual, semestreActual);
      });
    });

    // Carga inicial
    if (cursoProfesorIdActual) {
      window.cursoProfesorIdActualNotas = cursoProfesorIdActual;
      window.semestreActualNotas = semestreActual;
      await cargarDatos(cursoProfesorIdActual, semestreActual);
    }
  } catch (error) {
    console.error("Error al cargar la sección de notas:", error);
  }
}

async function validarYGuardarYRecargar(input, evaluacionId, estudianteId) {
  const inputs = document.querySelectorAll(".cuerpo-tabla td input");

  const setInputsDisabled = (disabled) => {
    inputs.forEach((inp) => {
      inp.disabled = disabled;
      inp.classList.toggle("disabled", disabled);
    });
  };

  try {
    setInputsDisabled(true);

    const notaGuardada = await validarYGuardar(
      input,
      evaluacionId,
      estudianteId,
    );

    if (notaGuardada) {
      const cursoProfesorId = window.cursoProfesorIdActualNotas;
      const semestreARecargar = window.semestreActualNotas;

      if (!cursoProfesorId || !semestreARecargar) return;

      // Invalidar caché del semestre actual para forzar datos frescos
      if (window.notasCacheNotas) {
        window.notasCacheNotas.delete(`${cursoProfesorId}-${semestreARecargar}`);
      }

      await new Promise((resolve) => setTimeout(resolve, 400));

      if (typeof funcionCargarDatos === "function") {
        await funcionCargarDatos(cursoProfesorId, semestreARecargar);
      }
      if (typeof funcionMarcarSemestre === "function") {
        funcionMarcarSemestre(cursoProfesorId, semestreARecargar);
      }
    }
  } catch (error) {
    console.error("Error al guardar y recargar la tabla:", error);
  } finally {
    setInputsDisabled(false);
  }
}

async function cargarSeccionPromedios(cursoId) {
  try {
    const res = await fetch(
      `/luminary/api/admin/cursos/curso_promedios.php?curso_id=${cursoId}`,
      { cache: "no-store" },
    );

    const data = await res.json();

    if (!data.success) return;

    const contenedorPrincipal = document.getElementById("curso-contenido");
    contenedorPrincipal.innerHTML = "";

    const asignaturas = data.asignaturas;

    // Crear tabla
    const tabla = document.createElement("table");
    tabla.classList.add("tabla-estudiantes");
    tabla.id = "tablaEstudiantes";

    // Header
    tabla.innerHTML = `
      <thead>
        <tr>
          <th>#</th>
          <th>Tipo</th>
          <th>Pend.</th>
          <th>Nombre Completo</th>
          ${asignaturas.map((a) => `<th style="text-align: center">${a}</th>`).join("")}
        </tr>
      </thead>
      <tbody></tbody>
    `;

    const tbody = tabla.querySelector("tbody");

    data.estudiantes.forEach((estudiante, index) => {
      const fila = document.createElement("tr");
      const celdasPromedios = asignaturas
        .map((asignatura) => {
          const promedio = estudiante.promedios[asignatura] ?? "N/A";

          let color = "";
          if (promedio === "P") color = "color: black;";
          else if (promedio === "N/A") color = "color: black;";
          else if (parseFloat(promedio) >= 4) color = "color: #305bad;";
          else if (parseFloat(promedio) < 4) color = "color: #f75353;";

          return `<td style="${color}"><span class="td-central">${promedio === "P" ? "<img class='icon-pendiente' src='/assets/icon/fluent-color--warning-16.svg'>" : promedio}</span></td>`;
        })
        .join("");

      fila.innerHTML = `
        <td>${index + 1}</td>
        <td><span class="tipo-estudiante"><img class="icon-estudiante" src="${estudiante.tipo_estudiante === "distancia" ? "/assets/icon/distancia.svg" : estudiante.tipo_estudiante === "tutoria" ? "/assets/icon/tutoria.svg" : "/assets/icon/presencial.svg"}"></span></td>
        <td><span class="tipo-estudiante link-pendiente" style="background: ${estudiante.notas_pendientes > 0 ? "#f8d03f" : "rgba(0, 0, 0, 0.1)"};" onclick="cargarView('pendientes', ${estudiante.id_estudiante})"><img class="icon-pendiente" src=${estudiante.notas_pendientes > 0 ? "/assets/icon/ion--warning.svg" : ""}>${estudiante.notas_pendientes > 0 ? estudiante.notas_pendientes : "-"}</span></td>
        <td><span class="estudiante-tabla" data-estudiante-id="${estudiante.id_estudiante}">${estudiante.nombre_estudiante} ${estudiante.apellidos_estudiante}</span></td>
        ${celdasPromedios}
      `;

      tbody.appendChild(fila);
    });

    const contenedorTabla = document.createElement("div");
    contenedorTabla.classList.add("contenedor-tabla");

    contenedorTabla.appendChild(tabla);

    contenedorPrincipal.appendChild(contenedorTabla);
  } catch (error) {
    console.log(error);
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

    const contenedorPrincipal = document.getElementById("curso-contenido");
    contenedorPrincipal.innerHTML = "";

    const contenedorAsignaturas = document.createElement("div");
    contenedorAsignaturas.classList.add("contenedor-asignaturas");
    const contenedorMaterial = document.createElement("div");
    contenedorMaterial.classList.add("contenedor-material");
    contenedorMaterial.id = "contenedorMaterial";

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
        <span>${asignatura.asignatura}</span>
      `;
      if (index === 0) {
        cardAsignatura.classList.add("seleccionado");
        cardAsignatura.classList.remove("deseleccionado");

        // Cargar evaluaciones automáticamente
        cargarMaterialAsignatura(asignatura.curso_profesor_id);
      }
      cardAsignatura.addEventListener("click", () => {
        cargarMaterialAsignatura(
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
