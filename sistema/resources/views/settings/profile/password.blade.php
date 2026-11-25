@extends('template.administrador')

@section('title', 'Cambiar contraseña')
@section('name_section', 'Cambiar contraseña')
@section('description', 'Actualiza la seguridad de tu cuenta')
@section('item_section', 'Configuración')
@section('route_section', route('password.edit'))
@section('section', 'Contraseña')
@section('module', 'Cuenta')

@section('content')

<div class="row justify-content-center">
    <div class="col-12 col-lg-7">

        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4 p-lg-5">

                <div class="text-center mb-4">
                    <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                         style="width:70px;height:70px;background:#fff0f4;color:#dc3566">
                        <i class="bi bi-shield-lock fs-2"></i>
                    </div>

                    <h4 class="fw-bold">Seguridad de tu cuenta</h4>
                    <p class="text-secondary small">
                        Cambia tu contraseña periódicamente.
                    </p>
                </div>

                @if(session('ok'))
                    <div class="alert alert-success">{{ session('ok') }}</div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger">{{ $errors->first() }}</div>
                @endif

                <form action="{{ route('password.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Contraseña actual</label>
                        <input type="password" name="current_password"
                               class="form-control" required
                               autocomplete="current-password">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nueva contraseña</label>
                        <input type="password" name="password"
                               class="form-control" required minlength="8"
                               autocomplete="new-password">
                        <small class="text-secondary">
                            Mínimo 8 caracteres.
                        </small>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">
                            Confirmar nueva contraseña
                        </label>
                        <input type="password" name="password_confirmation"
                               class="form-control" required minlength="8"
                               autocomplete="new-password">
                    </div>

                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-lock me-1"></i>
                        Actualizar contraseña
                    </button>

                </form>

            </div>
        </div>

    </div>
</div>

@endsection
