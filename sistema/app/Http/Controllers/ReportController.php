<?php
namespace App\Http\Controllers;

use App\Exports\SalesReportExport;
use App\Models\Sale;
use App\Models\User;
use App\Models\Category;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        return view('reports.index', $this->getReportData($request));
    }

    // Descarga el reporte PDF respetando los filtros seleccionados
    public function pdf(Request $request)
    {
        $data = $this->getReportData($request);

        return Pdf::loadView('reports.pdf', $data)
            ->setPaper('a4', 'portrait')
            ->download('reporte-ventas.pdf');
    }

    // Descarga el reporte Excel respetando los filtros seleccionados
    public function excel(Request $request)
    {
        $data = $this->getReportData($request);

        return Excel::download(
            new SalesReportExport($data['recentSales']),
            'reporte-ventas.xlsx'
        );
    }

    // Construye una sola vez la consulta utilizada por pantalla y exportaciones
    private function getReportData(Request $request): array
    {
        $request->validate(['from'=>'nullable|date','to'=>'nullable|date|after_or_equal:from','seller'=>'nullable|exists:users,id','category'=>'nullable|exists:categories,id']);
        $from = $request->input('from') ?: now()->startOfMonth()->toDateString();
        $to = $request->input('to') ?: now()->endOfMonth()->toDateString();
        $seller = $request->input('seller');
        $category = $request->input('category');
        $vendors = User::orderBy('name')->get();
        $categories = Category::orderBy('name')->get();
        $query = Sale::query()->whereDate('date','>=',$from)->whereDate('date','<=',$to)
            ->when($seller, fn($q)=>$q->where('user_id',$seller))
            ->when($category, fn($q)=>$q->whereHas('details.medicine',fn($m)=>$m->where('category_id',$category)));
        $totalSales = (clone $query)->sum('total');
        $salesCount = (clone $query)->count();
        $averageSale = $salesCount ? $totalSales/$salesCount : 0;
        $recentSales = (clone $query)->with(['customer','user'])->orderByDesc('date')->get();
        $daily = (clone $query)->selectRaw('DATE(date) as day, SUM(total) as revenue')->groupByRaw('DATE(date)')->pluck('revenue','day');
        $labels = []; $values = [];
        for ($day = Carbon::parse($from); $day->lte(Carbon::parse($to)); $day->addDay()) {
            $labels[] = $day->format('d/m/Y');
            $values[] = (float) ($daily[$day->toDateString()] ?? 0);
        }

        return compact('from','to','seller','category','vendors','categories','totalSales','salesCount','averageSale','recentSales','labels','values');
    }
}
