@php
    $empresa = $empresa ?? 'NEGOCIACIONES LUCARVI E.I.R.L';
    $ruc     = $ruc ?? '20511039470';
    $titulo  = $titulo ?? 'REPORTE';
    $hoy     = \Carbon\Carbon::now('America/Lima')->format('d/m/Y');
@endphp

<header id="pdf-header">
    <table width="100%">
        <tr>
            <td style="vertical-align:top;">
                <div style="font-weight:700; font-size:20px;">{{ $empresa }}</div>
                <div style="font-size:13px;">RUC:&nbsp; {{ $ruc }}</div>
            </td>

            <td style="text-align:right; vertical-align:top;">
                <table style="font-size:14px; display:inline-table;">
                    <tr>
                        <td style="font-weight:700;">Página:</td>
                        <td class="page-number"></td>
                    </tr>
                    <tr>
                        <td style="font-weight:700;">Fecha:</td>
                        <td>{{ $hoy }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <h1 style="margin: 18px 0 0 0; text-align:center; color:#001a66; 
               font-size:28px; letter-spacing:.5px; text-decoration: underline;">
        {{ strtoupper($titulo) }}
    </h1>
</header>
