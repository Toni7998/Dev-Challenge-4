const calendar = document.getElementById('calendar');
const currentMonth = document.getElementById('currentMonth');
const prevMonthButton = document.getElementById('prevMonth');
const nextMonthButton = document.getElementById('nextMonth');
const reservationForm = document.getElementById('reservationForm');
const selectedDateSpan = document.getElementById('selectedDate');
let selectedDate = null;
let reservations = [];
let today = new Date();
let currentYear = today.getFullYear();
let currentMonthIndex = today.getMonth();
const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');


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
    const name = document.getElementById('name').value.trim();
    const email = document.getElementById('email').value.trim();
    const phone = document.getElementById('phone').value.trim();
    const course = document.getElementById('course').value.trim();
    const time = document.getElementById('timeSlot').value.trim();

    if (!name || !email || !phone || !course || !time) {
        alert('Todos los campos son obligatorios');
        return;
    }

    // Validar formato de los datos
    if (!validateName(name) || !validateEmail(email) || !validatePhone(phone)) {
        alert('Por favor, verifica que todos los campos estén en el formato correcto.');
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
            const result = await response.json();
            alert('Reserva confirmada!');

            // Agregar la fecha reservada al arreglo de reservas
            reservations.push({ date: selectedDate, hour: time });

            // Actualizar el calendario
            updateCalendar();

            // Ocultar el formulario de reserva
            document.getElementById('bookingForm').style.display = 'none';
        } else {
            const errorData = await response.json();
            alert('Hubo un error al reservar la cita: ' + errorData.message);
        }
    } catch (error) {
        console.error('Error al guardar la reserva:', error);
        alert('Hubo un error al guardar la reserva. Inténtalo más tarde.');
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

    const todayStr = today.toISOString().split('T')[0];

    // Agregar espacios en blanco antes del primer día del mes
    for (let i = 0; i < (firstDay === 0 ? 6 : firstDay - 1); i++) {
        calendar.innerHTML += '<div class="day disabled"></div>';
    }

    // Crear los días del mes
    for (let day = 1; day <= daysInMonth; day++) {
        const dateStr = `${currentYear}-${(currentMonthIndex + 1).toString().padStart(2, '0')}-${day
            .toString()
            .padStart(2, '0')}`;

        let dayClass = 'day';

        if (reservations.some(r => r.date === dateStr)) {
            dayClass += ' reserved'; // Marcar como reservado
        }

        if (dateStr <= todayStr) {
            dayClass += ' disabled'; // Deshabilitar días pasados
        }

        const onClick = dayClass.includes('reserved') || dayClass.includes('disabled')
            ? '' // No agregar evento onclick si está reservado o deshabilitado
            : `onclick="selectDate('${dateStr}')"`;

        calendar.innerHTML += `<div class="${dayClass}" ${onClick}>${day}</div>`;
    }
}

function selectDate(date) {
    if (reservations.some(r => r.date === date)) {
        alert('Aquest dia ja està reservat.');
        return; // Evita abrir el formulario si el día está reservado
    }

    selectedDate = date;
    selectedDateSpan.textContent = date;
    bookingForm.style.display = 'block';

    const availableHours = ['09:00', '10:00', '11:00', '12:00', '13:00']; // Ejemplo de horas fijas

    const timeSlot = document.getElementById('timeSlot');
    timeSlot.innerHTML = ''; // Limpia las opciones anteriores
    availableHours.forEach(hour => {
        const option = document.createElement('option');
        option.value = hour;
        option.textContent = hour;
        timeSlot.appendChild(option);
    });
}


prevMonthButton.addEventListener('click', () => {
    currentMonthIndex--;
    if (currentMonthIndex < 0) {
        currentMonthIndex = 11;
        currentYear--;
    }
    updateCalendar();
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
