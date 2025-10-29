@extends('reports._layout', ['title' => 'REPORTE DE ASISTENCIA / PRODUCTIVIDAD'])

@section('content')
<table>
    <thead>
        <tr>
            <th>Apellidos y Nombres</th>
            <th>DNI</th>
            <th class="center">Fecha</th>
            <th class="center">Ing.</th>
            <th class="center">Sal. Ref.</th>
            <th class="center">Ret. Ref.</th>
            <th class="center">Salida</th>
            <th class="right">Horas Trab.</th>
            <th class="right">HE Tot.</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($rows as $r)
            @php
                $horas = number_format(($r->min_trabajados ?? 0) / 60, 2);
                $heTot = (int)($r->min_extra_25 + $r->min_extra_35 + $r->min_extra_100);
            @endphp
            <tr>
                <td>{{ $r->empleado->apellidos }} {{ $r->empleado->nombres }}</td>
                <td>{{ $r->empleado->dni }}</td>
                <td class="center">{{ \Carbon\Carbon::parse($r->fecha)->format('d/m/Y') }}</td>
                <td class="center">{{ $r->hora_ingreso }}</td>
                <td class="center">{{ $r->hora_salida_refrigerio }}</td>
                <td class="center">{{ $r->hora_retorno_refrigerio }}</td>
                <td class="center">{{ $r->hora_salida }}</td>
                <td class="right">{{ $horas }}</td>
                <td class="right">{{ $heTot }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
@endsection
