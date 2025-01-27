<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reserva Cancelada</title>
    <link href="{{ asset('css/calendar.css') }}" rel="stylesheet">
</head>

<body>
    <div style="text-align: center; padding: 20px;">
        @if (isset($success))
            <h1 style="color: green;">{{ $success }}</h1>
        @elseif (isset($error))
            <h1 style="color: red;">{{ $error }}</h1>
        @endif
        <p>Gracias por informarnos. ¡Esperamos verte en otra ocasión!</p>
    </div>
</body>

</html>