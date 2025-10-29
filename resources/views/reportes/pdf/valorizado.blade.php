@extends('reportes.pdf.layout')

@section('content')
<div class="meta">Tarifa por hora: S/. {{ number_format($tarifa, 2) }}</div>
<table>
    <thead>
    <tr>
        <th>Fecha</th>
        <th>Empleado</th>
        <th>DNI</th>
        <th class="right">Min Trab.</th>
        <th class="right">Horas</th>
        <th class="right">Importe (S/.)</th>
    </tr>
    </thead>
    <tbody>
    @php $total = 0; @endphp
    @forelse($rows as $x)
        @php $total += $x['importe']; @endphp
        <tr>
            <td>{{ $x['r']->fecha }}</td>
            <td>{{ optional($x['r']->empleado)->apellidos }} {{ optional($x['r']->empleado)->nombres }}</td>
            <td>{{ optional($x['r']->empleado)->dni }}</td>
            <td class="right">{{ $x['r']->min_trabajados }}</td>
            <td class="right">{{ number_format($x['horas'], 2) }}</td>
            <td class="right">{{ number_format($x['importe'], 2) }}</td>
        </tr>
    @empty
        <tr><td colspan="6" class="muted">Sin resultados.</td></tr>
    @endforelse
    <tr>
        <td colspan="5" class="right"><strong>Total</strong></td>
        <td class="right"><strong>{{ number_format($total, 2) }}</strong></td>
    </tr>
    </tbody>
</table>
@endsection
