<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Login</title>
    <link rel="stylesheet" href="{{ asset('assets/css/stylesLogin.css') }}">
</head>

<body>
    <header>
        <div class="header-image">
            <img src="{{ asset('assets/resources/LOGO_TECNM_BLANCO.png') }}" alt="ITSUR Logo">
        </div>
    </header>


    <form action="/login" method="POST">
        @csrf

        <div class="mb-3">
            <label for="clave">Clave</label>
            <input type="text" name="clave" id="clave" required autocomplete="off">
        </div>

        <!-- Manejo de errores de autenticación -->
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <button type="submit">Ingresar</button>
    </form>

</body>

</html>
