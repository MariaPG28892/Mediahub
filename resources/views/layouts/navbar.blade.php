<nav class="navbar-arcade">
    <div class="menu-toggle" onclick="toggleMenu()">
        ☰
    </div>
    <div class="navbar-menu">
        <div class="navbar-izquierda">
            <a href="{{ route('inicio') }}" class="nav-logo">MEDIAHUB</a>
        </div>
        
        <div class="navbar-centro">
            <a href="{{route('inicio_peliculas')}}" class="nav-link">Películas</a>
            <a href="{{route('inicio_series')}}" class="nav-link">Series</a>
            <a href="{{route('inicio_videojuegos')}}" class="nav-link">Videojuegos</a>
        </div>

        <div class="navbar-derecha">
            <a href="{{route('perfil_usuario')}}" class="nav-link">Perfil</a>

            <form method="POST" action="{{route('cerrar_sesion')}}">
                @csrf
                <button type="submit" class="nav-link logout-btn">
                    Cerrar sesión
                </button>
            </form>
        </div>
    </div>
</nav>

<script>
function toggleMenu() {
    document.querySelector('.navbar-menu').classList.toggle('active');
}
</script>