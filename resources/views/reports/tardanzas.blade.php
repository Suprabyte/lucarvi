@extends('reports._layout', ['title' => 'REPORTE GENERAL DE TARDANZAS'])

@section('content')
<table>
    <thead>
        <tr>
            <th>Apellidos y Nombres</th>
            <th>DNI</th>
            <th class="center">Fecha</th>
            <th class="center">H. Ingreso Prog.</th>
            <th class="center">H. Marcada</th>
            <th class="right">Tolerancia</th>
            <th class="right">Tardanza Real (min)</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($rows as $r)
            <tr>
                <td>{{ $r->empleado->apellidos }} {{ $r->empleado->nombres }}</td>
                <td>{{ $r->empleado->dni }}</td>
                <td class="center">{{ \Carbon\Carbon::parse($r->fecha)->format('d/m/Y') }}</td>
                <td class="center">{{ $r->hora_ingreso ? \Carbon\Carbon::parse($r->fecha.' '.$r->hora_ingreso)->format('H:i') : '—' }}</td>
                <td class="center">{{ $r->hora_ingreso ?? '—' }}</td>
                <td class="right">5</td>
                <td class="right">{{ $r->min_tardanza }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
@endsection
