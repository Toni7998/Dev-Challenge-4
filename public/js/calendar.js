const calendar = document.getElementById('calendar');
const currentMonth = document.getElementById('currentMonth');
const prevMonthButton = document.getElementById('prevMonth');
const nextMonthButton = document.getElementById('nextMonth');
const reservationForm = document.getElementById('bookingForm');
const selectedDateSpan = document.getElementById('selectedDate');
let selectedDate = null;
let reservations = [];
let today = new Date();
let currentYear = today.getFullYear();
let currentMonthIndex = today.getMonth();
const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
const timeSlot = document.getElementById('timeSlot');
const courseSelect = document.getElementById('course');

// Modal functions
const modal = document.getElementById('modal');
const modalTitle = document.getElementById('modalTitle');
const modalMessage = document.getElementById('modalMessage');
const modalActionButton = document.getElementById('modalActionButton');
const closeButton = document.querySelector('.close-button');

function showModal(title, message, callback = null) {
    modalTitle.textContent = title;
    modalMessage.textContent = message;
    modal.classList.remove('hidden');

    const closeModal = () => {
        modal.classList.add('hidden');
        if (callback) callback();
    };

    modalActionButton.onclick = closeModal;

    // Elimina cualquier otro evento que cierre el modal, como hacer clic fuera del modal
    window.onclick = null;
}

// Horarios de mañana y tarde
const schedule = {
    matí: ['08:00', '09:00', '10:00', '11:00', '12:00', '13:00'],
    tarda: ['15:00', '16:00', '17:00', '18:00', '19:00', '20:00']
};

async function loadReservations() {
    try {
        const response = await fetch('http://127.0.0.1:8000/api/reservations', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
            },
        });

        if (response.ok) {
            reservations = await response.json();
            updateCalendar(); // Actualiza el calendario con las reservas cargadas
        } else {
            console.error('Error al cargar las reservas:', await response.text());
        }
    } catch (error) {
        console.error('Error al cargar las reservas:', error);
    }
}

// Llama a loadReservations al cargar la página
document.addEventListener('DOMContentLoaded', () => {
    loadReservations();
});

// Actualiza las horas disponibles según el curso y su turno
function updateAvailableHours() {
    if (!selectedDate) return; // Si no hay una fecha seleccionada, no hacemos nada

    const selectedOption = courseSelect.options[courseSelect.selectedIndex];
    const shift = selectedOption?.getAttribute('data-shift'); // "matí" o "tarda"

    if (!shift) return;

    timeSlot.innerHTML = ''; // Limpiar opciones previas

    // Obtener las reservas del día en la misma franja horaria
    const reservedHours = reservations
        .filter(r => r.date === selectedDate && schedule[shift].includes(r.hour))
        .map(r => r.hour);

    // Generar opciones disponibles
    schedule[shift].forEach(hour => {
        const option = document.createElement('option');
        option.value = hour;
        option.textContent = reservedHours.includes(hour) ? `${hour} (Reservada)` : hour;
        option.disabled = reservedHours.includes(hour);
        timeSlot.appendChild(option);
    });
}

// Evento para actualizar horas al cambiar curso
courseSelect.addEventListener('change', () => {
    updateAvailableHours();
});

function validateName(name) {
    const nameRegex = /^[A-Za-zÀ-ÿ\s]{2,}$/; // Permite letras, espacios, y caracteres acentuados
    return nameRegex.test(name);
}

function validateEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/; // Validación básica de email
    return emailRegex.test(email);
}

function validatePhone(phone) {
    const phoneRegex = /^[0-9]{8,15}$/; // Solo números (8 a 15 dígitos)
    return phoneRegex.test(phone);
}

async function saveReservation() {
    const confirmButton = document.getElementById('confirmBooking');
    confirmButton.disabled = true; // Deshabilitar el botón mientras se procesa

    const name = document.getElementById('name').value.trim();
    const email = document.getElementById('email').value.trim();
    const phone = document.getElementById('phone').value.trim();
    const course = document.getElementById('course').value.trim();
    const time = document.getElementById('timeSlot').value.trim();

    if (!name || !email || !phone || !course || !time) {
        showModal('Camp Obligatori', 'Tots els camps són obligatoris.');
        confirmButton.disabled = false;
        return;
    }

    if (!validateName(name) || !validateEmail(email) || !validatePhone(phone)) {
        showModal('Format Invàlid', 'Verifica que tots els camps tinguin el format correcte.');
        confirmButton.disabled = false;
        return;
    }

    // Verificar si ya hay una reserva para el día y la hora seleccionada
    if (reservations.some(r => r.date === selectedDate && r.hour === time)) {
        showModal('Hora No Disponible', 'Aquesta hora ja està reservada. Tria una altra hora.');
        confirmButton.disabled = false;
        return;
    }

    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const data = {
        date: selectedDate,
        course: course,
        name: name,
        email: email,
        phone: phone,
        hour: time,
    };

    try {
        const response = await fetch('http://127.0.0.1:8000/reserve', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
            body: JSON.stringify(data),
        });

        if (response.ok) {
            showModal(
                'Reserva Confirmada ✅',
                'La teva cita s\'ha reservat correctament. 📅\n\n' +
                '🔹 Revisa el teu correu electrònic 📩 per rebre tota la informació sobre la teva reserva.\n\n' +
                '🔹 Si no el trobes, comprova la carpeta de correu no desitjat o spam. 📬',
                () => {
                    reservations.push({ date: selectedDate, hour: time });
                    updateCalendar();
                    document.getElementById('bookingForm').style.display = 'none';
                });
        } else {
            const errorData = await response.json();
            showModal('Error en la Reserva ❌', 'Hi ha hagut un error: ' + errorData.message);
        }
    } catch (error) {
        console.error('Error al guardar la reserva:', error);
        showModal('Error ❌', 'No s\'ha pogut guardar la reserva. Torna-ho a intentar més tard.');
    } finally {
        confirmButton.disabled = false;
    }
}


