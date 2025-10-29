@extends('pdf.layout')

@section('content')
    {{-- Puedes pintar tu rango, filtros, etc. --}}
    @if(!empty($rango))
        <p class="small"><span class="bold">Fechas:</span> {{ $rango }}</p>
    @endif

    <table class="b">
        <thead>
            <tr>
                <th class="b">Fecha</th>
                <th class="b">Empleado</th>
                <th class="b">DNI</th>
                <th class="b">Observación</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rows as $r)
                <tr>
                    <td class="b small">{{ $r->fecha }}</td>
                    <td class="b small">{{ $r->empleado }}</td>
                    <td class="b small">{{ $r->dni }}</td>
                    <td class="b small">{{ $r->obs }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
