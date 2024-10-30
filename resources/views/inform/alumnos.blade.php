<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Alumnos por mes</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />

    <!-- Custom styles for this template  -->
    <link href="{{ asset('assets/css/base.css') }}" rel="stylesheet">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</head>

<body>
    <h1 class="titulo">Alumnos totales por aula y mes</h1>

    <!-- Formulario de filtro por fechas -->
    <div class="container">
        <form id="formularioFiltro" action="mes" method="GET" class="mb-4">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="fechaInicio" class="form-label">Fecha de Inicio:</label>
                    <input type="date" id="fechaInicio" name="fechaInicio" class="form-control" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="fechaFin" class="form-label">Fecha de Fin:</label>
                    <input type="date" id="fechaFin" name="fechaFin" class="form-control" required>
                </div>
                <div class="col-md-4 d-flex align-items-end mb-3">
                    <button type="submit" class="btn btn-primary">Filtrar</button>
                </div>
            </div>
        </form>
    </div>

    <!-- Gráfica -->
    <div class="grafica">
        <canvas class="my-4" id="graficaAlumnosXAula"></canvas>
    </div>

    <!-- Tabla de software usados -->
    <h2>Tabla</h2>
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="table-active">
                <tr>
                    <th>Laboratorio</th>
                    @foreach ($labels as $mes)
                        <th>{{ $mes }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach ($tablaDatos as $aula => $totales)
                    <tr>
                        <td>{{ $aula }}</td>
                        @foreach ($labels as $mes)
                            <td>{{ $totales[$mes] }}</td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var ctx = document.getElementById("graficaAlumnosXAula").getContext('2d');

            // Asegúrate de que `labels` y `months` sean arrays
            var labels = @json(array_keys($tablaDatos)).filter(label => label !== 'Total');
            var months = @json($labels); // Mayuscula la primera letra
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
                data: labels.map(aula => tablaDatos[aula][mes] ?? 0),
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
                    labels: labels,
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




</body>

</html>
