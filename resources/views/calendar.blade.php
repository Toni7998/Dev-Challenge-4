<!DOCTYPE html>
<html lang="ca">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cita Prèvia Institut Baix Camp</title>
    <link href="{{ asset('css/calendar.css') }}" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    </head>

<body>
    <div class="container">
        <h2>Reserva la teva cita</h2>

        <label for="course">Selecciona el teu curs:</label>
        <select id="course" required>
            <option value="">-- Selecciona un curs --</option>
            <option value="DAW">DAW (matí)</option>
            <option value="DAM">DAM (tarda)</option>
            <option value="SMX">SMX (matí)</option>
        </select>

        <div class="calendar-container">
            <div class="calendar-header">
                <button id="prevMonth">&lt;</button>
                <h3 id="currentMonth">Gener 2025</h3>
                <button id="nextMonth">&gt;</button>
            </div>

            <div class="weekdays">
                <div>Dill</div>
                <div>Dim</div>
                <div>Dic</div>
                <div>Dij</div>
                <div>Div</div>
                <div>Dis</div>
                <div>Dom</div>
            </div>

            <div class="calendar-grid" id="calendar"></div>
        </div>

        <!-- En el formulario de reservas -->
        <div id="bookingForm" class="hidden">
            <h3>Detalls de la reserva</h3>
            <label for="timeSlot">Selecciona l'hora:</label>
            <select id="timeSlot" required></select>
            <input type="text" id="name" placeholder="El teu nom" required>
            <input type="email" id="email" placeholder="El teu correu" required>
            <input type="tel" id="phone" placeholder="Telèfon" required>
            <button id="confirmBooking">Confirmar Cita</button>
            <!-- Aquí añadimos un span para mostrar la fecha seleccionada -->
            <p>Data seleccionada: <span id="selectedDate"></span></p>
        </div>
    </div>

    <script src="{{ asset('js/calendar.js') }}"></script>
</body>

</html>