<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SalesReportExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    public function __construct(private Collection $sales)
    {
    }

    // Devuelve las columnas visibles en el reporte de ventas
    public function collection(): Collection
    {
        return $this->sales->map(fn ($sale) => [
            'fecha' => $sale->date?->format('d/m/Y H:i'),
            'cliente' => $sale->customer->name ?? 'Sin cliente',
            'vendedor' => $sale->user->name ?? 'Sin vendedor',
            'total' => (float) $sale->total,
        ]);
    }

    public function headings(): array
    {
        return ['Fecha', 'Cliente', 'Vendedor', 'Total'];
    }
}
