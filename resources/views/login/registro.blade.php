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
                    <input type="text" name="name" class="form-control" placeholder="Nombre completo" value="{{ old('name') }}" required>
                    @error('name')
                        <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                    {{-- USUARIO --}}
                    <input type="text" name="nombre_usuario" class="form-control" placeholder="Nombre de usuario" value="{{ old('nombre_usuario') }}" required>
                    @error('nombre_usuario')
                        <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                    {{-- FECHA --}}
                    <input type="date" name="fecha_nacimiento" class="form-control" value="{{ old('fecha_nacimiento') }}" required>
                    @error('fecha_nacimiento')
                        <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                    {{-- EMAIL --}}
                    <input type="email" name="email" class="form-control" placeholder="Email" value="{{ old('email') }}" required>

                    @error('email')
                        <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                    {{-- PASSWORD --}}
                    <input type="password" name="password" class="form-control" placeholder="Contraseña" required>
                    @error('password')
                        <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                    {{-- CONFIRMAR --}}
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