const VISTA_MATRICULAS = {
  pendientes: {
    endpoint: "/luminary/api/admin/matriculas/matriculas_pendientes.php",
    volver: "estudiantes",
    origen: "matriculas_pendientes",
    nueva: "matricula_pendiente_nueva",
    tabla: "matriculas_formulario",
    estadoFicha: "solicitud",
    activar: true,
    copiar: true,
    traspasar: false,
  },
  proximoAnio: {
    endpoint: "/luminary/api/admin/matriculas/matriculas_proximo_anio.php",
    volver: "matriculas_pendientes",
    origen: "matriculas_proximo_anio",
    nueva: null,
    tabla: "matriculas_proximo_anio",
    estadoFicha: "proximo_anio",
    activar: false,
    copiar: false,
    traspasar: true,
  },
};

let configMatriculasActual = VISTA_MATRICULAS.pendientes;
let solicitudesMatricula = [];
let cursosMatricula = [];
let cursoMatriculaActivo = null; // null = todos, "sin-curso" = sin curso válido

async function initMatriculasPendientes() {
  await initMatriculas(VISTA_MATRICULAS.pendientes);
}

async function initMatriculasProximoAnio() {
  await initMatriculas(VISTA_MATRICULAS.proximoAnio);
}

async function initMatriculas(config) {
  configMatriculasActual = config;
  sessionStorage.setItem("tablaMatricula", config.tabla);
  sessionStorage.setItem("vistaMatriculaOrigen", config.origen);

  document.getElementById("volver")?.addEventListener("click", () => {
    cargarView(config.volver);
  });

  const btnNueva = document.getElementById("btn-matricula-pendiente");
  if (btnNueva && config.nueva) {
    btnNueva.addEventListener("click", () => cargarView(config.nueva));
  }

  document
    .getElementById("btn-matriculas-proximo-anio")
    ?.addEventListener("click", () => {
      cargarView("matriculas_proximo_anio");
    });

  if (config.traspasar) {
    document
      .getElementById("btn-traspasar-primero-nivel")
      ?.addEventListener("click", () => {
        traspasarPrimerNivelProximoAnio();
      });
  }

  solicitudesMatricula = [];
  cursosMatricula = [];
  cursoMatriculaActivo = null;

  await cargarMatriculas(config);
}

