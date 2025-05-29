document.addEventListener('DOMContentLoaded', function () {
    const dateHeading = document.getElementById('date-heading');
    const eventsContainer = document.getElementById('events-container');
    const prevDayButton = document.getElementById('prev-day');
    const nextDayButton = document.getElementById('next-day');

    const urlParams = new URLSearchParams(window.location.search);
    const selectedDate = urlParams.get('date');

    function loadEventsForDate(date) {
        if (date) {
            const formattedDateLong = new Date(date).toLocaleDateString('es-ES', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric',
            });
            dateHeading.textContent = formattedDateLong;

            fetch(`/api/events/${date}`)
                .then(response => {
                    if (!response.ok) throw new Error('No se pudo cargar la lista de eventos');
                    return response.json();
                })
                .then(events => {
                    eventsContainer.innerHTML = ''; 

                    if (events.length === 0) {
                        eventsContainer.innerHTML = '<p class="p-4">No hay eventos para este día.</p>';
                    } else {
                        events.forEach(event => {
                            const startTime = formatTime(event.start_date);
                            const endTime = formatTime(event.end_date);

                            const eventDiv = document.createElement('div');
                            eventDiv.className = 'p-4 border-b border-gray-200';
                            eventDiv.innerHTML = `
                                <h2 class="text-lg font-bold">${event.title}</h2>
                                <p>${event.description || ''}</p>
                                <p><strong>Hora:</strong> ${startTime} - ${endTime}</p>
                            `;
                            eventsContainer.appendChild(eventDiv);
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    eventsContainer.innerHTML = '<p class="p-4 text-red-600">Error al cargar los eventos.</p>';
                });
        } else {
            dateHeading.textContent = 'No se ha seleccionado una fecha';
            eventsContainer.innerHTML = '<p class="p-4">No se ha proporcionado una fecha.</p>';
        }
    }

    function formatTime(dateTimeString) {
        const date = new Date(dateTimeString);
        if (isNaN(date)) return 'Hora no válida';
        const hours = date.getHours().toString().padStart(2, '0');
        const minutes = date.getMinutes().toString().padStart(2, '0');
        return `${hours}:${minutes}`;
    }

    if (selectedDate) {
        loadEventsForDate(selectedDate);
    } else {
        const today = new Date().toISOString().split('T')[0];
        window.location.href = `/events/day?date=${today}`;
    }

    prevDayButton.addEventListener('click', function () {
        if (selectedDate) {
            const currentDate = new Date(selectedDate);
            currentDate.setDate(currentDate.getDate() - 1);
            window.location.href = `/events/day?date=${currentDate.toISOString().split('T')[0]}`;
        } else {
            const today = new Date();
            today.setDate(today.getDate() - 1);
            window.location.href = `/events/day?date=${today.toISOString().split('T')[0]}`;
        }
    });

    nextDayButton.addEventListener('click', function () {
        if (selectedDate) {
            const currentDate = new Date(selectedDate);
            currentDate.setDate(currentDate.getDate() + 1);
            window.location.href = `/events/day?date=${currentDate.toISOString().split('T')[0]}`;
        } else {
            const today = new Date();
            today.setDate(today.getDate() + 1);
            window.location.href = `/events/day?date=${today.toISOString().split('T')[0]}`;
        }
    });
});