//Función para seleccionar el género desde js, para poder hacerlo más fácil cambiando la ruta
function enviarGenero() {
    let genero = document.getElementById('selectGenero').value;

    if (!genero) {
        window.location.href = "{{ route('inicio_peliculas') }}";
        return false;
    }

    window.location.href = "/peliculas/genero/" + genero;
    return false;
}
