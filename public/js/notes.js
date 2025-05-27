document.addEventListener('DOMContentLoaded', function () {
    const allNotes = Array.from(document.querySelectorAll('.note-card'));

    allNotes.forEach(note => note.style.display = 'block');

    const colorSelect = document.getElementById('color');
    const form = document.querySelector('.note-form');

    if (colorSelect && form) {
        function updateBackground() {
            const color = colorSelect.value;
            form.style.backgroundColor = color;
        }

        colorSelect.addEventListener('input', updateBackground);
        updateBackground();
    }
});
