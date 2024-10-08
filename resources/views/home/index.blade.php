<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Aulas</title>
    <link rel="stylesheet" href="{{ asset('assets/css/stylesIndex.css') }}">
</head>

<body>
    <header>
        <div class="header-image">
            <img src="{{ asset('assets/resources/LOGO_TECNM_BLANCO.png') }}" alt="ITSUR Logo">
        </div>
    </header>

    <form action="/home" method="POST" class="form-sol">
        @csrf
        <div class="container">
            <div class="cont-ind">
                <div class="mb-4">
                    <label for="nombreDocente">Nombre Docente</label>
                    <input type="text" id="nombreDocente" name="nombreDocente" disabled value="{{ $user->nombre }}"
                        readonly>
                    <input type="hidden" id="nombreDocenteHidden" name="nombreDocente" value="{{ $user->nombre }}">
                </div>

                <div class="mb-4">
                    <label for="aulas">Aula</label>
                    <select id="aulas" name="aula" class="selec">
                        <option value="" disabled selected>Seleccione un aula</option>
                        <!-- Opciones de aula -->
                    </select>
                </div>

                <div class="mb-4">
                    <label for="carreras">Carrera</label>
                    <select id="carreras" name="carrera" class="selec">
                        <option value="" disabled selected>Seleccione una carrera</option>
                        <!-- Opciones de carrera -->
                    </select>
                </div>

                <div class="mb-4">
                    <label for="materias">Materia</label>
                    <select id="materias" name="materia" class="selec">
                        <option value="" disabled selected>Seleccione una materia</option>
                        <!-- Opciones de materia -->
                    </select>
                </div>

                <div class="mb-4">
                    <label for="numAlumnos">N. Alumnos</label>
                    <input type="number" id="numAlumnos" name="numAlumnos" min="1" max="40"
                        autocomplete="off">
                </div>

                <div class="mb-4">
                    <label for="comentario">Comentario</label><br>
                    <textarea id="comentario" name="comentario" rows="10" cols="30"></textarea>
                </div>
            </div>

            <div class="cont-ind">
                <div class="mb-4">
                    <label for="software">Software</label><br>
                    <div id="software-checkboxes" name="software">
                        <!-- Los checkboxes se cargarán aquí -->
                    </div>
                </div>
                <button type="submit">Registrar Salida</button>
            </div>
        </div>
    </form>


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const carrerasUrl = '/api/carreras';
            const materiasUrl = '/api/materias';
            const softwareUrl = '/api/software';

            // Cargar las carreras al inicio
            fetch(carrerasUrl)
                .then(response => response.json())
                .then(data => {
                    const carrerasSelect = document.getElementById('carreras');
                    carrerasSelect.innerHTML =
                        '<option value="" disabled selected>Seleccione una carrera</option>';
                    data.forEach(carrera => {
                        const option = document.createElement('option');
                        option.value = carrera.idCarrera;
                        option.textContent = carrera.carrera;
                        carrerasSelect.appendChild(option);
                    });
                })
                .catch(error => console.error('Error al cargar las carreras:', error));

            // Escuchar el cambio en la selección de carrera
            const carrerasSelect = document.getElementById('carreras');
            carrerasSelect.addEventListener('change', function() {
                const selectedCarrera = carrerasSelect.value;

                if (selectedCarrera) {
                    // Llamar a la API para obtener las materias según la carrera seleccionada
                    fetch(`/api/materias/${selectedCarrera}`)
                        .then(response => response.json())
                        .then(data => {
                            const materiasSelect = document.getElementById('materias');
                            materiasSelect.innerHTML =
                                '<option value="" disabled selected>Seleccione una materia</option>';
                            data.materias.forEach(materia => {
                                const option = document.createElement('option');
                                option.value = materia.idMateria;
                                option.textContent = materia.nombre;
                                materiasSelect.appendChild(option);
                            });
                        })
                        .catch(error => console.error('Error al cargar las materias:', error));
                }
            });

            // Cargar las aulas al inicio
            const aulasUrl = '/api/aulas';
            fetch(aulasUrl)
                .then(response => response.json())
                .then(data => {
                    const aulasSelect = document.getElementById('aulas');
                    aulasSelect.innerHTML = '<option value="" disabled selected>Seleccione un aula</option>';
                    data.forEach(aula => {
                        const option = document.createElement('option');
                        option.value = aula.idAula;
                        option.textContent = aula.nombre;
                        aulasSelect.appendChild(option);
                    });
                })
                .catch(error => console.error('Error al cargar las aulas:', error));

            // Cargar el software al inicio y generar los checkboxes
            fetch(softwareUrl)
                .then(response => response.json())
                .then(data => {
                    const softwareCheckboxes = document.getElementById('software-checkboxes');
                    data.forEach(software => {
                        const checkbox = document.createElement('input');
                        checkbox.type = 'checkbox';
                        checkbox.id = `software_${software.idSoftware}`;
                        checkbox.name = 'software[]';
                        checkbox.value = software.idSoftware;

                        const label = document.createElement('label');
                        label.htmlFor = `software_${software.idSoftware}`;
                        label.textContent = software.nombre;

                        const div = document.createElement('div');
                        div.appendChild(checkbox);
                        div.appendChild(label);

                        softwareCheckboxes.appendChild(div);
                    });
                })
                .catch(error => console.error('Error al cargar el software:', error));

        });

        document.querySelector('.form-sol').addEventListener('submit', function(e) {
            let valid = true;


            const aula = document.getElementById('aulas').value;
            const carrera = document.getElementById('carreras').value;
            const materia = document.getElementById('materias').value;
            const numAlumnos = document.getElementById('numAlumnos').value;
            const softwareCheckboxes = document.querySelectorAll(
                '#software-checkboxes input[type="checkbox"]:checked');

            if (!aula) {
                alert('Debe seleccionar una aula.');
                valid = false;
            }

            if (!carrera) {
                alert('Debe seleccionar una carrera.');
                valid = false;
            }

            if (!materia) {
                alert('Debe seleccionar una materia.');
                valid = false;
            }

            if (!numAlumnos || isNaN(numAlumnos) || numAlumnos < 1 || numAlumnos > 40) {
                alert('Debe ingresar una cantidad de alumnos');
                valid = false;
            }

            if (softwareCheckboxes.length === 0) {
                alert('Debe seleccionar al menos un software.');
                valid = false;
            }

            if (!valid) {
                e.preventDefault();
            }
        });
    </script>
</body>

</html>
