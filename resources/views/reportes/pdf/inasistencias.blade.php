@extends('reportes.pdf.layout')

@section('content')
<table>
    <thead>
    <tr>
        <th>Fecha</th>
        <th>Empleado</th>
        <th>DNI</th>
        <th>Observación</th>
    </tr>
    </thead>
    <tbody>
    @forelse($rows as $r)
        <tr>
            <td>{{ $r->fecha }}</td>
            <td>{{ optional($r->empleado)->apellidos }} {{ optional($r->empleado)->nombres }}</td>
            <td>{{ optional($r->empleado)->dni }}</td>
            <td class="muted">Sin ingreso/salida registrada.</td>
        </tr>
    @empty
        <tr><td colspan="4" class="muted">Sin resultados.</td></tr>
    @endforelse
    </tbody>
</table>
@endsection