function escaparHTML(valor) {
  return String(valor ?? "").replace(
    /[&<>"']/g,
    (caracter) =>
      ({
        "&": "&amp;",
        "<": "&lt;",
        ">": "&gt;",
        '"': "&quot;",
        "'": "&#39;",
      })[caracter],
  );
}

function solicitudPerteneceACurso(matricula, cursoId) {
  if (cursoId === null) return true;

  const id = Number(matricula.curso_preferido);

  if (cursoId === "sin-curso") {
    return !id || !cursosMatricula.some((curso) => Number(curso.id) === id);
  }

  return id === Number(cursoId);
}

function construirFiltroCursos(onCambio) {
  const contenedor = document.getElementById("filtro-cursos-matricula");
  if (!contenedor) return;
  contenedor.innerHTML = "";

  const agregarBoton = (valor, etiqueta) => {
    const cantidad = solicitudesMatricula.filter((matricula) =>
      solicitudPerteneceACurso(matricula, valor),
    ).length;

    const boton = document.createElement("button");
    boton.type = "button";
    boton.innerHTML = `${etiqueta} <span class="contador-curso">${cantidad}</span>`;
    if (valor === cursoMatriculaActivo) boton.classList.add("seleccionado");

    boton.addEventListener("click", () => {
      cursoMatriculaActivo = valor;
      contenedor
        .querySelectorAll("button")
        .forEach((otro) => otro.classList.remove("seleccionado"));
      boton.classList.add("seleccionado");
      onCambio();
    });

    contenedor.appendChild(boton);
  };

  agregarBoton(null, "Todos");

  cursosMatricula.forEach((curso) => {
    const etiqueta = curso.letra
      ? `${curso.curso_nivel ?? ""} ${curso.letra}`.trim()
      : (curso.curso ?? `Curso ${curso.id}`);
    agregarBoton(Number(curso.id), escaparHTML(etiqueta));
  });

  const haySinCurso = solicitudesMatricula.some((matricula) =>
    solicitudPerteneceACurso(matricula, "sin-curso"),
  );
  if (haySinCurso) agregarBoton("sin-curso", "Sin curso");
}

async function cargarMatriculas(config) {
  try {
    const [resMatriculas, resCursos] = await Promise.all([
      fetch(config.endpoint, { cache: "no-store" }),
      fetch("/luminary/api/admin/cursos/cursos.php", { cache: "no-store" }),
    ]);

    const [data, dataCursos] = await Promise.all([
      resMatriculas.json(),
      resCursos.json(),
    ]);

    if (!data.success) return;

    solicitudesMatricula = data.matriculas_pendientes ?? [];
    cursosMatricula = dataCursos.success ? (dataCursos.cursos ?? []) : [];

    const contenedorPrincipal = document.getElementById("contenido-principal");
    if (!contenedorPrincipal) return;
    contenedorPrincipal.innerHTML = "";

    const buscadorEst = document.createElement("div");
    buscadorEst.classList.add("buscador");
    buscadorEst.innerHTML = `
      <input type="text" id="buscadorEstudiantes" placeholder="Buscar por nombre">
    `;
    contenedorPrincipal.appendChild(buscadorEst);

    const buscadorInput = document.getElementById("buscadorEstudiantes");

    const tabla = document.createElement("table");
    tabla.classList.add("tabla-estudiantes");
    tabla.id = "tablaEstudiantes";

    tabla.innerHTML = `
      <thead>
        <tr>
          <th>#</th>
          <th>Nombre Completo</th>
          <th>RUT</th>
          <th>Edad</th>
          <th>Curso</th>
          <th>Fecha de registro</th>
          <th>Año de Matrícula</th>
          <th class="td-central">Acciones</th>
        </tr>
      </thead>
      <tbody></tbody>
    `;

    const tbody = tabla.querySelector("tbody");

    function aplicarFiltros() {
      const texto = (buscadorInput.value ?? "").trim().toLowerCase();

      const visibles = solicitudesMatricula.filter((matricula) => {
        const nombreCompleto =
          `${matricula.nombre_estudiante ?? ""} ${matricula.apellidos_estudiante ?? ""}`.toLowerCase();
        return (
          nombreCompleto.includes(texto) &&
          solicitudPerteneceACurso(matricula, cursoMatriculaActivo)
        );
      });

      tbody.innerHTML = "";

      if (!visibles.length) {
        const fila = document.createElement("tr");
        fila.classList.add("fila-vacia");
        fila.innerHTML = `<td colspan="8">No hay solicitudes que coincidan con los filtros.</td>`;
        tbody.appendChild(fila);
        return;
      }

      visibles.forEach((matricula, index) => {
        const fila = document.createElement("tr");
        const idMatricula = Number(matricula.id);
        const idEstudiante = Number(matricula.id_estudiante ?? 0);
        const copiada = matricula.estado === "Copiada";

        if (copiada) {
          fila.classList.add("fila-copiada");
        }

        const botonPdf = copiada
          ? ""
          : `<button class="btn-mini btn-pdf" onclick="generarFichaMatricula(${idMatricula}, '${config.estadoFicha}')" style="margin-bottom: 0;">
                <img src="/assets/icon/pdf-red.svg">
              </button>`;

        const botonActivar =
          !copiada && config.activar
            ? `<button hidden onclick="activarMatricula(${idMatricula})"><img src="/assets/icon/listo-white.svg"></button>`
            : "";

        const botonCopiar =
          !copiada && config.copiar
            ? `<button class="btn-mini bg-warning" title="Copiar a matrículas próximo año" onclick="copiarMatriculaProximoAnio(${idMatricula})"><img src="/assets/icon/transfer.svg" style="width: 16px; height: 16px; filter: invert(1);" alt="Copiar"></button>`
            : "";

        const acciones = copiada
          ? `<div class="td-central"><span class="estado-copiada">Copiada</span></div>`
          : `<div class="td-central contenedor-botones">
              <button class="btn-mini" onclick="cargarView('matricula_editar', ${idMatricula})"><img src="/assets/icon/editar.svg"></button>
              <button class="btn-mini btn-negativo" onclick="eliminarMatricula(${idMatricula})"><img src="/assets/icon/delete.svg"></button>
              ${botonCopiar}
              ${botonActivar}
            </div>`;

        fila.innerHTML = `
          <td>${index + 1}</td>
          <td>
            <div class="flex-align">
              <span class="estudiante-tabla" data-estudiante-id="${idEstudiante}">${escaparHTML(matricula.apellidos_estudiante)} ${escaparHTML(matricula.nombre_estudiante)}</span>
              ${botonPdf}
            </div>
          </td>
          <td>${escaparHTML(matricula.rut_estudiante ?? "-")}</td>
          <td>${escaparHTML(matricula.edad ?? "-")}</td>
          <td>${escaparHTML(matricula.curso ?? "-")}</td>
          <td>${escaparHTML(matricula.fecha_registro ?? "-")}</td>
          <td>${escaparHTML(matricula.matricula_ano ?? "-")}</td>
          <td>
            ${acciones}
          </td>
        `;

        tbody.appendChild(fila);
      });
    }

    buscadorInput.addEventListener("input", aplicarFiltros);

    construirFiltroCursos(aplicarFiltros);
    aplicarFiltros();

    const contenedorTabla = document.createElement("div");
    contenedorTabla.classList.add("contenedor-tabla");
    contenedorTabla.appendChild(tabla);
    contenedorPrincipal.appendChild(contenedorTabla);
  } catch (error) {
    console.error("Error cargando matrículas:", error);
  }
}

async function eliminarMatricula(idMatricula) {
  const config = configMatriculasActual;

  if (!confirm("¿Estás seguro de eliminar la solicitud de matrícula?")) {
    return;
  }

  const formData = new FormData();

  formData.append("id_matricula", idMatricula);
  formData.append("tabla", config.tabla);

  const res = await fetch(
    "/luminary/api/admin/matriculas/matricula_pendiente_eliminar.php",
    {
      method: "POST",
      body: formData,
    },
  );

  const data = await res.json();

  if (data.success) {
    mostrarMensaje("Matrícula eliminada correctamente", "green");
    cargarView(config.origen);
  } else {
    mostrarMensaje(data.message, "red");
  }
}

async function copiarMatriculaProximoAnio(idMatricula) {
  const config = configMatriculasActual;

  if (!confirm("¿Copiar esta matrícula a Matrículas próximo año?")) {
    return;
  }

  try {
    const formData = new FormData();
    formData.append("id_matricula", idMatricula);
    formData.append("tabla_origen", config.tabla);

    const res = await fetch(
      "/luminary/api/admin/matriculas/matricula_copiar_proximo_anio.php",
      {
        method: "POST",
        body: formData,
      },
    );

    const data = await res.json();

    if (data.success) {
      mostrarMensaje("Matrícula copiada a próximo año correctamente", "green");
      cargarView(config.origen);
    } else {
      mostrarMensaje(data.message ?? "No se pudo copiar la matrícula", "red");
    }
  } catch (error) {
    console.error("Error al copiar la matrícula:", error);
    mostrarMensaje("Error al copiar la matrícula", "red");
  }
}

async function traspasarPrimerNivelProximoAnio() {
  const config = configMatriculasActual;

  if (
    !confirm(
      "¿Copiar las matrículas activas de 1° nivel a sus cursos de 2° en Matrículas próximo año?",
    )
  ) {
    return;
  }

  try {
    const formData = new FormData();
    formData.append("tabla_destino", config.tabla);

    const res = await fetch(
      "/luminary/api/admin/matriculas/matriculas_proximo_anio_traspasar.php",
      {
        method: "POST",
        body: formData,
      },
    );

    const data = await res.json();

    if (data.success) {
      const partes = [
        `${data.copiados ?? 0} copiados`,
        `${data.omitidos_duplicados ?? 0} duplicados omitidos`,
        `${data.sin_mapeo ?? 0} sin mapeo`,
      ];

      if (data.errores) {
        partes.push(`${data.errores} con error`);
      }

      mostrarMensaje(`Traspaso completado: ${partes.join(", ")}`, "green");
      cargarView(config.origen);
    } else {
      mostrarMensaje(data.message ?? "No se pudo realizar el traspaso", "red");
    }
  } catch (error) {
    console.error("Error al traspasar matrículas de 1° nivel:", error);
    mostrarMensaje("Error al traspasar matrículas de 1° nivel", "red");
  }
}

async function activarMatricula(idMatricula) {
  try {
    const res = await fetch(
      `/luminary/api/admin/matriculas/matricula_activar.php?id=${idMatricula}`,
      { cache: "no-store" },
    );
    const data = await res.json();

    if (data.success) {
      mostrarMensaje("Matrícula activada correctamente", "green");
      cargarView("matriculas_pendientes");
    } else {
      mostrarMensaje(data.message, "red");
      console.log("Error al activar la matrícula:", data.message);
    }
  } catch (error) {
    console.log("Error al activar la matrícula:", error);
    mostrarMensaje("Error al activar la matrícula", "red");
  }
}

function generarFichaMatricula(idMatricula, estado) {
  window.open(
    `/luminary/api/admin/matriculas/generar_ficha_matricula.php?id=${idMatricula}&estado=${estado}`,
    "_blank",
  );
}
