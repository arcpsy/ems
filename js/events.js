function toggleView(viewType) {
    const gridView = document.getElementById('gridView');
    const listView = document.getElementById('listView');
    const gridBtn = document.getElementById('gridBtn');
    const listBtn = document.getElementById('listBtn');
    
    if (viewType === 'list') {
        gridView.classList.remove('active');
        listView.classList.add('active');
        gridBtn.classList.remove('active');
        listBtn.classList.add('active');
    } else {
        listView.classList.remove('active');
        gridView.classList.add('active');
        listBtn.classList.remove('active');
        gridBtn.classList.add('active');
    }
}

// Character counter for event remarks
document.addEventListener('DOMContentLoaded', function() {
    const remarksTextarea = document.getElementById('event_remarks');
    const charCounter = document.getElementById('charCounter');
    const maxLength = 1000;

    function updateCharCounter() {
        const currentLength = remarksTextarea.value.length;
        const remaining = Math.max(0, maxLength - currentLength);
        charCounter.textContent = `${remaining} characters remaining`;
        if (remaining <= 50) {
            charCounter.className = 'text-warning';
        } else {
            charCounter.className = 'text-muted';
        }
    }

    remarksTextarea.addEventListener('input', updateCharCounter);
    updateCharCounter();
});