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
                <div class="d-flex justify-content-around align-items-center mb-4">
                    <form id="formularioFiltro" action="{{ route('filtrar') }}" method="GET" class="mb-4">

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
                    <div class="col-md-11">
                        <div class="container">

                            @include('partials.Soft', [
                                'labels' => $labels,
                                'data' => $data,
                                'porcentajes' => $porcentajes,
                                'carreras' => $carreras,
                            ])

                        </div>
                    </div>

                    <div class="col-md-11">
                        <div class="container">

                            @include('partials.Alum', [
                                'tablaDatos' => $tablaDatos,
                                'labelsAulas' => $labelsAulas,
                            ])

                        </div>
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
            const downloadPDFButton = document.getElementById('downloadPDF');
            const downloadWordButton = document.getElementById('downloadWord');

            const canvas = document.createElement('canvas');
            const context = canvas.getContext('2d', {
                willReadFrequently: true
            });


            if (downloadPDFButton) {
                downloadPDFButton.addEventListener('click', function() {
                    const {
                        jsPDF
                    } = window.jspdf;
                    const pdf = new jsPDF('p', 'mm', 'a4');
                    pdf.text('Informe: Gráficas y Tablas', 10, 10);

                    let imgHeight = 0;

                    // Captura gráfica Software por Aula
                    const canvasSoftware = document.getElementById('graficaSoftwareXAula');
                    if (canvasSoftware) {
                        const imgSoftware = canvasSoftware.toDataURL('image/png');
                        const imgWidth = 150; // Reduce el tamaño de la imagen
                        imgHeight = (canvasSoftware.height / canvasSoftware.width) * imgWidth;
                        pdf.addImage(imgSoftware, 'PNG', 10, 20, imgWidth, imgHeight);
                    }

                    // Captura tabla Software por Aula
                    const tableSoftware = document.getElementById('tablaSoftwareXAula');
                    if (tableSoftware) {
                        const tableSoftwareHeaders = Array.from(tableSoftware.querySelectorAll('thead th'))
                            .map(th => th.innerText);
                        const tableSoftwareRows = Array.from(tableSoftware.querySelectorAll('tbody tr'))
                            .map(tr => Array.from(tr.querySelectorAll('td')).map(td => td.innerText));

                        const startY = 20 + imgHeight +
                            10; // Ajusta la posición de la tabla después de la imagen
                        pdf.autoTable({
                            head: [tableSoftwareHeaders],
                            body: tableSoftwareRows,
                            startY: startY
                        });
                    }

                    // Captura gráfica Alumnos por Aula
                    const canvasAlum = document.getElementById('graficaAlumnosXAula');
                    if (canvasAlum) {
                        const imgAlum = canvasAlum.toDataURL('image/png');
                        const imgWidth = 120; // Reducir el tamaño de la imagen
                        const imgHeightAlum = (canvasAlum.height / canvasAlum.width) * imgWidth;

                        // Verifica si hay suficiente espacio para la gráfica, si no añade una nueva página
                        let startY = pdf.lastAutoTable.finalY + 10;
                        if (startY + imgHeightAlum > pdf.internal.pageSize.height) {
                            pdf.addPage();
                            startY = 10; // Restablece la coordenada Y en la nueva página
                        }
                        pdf.addImage(imgAlum, 'PNG', 10, startY, imgWidth, imgHeightAlum);
                    }

                    // Captura tabla Alumnos por Aula por Mes
                    const tableAlum = document.getElementById('tablaAlumnosXAula');
                    if (tableAlum) {
                        const tableAlumHeaders = Array.from(tableAlum.querySelectorAll('thead th'))
                            .map(th => th.innerText);
                        const tableAlumRows = Array.from(tableAlum.querySelectorAll('tbody tr'))
                            .map(tr => Array.from(tr.querySelectorAll('td')).map(td => td.innerText));

                        const startY = 20 + imgHeight +
                            40; // Ajusta la posición de la tabla después de la imagen
                        pdf.autoTable({
                            head: [tableAlumHeaders],
                            body: tableAlumRows,
                            startY: startY
                        });
                    }

                    // Descargar el PDF
                    pdf.save('Informe_Graficas_Tablas.pdf');
                });

            }

            if (downloadWordButton) {
                downloadWordButton.addEventListener('click', async function() {
                    try {
                        const tableSoftware = document.getElementById('tablaSoftwareXAula');
                        const tableAlumnos = document.getElementById('tablaAlumnosXAula');
                        const graph1 = document.getElementById('graficaSoftwareXAula');
                        const graph2 = document.getElementById('graficaAlumnosXAula');

                        if (tableSoftware && tableAlumnos && graph1 && graph2) {
                            const {
                                Document,
                                Packer,
                                Paragraph,
                                Table,
                                TableCell,
                                TableRow,
                                TextRun,
                                ImageRun,
                                AlignmentType,
                                WidthType
                            } = window.docx;

                            // Crear encabezados y filas de la tabla Software
                            const tableSoftwareHeaders = Array.from(tableSoftware.querySelectorAll(
                                'thead th')).map(th => th.innerText);
                            const tableSoftwareRows = Array.from(tableSoftware.querySelectorAll(
                                'tbody tr')).map(tr =>
                                Array.from(tr.querySelectorAll('td')).map(td => td.innerText)
                            );

                            // Crear encabezados y filas de la tabla Alumnos por Aula
                            const tableAlumnosHeaders = Array.from(tableAlumnos.querySelectorAll(
                                'thead th')).map(th => th.innerText);
                            const tableAlumnosRows = Array.from(tableAlumnos.querySelectorAll(
                                'tbody tr')).map(tr =>
                                Array.from(tr.querySelectorAll('td')).map(td => td.innerText)
                            );


                            //Capturar gráficas como imágenes usando html2canvas con mayor escala
                            const canvas1 = document.getElementById('graficaSoftwareXAula');
                            const imageGraph1 = canvas1.toDataURL('image/png');

                            const canvas2 = document.getElementById('graficaAlumnosXAula');
                            const imageGraph2 = canvas2.toDataURL('image/png');

                            // Definir el ancho máximo de las gráficas en píxeles
                            const maxWidth = 700;

                            // Generar documento Word
                            const doc = new Document({
                                sections: [{
                                    properties: {},
                                    children: [
                                        new Paragraph({
                                            children: [new TextRun({
                                                text: "Informe: Gráficas y Tablas",
                                                bold: true,
                                                size: 32
                                            })],
                                            alignment: AlignmentType.CENTER,
                                            spacing: {
                                                after: 300
                                            }
                                        }),

                                        // Insertar Gráfica 1
                                        new Paragraph({
                                            text: "Gráfica uso de Software",
                                            spacing: {
                                                before: 300
                                            }
                                        }),
                                        new Paragraph({
                                            children: [
                                                new ImageRun({
                                                    data: imageGraph1,
                                                    transformation: {
                                                        width: 600, // Ajustar al ancho máximo
                                                        height: 300,
                                                    }
                                                })
                                            ],
                                            alignment: AlignmentType.CENTER
                                        }),

                                        // Crear la tabla Software por Aula en el documento Word
                                        new Paragraph({
                                            text: "Tabla uso de Software",
                                            spacing: {
                                                before: 300
                                            }
                                        }),
                                        new Table({
                                            width: {
                                                size: 100,
                                                type: WidthType.PERCENTAGE
                                            },
                                            rows: [
                                                new TableRow({
                                                    children: tableSoftwareHeaders
                                                        .map(header =>
                                                            new TableCell({
                                                                children: [
                                                                    new Paragraph(
                                                                        header
                                                                    )
                                                                ]
                                                            })
                                                        )
                                                }),
                                                ...tableSoftwareRows.map(
                                                    row =>
                                                    new TableRow({
                                                        children: row
                                                            .map(cell =>
                                                                new TableCell({
                                                                    children: [
                                                                        new Paragraph(
                                                                            cell
                                                                        )
                                                                    ]
                                                                })
                                                            )
                                                    })
                                                )
                                            ]
                                        }),


                                        // Insertar Gráfica 2
                                        new Paragraph({
                                            text: "Gráfica Alumnos atendidos por Aula",
                                            spacing: {
                                                before: 300
                                            }
                                        }),
                                        new Paragraph({
                                            children: [
                                                new ImageRun({
                                                    data: imageGraph2,
                                                    transformation: {
                                                        width: 350, // Ajustar al ancho máximo
                                                        height: 350, // Mantener proporción
                                                    }
                                                })
                                            ],
                                            alignment: AlignmentType.CENTER
                                        }),

                                        // Crear la tabla Alumnos por Aula en el documento Word
                                        new Paragraph({
                                            text: "Tabla Alumnos por Aula",
                                            spacing: {
                                                before: 300
                                            }
                                        }),
                                        new Table({
                                            width: {
                                                size: 100,
                                                type: WidthType.PERCENTAGE
                                            },
                                            rows: [
                                                new TableRow({
                                                    children: tableAlumnosHeaders
                                                        .map(header =>
                                                            new TableCell({
                                                                children: [
                                                                    new Paragraph(
                                                                        header
                                                                    )
                                                                ]
                                                            })
                                                        )
                                                }),
                                                ...tableAlumnosRows.map(
                                                    row =>
                                                    new TableRow({
                                                        children: row
                                                            .map(cell =>
                                                                new TableCell({
                                                                    children: [
                                                                        new Paragraph(
                                                                            cell
                                                                        )
                                                                    ]
                                                                })
                                                            )
                                                    })
                                                )
                                            ]
                                        })
                                    ]
                                }]
                            });

                            Packer.toBlob(doc).then(blob => {
                                const downloadLink = document.createElement("a");
                                downloadLink.href = URL.createObjectURL(blob);
                                downloadLink.download = "Informe_Graficas_Tablas.docx";
                                downloadLink.click();
                            }).catch(err => {
                                console.error("Error al crear el documento:", err);
                            });
                        }
                    } catch (error) {
                        console.error("Error al importar docx:", error);
                    }
                });
            }

        });
    </script>

</body>

</html>
