async function initMatriculaNueva() {
    document.getElementById("volver").addEventListener("click", () => {
        history.back();
    });

    // FORMATEAR RUT EN TIEMPO REAL
    const inputRut = document.querySelector(".formateador_rut");

    inputRut.addEventListener("input", function () {
    let valor = this.value
        .replace(/\./g, "")
        .replace(/-/g, "")
        .replace(/[^0-9kK]/g, "");

    // Limitar a máximo 9 dígitos incluyendo DV
    if (valor.length > 9) {
        valor = valor.substring(0, 9);
    }

    // Aplicar formato
    if (valor.length > 1) {
        let cuerpo = valor.slice(0, -1);
        let dv = valor.slice(-1);

        // Poner puntos al cuerpo
        cuerpo = cuerpo.replace(/\B(?=(\d{3})+(?!\d))/g, ".");

        this.value = cuerpo + "-" + dv.toUpperCase();
    } else {
        this.value = valor;
    }
    });

    // IMPEDIR PEGAR FORMATO INCORRECTO
    inputRut.addEventListener("paste", function (e) {
    e.preventDefault();
    });

    document.getElementById("guardar_matricula").addEventListener("click", guardarMatricula);
}

async function guardarMatricula() {
  const formData = new FormData();

  formData.append("nombre_estudiante", document.querySelector('[name="nombre_estudiante"]').value);
  formData.append("apellidos_estudiante", document.querySelector('[name="apellidos_estudiante"]').value);
  formData.append("fecha_nacimiento", document.querySelector('[name="fecha_nacimiento"]').value);
  formData.append("rut_estudiante", document.querySelector('[name="rut_estudiante"]').value);
  formData.append("serie_carnet_estudiante", document.querySelector('[name="serie_carnet_estudiante"]').value);
  formData.append("etnia_estudiante", document.querySelector('[name="etnia_estudiante"]').value);
  formData.append("direccion_estudiante", document.querySelector('[name="direccion_estudiante"]').value);
  formData.append("correo_estudiante", document.querySelector('[name="correo_estudiante"]').value);
  formData.append("curso_preferido", document.querySelector('[name="curso_preferido"]').value);
  formData.append("telefono_estudiante", document.querySelector('[name="telefono_estudiante"]').value);
  formData.append("hijos_estudiante", document.querySelector('[name="hijos_estudiante"]').value);
  formData.append("situacion_especial_estudiante", document.querySelector('[name="situacion_especial_estudiante"]').value);
  formData.append("programa_estudiante", document.querySelector('[name="programa_estudiante"]').value);
  formData.append("nombre_apoderado", document.querySelector('[name="nombre_apoderado"]').value);
  formData.append("rut_apoderado", document.querySelector('[name="rut_apoderado"]').value);
  formData.append("parentezco_apoderado", document.querySelector('[name="parentezco_apoderado"]').value);
  formData.append("direccion_apoderado", document.querySelector('[name="direccion_apoderado"]').value);
  formData.append("telefono_apoderado", document.querySelector('[name="telefono_apoderado"]').value);
  formData.append("situacion_especial_apoderado", document.querySelector('[name="situacion_especial_apoderado"]').value);
  

  const res = await fetch(
    "/luminary/api/admin/matriculas/matricula_crear.php",
    {
      method: "POST",
      body: formData,
    },
  );

  const data = await res.json();

  if (data.success) {
    
    mostrarMensaje("Matrícula creada correctamente", "green");

    cargarView("estudiantes");

  } else {
    alert(data.message);
  }
}

async function eliminarMatricula(idEstudiante) {
  const confirmed = confirm("¿Estás seguro de eliminar al estudiante?");

  if (confirmed === false) {
    return;
  }

  const formData = new FormData();
  formData.append("id_estudiante", idEstudiante);

  const res = await fetch(
    "/luminary/api/admin/matriculas/matricula_eliminar.php",
    {
      method: "POST",
      body: formData,
    },
  );

  const data = await res.json();

  if (data.success) {
    mostrarMensaje("Estudiante eliminado correctamente", "green");
    cargarView("estudiantes");
  } else {
    alert(data.message);
  }
}