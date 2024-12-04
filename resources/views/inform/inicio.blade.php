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

    <!-- Cargar chart -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

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
                    <div class="col-md-11">
                        <div class="container">

                            @include('partials.Reg', [
                                'labels' => $labels,
                                'data' => $data,
                                'diasHabilesPorMesArray' => $diasHabilesPorMesArray,
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

    <!-- Cargar html2canvas -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/0.5.0-beta4/html2canvas.min.js"></script>

    <!-- Cargar docx -->
    <script src="https://cdn.jsdelivr.net/npm/docx@7.3.0/build/index.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const downloadPDFButton = document.getElementById('downloadPDF');
            const downloadWordButton = document.getElementById('downloadWord');

            if (downloadPDFButton) {
                downloadPDFButton.addEventListener('click', function() {
                    const {
                        jsPDF
                    } = window.jspdf;
                    const pdf = new jsPDF('p', 'mm', 'a4');
                    pdf.text('Informe: Registros y Disponibilidad', 10, 10);

                    let imgHeight = 0;

                    // Captura gráfica de Registros
                    const canvasRegistros = document.getElementById('registrosChart');
                    if (canvasRegistros) {
                        const imgRegistros = canvasRegistros.toDataURL('image/png');
                        const imgWidth = 150; // Ajuste del tamaño de la imagen
                        imgHeight = (canvasRegistros.height / canvasRegistros.width) * imgWidth;
                        pdf.addImage(imgRegistros, 'PNG', 10, 20, imgWidth, imgHeight);
                    }

                    // Captura tabla de Registros
                    const tableRegistros = document.getElementById('tablaRegistros');
                    if (tableRegistros) {
                        const tableHeaders = Array.from(tableRegistros.querySelectorAll('thead th')).map(
                            th => th.innerText);
                        const tableRows = Array.from(tableRegistros.querySelectorAll('tbody tr')).map(tr =>
                            Array.from(tr.querySelectorAll('td')).map(td => td.innerText)
                        );

                        let startY = 20 + imgHeight + 10; // Posición de la tabla después de la imagen
                        pdf.autoTable({
                            head: [tableHeaders],
                            body: tableRows,
                            startY: startY,
                            theme: 'grid',
                            styles: {
                                fontSize: 8, // Tamaño de fuente reducido para ajuste en página
                                cellPadding: 2,
                            },
                            margin: {
                                left: 10,
                                right: 10
                            }, // Márgenes laterales
                            tableWidth: 'auto', // Ajuste automático del ancho de la tabla
                            columnStyles: {
                                0: {
                                    cellWidth: 'auto'
                                },
                                1: {
                                    cellWidth: 'auto'
                                },
                                2: {
                                    cellWidth: 'auto'
                                }
                            }
                        });
                    }

                    // Descargar el PDF
                    pdf.save('Informe_Registros_Disponibilidad.pdf');
                });
            }

            if (downloadWordButton) {
    downloadWordButton.addEventListener('click', async function() {
        try {
            const tableRegistros = document.getElementById('tablaRegistros');
            const graph1 = document.getElementById('registrosChart');

            if (tableRegistros && graph1) {
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

                // Crear encabezados y filas de la tabla Registros
                const tableHeaders = Array.from(tableRegistros.querySelectorAll('thead th'))
                    .map(th => th.innerText || "");
                const tableRows = Array.from(tableRegistros.querySelectorAll('tbody tr'))
                    .map(tr =>
                        Array.from(tr.querySelectorAll('td')).map(td => td.innerText || "")
                    );

                // Capturar la gráfica como imagen usando html2canvas sin el parámetro 'scale'
                const canvas1 = document.getElementById('registrosChart');
                const imageGraph1 = canvas1.toDataURL('image/png');

                // Definir el ancho máximo de la gráfica para ajustarlo a la página
                const maxWidth = 500;
                const maxHeight = 300;

                // Generar el documento Word
                const doc = new Document({
                    sections: [{
                        properties: {},
                        children: [
                            new Paragraph({
                                children: [new TextRun({
                                    text: "Informe: Registros y Disponibilidad",
                                    bold: true,
                                    size: 32
                                })],
                                alignment: AlignmentType.CENTER,
                                spacing: {
                                    after: 300
                                }
                            }),

                            // Insertar la Gráfica de Registros
                            new Paragraph({
                                text: "Gráfica de Registros",
                                spacing: {
                                    before: 300
                                }
                            }),
                            new Paragraph({
                                children: [
                                    new ImageRun({
                                        data: imageGraph1,
                                        transformation: {
                                            width: maxWidth,  // Ajustado para que quepa bien en la página
                                            height: maxHeight  // Altura ajustada
                                        }
                                    })
                                ],
                                alignment: AlignmentType.CENTER
                            }),

                            // Crear la tabla de Registros en el documento Word
                            new Paragraph({
                                text: "Tabla de Registros",
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
                                        children: tableHeaders
                                            .map(header =>
                                                new TableCell({
                                                    children: [
                                                        new Paragraph(header)
                                                    ]
                                                })
                                            )
                                    }),
                                    ...tableRows.map(row =>
                                        new TableRow({
                                            children: row
                                                .map(cell =>
                                                    new TableCell({
                                                        children: [
                                                            new Paragraph(cell)
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

                // Descargar el archivo Word
                Packer.toBlob(doc).then(blob => {
                    const downloadLink = document.createElement("a");
                    downloadLink.href = URL.createObjectURL(blob);
                    downloadLink.download = "Informe_Registros_Disponibilidad.docx";
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
