<!DOCTYPE html>
<html lang="ca">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reserva Anul·lada</title>
</head>

<body>
    @if (isset($success))
        <h1>Reserva anul·lada</h1>
        <p>{{ $success }}</p>
    @elseif (isset($error))
        <h1>Error</h1>
        <p>{{ $error }}</p>
    @endif
</body>

</html>