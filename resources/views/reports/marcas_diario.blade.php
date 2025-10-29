@extends('reports._layout', ['title' => 'REPORTE DE MARCAS DIARIO'])

@section('content')
@foreach ($rows as $empleadoId => $marcas)
    <table style="margin-bottom: 10px;">
        <thead>
            <tr>
                <th colspan="5">
                    {{ optional($marcas->first()->empleado)->apellidos }} {{ optional($marcas->first()->empleado)->nombres }}
                    — DNI: {{ optional($marcas->first()->empleado)->dni }}
                </th>
            </tr>
            <tr>
                <th>Fecha</th>
                <th>MAR1</th>
                <th>MAR2</th>
                <th>MAR3</th>
                <th>MAR4</th>
            </tr>
        </thead>
        <tbody>
            @php
                $byDate = $marcas->groupBy(fn($m) => $m->timestamp->format('Y-m-d'));
            @endphp
            @foreach ($byDate as $fecha => $marcasDia)
                @php
                    $times = $marcasDia->sortBy('timestamp')->pluck('timestamp')->map->format('H:i')->values();
                @endphp
                <tr>
                    <td>{{ \Carbon\Carbon::parse($fecha)->format('d/m/Y') }}</td>
                    <td>{{ $times[0] ?? '' }}</td>
                    <td>{{ $times[1] ?? '' }}</td>
                    <td>{{ $times[2] ?? '' }}</td>
                    <td>{{ $times[3] ?? '' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endforeach
@endsection
