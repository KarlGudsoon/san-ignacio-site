async function subirMaterial(e) {
  e.preventDefault();

  const curso_profesor_id = document.getElementById(
    "material_curso_profesor_id",
  ).value;
  const titulo = document.getElementById("material_titulo").value;
  const descripcion = document.getElementById("material_descripcion").value;
  const categoria_id = document.getElementById("material_categoria_id").value;
  const archivoInput = document.getElementById("material_archivo");

  if (!archivoInput.files.length) {
    alert("Selecciona un archivo");
    return;
  }

  const formData = new FormData();
  formData.append("material_curso_profesor_id", curso_profesor_id);
  formData.append("material_categoria_id", categoria_id);
  formData.append("material_titulo", titulo);
  formData.append("material_descripcion", descripcion);
  formData.append("material_archivo", archivoInput.files[0]);

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

async function cargarMaterial(curso_profesor_id) {
  try {
    const res = await fetch(
      `/luminary/api/docente/material/material_listar.php?curso_profesor_id=${curso_profesor_id}`,
    );

    const data = await res.json();

    if (!data.success) return;

    const contenedor = document.getElementById("material-curso");
    contenedor.innerHTML = "";

    // 🔹 Agrupar por categoría
    const agrupado = {};

    data.material.forEach((mat) => {
      const categoria = mat.categoria_nombre || "Sin categoría";

      if (!agrupado[categoria]) {
        agrupado[categoria] = [];
      }

      agrupado[categoria].push(mat);
    });

    // 🔹 Crear HTML
    for (const categoria in agrupado) {
      const bloqueCategoria = document.createElement("div");
      bloqueCategoria.classList.add("bloque-categoria");

      bloqueCategoria.innerHTML = `
        <h3 class="titulo-categoria">📂 ${categoria}</h3>
        <div class="items-categoria"></div>
      `;

      const contenedorItems = bloqueCategoria.querySelector(".items-categoria");

      agrupado[categoria].forEach((mat) => {
        let previewHTML = "";

        if (mat.tipo === "pdf") {
          previewHTML = `
          <div class="preview-iframe">
            <iframe 
              src="/luminary/uploads/material/${mat.archivo}" 
              width="100%" 
              height="100%">
            </iframe>
          </div>
        `;
        }
        if (["doc", "docx", "ppt", "pptx"].includes(mat.tipo)) {
          previewHTML = `
            <iframe
              src="https://view.officeapps.live.com/op/embed.aspx?src=${encodeURIComponent(
                window.location.origin +
                  "/luminary/uploads/material/" +
                  mat.archivo,
              )}"
              width="100%"
              height="100%">
            </iframe>
          `;
        }
        if (["jpg", "jpeg", "png"].includes(mat.tipo)) {
          previewHTML = `
          <img 
            src="/luminary/uploads/material/${mat.archivo}" 
            class="preview-img">
        `;
        }

        contenedorItems.innerHTML += `
          <div class="item-material">
            <div class="preview-material">
              ${previewHTML}
              <div class="info-material">
                <strong>${mat.titulo}</strong>
                <p>${mat.descripcion || ""}</p>
                <small>${mat.fecha_subida}</small>
              </div>
            </div>
            <div class="acciones-material">
              <a href="/luminary/uploads/material/${mat.archivo}" class="btn-ver" target="_blank">
                Ver
              </a>
              <a href="/luminary/uploads/material/${mat.archivo}" download class="btn-descargar">
                Descargar
              </a>
              <button onclick="eliminarMaterial(${mat.id})" class="btn-eliminar">
                🗑
              </button>
            </div>
          </div>
        `;
      });

      contenedor.appendChild(bloqueCategoria);
    }

    console.log("Material cargado correctamente");
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
      alert(data.message);
    }
  } catch (error) {
    console.error("Error eliminando:", error);
  }
}
