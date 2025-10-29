@extends('reportes.pdf.layout')

@section('content')
<table>
    <thead>
    <tr>
        <th>Fecha</th>
        <th>Empleado</th>
        <th>DNI</th>
        <th>Hora Ingreso</th>
        <th class="right">Min Tardanza</th>
    </tr>
    </thead>
    <tbody>
    @forelse($rows as $r)
        <tr>
            <td>{{ $r->fecha }}</td>
            <td>{{ optional($r->empleado)->apellidos }} {{ optional($r->empleado)->nombres }}</td>
            <td>{{ optional($r->empleado)->dni }}</td>
            <td>{{ $r->hora_ingreso ?? '-' }}</td>
            <td class="right">{{ $r->min_tardanza }}</td>
        </tr>
    @empty
        <tr><td colspan="5" class="muted">Sin resultados.</td></tr>
    @endforelse
    </tbody>
</table>
@endsection