document.getElementById('confirmBooking').addEventListener('click', saveReservation);

function updateCalendar() {
    calendar.innerHTML = '';
    const firstDay = new Date(currentYear, currentMonthIndex, 1).getDay();
    const daysInMonth = new Date(currentYear, currentMonthIndex + 1, 0).getDate();

    currentMonth.textContent = new Date(currentYear, currentMonthIndex).toLocaleString('ca-ES', {
        month: 'long',
        year: 'numeric',
    });

    const todayStr = today.toISOString().split('T')[0]; // Obtiene la fecha de hoy en formato 'YYYY-MM-DD'
    const todayDateStr = `${today.getFullYear()}-${(today.getMonth() + 1).toString().padStart(2, '0')}-${today.getDate().toString().padStart(2, '0')}`;

    if (currentYear === today.getFullYear() && currentMonthIndex === today.getMonth()) {
        prevMonthButton.disabled = true;
    } else {
        prevMonthButton.disabled = false;
    }

    // Espacios en blanco antes del primer día del mes
    for (let i = 0; i < (firstDay === 0 ? 6 : firstDay - 1); i++) {
        const dayDiv = document.createElement('div');
        dayDiv.classList.add('day', 'disabled');
        calendar.appendChild(dayDiv);
    }

    for (let day = 1; day <= daysInMonth; day++) {
        const dateStr = `${currentYear}-${(currentMonthIndex + 1).toString().padStart(2, '0')}-${day.toString().padStart(2, '0')}`;
        const dayDiv = document.createElement('div');
        dayDiv.classList.add('day');

        const reservedHours = reservations.filter(r => r.date === dateStr).map(r => r.hour);

        // Si es un día pasado con reservas, marcarlo en amarillo
        if (dateStr < todayStr && reservedHours.length > 0) {
            dayDiv.classList.add('past-reserved'); // Añadir clase para días pasados con reservas
        }

        // Verificamos si el día está lleno o tiene reservas
        if (reservedHours.length === 5) {
            dayDiv.classList.add('full');
        } else if (reservedHours.length > 0) {
            dayDiv.classList.add('reserved');
        }

        // Si el día es pasado, marcamos como disabled para que no se pueda seleccionar
        if (dateStr < todayStr) {
            dayDiv.classList.add('disabled');
        }

        // Resaltamos el día actual
        if (dateStr === todayDateStr) {
            dayDiv.classList.add('disabled');
        }

        // Función de selección para los días disponibles
        const onClick = (reservedHours.length === 5 || dayDiv.classList.contains('disabled'))
            ? null
            : function () { selectDate(dateStr, reservedHours); };

        dayDiv.textContent = day;
        dayDiv.onclick = onClick;
        calendar.appendChild(dayDiv);
    }
}

updateCalendar(); // Inicializar el calendario al cargar

function selectDate(date) {
    if (!courseSelect.value) {
        showModal('Selecció Requerida', 'Si us plau, selecciona un grup abans de triar un dia.');
        return;
    }

    // Remover la clase 'selected' de todos los días previamente seleccionados
    document.querySelectorAll('.day.selected').forEach(day => day.classList.remove('selected'));

    // Buscar el elemento del día seleccionado y añadir la clase 'selected'
    const dayElements = document.querySelectorAll('.calendar-grid .day');
    dayElements.forEach(dayElement => {
        if (dayElement.textContent === date.split('-')[2]) {
            dayElement.classList.add('selected');
        }
    });

    selectedDate = date;
    selectedDateSpan.textContent = date;
    bookingForm.style.display = 'block';

    updateAvailableHours();
}


prevMonthButton.addEventListener('click', () => {
    if (currentYear > today.getFullYear() || (currentYear === today.getFullYear() && currentMonthIndex > today.getMonth())) {
        currentMonthIndex--;
        if (currentMonthIndex < 0) {
            currentMonthIndex = 11;
            currentYear--;
        }
        updateCalendar();
    }
});

nextMonthButton.addEventListener('click', () => {
    currentMonthIndex++;
    if (currentMonthIndex > 11) {
        currentMonthIndex = 0;
        currentYear++;
    }
    updateCalendar();
});

updateCalendar();
