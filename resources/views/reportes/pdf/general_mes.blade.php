<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte General de Ausencias</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-100 p-8">

    <div class="container mx-auto bg-white p-6 rounded-lg shadow-lg">
        <h1 class="text-3xl font-bold mb-6 text-gray-800">Reporte General de Ausencias por Mes</h1>
        
        <p class="text-gray-600 mb-6">
            Este gráfico muestra el total de ausencias agrupadas por motivo a lo largo del tiempo.
        </p>

        <div class="w-full h-96">
            <canvas id="reporteGrafico"></canvas>
        </div>
    </div>

    <script>
        // Obtenemos los datos que pasamos desde el controlador
        const chartData = {!! $chartData !!};

        // Configuramos el gráfico
        const ctx = document.getElementById('reporteGrafico').getContext('2d');
        const myChart = new Chart(ctx, {
            type: 'bar', // Tipo de gráfico: barras
            data: {
                labels: chartData.labels,
                datasets: chartData.datasets
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    title: {
                        display: true,
                        text: 'Ausencias Mensuales por Motivo',
                        font: { size: 18 }
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false
                    }
                },
                scales: {
                    x: {
                        stacked: true, // Apilamos las barras
                    },
                    y: {
                        stacked: true, // Apilamos las barras
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Número de Ausencias'
                        }
                    }
                }
            }
        });
    </script>

</body>
</html>