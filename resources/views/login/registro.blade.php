<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro</title>
    <link rel="icon" href="{{ Storage::url('favicon.ico') }}">
    <link rel="stylesheet" href="{{ asset('adminlte/dist/css/adminlte.min.css') }}">
    <link rel="stylesheet" href="{{ asset('adminlte/dist/css/mediahub-login.css') }}">
</head>
<body class="mediahub-login-body">
    <div class="login-wrapper">
        <div class="register-box">
            {{-- LOGO --}}
            <div class="register-logo">
                <b>MEDIAHUB</b>
                <div class="arcade-subtitle">INSERTE SUS DATOS</div>
            </div>
            {{-- CARD --}}
            <div class="card mediahub-card mediahub-card-register">
                <p class="mensaje-login">CREAR USUARIO</p>
                <form action="{{ route('validar_registro') }}" method="POST">
                    @csrf
                    {{-- NOMBRE --}}
                    <label for="name" class="resgistro-label">Nombre completo</label>
                    <input type="text" name="name" class="form-control" placeholder="Nombre completo" value="{{ old('name') }}" required>
                    @error('name')
                        <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                    {{-- USUARIO --}}
                    <label for="nombre_usuario" class="resgistro-label">Nombre de usuario</label>
                    <input type="text" name="nombre_usuario" class="form-control" placeholder="Nombre de usuario" value="{{ old('nombre_usuario') }}" required>
                    @error('nombre_usuario')
                        <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                    {{-- FECHA --}}
                    <label for="fecha_nacimiento" class="resgistro-label">Fecha de nacimiento</label>
                    <input type="date" name="fecha_nacimiento" class="form-control" value="{{ old('fecha_nacimiento') }}" required>
                    @error('fecha_nacimiento')
                        <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                    {{-- EMAIL --}}
                    <label for="email" class="resgistro-label">Email</label>
                    <input type="email" name="email" class="form-control" placeholder="Email" value="{{ old('email') }}" required>
                    @error('email')
                        <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                    {{-- PASSWORD --}}
                    <label for="password" class="resgistro-label">Contraseña</label>
                    <input type="password" name="password" class="form-control" placeholder="Contraseña" required>
                    @error('password')
                        <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                    {{-- CONFIRMAR --}}
                    <label for="password_confirmation" class="resgistro-label">Confirmar contraseña</label>
                    <input type="password" name="password_confirmation" class="form-control" placeholder="Confirmar contraseña" required>
                    {{-- BOTONES--}}
                    <div class="buttons-wrapper">
                        <a href="{{ route('login') }}">Volver al login</a>
                        <button type="submit" class="btn btn-primary">
                            GUARDAR DATOS
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>