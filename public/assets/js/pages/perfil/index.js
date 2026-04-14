function cambiarPestanaPeliculas(el, id) {

    const container = el.closest('.perfil-seccion');

    container.querySelectorAll('.contenido-pestana')
        .forEach(e => e.classList.remove('activo'));

    container.querySelectorAll('.pestana')
        .forEach(e => e.classList.remove('activa'));

    const target = container.querySelector('#peliculas-' + id);

    if (target) {
        target.classList.add('activo');
    }

    el.classList.add('activa');

    setTimeout(actualizarFlechas, 50);
}

function cambiarPestanaSeries(el, id) {

    const container = el.closest('.perfil-seccion');

    container.querySelectorAll('.contenido-pestana')
        .forEach(e => e.classList.remove('activo'));

    container.querySelectorAll('.pestana')
        .forEach(e => e.classList.remove('activa'));

    const target = container.querySelector('#series-' + id);

    if (target) {
        target.classList.add('activo');
    }

    el.classList.add('activa');

    setTimeout(actualizarFlechas, 50);
}

function cambiarPestanaVideojuegos(btn, tab) {
    document.querySelectorAll('#videojuegos-pendientes, #videojuegos-jugados, #videojuegos-favoritos')
        .forEach(el => el.classList.remove('activo'));

    document.getElementById('videojuegos-' + tab).classList.add('activo');

    document.querySelectorAll('.pestana').forEach(b => b.classList.remove('activa'));
    btn.classList.add('activa');
}

function moverCarruselVideojuegos(btn, dir) {
    const carousel = btn.parentElement.querySelector('.carousel');
    carousel.scrollBy({ left: dir * 300, behavior: 'smooth' });
}

function moverCarrusel(btn, direccion) {

    const carrusel = btn.parentElement.querySelector('.carousel');
    if (!carrusel) return;

    const card = carrusel.querySelector('.card');
    const paso = card ? card.offsetWidth + 15 : 200;

    carrusel.scrollBy({
        left: paso * 2 * direccion,
        behavior: 'smooth'
    });
}


function moverCarruselSeries(btn, direccion) {

    const carrusel = btn.parentElement.querySelector('.carousel');
    if (!carrusel) return;

    const card = carrusel.querySelector('.card');
    const paso = card ? card.offsetWidth + 15 : 200;

    carrusel.scrollBy({
        left: paso * 2 * direccion,
        behavior: 'smooth'
    });
}

function moverCarruselVideojuegos(btn, direccion) {

    const carrusel = btn.parentElement.querySelector('.carousel');
    if (!carrusel) return;

    const card = carrusel.querySelector('.card');
    const paso = card ? card.offsetWidth + 15 : 200;

    carrusel.scrollBy({
        left: paso * 2 * direccion,
        behavior: 'smooth'
    });
}

function moverCarruselGenerico(btn, direccion) {

    const carrusel = btn.parentElement.querySelector('.carousel');
    if (!carrusel) return;

    const card = carrusel.querySelector('.card');
    const paso = card ? card.offsetWidth + 15 : 200;

    carrusel.scrollBy({
        left: paso * 2 * direccion,
        behavior: 'smooth'
    });
}

function actualizarFlechas() {

    document.querySelectorAll('.carousel-wrapper').forEach(wrapper => {

        const carrusel = wrapper.querySelector('.carousel');
        const izquierda = wrapper.querySelector('.boton-carrusel.izquierda');
        const derecha = wrapper.querySelector('.boton-carrusel.derecha');

        if (!carrusel || !izquierda || !derecha) return;

        const maxScroll = carrusel.scrollWidth - carrusel.clientWidth;

        if (maxScroll <= 0) {
            izquierda.style.display = 'none';
            derecha.style.display = 'none';
        } else {
            izquierda.style.display = 'flex';
            derecha.style.display = 'flex';
        }
    });
}

window.addEventListener('load', actualizarFlechas);
window.addEventListener('resize', actualizarFlechas);