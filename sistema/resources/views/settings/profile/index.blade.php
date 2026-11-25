@extends('template.administrador')

@section('title', 'Mi Perfil')
@section('name_section', 'Mi Perfil')
@section('description', 'Consulta y actualiza tus datos personales')
@section('item_section', 'Configuración')
@section('route_section', route('profile.edit'))
@section('section', 'Mi Perfil')
@section('module', 'Cuenta')

@section('content')

<div class="row justify-content-center">
    <div class="col-12 col-xl-8">

        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4 p-lg-5">

                <div class="text-center mb-4">
                    <div class="rounded-circle d-inline-flex align-items-center justify-content-center text-white fw-bold"
                         style="width:85px;height:85px;background:#dc3566;font-size:30px">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>

                    <h4 class="fw-bold mt-3 mb-1">
                        {{ $user->name }}
                    </h4>

                    <p class="text-secondary small">
                        {{ $user->role->name ?? 'Usuario del sistema' }}
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

                <form action="{{ route('profile.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nombre completo</label>
                        <input type="text" name="name"
                               class="form-control"
                               value="{{ old('name', $user->name) }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Correo electrónico</label>
                        <input type="email" name="email"
                               class="form-control"
                               value="{{ old('email', $user->email) }}" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Teléfono</label>
                        <input type="text" name="phone"
                               class="form-control"
                               value="{{ old('phone', $user->phone) }}">
                    </div>

                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-check-circle me-1"></i>
                        Guardar cambios
                    </button>

                </form>

            </div>
        </div>

    </div>
</div>

@endsection
