@extends('template.administrador')

@section('title', 'Preferencias')
@section('name_section', 'Preferencias')
@section('description', 'Personaliza algunas opciones de tu cuenta')
@section('item_section', 'Configuración')
@section('route_section', route('preferences.edit'))
@section('section', 'Preferencias')
@section('module', 'Cuenta')

@section('content')

<div class="row justify-content-center">
    <div class="col-12 col-lg-8">

        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4">

                <div class="mb-4">
                    <h5 class="fw-bold">Personalización del sistema</h5>
                    <p class="text-secondary small">
                        Configura tus preferencias de visualización y avisos.
                    </p>
                </div>

                @if(session('ok'))
                    <div class="alert alert-success">{{ session('ok') }}</div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger">{{ $errors->first() }}</div>
                @endif

                <form action="{{ route('preferences.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <label class="form-label fw-semibold">
                            Apariencia preferida
                        </label>

                        <select name="theme" class="form-select">
                            <option value="light"
                                @selected($preferences['theme'] === 'light')>
                                Modo claro
                            </option>

                            <option value="dark"
                                @selected($preferences['theme'] === 'dark')>
                                Modo oscuro
                            </option>
                        </select>

                        <small class="text-secondary">
                            Esta preferencia se guardará en la sesión.
                        </small>
                    </div>

                    <div class="form-check form-switch mb-4">
                        <input type="hidden" name="notifications" value="0">

                        <input
                            class="form-check-input"
                            type="checkbox"
                            role="switch"
                            name="notifications"
                            id="notifications"
                            value="1"
                            @checked($preferences['notifications'])
                        >

                        <label class="form-check-label fw-semibold"
                               for="notifications">
                            Recibir notificaciones del sistema
                        </label>
                    </div>

                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-check-circle me-1"></i>
                        Guardar preferencias
                    </button>

                </form>

            </div>
        </div>

    </div>
</div>

@endsection
