<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $titulo ?? 'Reporte' }}</title>
    <style>
        @page { margin: 24px 28px; }
        body { font-family: DejaVu Sans, Arial, Helvetica, sans-serif; font-size: 12px; color: #111; }
        h1 { font-size: 18px; margin: 0 0 8px; }
        .meta { font-size: 11px; color: #555; margin-bottom: 12px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 6px 8px; border-bottom: 1px solid #e5e7eb; text-align: left; }
        th { background: #f3f4f6; font-weight: 600; }
        .right { text-align: right; }
        .muted { color: #6b7280; }
    </style>
</head>
<body>
    <h1>{{ $titulo ?? '' }}</h1>
    <div class="meta">
        Rango: {{ $p['desde']->toDateString() }} — {{ $p['hasta']->toDateString() }}
        @isset($p['empleado_id']) &nbsp;|&nbsp; Empleado: {{ $p['empleado_id'] ?: 'Todos' }} @endisset
        @isset($p['area_id']) &nbsp;|&nbsp; Área: {{ $p['area_id'] ?: 'Todas' }} @endisset
    </div>

    @yield('content')
</body>
</html>
