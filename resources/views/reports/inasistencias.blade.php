@extends('reports._layout', ['title' => 'REPORTE DE AUSENCIAS INJUSTIFICADAS'])

@section('content')
<table>
    <thead>
        <tr>
            <th>Apellidos y Nombres</th>
            <th>Código</th>
            <th>Área</th>
            <th>Fechas inasistidas</th>
            <th class="right">Total días</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($faltas as $row)
            <tr>
                <td>{{ $row['empleado']->apellidos }} {{ $row['empleado']->nombres }}</td>
                <td>{{ $row['empleado']->codigo }}</td>
                <td>{{ optional($row['empleado']->area)->nombre }}</td>
                <td>
                    @foreach ($row['fechas'] as $d)
                        {{ $d->isoFormat('dddd DD/MM/YYYY') }}@if(!$loop->last), @endif
                    @endforeach
                </td>
                <td class="right">{{ $row['total'] }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
@endsection
