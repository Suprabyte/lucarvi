@extends('reports._layout', ['title' => 'REPORTE DE DATOS VALORIZADOS'])

@section('content')
<table>
    <thead>
        <tr>
            <th>Apellidos y Nombres</th>
            <th>DNI</th>
            <th class="right">Tot. Horas Labor.</th>
            <th class="right">Valor/Hora</th>
            <th class="right">Monto H. Labor</th>
            <th class="right">Tot. Tardanza</th>
            <th class="right">Monto Tardanza</th>
            <th class="right">HE 1.25 (min / S/.)</th>
            <th class="right">HE 1.35 (min / S/.)</th>
            <th class="right">HE 2.00 (min / S/.)</th>
            <th class="right">Monto a Pagar</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($rows as $r)
            @php
                $horas = number_format(($r['minLab'] ?? 0)/60, 2);
            @endphp
            <tr>
                <td>{{ $r['empleado']->apellidos }} {{ $r['empleado']->nombres }}</td>
                <td>{{ $r['empleado']->dni }}</td>
                <td class="right">{{ $horas }}</td>
                <td class="right">{{ number_format(request('tarifa_hora', 0), 2) }}</td>
                <td class="right">{{ number_format($r['montoHoras'], 2) }}</td>
                <td class="right">{{ (int)$r['minTar'] }}</td>
                <td class="right">{{ number_format($r['montoTard'], 2) }}</td>
                <td class="right">{{ (int)$r['he25'] }} / {{ number_format($r['montoHE125'], 2) }}</td>
                <td class="right">{{ (int)$r['he35'] }} / {{ number_format($r['montoHE135'], 2) }}</td>
                <td class="right">{{ (int)$r['he100'] }} / {{ number_format($r['montoHE200'], 2) }}</td>
                <td class="right totals">{{ number_format($r['totalPagar'], 2) }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
@endsection
