<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Medicine;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SaleController extends Controller
{
    // Listado de ventas (con cliente y vendedor cargados)
    public function index()
    {
        $sales = Sale::with(['customer', 'user'])->latest('date')->get();

        return view('management.sales.index', compact('sales'));
    }

    // Formulario para registrar una venta
    public function create()
    {
        $customers = Customer::orderBy('name')->get();
        $medicines = Medicine::where('stock', '>', 0)->orderBy('name')->get();

        return view('management.sales.create', compact('customers', 'medicines'));
    }

    // Guardar la venta con sus detalles
    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'items' => 'required|array|min:1',
            'items.*.medicine_id' => 'required|distinct|exists:medicines,id',
            'items.*.amount' => 'required|integer|min:1',
        ], [
            'items.required' => 'Debe agregar al menos un medicamento.',
            'items.*.medicine_id.distinct' => 'No repita el mismo medicamento, aumente la cantidad.',
        ]);

        // Transacción: si algo falla, no se guarda nada
        $sale = DB::transaction(function () use ($request) {
            // 1. Crear la venta (el total se calcula después)
            $sale = Sale::create([
                'customer_id' => $request->customer_id,
                'user_id' => auth()->id(),
                'date' => now(),
                'total' => 0,
                'igv' => 0,
            ]);

            $total = 0;

            // 2. Registrar cada medicamento vendido
            foreach ($request->items as $item) {
                // lockForUpdate(): bloquea la fila hasta terminar, para que dos ventas
                // a la vez no vendan el mismo stock
                $medicine = Medicine::lockForUpdate()->find($item['medicine_id']);

                if ($medicine->stock < $item['amount']) {
                    // Lanzar la excepción deshace todo lo guardado en la transacción (rollback)
                    throw ValidationException::withMessages([
                        'items' => "Stock insuficiente para {$medicine->name} (disponible: {$medicine->stock}).",
                    ]);
                }

                // El precio se toma de la base de datos, no del formulario
                $subtotal = $medicine->price * $item['amount'];

                $sale->details()->create([
                    'medicine_id' => $medicine->id,
                    'amount' => $item['amount'],
                    'price_unit' => $medicine->price,
                    'subtotal' => $subtotal,
                ]);

                // 3. Descontar el stock
                $medicine->decrement('stock', $item['amount']);

                $total += $subtotal;
            }

            // 4. Guardar total e IGV (el precio ya incluye el 18% de IGV)
            $sale->update([
                'total' => $total,
                'igv' => round($total * 18 / 118, 2),
            ]);

            return $sale;
        });

        return to_route('sales.show', $sale)->with('ok', 'Venta registrada');
    }

    // Ver el detalle de una venta
    public function show(Sale $sale)
    {
        $sale->load(['customer', 'user', 'details.medicine']);

        return view('management.sales.show', compact('sale'));
    }

    // Anular venta: devuelve el stock y la elimina
    public function destroy(Sale $sale)
    {
        DB::transaction(function () use ($sale) {
            foreach ($sale->details as $detail) {
                Medicine::where('id', $detail->medicine_id)->increment('stock', $detail->amount);
            }

            // Los detalles se borran solos (cascadeOnDelete en la migración)
            $sale->delete();
        });

        return to_route('sales.index')->with('ok', 'Venta anulada y stock devuelto');
    }

    // Formulario para editar una venta sin cambiar su vendedor ni fecha
    public function edit(Sale $sale)
    {
        $sale->load(['customer', 'details.medicine']);
        $customers = Customer::orderBy('name')->get();
        $medicines = Medicine::orderBy('name')->get();

        return view('management.sales.edit', compact('sale', 'customers', 'medicines'));
    }

    // Actualizar la venta y ajustar el stock con integridad transaccional
    public function update(Request $request, Sale $sale)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'items' => 'required|array|min:1',
            'items.*.medicine_id' => 'required|distinct|exists:medicines,id',
            'items.*.amount' => 'required|integer|min:1',
        ], [
            'items.required' => 'Debe agregar al menos un medicamento.',
            'items.*.medicine_id.distinct' => 'No repita el mismo medicamento, aumente la cantidad.',
        ]);

        DB::transaction(function () use ($request, $sale) {
            $sale->load('details');

            // Devuelve primero todo el stock consumido por los detalles originales
            foreach ($sale->details as $detail) {
                Medicine::whereKey($detail->medicine_id)->increment('stock', $detail->amount);
            }

            // Los precios históricos se conservan por medicamento
            $historicalPrices = $sale->details->pluck('price_unit', 'medicine_id');
            $sale->details()->delete();

            $total = 0;

            foreach ($request->items as $item) {
                $medicine = Medicine::lockForUpdate()->findOrFail($item['medicine_id']);

                if ($medicine->stock < $item['amount']) {
                    throw ValidationException::withMessages([
                        'items' => "Stock insuficiente para {$medicine->name} (disponible: {$medicine->stock}).",
                    ]);
                }

                $priceUnit = $historicalPrices->has($medicine->id)
                    ? (float) $historicalPrices[$medicine->id]
                    : (float) $medicine->price;
                $subtotal = $priceUnit * $item['amount'];

                $sale->details()->create([
                    'medicine_id' => $medicine->id,
                    'amount' => $item['amount'],
                    'price_unit' => $priceUnit,
                    'subtotal' => $subtotal,
                ]);

                $medicine->decrement('stock', $item['amount']);
                $total += $subtotal;
            }

            $sale->update([
                'customer_id' => $request->customer_id,
                'total' => $total,
                'igv' => round($total * 18 / 118, 2),
            ]);
        });

        return to_route('sales.show', $sale)->with('ok', 'Venta actualizada');
    }
}