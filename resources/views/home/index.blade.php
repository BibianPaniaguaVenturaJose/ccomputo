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
                    <label for="nombreDocente" class="custom-label">Nombre Docente</label>
                    <input type="text" id="nombreDocente" name="nombreDocente" disabled value="{{ $user->nombre }}"
                        readonly>
                    <input type="hidden" id="nombreDocenteHidden" name="nombreDocente" value="{{ $user->nombre }}">
                </div>

                <div class="mb-4">
                    <label for="aulas" class="custom-label">Aula</label>
                    <select id="aulas" name="aula" class="selec">
                        <option value="" disabled selected>Seleccione un aula</option>
                        <!-- Opciones de aula -->
                    </select>
                </div>

                <div class="mb-4">
                    <label for="carrera" class="custom-label">Carrera</label>
                    <input type="text" id="carrera" class="selec" disabled readonly>
                    <input type="hidden" id="hcarrera" name="carrera" readonly />
                    <!-- Este es el campo que se envía -->
                </div>

                <div class="mb-4">
                    <label for="materias" class="custom-label">Materia</label>
                    <select id="materias" name="materia" class="selec">
                        <option value="" disabled selected>Seleccione una materia</option>
                        <!-- Opciones de materia -->
                    </select>
                </div>

                <div class="mb-4">
                    <label for="numAlumnos" class="custom-label">N. Alumnos</label>
                    <input type="number" id="numAlumnos" name="numAlumnos" min="1" max="40"
                        autocomplete="off">
                </div>

                <div class="mb-4">
                    <label for="comentario" class="custom-label">Comentario</label><br>
                    <textarea id="comentario" name="comentario" rows="4" cols="30"></textarea>
                </div>
            </div>

            <div class="cont-ind">
                <div class="mb-4">
                    <label for="software-checkboxes" class="custom-label">Software</label><br>
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

            var idCarrera = 0;
            // Recuperar la clave de la sesión
            var claveDocente = '{{ $user->clave }}';

            // Cargar las materias asignadas al docente al acceder
            fetch(`/api/materias/${claveDocente}`) // Usando la clave del docente
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


            const materiasSelect = document.getElementById('materias');
            materiasSelect.addEventListener('change', function() {
                const selectedMateria = materiasSelect.value; // Obtener el idMateria seleccionado

                if (selectedMateria) {
                    // Llamar a la API para obtener la carrera usando el idMateria
                    fetch(`/api/materias/carrera/${selectedMateria}`)
                        .then(response => response.json())
                        .then(data => {
                            const carreraInput = document.getElementById('carrera');
                            const carreraHidden = document.getElementById('hcarrera');
                            // Comprobar si se recibió un dato válido de carrera
                            if (data && data.carrera) {
                                carreraInput.value = data.carrera; // Asignar el nombre de la carrera
                                carreraHidden.value = data.ID;
                            } else {
                                carreraInput.value = 'Carrera no encontrada';
                                carreraHidden.value = '';
                            }
                            carreraInput.disabled = true; // Deshabilitar el campo

                        })
                        .catch(error => console.error('Error al cargar la carrera:', error));
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

                    // Añadir evento para cuando se seleccione un aula
                    aulasSelect.addEventListener('change', function() {
                        const selectedAulaId = this.value;
                        cargarSoftwarePorAula(selectedAulaId);
                    });
                })
                .catch(error => console.error('Error al cargar las aulas:', error));

            // Función para cargar el software basado en el aula seleccionada
            function cargarSoftwarePorAula(idAula) {
                const softwareUrl = `/api/software/${idAula}`; // Asume que tienes una API que acepta el idAula
                fetch(softwareUrl)
                    .then(response => response.json())
                    .then(data => {
                        const softwareCheckboxes = document.getElementById('software-checkboxes');
                        softwareCheckboxes.innerHTML = ''; // Limpiar los checkboxes previos
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
            }


            document.querySelector('.form-sol').addEventListener('submit', function(e) {
                // Limpia los mensajes de error previos
                document.querySelectorAll('.error-message').forEach(el => el.remove());

                let valid = true;

                const aula = document.getElementById('aulas').value;
                const materia = document.getElementById('materias').value;
                const carrera = document.getElementById('hcarrera').value;
                const numAlumnos = document.getElementById('numAlumnos').value;
                const softwareCheckboxes = document.querySelectorAll(
                    '#software-checkboxes input[type="checkbox"]:checked');

                // Función para mostrar el mensaje de error
                function showError(element, message) {
                    const error = document.createElement('span');
                    error.className = 'error-message';
                    error.style.color = 'red';
                    error.textContent = message;
                    element.parentNode.appendChild(error);
                }

                // Validaciones
                if (!aula) {
                    showError(document.getElementById('aulas'), 'Debe seleccionar una aula.');
                    valid = false;
                }

                if (!materia) {
                    showError(document.getElementById('materias'), 'Debe seleccionar una materia.');
                    valid = false;
                }

                if (!carrera) {
                    showError(document.getElementById('hcarrera'), 'Debe seleccionar una carrera.');
                    valid = false;
                }

                if (!numAlumnos || isNaN(numAlumnos) || numAlumnos < 1 || numAlumnos > 40) {
                    showError(document.getElementById('numAlumnos'),
                        'Debe ingresar una cantidad de alumnos entre 1 y 40.');
                    valid = false;
                }

                if (softwareCheckboxes.length === 0) {
                    showError(document.getElementById('software-checkboxes'),
                        'Debe seleccionar al menos un software.');
                    valid = false;
                }

                // Prevenir el envío del formulario si no es válido
                if (!valid) {
                    e.preventDefault();
                }
            });

        });
    </script>
</body>

</html>
