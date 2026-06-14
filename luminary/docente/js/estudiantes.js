async function initEstudiantes() {
    try {
        const res = await fetch(
        `/luminary/api/docente/jefatura/estudiante_notas.php?estudiante_id=${estudianteId}`,
        { cache: "no-store" },
        );

        const data = await res.json();

        

    } catch (error) {
        console.error('Error al inicializar estudiantes:', error);
    }
}