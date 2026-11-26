@extends('template.administrador')

@section('title', 'Ventas')
@section('name_section', 'Ventas')
@section('description', 'Editar venta registrada')
@section('item_section', 'Editar')
@section('route_section', route('sales.index'))
@section('section', 'Ventas')
@section('module', 'Gestión')

@section('content')
<div class="card bg-white border-0 shadow-sm border-5 border-top border-secondary">
    <div class="card-body">
        <form action="{{ route('sales.update', $sale) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row">
                <div class="col-12 col-md-6 mb-3">
                    <label for="customer_id" class="form-label">Cliente</label>
                    <select name="customer_id" id="customer_id" class="form-select @error('customer_id') is-invalid @enderror">
                        <option value="">-- Seleccione --</option>
                        @foreach ($customers as $customer)
                            <option value="{{ $customer->id }}" @selected(old('customer_id', $sale->customer_id) == $customer->id)>
                                {{ $customer->name }} - DNI {{ $customer->dni }}
                            </option>
                        @endforeach
                    </select>
                    @error('customer_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12 col-md-6 mb-3">
                    <label for="seller_name" class="form-label">Vendedor</label>
                    <input type="text" id="seller_name" class="form-control" value="{{ $sale->user->name }}" disabled>
                </div>
            </div>

            <h6 class="text-uppercase small text-muted mt-2 mb-2">Medicamentos</h6>

            @if ($errors->has('items') || $errors->has('items.*'))
                <div class="alert alert-danger py-2">
                    @foreach ($errors->get('items') as $message)
                        <div>{{ $message }}</div>
                    @endforeach
                    @foreach ($errors->get('items.*') as $messages)
                        @foreach ((array) $messages as $message)
                            <div>{{ $message }}</div>
                        @endforeach
                    @endforeach
                </div>
            @endif

            <table class="table table-sm align-middle">
                <thead>
                    <tr>
                        <th class="small text-uppercase">Medicamento</th>
                        <th class="small text-uppercase text-center" style="width: 120px">Cantidad</th>
                        <th class="small text-uppercase text-end" style="width: 120px">Precio</th>
                        <th class="small text-uppercase text-end" style="width: 120px">Subtotal</th>
                        <th style="width: 50px"></th>
                    </tr>
                </thead>
                <tbody id="items">
                    @foreach (old('items', $sale->details->map(fn ($detail) => ['medicine_id' => $detail->medicine_id, 'amount' => $detail->amount, 'price_unit' => $detail->price_unit])->values()->all()) as $i => $item)
                    <tr>
                        <td>
                            <select name="items[{{ $i }}][medicine_id]" class="form-select form-select-sm">
                                <option value="" data-price="0">-- Seleccione --</option>
                                @foreach ($medicines as $medicine)
                                    @php
                                        $historicalPrice = (float) ($item['price_unit'] ?? $medicine->price);
                                        $selectedPrice = ($item['medicine_id'] ?? '') == $medicine->id
                                            ? $historicalPrice
                                            : (float) $medicine->price;
                                    @endphp
                                    <option value="{{ $medicine->id }}" data-price="{{ $selectedPrice }}" @selected(($item['medicine_id'] ?? '') == $medicine->id)>
                                        {{ $medicine->name }} (stock: {{ $medicine->stock }})
                                    </option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <input type="number" name="items[{{ $i }}][amount]" min="1" class="form-control form-control-sm text-center" value="{{ $item['amount'] ?? 1 }}">
                        </td>
                        <td class="text-end price">S/ 0.00</td>
                        <td class="text-end subtotal">S/ 0.00</td>
                        <td class="text-center">
                            <button type="button" class="btn btn-sm btn-outline-danger remove-item" title="Quitar"><i class="bi bi-trash"></i></button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" class="text-end fw-semibold">Total (incluye IGV)</td>
                        <td class="text-end fw-bold" id="total">S/ 0.00</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>

            <button type="button" id="add-item" class="btn btn-outline-primary btn-sm mb-4">
                <i class="bi bi-plus-lg me-1"></i>Agregar medicamento
            </button>

            <div>
                <button type="submit" class="btn btn-primary text-white">Actualizar venta</button>
                <a href="{{ route('sales.index') }}" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const tbody = document.getElementById('items');

    function recalculate() {
        let total = 0;
        tbody.querySelectorAll('tr').forEach(row => {
            const option = row.querySelector('select').selectedOptions[0];
            const price = parseFloat(option?.dataset.price || 0);
            const amount = parseInt(row.querySelector('input').value || 0);
            const subtotal = price * amount;

            row.querySelector('.price').textContent = 'S/ ' + price.toFixed(2);
            row.querySelector('.subtotal').textContent = 'S/ ' + subtotal.toFixed(2);
            total += subtotal;
        });

        document.getElementById('total').textContent = 'S/ ' + total.toFixed(2);
    }

    document.getElementById('add-item').addEventListener('click', () => {
        const index = tbody.querySelectorAll('tr').length;
        const templateRow = tbody.querySelector('tr');
        if (!templateRow) return;

        const row = templateRow.cloneNode(true);
        row.querySelectorAll('select, input').forEach(field => {
            field.name = field.name.replace(/items\[\d+\]/, 'items[' + index + ']');
            if (field.tagName === 'INPUT') field.value = 1;
        });
        row.querySelector('select').selectedIndex = 0;
        tbody.appendChild(row);
        recalculate();
    });

    tbody.addEventListener('click', e => {
        if (e.target.closest('.remove-item')) {
            e.target.closest('tr').remove();
            recalculate();
        }
    });

    tbody.addEventListener('input', recalculate);
    tbody.addEventListener('change', recalculate);
    recalculate();
</script>
@endpush