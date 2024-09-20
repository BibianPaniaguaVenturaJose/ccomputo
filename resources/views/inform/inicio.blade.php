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

            <main role="main" class="col-md-9 ml-sm-auto col-lg-10 pt-3 px-4">
                <div
                    class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
                    <h1 class="h2">Grafica</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <!-- Formulario de filtro por fechas -->

                        <div class="btn-group mr-2">
                            <form id="formularioFiltro" action="sol" method="GET" class="mb-4">
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label for="fechaInicio" class="form-label">Fecha de Inicio:</label>
                                        <input type="date" id="fechaInicio" name="fechaInicio" class="form-control"
                                            required>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="fechaFin" class="form-label">Fecha de Fin:</label>
                                        <input type="date" id="fechaFin" name="fechaFin" class="form-control"
                                            required>
                                    </div>
                                    <div class="col-md-4 d-flex align-items-end mb-3">
                                        <button type="submit" class="btn btn-primary">Filtrar</button>
                                    </div>
                                </div>
                            </form>
                            <button class="btn btn-sm btn-outline-secondary">Share</button>
                            <button class="btn btn-sm btn-outline-secondary">Export</button>
                        </div>
                    </div>
                </div>

                <canvas class="my-4" id="graficaAlumnosXAula" width="900" height="380"></canvas>

                <h2>Tabla</h2>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-responsive">
                        <thead class="table-active">
                            <tr>
                                <th>Aula</th>
                                <th>Alumnos Totales</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($labels as $index => $aula)
                                <tr>
                                    <td>{{ $aula }}</td>
                                    <td>{{ $data[$index] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

            </main>
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

    <!-- Muestra la grafica de inicio de alumnos totales -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var ctx = document.getElementById("graficaAlumnosXAula").getContext('2d');
            var myChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: @json($labels), // Las aulas
                    datasets: [{
                        data: @json($data), // El número de alumnos por aula
                        lineTension: 0,
                        backgroundColor: 'transparent',
                        borderColor: '#007bff',
                        borderWidth: 4,
                        pointBackgroundColor: '#007bff'

                    }]
                },
                options: {
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero: false
                            }
                        }]
                    },
                    legend: {
                        display: true,
                        text: 'Número de Alumnos por Aula y Mes'
                    }
                }
            });
        });
    </script>


</body>

</html>
