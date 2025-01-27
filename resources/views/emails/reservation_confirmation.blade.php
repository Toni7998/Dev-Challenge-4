<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmación de Reserva</title>
</head>

<body>
    <h1>¡Hola, {{ $reservation['name'] }}!</h1>
    <p>Gracias por realizar tu reserva.</p>
    <p>Estos son los detalles de tu reserva:</p>
    <ul>
        <li><strong>Fecha:</strong> {{ $reservation['date'] }}</li>
        <li><strong>Hora:</strong> {{ $reservation['hour'] }}</li>
        <li><strong>Curso:</strong> {{ $reservation['course'] }}</li>
        <li><strong>Teléfono:</strong> {{ $reservation['phone'] }}</li>
    </ul>

    <p>Si no puedes asistir, puedes cancelar tu reserva haciendo clic en el siguiente enlace:</p>
    <p>
        <a href="{{ $cancelUrl }}" style="color: #f33; text-decoration: underline;">
            Cancelar mi reserva
        </a>
    </p>

    <p>Si tienes alguna pregunta, no dudes en contactarnos.</p>
    <p>Saludos,<br>El equipo de {{ config('app.name') }}</p>
</body>

</html>