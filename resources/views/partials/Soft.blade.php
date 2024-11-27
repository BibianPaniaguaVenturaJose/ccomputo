<h1>Estadístico de Uso de Software por programa educativo</h1>

<!-- Gráfica -->
<div class="grafica">
    <canvas id="graficaSoftwareXAula"></canvas>
</div>

<!-- Tabla de software usados -->
<table class="table table-bordered table-striped table-responsive" id="tablaSoftwareXAula">
    <thead>
        <tr>
            <th>Software</th>
            <th>Alumnos</th>
            <th>Porcentaje (%)</th> <!-- Nueva columna para el porcentaje -->
        </tr>
    </thead>
    <tbody>
        @php
            // Agrupar los datos para que no se repita el software
            $softwareData = [];
            foreach ($labels as $index => $software) {
                if (!isset($softwareData[$software])) {
                    $softwareData[$software] = [
                        'alumnos' => 0,
                        'porcentaje' => 0,
                    ];
                }
                // Sumar los alumnos y porcentajes de cada software
                $softwareData[$software]['alumnos'] += $data[$index];
                $softwareData[$software]['porcentaje'] += $porcentajes[$index];
            }
        @endphp

        @foreach ($softwareData as $software => $info)
            <tr>
                <td>{{ $software }}</td>
                <td>{{ $info['alumnos'] }}</td>
                <td>{{ number_format($info['porcentaje'], 2) }}%</td> <!-- Mostrar porcentaje con 2 decimales -->
            </tr>
        @endforeach
    </tbody>
</table>

<!-- Script para redibujar la gráfica con datos filtrados -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var labels = @json($labels); // Lista de software (etiquetas)
        var data = @json($data); // Total de alumnos por software y carrera
        var carreras = @json($carreras); // Carreras correspondientes a cada software

        // Eliminar el último registro, el de Total General
        labels.pop();

        // Crear un mapa para agrupar los datos por software y carrera
        var softwareData = {};
        labels.forEach(function(software, index) {
            if (!softwareData[software]) {
                softwareData[software] = {};
            }
            if (!softwareData[software][carreras[index]]) {
                softwareData[software][carreras[index]] = 0;
            }
            // Sumar los valores de los alumnos por carrera para cada software
            softwareData[software][carreras[index]] += data[index];
        });

        // Asigna colores específicos a cada carrera
        var colors = {
            'Ambiental': '#FFAD99', // Naranja pastel
            'Sistemas C.': '#99FFB3', // Verde pastel
            'Gestion': '#99BBFF', // Azul pastel
            'Electronica': '#FF99CC', // Rosa pastel
            'Industrial': '#99FFFF', // Turquesa pastel
            'Automotrices': '#FFEB99', // Amarillo pastel
            'Gastronomia': '#D699FF', // Púrpura pastel

            // Se agregan mas carreras y colores, si es necesario
        };

        // Crear los datasets, una barra apilada por software con valores para cada carrera
        var datasets = Object.keys(colors).map(function(carrera) {
            return {
                label: carrera, // Nombre de la carrera
                data: [...new Set(labels)].map(function(
                    software) { // Asegurarse de tener una única barra por software
                    return softwareData[software][carrera] ||
                        0; // Asigna el valor correspondiente, o 0 si no existe
                }),
                backgroundColor: colors[carrera], // Color predefinido de la carrera
                borderColor: colors[carrera],
                borderWidth: 1
            };
        });

        // Crear la gráfica de barras horizontales
        var ctx = document.getElementById('graficaSoftwareXAula').getContext('2d');
        var graficaSoftwareXAula = new Chart(ctx, {
            type: 'bar', // Tipo de gráfica, 'bar' se usa para gráficos de barras horizontales
            data: {
                labels: [...new Set(labels)], // Software (etiquetas únicas)
                datasets: datasets // Se apilarán por carrera
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                indexAxis: 'y', // Esto configura el gráfico para ser horizontal
                scales: {
                    x: {
                        beginAtZero: true,
                        stacked: true
                    },
                    y: {
                        beginAtZero: true,
                        stacked: true
                    }
                }
            }
        });

    });
</script>
