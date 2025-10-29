<x-filament-panels::page>
    {{-- Listener global para abrir el PDF en popup --}}
    <div
        x-data
        x-on:open-window.window="
            const url = $event.detail.url;
            if (!url) return;

            const w = Math.min(1100, screen.width  - 80);
            const h = Math.min(800,  screen.height - 120);
            const left = (screen.width  - w) / 2;
            const top  = (screen.height - h) / 2;

            const features = [
                'toolbar=yes', 'location=no', 'status=no', 'menubar=no',
                'scrollbars=yes', 'resizable=yes',
                `width=${w}`, `height=${h}`, `left=${left}`, `top=${top}`
            ].join(',');

            const win = window.open(url, 'ReportePDF', features);

            // Si el popup está bloqueado, fallback: abrir en la misma pestaña
            if (!win || win.closed || typeof win.closed === 'undefined') {
                window.location.href = url;
            } else {
                win.focus();
            }
        "
    ></div>

    {{-- Contenido simple (las acciones están en el header) --}}
    <x-filament::section>
        <x-slot name="heading">Generación de reportes</x-slot>
        <p class="text-sm text-gray-500">
            Usa los botones de la parte superior para elegir el reporte, completa los filtros y presiona
            <span class="font-semibold">Abrir</span>. El documento se mostrará en un popup centrado.
        </p>
    </x-filament::section>
</x-filament-panels::page>
