<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <style>
        @page { margin: 120px 28px 70px 28px; } /* top right bottom left */

        /* Cabecera fija */
        #pdf-header {
            position: fixed;
            top: -90px; left: 0; right: 0;
        }

        /* Pie de página */
        #pdf-footer {
            position: fixed;
            bottom: -50px; left: 0; right: 0;
            font-size: 11px; color: #666;
        }

        /* Contador de página Dompdf */
        .page-number:before { content: counter(page); }

        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color:#111; }
        table { border-collapse: collapse; width: 100%; }
        th, td { padding: 6px 8px; }
        th { background: #f2f4f7; }
        .muted { color:#6b7280; }
        .small { font-size: 11px; }
        .right { text-align:right; }
        .center { text-align:center; }
        .bold { font-weight:700; }
        .border { border:1px solid #e5e7eb; }
        .b { border:1px solid #e5e7eb; }
        .bt { border-top:1px solid #e5e7eb; }
        .bb { border-bottom:1px solid #e5e7eb; }
    </style>
</head>
<body>
    {{-- Cabecera global --}}
    @include('pdf.partials.header', [
        'empresa' => $empresa ?? null,
        'ruc'     => $ruc ?? null,
        'titulo'  => $titulo ?? null,
    ])

    {{-- Contenido específico de cada reporte --}}
    <main>
        @yield('content')
    </main>

    {{-- Pie global opcional --}}
    <div id="pdf-footer" class="center muted">
        {{ $empresa ?? 'NEGOCIACIONES LUCARVI E.I.R.L' }} — Generado por Sistema de Asistencia
    </div>
</body>
</html>
