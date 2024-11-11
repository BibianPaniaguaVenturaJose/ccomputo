<!DOCTYPE html>
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
                        <li class="nav-item">
                            <a class="nav-link" href="/inform/excel">
                                <span data-feather="archive"></span>
                                Importar Materias
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Columna para el contenido principal -->
            <div class="col-md-10">
                <!-- Aquí se pone el contenido -->
                <div class="row">
                    <form action="" method="post" enctype="multipart/form-data" class="custom-form">
                        @csrf
                        <div class="form-group">
                            <label for="file" class="form-label">Seleccionar archivo</label>
                            <input type="file" name="file" id="file" class="file-input">
                        </div>
                        <div class="button-group">
                            <input type="submit" value="IMPORTAR" class="submit-button">
                        </div>
                    </form>

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


</body>

</html>

