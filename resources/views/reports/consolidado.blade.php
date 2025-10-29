@extends('reports._layout', ['title' => 'REPORTE DE DATOS CONSOLIDADOS'])

@section('content')
<table>
    <thead>
        <tr>
            <th>Apellidos y Nombres</th>
            <th>DNI</th>
            <th class="right">Horas Ordinarias</th>
            <th class="right">Tardanzas (min)</th>
            <th class="right">Faltas</th>
            <th class="right">HE 1.25</th>
            <th class="right">HE 1.35</th>
            <th class="right">HE 2.00</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($rows as $r)
            <tr>
                <td>{{ $r['empleado']->apellidos }} {{ $r['empleado']->nombres }}</td>
                <td>{{ $r['empleado']->dni }}</td>
                <td class="right">{{ number_format(($r['minLab'] ?? 0)/60, 2) }}</td>
                <td class="right">{{ (int)$r['tard'] }}</td>
                <td class="right">{{ (int)$r['faltas'] }}</td>
                <td class="right">{{ (int)$r['he25'] }}</td>
                <td class="right">{{ (int)$r['he35'] }}</td>
                <td class="right">{{ (int)$r['he100'] }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
@endsection
