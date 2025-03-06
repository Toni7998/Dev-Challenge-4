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
            <option value="DAW" data-shift="matí">DAW (Matí)</option>
            <option value="DAW" data-shift="tarda">DAW (Tarda)</option>
            <option value="DAM" data-shift="matí">DAM (Matí)</option>
            <option value="DAM" data-shift="tarda">DAM (Tarda)</option>
            <option value="ASIX" data-shift="matí">ASIX (Matí)</option>
            <option value="ASIX" data-shift="tarda">ASIX (Tarda)</option>
            <option value="SMX" data-shift="matí">SMX (Matí)</option>
            <option value="SMX" data-shift="tarda">SMX (Tarda)</option>
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

    <!-- Modal -->
    <div id="modal" class="modal hidden">
        <div class="modal-content">
            <span class="close-button">&times;</span>
            <h3 id="modalTitle"></h3>
            <p id="modalMessage"></p>
            <button id="modalActionButton">OK</button>
        </div>
    </div>

    <script src="{{ asset(path: 'js/calendar.js') }}"></script>
</body>

</html>