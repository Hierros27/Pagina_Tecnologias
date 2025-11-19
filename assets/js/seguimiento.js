document.addEventListener("DOMContentLoaded", () => {
    console.log("Cargando estado...");

    fetch("assets/api/obtener_estado.php")
        .then(res => res.json())
        .then(data => {
            console.log("Estado recibido:", data);

            const estado = data.estado;
            actualizarBarra(estado);
        })
        .catch(error => console.error("Error al obtener estado:", error));
});


function actualizarBarra(estado) {
    const pasos = document.querySelectorAll(".step");

    pasos.forEach((step, index) => {
        let numeroPaso = index + 1;

        if (numeroPaso < estado) {
            step.classList.add("completed");
        } 
        
        if (numeroPaso === estado) {
            step.classList.add("active");
        }
    });
}
