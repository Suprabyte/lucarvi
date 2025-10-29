<x-filament-panels::page>
    @php
        /** @var \App\Filament\Pages\ZktecoSync $this */
        $r = $this->lastResult;
    @endphp

    {{-- === Listener para abrir POPUP centrado === --}}
    <div
        x-data
        x-on:open-popup.window="
            const url = $event.detail.url;
            const w   = Math.min(1200, screen.width  - 80);
            const h   = Math.min(800,  screen.height - 100);
            const l   = Math.max(0, (screen.width  - w) / 2);
            const t   = Math.max(0, (screen.height - h) / 2);
            const win = window.open(
                url,
                'reporte_pdf',
                `width=${w},height=${h},left=${l},top=${t},resizable=yes,scrollbars=yes`
            );
            if (!win) { window.location.href = url; } // fallback si el navegador bloquea popups
        "
    ></div>

    <div class="space-y-8">
        {{-- === Resumen de la última sincronización === --}}
        <x-filament::section>
            <x-slot name="heading">Resumen de la última sincronización</x-slot>

            @if(!$r)
                <p class="text-sm text-gray-500">
                    Aún no ejecutas la sincronización. Usa el botón
                    <span class="font-semibold">“Sincronizar ahora”</span>.
                </p>
            @else
                @if($r['ok'] ?? false)
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                        <div class="p-4 bg-green-50 border border-green-200 rounded-xl text-center">
                            <div class="text-xs text-gray-500">Insertadas</div>
                            <div class="text-3xl font-bold text-green-700">{{ $r['inserted'] ?? 0 }}</div>
                        </div>
                        <div class="p-4 bg-blue-50 border border-blue-200 rounded-xl text-center">
                            <div class="text-xs text-gray-500">Actualizadas</div>
                            <div class="text-3xl font-bold text-blue-700">{{ $r['updated'] ?? 0 }}</div>
                        </div>
                        <div class="p-4 bg-amber-50 border border-amber-200 rounded-xl text-center">
                            <div class="text-xs text-gray-500">Omitidas</div>
                            <div class="text-3xl font-bold text-amber-700">{{ $r['skipped'] ?? 0 }}</div>
                        </div>
                        <div class="p-4 bg-indigo-50 border border-indigo-200 rounded-xl text-center">
                            <div class="text-xs text-gray-500">Asistencias generadas</div>
                            <div class="text-3xl font-bold text-indigo-700">{{ $r['asistencias_generadas'] ?? 0 }}</div>
                        </div>
                    </div>
                @else
                    <div class="p-4 bg-red-50 border border-red-200 rounded-xl">
                        <div class="text-sm text-red-700 font-medium flex items-center gap-2">
                            <x-heroicon-o-exclamation-circle class="w-5 h-5" />
                            {{ $r['message'] ?? 'Error desconocido' }}
                        </div>
                    </div>
                @endif

                @if(!empty($r['errores']))
                    <div class="mt-6">
                        <div class="font-semibold text-red-700 mb-2">Errores (máx 10):</div>
                        <ul class="list-disc pl-6 text-sm text-red-600 space-y-1">
                            @foreach(array_slice($r['errores'], 0, 10) as $e)
                                <li>{{ $e }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            @endif
        </x-filament::section>

        {{-- === Últimas 50 marcaciones importadas === --}}
        <x-filament::section>
            <x-slot name="heading">Últimas 50 marcaciones importadas (ZKTeco / n8n)</x-slot>

            @if(empty($this->recentMarks))
                <p class="text-sm text-gray-500">No hay marcaciones recientes que mostrar.</p>
            @else
                <div class="overflow-hidden rounded-xl border border-gray-200 shadow-sm">
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm text-gray-800">
                            <thead class="bg-gray-100 sticky top-0 z-10">
                                <tr class="text-left text-xs uppercase tracking-wider text-gray-600">
                                    <th class="px-4 py-3">Empleado</th>
                                    <th class="px-4 py-3">DNI</th>
                                    <th class="px-4 py-3">Fecha / Hora</th>
                                    <th class="px-4 py-3">Tipo</th>
                                    <th class="px-4 py-3">Hash</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                @foreach($this->recentMarks as $m)
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-4 py-2 font-medium text-gray-900">{{ $m['empleado'] ?? '-' }}</td>
                                        <td class="px-4 py-2 text-gray-700">{{ $m['dni'] ?? '-' }}</td>
                                        <td class="px-4 py-2 text-gray-700">{{ $m['timestamp'] ?? '-' }}</td>
                                        <td class="px-4 py-2">
                                            @php
                                                $tipoColor = match($m['tipo']) {
                                                    'entrada' => 'text-green-700 bg-green-50 border-green-200',
                                                    'salida'  => 'text-red-700 bg-red-50 border-red-200',
                                                    default   => 'text-gray-700 bg-gray-50 border-gray-200',
                                                };
                                            @endphp
                                            <span class="px-2 py-1 border rounded-md text-xs font-semibold {{ $tipoColor }}">
                                                {{ $m['tipo'] ?? '—' }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-2 text-gray-500 font-mono text-xs">
                                            {{ \Illuminate\Support\Str::limit($m['hash'] ?? '-', 12) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="bg-gray-50 text-right text-xs text-gray-500 px-4 py-2">
                        Mostrando {{ count($this->recentMarks) }} registros
                    </div>
                </div>
            @endif
        </x-filament::section>
    </div>
</x-filament-panels::page>
