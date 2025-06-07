<!DOCTYPE html>
<html lang="ca">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Gestió de Disponibilitat - Institut Baix Camp</title>
    <link href="{{ asset('css/calendar.css') }}" rel="stylesheet" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <style>
        body {
            background: #f4f6f8;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            color: #333;
        }

        .container {
            max-width: 480px;
            margin: 40px auto;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 8px 16px rgb(0 0 0 / 0.1);
            padding: 24px 32px;
        }

        h2 {
            text-align: center;
            margin-bottom: 24px;
            color: #004085;
        }

        label {
            display: block;
            margin-top: 16px;
            font-weight: 600;
            color: #0056b3;
        }

        input[type="date"],
        select {
            width: 100%;
            padding: 10px 12px;
            margin-top: 6px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }

        input[type="date"]:focus,
        select:focus {
            border-color: #0056b3;
            outline: none;
        }

        .checkbox-group {
            margin-top: 12px;
            max-height: 180px;
            overflow-y: auto;
            border: 1px solid #ddd;
            padding: 12px;
            border-radius: 6px;
            background-color: #fafafa;
        }

        .checkbox-group label {
            display: flex;
            align-items: center;
            margin-bottom: 8px;
            font-weight: normal;
            color: #212529;
            cursor: pointer;
        }

        .checkbox-group input[type="checkbox"] {
            margin-right: 10px;
            width: 18px;
            height: 18px;
            cursor: pointer;
        }

        .btn-primary {
            width: 100%;
            margin-top: 24px;
            padding: 12px 0;
            background-color: #0056b3;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .btn-primary:hover {
            background-color: #003d80;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            padding: 12px 16px;
            border-radius: 6px;
            margin-bottom: 20px;
            border: 1px solid #c3e6cb;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Gestió de Disponibilitat</h2>

        @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <form id="availabilityForm">
            <label for="date">Dia:</label>
            <input type="date" name="date" id="date" required>

            <label for="shift">Torn:</label>
            <select name="shift" id="shift" required>
                <option value="matí">Matí</option>
                <option value="tarda">Tarda</option>
            </select>

            <label for="hours">Hores disponibles:</label>
            <div class="checkbox-group" id="hours">
                @foreach (['08:00','09:00','10:00','11:00','12:00','13:00','15:00','16:00','17:00','18:00','19:00','20:00'] as $hour)
                <label>
                    <input type="checkbox" name="hours[]" value="{{ $hour }}">
                    {{ $hour }}
                </label>
                @endforeach
            </div>

            <button type="submit" class="btn-primary">Guardar</button>
        </form>

        <div id="message" style="margin-top:20px; text-align:center; font-weight:bold;"></div>
    </div>
</body>

<script>
    const form = document.getElementById('availabilityForm');
    const messageDiv = document.getElementById('message');
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        messageDiv.textContent = '';
        const date = document.getElementById('date').value;
        const shift = document.getElementById('shift').value;
        const checkboxes = document.querySelectorAll('input[name="hours[]"]:checked');
        const hours = Array.from(checkboxes).map(cb => cb.value);

        if (hours.length === 0) {
            messageDiv.style.color = 'red';
            messageDiv.textContent = 'Selecciona almenys una hora.';
            return;
        }

        // Desactivar botón para evitar múltiples envíos
        form.querySelector('button[type="submit"]').disabled = true;

        try {
            for (const hour of hours) {
                // Construimos el payload solo con los campos necesarios
                const payload = {
                    date,
                    hour,
                    shift,
                };

                const response = await fetch('/reserve', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify(payload)
                });

                if (!response.ok) {
                    const error = await response.json();
                    throw new Error(`Error reservant hora ${hour}: ${error.message || JSON.stringify(error)}`);
                }
            }

            messageDiv.style.color = 'green';
            messageDiv.textContent = 'Disponibilitat guardada correctament!';
            form.reset();

        } catch (error) {
            messageDiv.style.color = 'red';
            messageDiv.textContent = error.message;
        } finally {
            form.querySelector('button[type="submit"]').disabled = false;
        }
    });
</script>



</html>