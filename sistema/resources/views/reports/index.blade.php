@extends('template.administrador')

@section('title', 'Reportes')
@section('name_section', 'Reportes')
@section('description', 'Resumen y análisis de las ventas de la farmacia')
@section('item_section', 'Inicio')
@section('route_section', route('reports.index'))
@section('section', 'Reportes')
@section('module', 'Administración')

@section('content')

{{-- Filtros por fecha --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form action="{{ route('reports.index') }}" method="GET" class="row g-3 align-items-end">

            <div class="col-12 col-md-4">
                <label for="from" class="form-label small fw-semibold">Fecha desde</label>
                <input type="date" name="from" id="from"
                       class="form-control" value="{{ $from }}">
            </div>

            <div class="col-12 col-md-4">
                <label for="to" class="form-label small fw-semibold">Fecha hasta</label>
                <input type="date" name="to" id="to"
                       class="form-control" value="{{ $to }}">
            </div>

            <div class="col-12 col-md-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-grow-1">
                    <i class="bi bi-funnel me-1"></i> Filtrar
                </button>

                <a href="{{ route('reports.index') }}"
                   class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-counterclockwise"></i>
                </a>

                <button type="button" onclick="window.print()"
                        class="btn btn-outline-dark">
                    <i class="bi bi-printer"></i>
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Indicadores principales --}}
<div class="row g-3 mb-4">

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="text-secondary small mb-2">Ingresos totales</div>
                <h4 class="fw-bold mb-0">
                    S/ {{ number_format($totalSales, 2) }}
                </h4>
                <i class="bi bi-cash-stack text-success fs-4"></i>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="text-secondary small mb-2">Ventas realizadas</div>
                <h4 class="fw-bold mb-0">{{ $salesCount }}</h4>
                <i class="bi bi-cart-check text-primary fs-4"></i>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="text-secondary small mb-2">IGV acumulado</div>
                <h4 class="fw-bold mb-0">
                    S/ {{ number_format($totalIgv, 2) }}
                </h4>
                <i class="bi bi-receipt text-info fs-4"></i>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="text-secondary small mb-2">Promedio por venta</div>
                <h4 class="fw-bold mb-0">
                    S/ {{ number_format($averageSale, 2) }}
                </h4>
                <i class="bi bi-graph-up-arrow text-warning fs-4"></i>
            </div>
        </div>
    </div>

</div>

{{-- Medicamentos más vendidos --}}
<div class="row g-3 mb-4">

    <div class="col-12 col-lg-7">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3">
                <h6 class="fw-bold mb-0">
                    <i class="bi bi-trophy me-2 text-warning"></i>
                    Medicamentos más vendidos
                </h6>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-sm">
                        <thead>
                            <tr>
                                <th>Medicamento</th>
                                <th class="text-center">Cantidad</th>
                                <th class="text-end">Ingresos</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topMedicines as $medicine)
                                <tr>
                                    <td>{{ $medicine->name }}</td>
                                    <td class="text-center">
                                        {{ $medicine->quantity_sold }}
                                    </td>
                                    <td class="text-end">
                                        S/ {{ number_format($medicine->revenue, 2) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-3">
                                        No hay ventas registradas en este periodo.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Stock bajo --}}
    <div class="col-12 col-lg-5">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3">
                <h6 class="fw-bold mb-0">
                    <i class="bi bi-exclamation-triangle me-2 text-danger"></i>
                    Medicamentos con stock bajo
                </h6>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-sm">
                        <thead>
                            <tr>
                                <th>Medicamento</th>
                                <th>Categoría</th>
                                <th class="text-center">Stock</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($lowStock as $medicine)
                                <tr>
                                    <td>{{ $medicine->name }}</td>
                                    <td>{{ $medicine->category->name ?? 'Sin categoría' }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-danger">
                                            {{ $medicine->stock }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-3">
                                        No hay medicamentos con stock bajo.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- Últimas ventas --}}
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <h6 class="fw-bold mb-0">
            <i class="bi bi-clock-history me-2 text-primary"></i>
            Últimas ventas registradas
        </h6>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover table-sm">
                <thead>
                    <tr>
                        <th>N.º</th>
                        <th>Fecha</th>
                        <th>Cliente</th>
                        <th>Vendedor</th>
                        <th class="text-end">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentSales as $sale)
                        <tr>
                            <td>{{ $sale->id }}</td>
                            <td>{{ $sale->date?->format('d/m/Y H:i') }}</td>
                            <td>{{ $sale->customer->name ?? 'Sin cliente' }}</td>
                            <td>{{ $sale->user->name ?? 'Sin vendedor' }}</td>
                            <td class="text-end">
                                S/ {{ number_format($sale->total, 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-3">
                                No hay ventas registradas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<style>
    @media print {
        #sidebar,
        .app-header,
        form,
        button {
            display: none !important;
        }

        .main-wrapper {
            width: 100% !important;
        }

        .app-content {
            padding: 0 !important;
            overflow: visible !important;
        }

        .card {
            break-inside: avoid;
            box-shadow: none !important;
            border: 1px solid #ddd !important;
        }
    }
</style>
@endpush
