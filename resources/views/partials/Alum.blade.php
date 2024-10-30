<h1>Estadisticos de Alumnos atendidos por mes</h1>

<!-- Gráfica -->
<div class="grafica">
    <canvas class="my-4" id="graficaAlumnosXAula"></canvas>
</div>

<!-- Tabla de software usados -->
<div class="table-responsive">
    <table class="table table-bordered table-striped" id="tablaAlumnosXAula">
        <thead class="table-active">
            <tr>
                <th>Laboratorio</th>
                @foreach ($labelsAulas as $mes)
                    <th>{{ $mes }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($tablaDatos as $aula => $totales)
                <tr>
                    <td>{{ $aula }}</td>
                    @foreach ($labelsAulas as $mes)
                        <td>{{ $totales[$mes] }}</td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var ctx = document.getElementById("graficaAlumnosXAula").getContext('2d');

        // Asegúrate de que `labels` y `months` sean arrays
        var labelsAulas = @json(array_keys($tablaDatos)).filter(label => label !== 'Total');
        var months = @json($labelsAulas); // Mayuscula la primera letra
        var tablaDatos = @json($tablaDatos);


        if (!Array.isArray(months)) {
            console.error("Expected `months` to be an array.");
            return;
        }

        var colors = [
            'rgba(255, 99, 132, 0.5)', 'rgba(54, 162, 235, 0.5)', 'rgba(255, 206, 86, 0.5)',
            'rgba(75, 192, 192, 0.5)', 'rgba(153, 102, 255, 0.5)', 'rgba(255, 159, 64, 0.5)',
            'rgba(199, 199, 199, 0.5)', 'rgba(255, 105, 180, 0.5)', 'rgba(128, 0, 128, 0.5)',
            'rgba(0, 255, 255, 0.5)'
        ];

        var borderColors = [
            'rgba(255, 99, 132, 1)', 'rgba(54, 162, 235, 1)', 'rgba(255, 206, 86, 1)',
            'rgba(75, 192, 192, 1)', 'rgba(153, 102, 255, 1)', 'rgba(255, 159, 64, 1)',
            'rgba(199, 199, 199, 1)', 'rgba(255, 105, 180, 1)', 'rgba(128, 0, 128, 1)',
            'rgba(0, 255, 255, 1)'
        ];

        var datasets = months.map((mes, index) => ({
            label: mes.charAt(0).toUpperCase() + mes.slice(1),
            data: labelsAulas.map(aula => tablaDatos[aula][mes] ?? 0),
            backgroundColor: colors[index % colors.length],
            borderColor: borderColors[index % borderColors.length],
            pointBorderColor: '#fff',
            pointHoverBackgroundColor: '#fff',
            borderWidth: 1
        }));


        if (datasets.length === 0 || datasets.every(ds => ds.data.every(d => d === 0))) {
            console.error("No hay datos suficientes para los meses seleccionados.");
            return;
        }

        var myChart = new Chart(ctx, {
            type: 'radar',
            data: {
                labels: labelsAulas,
                datasets: datasets
            },
            options: {
                scales: {
                    r: {
                        angleLines: {
                            display: true
                        },
                        suggestedMin: 0,
                        suggestedMax: Math.max(...Object.values(tablaDatos).flat().map(val => val ??
                            0)),
                        ticks: {
                            stepSize: 10
                        }
                    }
                },
                elements: {
                    line: {
                        tension: 0.1
                    }
                },
                legend: {
                    display: true,
                    position: 'top'
                }
            }
        });
    });
</script>
