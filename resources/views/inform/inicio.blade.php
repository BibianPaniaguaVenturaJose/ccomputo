<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Informes</title>

    <!-- Cargar docx -->
    <script src="https://cdn.jsdelivr.net/npm/docx@7.3.0/build/index.min.js"></script>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.13/jspdf.plugin.autotable.min.js"></script>

    <link href="{{ asset('assets/css/base.css') }}" rel="stylesheet">
</head>

<body>
    <main role="main" class="container-fluid mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <form id="formularioFiltro" action="sol" method="GET" class="mb-4">
                <h1 class="h2">Gráfica de Alumnos</h1>
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

        <div class="grafica mb-4">
            <canvas id="graficaAlumnosXAula"></canvas>
        </div>


        <h2>Tabla de Alumnos por Aula</h2>
        <div class="table-responsive" id="content">
            <table id="tablaDatos" class="table table-bordered table-striped">
                <thead class="table-light">
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

    <script src="https://cdnjs.cloudflare.com/ajax/libs/docx/7.3.0/docx.js"></script>

    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>

    <script>

        document.addEventListener('DOMContentLoaded', function() {
            var ctx = document.getElementById("graficaAlumnosXAula").getContext('2d');
            var myChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: @json($labels), // Las aulas
                    datasets: [{
                        data: @json($data), // El número de alumnos por aula
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.5)',
                            'rgba(54, 162, 235, 0.5)',
                            'rgba(255, 206, 86, 0.5)',
                            'rgba(75, 192, 192, 0.5)',
                            'rgba(153, 102, 255, 0.5)',
                            'rgba(255, 159, 64, 0.5)'
                        ],
                        borderColor: [
                            'rgba(255, 99, 132, 1)',
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 206, 86, 1)',
                            'rgba(75, 192, 192, 1)',
                            'rgba(153, 102, 255, 1)',
                            'rgba(255, 159, 64, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        title: {
                            display: true,
                            text: 'Número de Alumnos por Aula'
                        }
                    }
                }
            });

            // Descargar PDF
            document.getElementById('downloadPDF').addEventListener('click', function() {
                const {
                    jsPDF
                } = window.jspdf;

                const pdf = new jsPDF('p', 'mm', 'a4');
                pdf.text('Número de Alumnos por Aula', 10, 10);

                const imgData = myChart.toBase64Image();
                const imgWidth = 190;
                const imgHeight = (myChart.height / myChart.width) * imgWidth;
                const maxHeight = 100;

                let finalImgHeight = imgHeight;
                if (imgHeight > maxHeight) {
                    const scaleFactor = maxHeight / imgHeight;
                    finalImgHeight = imgHeight * scaleFactor;
                }

                const x = (pdf.internal.pageSize.width - imgWidth) / 2;
                pdf.addImage(imgData, 'PNG', x, 20, imgWidth, finalImgHeight);

                const startY = 20 + finalImgHeight + 10;

                const contentElement = document.getElementById('content');
                const tableData = [];
                const tableHeaders = [];

                const table = contentElement.querySelector('table');
                const headers = table.querySelectorAll('thead tr th');
                headers.forEach((header) => {
                    tableHeaders.push(header.innerText);
                });

                const rows = table.querySelectorAll('tbody tr');
                rows.forEach((row) => {
                    const rowData = [];
                    row.querySelectorAll('td').forEach((cell) => {
                        rowData.push(cell.innerText);
                    });
                    tableData.push(rowData);
                });

                pdf.autoTable({
                    head: [tableHeaders],
                    body: tableData,
                    startY: startY
                });

                window.open(pdf.output('bloburl'), '_blank');
            });

            document.getElementById('downloadWord').addEventListener('click', function() {
    const {
        Document,
        Packer,
        Paragraph,
        Table,
        TableCell,
        TableRow,
        TextRun,
        WidthType,
        AlignmentType,
        ImageRun
    } = window.docx;

    // 1. Captura la gráfica (debe ser un <canvas>)
    const canvas = document.getElementById('graficaAlumnosXAula'); // Asegúrate de usar el ID correcto de tu gráfico
    const chartImage = canvas.toDataURL('image/png');

    // Crear el documento Word
    const doc = new Document({
        sections: [{
            properties: {},
            children: [
                new Paragraph({
                    children: [
                        new TextRun({
                            text: "Informe: Alumnos por Aula",
                            bold: true,
                            size: 32,
                            color: "000000"
                        }),
                    ],
                    alignment: AlignmentType.CENTER,
                    spacing: {
                        after: 300
                    },
                }),
                new Paragraph({
                    children: [
                        new TextRun({
                            text: "Tabla de Alumnos por Aula",
                            bold: true,
                            size: 24,
                            color: "000000"
                        }),
                    ],
                    alignment: AlignmentType.LEFT,
                    spacing: {
                        after: 300
                    },
                }),
                new Paragraph({
                    text: "\n"
                }),

                // 2. Insertar la imagen del gráfico
                new Paragraph({
                    children: [
                        new ImageRun({
                            data: chartImage,
                            transformation: {
                                width: 600, // Ajusta el tamaño según lo necesites
                                height: 300,
                            },
                        }),
                    ],
                    alignment: AlignmentType.CENTER,
                    spacing: {
                        after: 300
                    },
                }),

                new Paragraph({
                    text: "\n"
                }),

                // 3. Crear la tabla
                new Table({
                    width: {
                        size: 100,
                        type: WidthType.PERCENTAGE,
                    },
                    rows: [
                        new TableRow({
                            children: Array.from(document
                                    .querySelectorAll(
                                        'table thead tr th'))
                                .map(header => new TableCell({
                                    children: [
                                        new Paragraph(
                                            header.innerText
                                        )
                                    ]
                                })),
                        }),
                        ...Array.from(document.querySelectorAll(
                            'table tbody tr')).map(row =>
                            new TableRow({
                                children: Array.from(row
                                        .querySelectorAll('td'))
                                    .map(cell => new TableCell({
                                        children: [
                                            new Paragraph(
                                                cell.innerText
                                            )
                                        ]
                                    })),
                            })
                        ),
                    ],
                })
            ]
        }]
    });

    // Generar el archivo Word y preparar la descarga
    Packer.toBlob(doc).then(blob => {
        const downloadLink = document.createElement("a");
        downloadLink.href = URL.createObjectURL(blob);
        downloadLink.download = "Informe_Alumnos_Aula.docx";
        downloadLink.click();
    }).catch(err => {
        console.error("Error al crear el documento:", err);
    });
});




        });
    </script>
</body>

</html>
