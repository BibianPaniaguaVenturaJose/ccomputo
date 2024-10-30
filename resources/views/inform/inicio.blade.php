<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="../../../../favicon.ico">

    <title>Informes</title>

    <!-- Cargar jsPDF -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>


    <!-- Cargar docx -->
    <script src="https://cdn.jsdelivr.net/npm/docx@7.3.0/build/index.min.js"></script>

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
                            <a class="nav-link active" href="/inform/home">
                                <span data-feather="home"></span>
                                Home
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/inform/inicio">
                                <span data-feather="file"></span>
                                Disponibilidad
                            </a>
                        </li>
                    </ul>
                    </ul>
                </div>
            </nav>

            <!-- Columna para el contenido principal -->
            <div class="col-md-10">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <form id="formularioFiltro" action="{{ route('range') }}" method="GET" class="mb-4">

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
                    <div>
                        <button class="btn btn-sm btn-outline-secondary" id="downloadPDF">Descargar PDF</button>
                        <button class="btn btn-sm btn-outline-secondary" id="downloadWord">Descargar Word</button>
                    </div>
                </div>
                <!-- Aquí se pone el contenido -->
                <div class="row">
                    <div class= "container">
                        <h2>Registros por Mes</h2>

                        <div class= "grafica">
                            <canvas id="graficaPorMes"></canvas>
                        </div>

                        <!-- Tabla de Datos -->

                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Periodo</th>
                                    <th>Cantidad de Registros</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($labels as $index => $label)
                                    <tr>
                                        <td>{{ $label }}</td>
                                        <td>{{ $data[$index] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Bootstrap core JavaScript ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->

    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"
        integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous">
    </script>
    <script>
        window.jQuery || document.write('<script src="../../../../assets/js/vendor/jquery-slim.min.js"><\/script>')
    </script>


    <!-- Icons -->
    <script src="https://unpkg.com/feather-icons/dist/feather.min.js"></script>
    <script>
        feather.replace()
    </script>

    <!-- JS para Datepicker -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>

    <!-- Graphs -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Datos obtenidos de PHP
            var labels = @json($labels); // Etiquetas de cada mes y año
            var data = @json($data); // Cantidad de registros por mes

            // Configuración del dataset
            var dataset = {
                label: 'Cantidad de Registros',
                data: data,
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            };

            // Crear la gráfica
            var ctx = document.getElementById('graficaPorMes').getContext('2d');
            var graficaPorMes = new Chart(ctx, {
                type: 'bar', // Tipo de gráfica de barras
                data: {
                    labels: labels,
                    datasets: [dataset]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        });
    </script>


</body>

</html>
