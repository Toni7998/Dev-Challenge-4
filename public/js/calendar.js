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
            showModal('Reserva Confirmada ✅', 'La teva cita s\'ha reservat correctament.', () => {
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

    const todayStr = today.toISOString().split('T')[0];

    // 🔒 Deshabilitar botón de "Mes anterior" si se muestra el mes actual o anterior
    if (currentYear === today.getFullYear() && currentMonthIndex === today.getMonth()) {
        prevMonthButton.disabled = true;
    } else {
        prevMonthButton.disabled = false;
    }

    // Espacios en blanco antes del primer día del mes
    for (let i = 0; i < (firstDay === 0 ? 6 : firstDay - 1); i++) {
        calendar.innerHTML += '<div class="day disabled"></div>';
    }

    // Crear los días del mes
    for (let day = 1; day <= daysInMonth; day++) {
        const dateStr = `${currentYear}-${(currentMonthIndex + 1).toString().padStart(2, '0')}-${day.toString().padStart(2, '0')}`;
        let dayClass = 'day';

        if (reservations.some(r => r.date === dateStr)) {
            dayClass += ' reserved';
        }

        if (dateStr < todayStr) {
            dayClass += ' disabled';
        }

        const onClick = dayClass.includes('reserved') || dayClass.includes('disabled')
            ? ''
            : `onclick="selectDate('${dateStr}')"`;

        calendar.innerHTML += `<div class="${dayClass}" ${onClick}>${day}</div>`;
    }
}

updateCalendar(); // Inicializar el calendario al cargar

function selectDate(date) {
    if (reservations.some(r => r.date === date)) {
        showModal('Data No Disponible', 'Aquest dia ja està reservat.');
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

    // Solo permite retroceder si el mes mostrado es posterior al actual
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
