function activarAnimacionUnidades() {
  document.querySelectorAll(".unidad-header").forEach((header) => {
    header.addEventListener("click", () => {
      const contenido = header.nextElementSibling;
      const icono = header.querySelector(".icono-toggle");

      if (contenido.style.maxHeight) {
        contenido.style.maxHeight = null;
        icono.style.transform = "rotate(0deg)";
      } else {
        contenido.style.maxHeight = contenido.scrollHeight + "px";
        icono.style.transform = "rotate(180deg)";
      }
    });
  });
}

async function cargarMaterial(curso_profesor_id) {
  try {
    const [resUnidades, resMaterial, resEstudiante] = await Promise.all([
      fetch(
        `/luminary/api/estudiante/unidades/unidades_listar.php?curso_profesor_id=${curso_profesor_id}`,
      ),
      fetch(
        `/luminary/api/estudiante/material/material_listar.php?curso_profesor_id=${curso_profesor_id}`,
      ),
      fetch(
        `/luminary/api/estudiante/me.php`,
      ),

    ]);

    const dataUnidades = await resUnidades.json();
    const dataMaterial = await resMaterial.json();
    const dataEstudiante = await resEstudiante.json();

    if (!dataUnidades.success) return;

    const contenedor = document.getElementById("material-curso");
    contenedor.innerHTML = "";

    const unidades = dataUnidades.unidades;
    const materiales = dataMaterial.success ? dataMaterial.material : [];

    // 🔹 Verificar si no hay unidades
    if (unidades.length === 0) {
      contenedor.innerHTML = `
        <div class="no-unidades">
          <p>No se han creado unidades ni se ha subido material</p>
        </div>
      `;
      return;
    }

    // 🔹 Agrupar materiales por unidad y categoría
    const agrupado = {};

    materiales.forEach((mat) => {
      if (!agrupado[mat.unidad_id]) {
        agrupado[mat.unidad_id] = {};
      }

      const categoria = mat.categoria_nombre || "Sin categoría";

      if (!agrupado[mat.unidad_id][categoria]) {
        agrupado[mat.unidad_id][categoria] = [];
      }

      agrupado[mat.unidad_id][categoria].push(mat);
    });

    // 🔹 Dibujar TODAS las unidades aunque estén vacías
    unidades.forEach((unidad) => {
      const bloqueUnidad = document.createElement("div");
      bloqueUnidad.classList.add("bloque-unidad");

      bloqueUnidad.innerHTML = `
        <div class="unidad-header">
          <h3>📘 ${unidad.nombre}</h3>
          <div class="acciones-unidad">
            <span class="icono-toggle">▼</span>
          </div>
        </div>
        <div class="unidad-contenido"></div>
      `;

      const contenidoUnidad = bloqueUnidad.querySelector(".unidad-contenido");

      const categoriasUnidad = agrupado[unidad.id];

      if (!categoriasUnidad) {
        contenidoUnidad.innerHTML = `
          <p class="unidad-vacia">No hay material en esta unidad</p>
        `;
      } else {
        for (const categoria in categoriasUnidad) {
          const bloqueCategoria = document.createElement("div");
          bloqueCategoria.classList.add("bloque-categoria");

          bloqueCategoria.innerHTML = `
            <h4 class="titulo-categoria">📂 ${categoria}</h4>
            <div class="items-categoria"></div>
          `;

          const contenedorItems =
            bloqueCategoria.querySelector(".items-categoria");

          categoriasUnidad[categoria].forEach((mat) => {
            let previewHTML = "";
            let archivoURL = "";

            if (mat.tipo === "pdf") {
              previewHTML = `<div class="preview-pdf"><img src="/assets/icon/teenyicons--pdf-solid.svg" alt="Vista previa del PDF"></div>`;
            }

            if (mat.tipo === "imagen") {
              previewHTML = `
                <div class="preview-img">
                  <img src="/luminary/uploads/material/${mat.archivo}" alt="Vista previa de la imagen">
                </div>
              `;
            }

            if (mat.tipo === "doc" || mat.tipo === "ppt") {
              previewHTML = `<div class="preview-doc">📄 Documento</div>`;
            }

            if (mat.tipo === "video") {
              previewHTML = `<div class="preview-video"><img src="/assets/icon/lets-icons--video-fill.svg"></div>`;
            } else if (mat.tipo === "enlace") {
              previewHTML = `<div class="preview-enlace"><img src="/assets/icon/mdi--web.svg"></div>`;
            }

            if (mat.tipo === "enlace" || mat.tipo === "video") {
              archivoURL = mat.archivo;
            } else {
              if (dataEstudiante.tipo === "distancia") {
                archivoURL = `/luminary/uploads/material_distancia/${mat.archivo}`;
              } else {
                archivoURL = `/luminary/uploads/material/${mat.archivo}`;
              }
            }

            contenedorItems.innerHTML += `
              <div class="item-material">
                <div class="preview-material">
                  ${previewHTML}
                  <div class="acciones-material">
                    ${mat.tipo === "enlace" || mat.tipo === "video" ? "" : `<a href="${archivoURL}" download class="btn-material"><img src="/assets/icon/material-symbols--download-rounded.svg"></a>`}
                  </div>
                </div>
                <div class="info-material">
                  <span class="fecha-subida">${mat.fecha_subida_formateada}</span>
                  <a href="${archivoURL}" class="btn-ver" target="_blank">
                    <strong class="titulo-material">${mat.titulo}</strong>
                  </a>
                  <p>${mat.descripcion || "Sin descripción"}</p>
                  
                </div>
              </div>
            `;
          });

          contenidoUnidad.appendChild(bloqueCategoria);
        }
      }

      contenedor.appendChild(bloqueUnidad);
    });

    activarAnimacionUnidades();
  } catch (error) {
    console.error("Error cargando material:", error);
  }
}
