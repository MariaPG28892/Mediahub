// Función para seleccionar el género de series
function enviarGenero() {
    let genero = document.getElementById('selectGenero').value;

    if (!genero) {
        window.location.href = "/series";
        return false;
    }

    window.location.href = "/series/genero/" + genero;
    return false;
}