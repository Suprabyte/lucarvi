@extends('reports._layout', ['title' => 'REPORTE DE ASISTENCIA'])

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
            <th class="right">Min. Trab.</th>
            <th class="right">Tardanza</th>
            <th class="right">HE 1.25</th>
            <th class="right">HE 1.35</th>
            <th class="right">HE 2.00</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($rows as $r)
            <tr>
                <td>{{ $r->empleado->apellidos }} {{ $r->empleado->nombres }}</td>
                <td>{{ $r->empleado->dni }}</td>
                <td class="center">{{ \Carbon\Carbon::parse($r->fecha)->format('d/m/Y') }}</td>
                <td class="center">{{ $r->hora_ingreso }}</td>
                <td class="center">{{ $r->hora_salida_refrigerio }}</td>
                <td class="center">{{ $r->hora_retorno_refrigerio }}</td>
                <td class="center">{{ $r->hora_salida }}</td>
                <td class="right">{{ $r->min_trabajados }}</td>
                <td class="right">{{ $r->min_tardanza }}</td>
                <td class="right">{{ $r->min_extra_25 }}</td>
                <td class="right">{{ $r->min_extra_35 }}</td>
                <td class="right">{{ $r->min_extra_100 }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
@endsection
