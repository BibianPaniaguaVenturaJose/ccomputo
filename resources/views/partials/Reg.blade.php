<h1>Estadístico de Uso y Registro de Laboratorios</h1>

<!-- Gráfica -->
<div class="grafica">
    <canvas id="registrosChart"></canvas>
</div>

<!-- Tabla de Registros y Días Hábiles -->
<table class="table table-striped" id="tablaRegistros">
    <thead>
        <tr>
            <th>Mes y Año</th>
            <th>Registros</th>
            <th>Horas Disponibles</th>
        </tr>
    </thead>
    <tbody>
        @php
            // Definir el arreglo de nombres de meses
            $meses = [
                '01' => 'Enero', '02' => 'Febrero', '03' => 'Marzo', '04' => 'Abril',
                '05' => 'Mayo', '06' => 'Junio', '07' => 'Julio', '08' => 'Agosto',
                '09' => 'Septiembre', '10' => 'Octubre', '11' => 'Noviembre', '12' => 'Diciembre',
            ];
        @endphp
        @foreach ($labels as $index => $periodo)
            @php
                // Dividir el periodo en año y mes
                [$year, $mes] = explode('-', $periodo);
                // Convertir el número de mes al nombre correspondiente
                $nombreMes = $meses[$mes];
            @endphp
            <tr>
                <td>{{ $nombreMes }} {{ $year }}</td>
                <td>{{ $data[$index] }}</td>
                <td>{{ $diasHabilesPorMesArray[$periodo] ?? 0 }}</td>
            </tr>
        @endforeach
    </tbody>
</table>


<script>
    // Convertir las etiquetas y los datos a arrays
    var labels = @json($labels);
    var dataRegistros = @json($data);
    var diasHabilesPorMesArray = @json($diasHabilesPorMesArray);

    // Arreglo auxiliar para convertir el número de mes a su nombre
    var meses = {
        '01': 'Enero',
        '02': 'Febrero',
        '03': 'Marzo',
        '04': 'Abril',
        '05': 'Mayo',
        '06': 'Junio',
        '07': 'Julio',
        '08': 'Agosto',
        '09': 'Septiembre',
        '10': 'Octubre',
        '11': 'Noviembre',
        '12': 'Diciembre'
    };

    // Convertir las etiquetas para que muestren el nombre del mes
    labels = labels.map(function(label) {
        var [year, mes] = label.split('-'); // Dividir en año y mes
        return `${meses[mes]} ${year}`; // Convertir el mes y año al formato deseado
    });

    // Crear un nuevo array para los días hábiles filtrados
    var diasHabilesData = [];

    // Filtrar los días hábiles para que coincidan con las etiquetas
    labels.forEach(function(label) {
        // Convertir la etiqueta de nuevo al formato "año-mes" para la verificación
        var [mesNombre, year] = label.split(' ');
        var mesNumero = Object.keys(meses).find(key => meses[key] === mesNombre);
        var labelOriginal = `${year}-${mesNumero}`;

        // Si el mes y año de la etiqueta existen en el array de días hábiles
        if (diasHabilesPorMesArray[labelOriginal] !== undefined) {
            diasHabilesData.push(diasHabilesPorMesArray[labelOriginal]);
        } else {
            diasHabilesData.push(0); // Si no hay días hábiles, se coloca un 0
        }
    });

    // Crear la gráfica
    var ctx = document.getElementById('registrosChart').getContext('2d');
    var miGrafica = new Chart(ctx, {
        type: 'bar', // Cambias el yipo de grafico: bar, line, etc.
        data: {
            labels: labels,
            datasets: [{
                    label: 'Registros',
                    data: dataRegistros,
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                },
                {
                    label: 'Horas Disponibles',
                    data: diasHabilesData, // Usar el array filtrado
                    backgroundColor: 'rgba(255, 206, 86, 0.2)',
                    borderColor: 'rgba(255, 206, 86, 1)',
                    borderWidth: 1
                }
            ]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>
