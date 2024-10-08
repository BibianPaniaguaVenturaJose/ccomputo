<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="../../../../favicon.ico">

    <title>Informes</title>

    <!-- Bootstrap core CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template  -->
    <link href="{{ asset('assets/css/inform.css') }}" rel="stylesheet">

    <!-- CSS para Datepicker -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css"
        rel="stylesheet">

</head>

<body>
    <nav class="navbar navbar-dark sticky-top bg-dark flex-md-nowrap p-0">
        <a class="navbar-brand col-sm-3 col-md-2 mr-0" href="#">DASHBOARD</a>
        <input class="form-control form-control-dark w-100" type="text" placeholder="Search" aria-label="Search">
        <ul class="navbar-nav px-3">
            <li class="nav-item text-nowrap">
                <a class="nav-link" href="#">Sign out</a>
            </li>
        </ul>
    </nav>

    <div class="container-fluid">
        <div class="row">

            <nav class="col-md-2 d-none d-md-flex bg-light sidebar">
                <div class="sidebar-sticky">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link active" href="#" onclick="loadContent('/inform/inicio')">
                                <span data-feather="home"></span>
                                Home
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Columna para el contenido principal -->
            <div class="col-md-10">
                <!-- Aquí puedes poner tus contenidos dentro de columnas -->
                <div class="row">
                    <div class="col-md-7">
                        <div class="container">
                            Lorem ipsum dolor, sit amet consectetur adipisicing elit. Debitis eum assumenda incidunt
                            aperiam nam tempora illo? Consequuntur est nam quidem itaque, sunt sequi repudiandae
                            adipisci ut quae, cupiditate distinctio maiores?
                        </div>
                    </div>

                    <div class="col-md-7">
                        <div class="container">
                            <canvas class="my-4" id="graficaAlumnosXAula"></canvas>
                            Lorem, ipsum dolor sit amet consectetur adipisicing elit. Alias fugit laboriosam, magnam
                            accusantium, corrupti, facilis doloribus ratione iusto explicabo cumque error sed ut. Natus
                            dolores dolorum pariatur vero accusamus incidunt!
                        </div>
                    </div>

                    <div class="col-md-7">
                        <div class="container">
                            <h2>Tabla</h2>
                            <table class="table table-bordered table-striped">
                                <thead class="table-active">
                                    <tr>
                                        <th>Laboratorio</th>
                                        @if(isset($labels) && count($labels) > 0)
                                            @foreach ($labels as $mes)
                                                <th>{{ $mes }}</th>
                                            @endforeach
                                        @else
                                            <th>No data available</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(isset($tablaDatos) && count($tablaDatos) > 0)
                                        @foreach ($tablaDatos as $aula => $totales)
                                            <tr>
                                                <td>{{ $aula }}</td>
                                                @foreach ($labels as $mes)
                                                    <td>{{ $totales[$mes] ?? 0 }}</td>
                                                @endforeach
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="{{ count($labels) + 1 }}">No data available</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>

                            Lorem ipsum dolor sit amet consectetur adipisicing elit. Sed eius, voluptas dignissimos
                            voluptatem earum nam quam enim quas ullam quaerat dolorem ab, facere sequi pariatur amet
                            voluptatibus repudiandae soluta fuga?
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"
        integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous">
    </script>
    <script>
        window.jQuery || document.write('<script src="../../../../assets/js/vendor/jquery-slim.min.js"><\/script>')
    </script>
    <script src="../../../../assets/js/vendor/popper.min.js"></script>
    <script src="../../../../dist/js/bootstrap.min.js"></script>

    <!-- Icons -->
    <script src="https://unpkg.com/feather-icons/dist/feather.min.js"></script>
    <script>
        feather.replace()
    </script>

    <!-- JS para Datepicker -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>

    <!-- Graphs -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.1/Chart.min.js"></script>


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

            console.log("Datasets:", datasets);

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
