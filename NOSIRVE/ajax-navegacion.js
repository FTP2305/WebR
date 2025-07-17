document.addEventListener("DOMContentLoaded", function () {
    const main = document.getElementById("contenido-dinamico");
    const header = document.querySelector("header");
    const footer = document.querySelector("footer");

    function cargarContenido(url, push = true) {
        fetch(url)
            .then(res => res.text())
            .then(html => {
                const temp = document.createElement("div");
                temp.innerHTML = html;

                const nuevoMain = temp.querySelector("main") || temp;
                main.innerHTML = nuevoMain.innerHTML;

                // Volver a asignar eventos AJAX a nuevos enlaces cargados
                asignarEventosAjax();

                // Mostrar u ocultar header/footer si es Intranet
                const esIntranet = url.includes("Intranet");
                header.style.display = esIntranet ? "none" : "block";
                footer.style.display = esIntranet ? "none" : "block";

                if (push) {
                    history.pushState({ url: url }, '', url);
                }
            })
            .catch(err => {
                main.innerHTML = "<p>Error al cargar contenido.</p>";
                console.error("Error al cargar " + url, err);
            });
    }

    // Función para asignar eventos a todos los .nav-link (enlaces y botones)
    function asignarEventosAjax() {
        document.querySelectorAll(".nav-link").forEach(link => {
            link.addEventListener("click", function (e) {
                const urlReal = this.getAttribute("data-href") || this.getAttribute("href");
                const urlAjax = this.dataset.url;

                if (urlAjax && urlReal) {
                    e.preventDefault();
                    cargarContenido(urlReal);
                }
            });
        });
    }

    // Asignar eventos iniciales
    asignarEventosAjax();

    // Retroceso/avance del navegador
    window.addEventListener("popstate", function () {
        const url = location.pathname;
        cargarContenido(url, false);
    });
});
