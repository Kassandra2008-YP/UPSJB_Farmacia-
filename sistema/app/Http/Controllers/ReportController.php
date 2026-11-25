<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Medicine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        // Obtener filtros de fecha
        $from = $request->input('from');
        $to = $request->input('to');

        // Consultar ventas según el periodo seleccionado
        $salesQuery = Sale::query()
            ->when($from, function ($query) use ($from) {
                $query->whereDate('date', '>=', $from);
            })
            ->when($to, function ($query) use ($to) {
                $query->whereDate('date', '<=', $to);
            });

        // Indicadores principales
        $totalSales = (clone $salesQuery)->sum('total');
        $salesCount = (clone $salesQuery)->count();
        $totalIgv = (clone $salesQuery)->sum('igv');

        $averageSale = $salesCount > 0
            ? $totalSales / $salesCount
            : 0;

        // Últimas ventas
        $recentSales = (clone $salesQuery)
            ->with(['customer', 'user'])
            ->orderByDesc('date')
            ->limit(10)
            ->get();

        // Medicamentos más vendidos
        $topMedicines = DB::table('detail_sales')
            ->join('sales', 'detail_sales.sale_id', '=', 'sales.id')
            ->join('medicines', 'detail_sales.medicine_id', '=', 'medicines.id')
            ->when($from, function ($query) use ($from) {
                $query->whereDate('sales.date', '>=', $from);
            })
            ->when($to, function ($query) use ($to) {
                $query->whereDate('sales.date', '<=', $to);
            })
            ->select(
                'medicines.id',
                'medicines.name',
                DB::raw('SUM(detail_sales.amount) as quantity_sold'),
                DB::raw('SUM(detail_sales.subtotal) as revenue')
            )
            ->groupBy('medicines.id', 'medicines.name')
            ->orderByDesc('quantity_sold')
            ->limit(5)
            ->get();

        // Medicamentos con stock bajo
        $lowStock = Medicine::with('category')
            ->where('stock', '<=', 5)
            ->orderBy('stock')
            ->get();

        // Enviar todas las variables a la vista
        return view('reports.index', compact(
            'totalSales',
            'salesCount',
            'totalIgv',
            'averageSale',
            'recentSales',
            'topMedicines',
            'lowStock',
            'from',
            'to'
        ));
    }
}