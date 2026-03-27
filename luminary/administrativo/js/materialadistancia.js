async function subirMaterial(e) {
  e.preventDefault();

  const curso_profesor_id = document.getElementById(
    "material_curso_profesor_id",
  ).value;
  const titulo = document.getElementById("material_titulo").value;
  const unidad = document.getElementById("material_unidad_id").value;
  const descripcion = document.getElementById("material_descripcion").value;
  const categoria_id = document.getElementById("material_categoria_id").value;
  const archivoInput = document.getElementById("material_archivo");
  const enlaceInput = document.getElementById("material_enlace");

  const formData = new FormData();
  formData.append("material_curso_profesor_id", curso_profesor_id);
  formData.append("material_unidad_id", unidad);
  formData.append("material_categoria_id", categoria_id);
  formData.append("material_titulo", titulo);
  formData.append("material_descripcion", descripcion);

  if (archivoInput.required) {
    // Es archivo
    formData.append("tipo", "archivo");
    formData.append("material_archivo", archivoInput.files[0]);
  } else {
    // Es enlace
    formData.append("tipo", "enlace");
    formData.append("archivo", enlaceInput.value);
  }

  try {
    const res = await fetch(
      "/luminary/api/docente/material/material_subir.php",
      {
        method: "POST",
        body: formData,
      },
    );

    const data = await res.json();

    if (data.success) {
      document.getElementById("mensaje").textContent =
        "Material subido correctamente";
      document.getElementById("mensaje").classList.add("mostrar", "green");
      setTimeout(() => {
        document.getElementById("mensaje").classList.remove("mostrar");
      }, 5000);
      const cursoId = document.getElementById(
        "material_curso_profesor_id",
      ).value;
      cargarMaterial(cursoId);
      document.getElementById("form-subir-material").reset();
    } else {
      document.getElementById("mensaje").textContent = data.message;
      document.getElementById("mensaje").classList.add("mostrar", "red");
      setTimeout(() => {
        document.getElementById("mensaje").classList.remove("mostrar");
      }, 5000);
    }
  } catch (error) {
    console.error("Error:", error);
  }
}

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
    const [resUnidades, resMaterial] = await Promise.all([
      fetch(
        `/luminary/api/docente/unidades/unidades_listar.php?curso_profesor_id=${curso_profesor_id}`,
      ),
      fetch(
        `/luminary/api/docente/material/material_listar.php?curso_profesor_id=${curso_profesor_id}`,
      ),
    ]);

    const dataUnidades = await resUnidades.json();
    const dataMaterial = await resMaterial.json();

    if (!dataUnidades.success) return;

    const contenedor = document.getElementById("material-curso");
    contenedor.innerHTML = "";

    const unidades = dataUnidades.unidades;
    const materiales = dataMaterial.success ? dataMaterial.material : [];

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
            <div onclick="eliminarUnidad(${unidad.id})" class="btn-material">
              <img src="/assets/icon/ic--baseline-delete.svg" alt="Eliminar unidad">
            </div>
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
              archivoURL = `/luminary/uploads/material/${mat.archivo}`;
            }

            contenedorItems.innerHTML += `
              <div class="item-material">
                <div class="preview-material">
                  ${previewHTML}
                  <div class="acciones-material">
                    ${mat.tipo === "enlace" || mat.tipo === "video" ? "" : `<a href="${archivoURL}" download class="btn-material"><img src="/assets/icon/material-symbols--download-rounded.svg"></a>`}
                    <a onclick="eliminarMaterial(${mat.id})" class="btn-material"><img src="/assets/icon/ic--baseline-delete.svg"></a>
                  </div>
                </div>
                <div class="info-material">
                  <a href="${archivoURL}" class="btn-ver" target="_blank">
                    <strong>${mat.titulo}</strong>
                  </a>
                  <p>${mat.descripcion || ""}</p>
                  <span class="fecha-subida">${mat.fecha_subida_formateada}</span>
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

async function cargarCategoriasMaterial() {
  try {
    const responseCategorias = await fetch(
      "/luminary/api/docente/material/material_categorias.php",
    );

    if (!responseCategorias.ok) {
      throw new Error("Error cargando categorías");
    }

    const data = await responseCategorias.json();

    if (!data.success) {
      throw new Error("Error al obtener categorías");
    }

    const select = document.getElementById("material_categoria_id");
    select.innerHTML = `<option value="">Seleccione categoría</option>`;

    data.categorias.forEach((cat) => {
      select.innerHTML += `
        <option value="${cat.id}">${cat.nombre}</option>
      `;
    });
  } catch (error) {
    console.error("Error cargando categorías:", error);
  }
}

async function eliminarMaterial(materialId) {
  if (!confirm("¿Seguro que deseas eliminar este material?")) return;

  const formData = new FormData();
  formData.append("material_id", materialId);

  try {
    const res = await fetch(
      "/luminary/api/docente/material/material_eliminar.php",
      {
        method: "POST",
        body: formData,
      },
    );

    const data = await res.json();

    if (data.success) {
      // 🔄 Recargar lista
      const cursoId = document.getElementById(
        "material_curso_profesor_id",
      ).value;
      console.log(
        "Material eliminado, recargando lista para curso_profesor_id:",
        cursoId,
      );
      cargarMaterial(cursoId);

      document.getElementById("mensaje").textContent =
        "Material eliminado correctamente";
      document.getElementById("mensaje").classList.add("mostrar", "green");
      setTimeout(() => {
        document.getElementById("mensaje").classList.remove("mostrar");
      }, 5000);
    } else {
      document.getElementById("mensaje").textContent = data.message;
      document.getElementById("mensaje").classList.add("mostrar", "red");
      setTimeout(() => {
        document.getElementById("mensaje").classList.remove("mostrar");
      }, 5000);
    }
  } catch (error) {
    console.error("Error eliminando:", error);
  }
}
