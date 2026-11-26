<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de ventas</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #343a40; }
        h1 { font-size: 18px; margin-bottom: 4px; }
        .periodo { margin-bottom: 16px; color: #6c757d; }
        .kpis { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
        .kpis td { width: 33.33%; border: 1px solid #ddd; padding: 8px; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #343a40; color: #fff; text-align: left; }
        th, td { border: 1px solid #ddd; padding: 6px; }
        .right { text-align: right; }
    </style>
</head>
<body>
    <h1>Reporte de ventas</h1>
    <div class="periodo">Período: {{ $from }} al {{ $to }}</div>

    <table class="kpis">
        <tr>
            <td><strong>Total vendido</strong><br>S/ {{ number_format($totalSales, 2) }}</td>
            <td><strong>Número de ventas</strong><br>{{ $salesCount }}</td>
            <td><strong>Ticket promedio</strong><br>S/ {{ number_format($averageSale, 2) }}</td>
        </tr>
    </table>

    <table>
        <thead>
            <tr><th>Fecha</th><th>Cliente</th><th>Vendedor</th><th class="right">Total</th></tr>
        </thead>
        <tbody>
            @forelse($recentSales as $sale)
            <tr>
                <td>{{ $sale->date?->format('d/m/Y H:i') }}</td>
                <td>{{ $sale->customer->name ?? 'Sin cliente' }}</td>
                <td>{{ $sale->user->name ?? 'Sin vendedor' }}</td>
                <td class="right">S/ {{ number_format($sale->total, 2) }}</td>
            </tr>
            @empty
            <tr><td colspan="4">No hay ventas para los filtros seleccionados.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
