<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="icon" href="{{ Storage::url('favicon.ico') }}">
    <link rel="stylesheet" href="{{ asset('adminlte/dist/css/adminlte.min.css') }}">
    <link rel="stylesheet" href="{{ asset('adminlte/dist/css/mediahub-login.css') }}">
</head>
<body class="mediahub-login-body">
    <div class="login-wrapper">
        <div class="register-box">
            <div class="register-logo">
                <b>MEDIAHUB</b>
                <div class="arcade-subtitle">ACCESO AL SISTEMA</div>
            </div>
            <div class="card mediahub-card">
                <p class="mensaje-login">INICIAR SESIÓN</p>
                <form action="{{ route('iniciar_sesion') }}" method="POST">
                    @csrf
                    <div class="input-group">
                        <input type="email" name="email" class="form-control" placeholder="Email" value="{{ old('email') }}" required>
                    </div>
                    <div class="input-group">
                        <input type="password" name="password" class="form-control" placeholder="Contraseña" required>
                    </div>
                    @if ($errors->has('login'))
                        <div class="alert alert-danger">
                            {{ $errors->first('login') }}
                        </div>
                    @endif
                    <div class="buttons-wrapper">
                        <a href="{{ route('registro') }}">Crear cuenta</a>
                        <button type="submit" class="btn btn-primary">
                            ENTRAR
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>