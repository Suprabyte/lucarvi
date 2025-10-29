@extends('reportes.pdf.layout')

@section('content')
<table>
    <thead>
    <tr>
        <th>Empleado</th>
        <th>DNI</th>
        <th class="right">Jornadas</th>
        <th class="right">Min Trab.</th>
        <th class="right">Min Tard.</th>
        <th class="right">Extra 25</th>
        <th class="right">Extra 35</th>
        <th class="right">Extra 100</th>
    </tr>
    </thead>
    <tbody>
    @forelse($resumen as $r)
        <tr>
            <td>{{ $r['empleado'] }}</td>
            <td>{{ $r['dni'] }}</td>
            <td class="right">{{ $r['jornadas'] }}</td>
            <td class="right">{{ $r['min_trab'] }}</td>
            <td class="right">{{ $r['min_tard'] }}</td>
            <td class="right">{{ $r['min_extra25'] }}</td>
            <td class="right">{{ $r['min_extra35'] }}</td>
            <td class="right">{{ $r['min_extra100'] }}</td>
        </tr>
    @empty
        <tr><td colspan="8" class="muted">Sin resultados.</td></tr>
    @endforelse
    </tbody>
</table>
@endsection
