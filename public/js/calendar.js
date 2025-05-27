document.addEventListener('DOMContentLoaded', function () {
    const calendar = document.getElementById('calendar');
    const title = document.getElementById('calendar-title');
    const prevBtn = document.getElementById('prev-month');
    const nextBtn = document.getElementById('next-month');

    let currentDate = new Date();
    const minYear = currentDate.getFullYear() - 2;
    const maxYear = currentDate.getFullYear() + 2;

    function renderCalendar(date) {
        const year = date.getFullYear();
        const month = date.getMonth();
        const daysInMonth = new Date(year, month + 1, 0).getDate();
        let startDay = new Date(year, month, 1).getDay();
        startDay = (startDay + 6) % 7; 
        title.textContent = date.toLocaleString('en-US', { month: 'long', year: 'numeric' });

        calendar.innerHTML = '';

        const daysOfWeek = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
        daysOfWeek.forEach(day => {
            const div = document.createElement('div');
            div.textContent = day;
            div.classList.add('header');
            calendar.appendChild(div);
        });

        const totalCells = 42;
        const days = [];

        for (let i = startDay; i > 0; i--) {
            days.push({ day: new Date(year, month, 0).getDate() - i + 1, currentMonth: false });
        }

        for (let i = 1; i <= daysInMonth; i++) {
            days.push({ day: i, currentMonth: true });
        }

        while (days.length < totalCells) {
            days.push({ day: days.length - (startDay + daysInMonth) + 1, currentMonth: false });
        }

        days.forEach(({ day: dayNum, currentMonth }) => {
            const dayCell = document.createElement('div');
            dayCell.classList.add('day');
            dayCell.setAttribute('role', 'gridcell');
            dayCell.setAttribute('tabindex', currentMonth ? '0' : '-1');

            if (!currentMonth) dayCell.classList.add('outside');

            const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(dayNum).padStart(2, '0')}`;
            dayCell.dataset.fullDate = dateStr;

            const dayLabel = document.createElement('div');
            dayLabel.classList.add('font-bold', 'mb-1', 'text-sm');
            dayLabel.textContent = dayNum;
            dayCell.appendChild(dayLabel);

            if (currentMonth) {
                const eventsToday = calendarEvents.filter(ev => {
                    const eventStartDate = new Date(ev.start_date);
                    const eventDateString = `${eventStartDate.getFullYear()}-${String(eventStartDate.getMonth() + 1).padStart(2, '0')}-${String(eventStartDate.getDate()).padStart(2, '0')}`;
                    return eventDateString === dateStr;
                });

                eventsToday.forEach(ev => {
                    const eventDiv = document.createElement('div');
                    eventDiv.textContent = ev.title;
                    eventDiv.classList.add('event-item');
                    eventDiv.dataset.eventId = ev.id;
                    eventDiv.style.backgroundColor = ev.color ? ev.color.hex : '#60a5fa';
                    eventDiv.style.color = ev.color ? ev.color.text_color || 'black' : 'black';

                    eventDiv.setAttribute('role', 'button');
                    eventDiv.setAttribute('tabindex', '0');
                    eventDiv.setAttribute('aria-label', `Event: ${ev.title}. Created by: ${ev.creator?.name || 'Unknown'}. Start: ${new Date(ev.start_date).toLocaleString('en-US')}. End: ${new Date(ev.end_date).toLocaleString('en-US')}. Click to view details.`);

                    dayCell.appendChild(eventDiv);
                });
            }

            calendar.appendChild(dayCell);
        });
    }

    function changeMonth(delta) {
        currentDate.setMonth(currentDate.getMonth() + delta);
        if (currentDate.getFullYear() < minYear) currentDate.setFullYear(minYear);
        if (currentDate.getFullYear() > maxYear) currentDate.setFullYear(maxYear);
        renderCalendar(currentDate);
    }

    prevBtn.addEventListener('click', () => changeMonth(-1));
    nextBtn.addEventListener('click', () => changeMonth(1));

    document.addEventListener('keydown', function(e) {
        if (e.key === 'ArrowLeft') {
            changeMonth(-1);
        } else if (e.key === 'ArrowRight') {
            changeMonth(1);
        }
    });

    renderCalendar(currentDate);

    calendar.addEventListener('dblclick', function (event) {
        const clickedDay = event.target.closest('.day');
        if (!clickedDay || clickedDay.classList.contains('outside')) return;

        const formattedDate = clickedDay.dataset.fullDate;
        if (formattedDate) {
            window.location.href = `/events/create?date=${formattedDate}`;
        }
    });

    calendar.addEventListener('click', function (event) {
        const clickedEvent = event.target.closest('.event-item');
        if (!clickedEvent) return;

        const eventId = clickedEvent.dataset.eventId;
        const eventData = calendarEvents.find(ev => ev.id == eventId);

        if (!eventData) return;

        showEventPopup(eventData);
    });

    function showEventPopup(eventData) {
        if (!eventData) return;

        const overlay = document.createElement('div');
        overlay.id = 'popup-overlay';
        overlay.setAttribute('role', 'dialog');
        overlay.setAttribute('aria-modal', 'true');
        overlay.setAttribute('aria-labelledby', 'popup-title');

        overlay.addEventListener('click', function (e) {
            if (e.target === overlay) closePopup();
        });

        const popup = document.createElement('div');
        popup.classList.add('event-popup');
        popup.setAttribute('tabindex', "-1");

        const usersList = eventData.users || [];
        const currentUserId = currentAuthUserId;

        let creatorInfoHtml = `
            <p><strong>Creator:</strong> ${eventData.creator?.name || 'Unknown'} (${eventData.creator?.email || ''})
                ${eventData.is_creator ? '' : ' (Not editable)'}
            </p>
        `;

        const sharedUsers = usersList.filter(user => user.id !== eventData.creator_id);

        let sharedWithHtml = '';
        if (sharedUsers.length > 0) {
            sharedWithHtml += `<p><strong>Shared with:</strong></p><ul>`;
            sharedUsers.forEach(user => {
                sharedWithHtml += `<li>${user.name} (${user.email})</li>`;
            });
            sharedWithHtml += `</ul>`;
        } else {
            sharedWithHtml += `<p><strong>Shared with:</strong> No other users.</p>`;
        }

        popup.innerHTML = `
            <div class="popup-content" tabindex="-1">
                <h2 id="popup-title">${eventData.title}</h2>
                <p><strong>Start:</strong> ${new Date(eventData.start_date).toLocaleString('en-US')}</p>
                <p><strong>End:</strong> ${new Date(eventData.end_date).toLocaleString('en-US')}</p>
                ${creatorInfoHtml}
                ${sharedWithHtml}
                <div class="popup-buttons">
                    ${eventData.is_creator ? `
                        <button class="btn btn-primary edit-btn" onclick="window.location.href='/events/${eventData.id}/edit'" aria-label="Edit event">Edit</button>
                        <button class="btn btn-secondary cancel-btn" onclick="window.location.href='/events/'" aria-label="Close event details">Close</button>
                    ` : `
                        <button class="btn btn-secondary cancel-btn"onclick="window.location.href='/events/'" aria-label="Close event details">Close</button>
                    `}

                </div>
            </div>
        `;

        overlay.appendChild(popup);
        document.body.appendChild(overlay);

        setTimeout(() => {
            overlay.classList.add('visible');
            popup.focus();
        }, 50);
    }

    function closePopup() {
        const overlay = document.getElementById('popup-overlay');
        if (overlay) {
            overlay.classList.remove('visible');
            setTimeout(() => {
                overlay.remove();
            }, 300);
        }
    }

    function deleteEvent(eventId) {
        if (confirm("Are you sure you want to delete this event? This action cannot be undone.")) {
            console.log(`Attempting to delete event with ID: ${eventId}`);
            fetch(`/events/${eventId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json'
                }
            }).then(response => {
                if (response.ok) {
                    console.log(`Event ${eventId} deleted successfully.`);
                    window.location.reload(); 
                } else {
                    response.json().then(errorData => {
                        console.error('Error response from server:', errorData); 
                        alert(`Error deleting the event: ${errorData.message || 'You might not have permission.'}`);
                    }).catch(() => {
                        alert('Error deleting the event. An unknown error occurred or you might not have permission.');
                    });
                }
            }).catch(error => {
                console.error('Fetch error during delete:', error);
                alert('Network error or problem connecting to the server.');
            });
        }
    }

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closePopup();
    });
});
