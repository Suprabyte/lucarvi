<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte por Trabajador</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">

    <div class="container mx-auto bg-white p-6 rounded-lg shadow-lg">
        <h1 class="text-3xl font-bold mb-6 text-gray-800">Reporte de Ausencias por Trabajador</h1>

        @foreach($reporte as $nombre_completo => $ausencias)
            <div class="mb-8">
                <h2 class="text-2xl font-semibold mb-3 text-blue-700">{{ $nombre_completo }}</h2>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white border border-gray-200">
                        <thead class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                            <tr>
                                <th class="py-3 px-6 text-left">Año</th>
                                <th class="py-3 px-6 text-left">Mes</th>
                                <th class="py-3 px-6 text-left">Motivo</th>
                                <th class="py-3 px-6 text-center">Total de Días</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-700 text-sm font-light">
                            @foreach($ausencias as $item)
                                <tr class="border-b border-gray-200 hover:bg-gray-100">
                                    <td class="py-3 px-6 text-left whitespace-nowrap">{{ $item->anio }}</td>
                                    <td class="py-3 px-6 text-left">{{ $item->mes_nombre }}</td>
                                    <td class="py-3 px-6 text-left">{{ $item->motivo }}</td>
                                    <td class="py-3 px-6 text-center">
                                        <span class="bg-blue-200 text-blue-800 py-1 px-3 rounded-full text-xs font-semibold">
                                            {{ $item->total }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endforeach

    </div>

</body>
</html>