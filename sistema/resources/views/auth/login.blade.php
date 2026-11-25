<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Acceso | FARM SJB</title>

    <link rel="stylesheet" href="{{ asset('css/bootstrap.css') }}">

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            margin: 0;
            background: #edf1f7;
            font-family: Arial, sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 25px;
        }

        .login-container {
            width: 100%;
            max-width: 1000px;
            min-height: 590px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            background: white;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(24, 37, 54, .15);
        }

        .login-brand {
            background: linear-gradient(145deg, #172536, #263f5a);
            color: white;
            padding: 55px 45px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
        }

        .login-brand::before,
        .login-brand::after {
            content: "";
            position: absolute;
            border: 1px solid rgba(255,255,255,.10);
            border-radius: 50%;
            width: 330px;
            height: 330px;
        }

        .login-brand::before {
            right: -170px;
            top: -100px;
        }

        .login-brand::after {
            left: -190px;
            bottom: -180px;
            width: 420px;
            height: 420px;
        }

        .brand-logo {
            width: 65px;
            height: 65px;
            background: #dc3566;
            border-radius: 18px;
            display: grid;
            place-items: center;
            font-size: 36px;
            font-weight: bold;
            position: relative;
            z-index: 1;
        }

        .brand-content {
            position: relative;
            z-index: 1;
        }

        .brand-content h1 {
            font-size: 34px;
            font-weight: 800;
            margin-bottom: 15px;
        }

        .brand-content p {
            color: #cbd5e1;
            line-height: 1.8;
        }

        .brand-footer {
            position: relative;
            z-index: 1;
            color: #aebed0;
            font-size: 12px;
        }

        .login-form {
            padding: 55px 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .login-form h2 {
            font-weight: 800;
            color: #192b40;
            font-size: 29px;
        }

        .login-form .subtitle {
            color: #8793a2;
            margin-bottom: 32px;
        }

        .form-label {
            color: #34445a;
            font-weight: 600;
            font-size: 13px;
        }

        .form-control {
            padding: 13px 15px;
            border: 1px solid #dce3ec;
            border-radius: 10px;
            background: #f9fafc;
        }

        .form-control:focus {
            border-color: #dc3566;
            box-shadow: 0 0 0 3px rgba(220,53,102,.10);
            background: white;
        }

        .btn-login {
            background: #dc3566;
            color: white;
            border: none;
            border-radius: 10px;
            padding: 14px;
            font-weight: 700;
            transition: .2s;
        }

        .btn-login:hover {
            background: #c32957;
            color: white;
            transform: translateY(-1px);
        }

        .password-wrap {
            position: relative;
        }

        .password-wrap input {
            padding-right: 48px;
        }

        .toggle-password {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            border: none;
            background: transparent;
            color: #7d8998;
        }

        @media(max-width: 768px) {
            .login-container {
                grid-template-columns: 1fr;
                max-width: 440px;
            }

            .login-brand {
                padding: 30px;
                min-height: 230px;
            }

            .brand-content h1 {
                font-size: 25px;
            }

            .brand-footer {
                margin-top: 25px;
            }

            .login-form {
                padding: 35px 28px;
            }
        }
    </style>
</head>

<body>

<div class="login-container">

    <section class="login-brand">

        <div class="brand-logo">+</div>

        <div class="brand-content">
            <span class="text-uppercase small fw-bold"
                  style="letter-spacing:3px;color:#f18ba9">
                FARM SJB
            </span>

            <h1>Tu farmacia,<br>más cerca de ti.</h1>

            <p>
                Plataforma integral para la administración,
                control de medicamentos, ventas y atención
                a nuestros clientes.
            </p>
        </div>

        <div class="brand-footer">
            Sistema de Venta de Medicamentos
            <br>
            San Juan Bautista © {{ date('Y') }}
        </div>

    </section>


    <section class="login-form">

        <div class="mb-4">
            <span class="badge rounded-pill px-3 py-2 mb-3"
                  style="background:#fff0f4;color:#dc3566">
                ACCESO AL SISTEMA
            </span>

            <h2>¡Bienvenido!</h2>

            <p class="subtitle">
                Ingresa tus credenciales para continuar.
            </p>
        </div>


        @if(session('ok'))
            <div class="alert alert-success">
                {{ session('ok') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger">
                {{ $errors->first() }}
            </div>
        @endif


        <form action="{{ route('login.store') }}" method="POST">

            @csrf

            <div class="mb-4">
                <label for="email" class="form-label">
                    Correo electrónico
                </label>

                <input
                    type="email"
                    name="email"
                    id="email"
                    class="form-control"
                    placeholder="ejemplo@farmacia.com"
                    value="{{ old('email') }}"
                    autocomplete="username"
                    required
                    autofocus
                >
            </div>


            <div class="mb-3">
                <label for="password" class="form-label">
                    Contraseña
                </label>

                <div class="password-wrap">
                    <input
                        type="password"
                        name="password"
                        id="password"
                        class="form-control"
                        placeholder="Ingresa tu contraseña"
                        autocomplete="current-password"
                        required
                    >

                    <button
                        type="button"
                        class="toggle-password"
                        onclick="togglePassword()"
                        aria-label="Mostrar u ocultar contraseña">
                        <span id="passwordIcon">◉</span>
                    </button>
                </div>
            </div>


            <div class="form-check mb-4">
                <input
                    type="checkbox"
                    class="form-check-input"
                    id="remember"
                    name="remember"
                    value="1"
                >

                <label class="form-check-label small text-secondary"
                       for="remember">
                    Recordar mi sesión
                </label>
            </div>


            <button type="submit" class="btn btn-login w-100">
                Iniciar sesión →
            </button>

        </form>

        <p class="text-center text-secondary small mt-4 mb-0">
            Acceso exclusivo para personal autorizado.
        </p>

    </section>

</div>

<script>
    function togglePassword() {
        const password = document.getElementById('password');
        const icon = document.getElementById('passwordIcon');

        if (password.type === 'password') {
            password.type = 'text';
            icon.textContent = '◎';
        } else {
            password.type = 'password';
            icon.textContent = '◉';
        }
    }
</script>

</body>
</html>
