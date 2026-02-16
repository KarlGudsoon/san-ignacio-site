async function initEvaluaciones() {
    await cargarTipos();
    await cargarCursosProfesorSelect();

    const form = document.getElementById("formEvaluacion");

    form.addEventListener("submit", async (e) => {
        e.preventDefault();
        await guardarEvaluacion();
    });
}

async function cargarTipos() {
    try {
        const res = await fetch("/luminary/api/docente/evaluaciones/tipo_evaluacion.php", {
            cache: "no-store"
        });

        const data = await res.json();

        if (!data.success) return;

        const select = document.getElementById("tipoSelect");
        if (!select) return; // seguridad

        select.innerHTML = '<option value="">Seleccionar tipo</option>';

        data.tipos.forEach(tipo => {
            const option = document.createElement("option");
            option.value = tipo.id;
            option.textContent = tipo.nombre;
            select.appendChild(option);
        });

    } catch (error) {
        console.error("Error cargando tipos:", error);
    }
}

async function cargarCursosProfesorSelect() {
    try {
        const res = await fetch("/luminary/api/docente/cursos/cursos.php", {
            cache: "no-store"
        });

        const data = await res.json();

        if (!data.success) return;

        const select = document.getElementById("cursoProfesorSelect");
        if (!select) return; // seguridad

        select.innerHTML = '<option value="">Seleccionar tipo</option>';

        data.cursos.forEach(curso => {
            const option = document.createElement("option");
            option.value = curso.id;
            option.textContent = `${curso.curso_nivel} Nivel ${curso.curso_letra} - ${curso.asignatura}`;
            select.appendChild(option);
        });

    } catch (error) {
        console.error("Error cargando cursos:", error);
    }
}

async function guardarEvaluacion() {
    const formData = new FormData();

    formData.append("titulo", document.getElementById("titulo").value);
    formData.append("descripcion", document.getElementById("descripcion").value);
    formData.append("curso_profesor_id", document.getElementById("cursoProfesorSelect").value);
    formData.append("tipo_id", document.getElementById("tipoSelect").value);
    formData.append("fecha_aplicacion", document.getElementById("fecha").value);

    const res = await fetch("/luminary/api/docente/evaluaciones/crear_evaluacion.php", {
        method: "POST",
        body: formData
    });

    const data = await res.json();

    if (data.success) {
        alert("Evaluación creada correctamente");

        // 👉 aquí puedes abrir automáticamente la vista de carga de notas
        cargarView("evaluaciones");
    } else {
        alert(data.message);
    }
}
