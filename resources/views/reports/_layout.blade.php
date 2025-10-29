@php
    $empresa = $empresa ?? ['nombre' => 'Empresa', 'ruc' => '—'];
@endphp
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<title>{{ $title ?? 'Reporte' }}</title>
<style>
    @page { margin: 80px 35px 70px 35px; }
    body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #111; }
    header { position: fixed; top: -60px; left: 0; right: 0; height: 50px; }
    footer { position: fixed; bottom: -50px; left: 0; right: 0; height: 40px; font-size: 10px; color: #666; }
    .h-title { font-size: 16px; font-weight: 700; }
    .muted   { color: #666; }
    table { width: 100%; border-collapse: collapse; }
    th { background: #f1f5f9; border-bottom: 1px solid #e2e8f0; text-align: left; padding: 6px; font-size: 10px; }
    td { border-bottom: 1px solid #f1f5f9; padding: 6px; vertical-align: top; }
    .right { text-align: right; }
    .center { text-align: center; }
    .badge { border: 1px solid #cbd5e1; background: #f8fafc; padding: 2px 6px; border-radius: 4px; font-size: 10px; }
    .totals { font-weight: 700; background: #fafafa; }
</style>
</head>
<body>
<header>
    <table>
        <tr>
            <td class="h-title">{{ $title ?? '' }}</td>
            <td class="right">
                <div>{{ $empresa['nombre'] }}</div>
                <div class="muted">RUC: {{ $empresa['ruc'] }}</div>
            </td>
        </tr>
        <tr>
            <td class="muted">Fechas: {{ $desde->format('d/m/Y') }} — {{ $hasta->format('d/m/Y') }}</td>
            <td class="right muted">Fecha: {{ $hoy->format('d/m/Y') }}</td>
        </tr>
    </table>
</header>

<footer>
    <table>
        <tr>
            <td class="muted">Generado por sistema</td>
            <td class="right muted">Página <span class="page"></span> de <span class="topage"></span></td>
        </tr>
    </table>
</footer>

<main style="margin-top: 14px;">
    @yield('content')
</main>

<script type="text/php">
if (isset($pdf)) {
    $x = 520; $y = 810; // puede variar según orientación
    $text = "Página {PAGE_NUM} de {PAGE_COUNT}";
    $font = $fontMetrics->get_font("DejaVu Sans", "normal");
    $size = 9;
    $pdf->page_text($x, $y, $text, $font, $size, [0.4,0.4,0.4]);
}
</script>
</body>
</html>
