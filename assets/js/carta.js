const botones = document.querySelectorAll(".filtro-categoria");
const bloques = document.querySelectorAll(".bloque-categoria");

botones.forEach((boton) => {
    boton.addEventListener("click", function () {
        const categoriaSeleccionada = this.dataset.categoria;

        botones.forEach((b) => {
            b.classList.remove("btn-warning");
            b.classList.add("btn-outline-warning");
        });

        this.classList.remove("btn-outline-warning");
        this.classList.add("btn-warning");

        bloques.forEach((bloque) => {
            if (
                categoriaSeleccionada === "todas" ||
                bloque.dataset.categoria === categoriaSeleccionada
            ) {
                bloque.style.display = "block";
            } else {
                bloque.style.display = "none";
            }
        });
    });
});
