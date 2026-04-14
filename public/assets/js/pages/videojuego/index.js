function enviarGenero() {
    let genero = document.getElementById('selectGenero').value;

    if (!genero) {
        window.location.href = "/videojuegos";
        return false;
    }

    window.location.href = "/videojuegos/genero/" + genero;
    return false;
}