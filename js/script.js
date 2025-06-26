(function() {
    'use strict';
    window.addEventListener('load', function() {
        var forms = document.getElementsByClassName('needs-validation');
        var validation = Array.prototype.filter.call(forms, function(form) {
            form.addEventListener('submit', function(event) {
                if (form.checkValidity() === false) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    }, false);
})();

document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(function(alert) {
        if (alert.classList.contains('alert-success')) {
            setTimeout(function() {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }, 5000);
        }
    });
});

function confirmDelete(eventName) {
    return confirm(`Are you sure you want to delete the event "${eventName}"? This action cannot be undone.`);
}

function formatCurrency(input) {
    let value = input.value.replace(/[^\d.]/g, '');
    if (value) {
        input.value = parseFloat(value).toFixed(2);
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const pricingInputs = document.querySelectorAll('input[name="pricing"]');
    pricingInputs.forEach(function(input) {
        input.addEventListener('blur', function() {
            formatCurrency(this);
        });
    });
});

document.addEventListener('DOMContentLoaded', function() {
    const dateInputs = document.querySelectorAll('input[type="date"]');
    const today = new Date().toISOString().split('T')[0];
    
    dateInputs.forEach(function(input) {
        if (input.name === 'event_date' && !input.value) {
            input.min = today;
        }
    });
});

function filterEvents() {
    const searchInput = document.getElementById('eventSearch');
    const filter = searchInput.value.toLowerCase();
    const table = document.querySelector('.table tbody');
    const rows = table.getElementsByTagName('tr');

    for (let i = 0; i < rows.length; i++) {
        const cells = rows[i].getElementsByTagName('td');
        let found = false;
        
        for (let j = 0; j < cells.length - 1; j++) { 
            if (cells[j].textContent.toLowerCase().indexOf(filter) > -1) {
                found = true;
                break;
            }
        }
        
        rows[i].style.display = found ? '' : 'none';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const eventsTable = document.querySelector('.table');
    if (eventsTable && eventsTable.querySelector('tbody tr')) {
        const cardBody = eventsTable.closest('.card-body');
        const searchDiv = document.createElement('div');
        searchDiv.className = 'mb-3';
        searchDiv.innerHTML = `
            <div class="input-group">
                <span class="input-group-text">
                    <i class="bi bi-search"></i>
                </span>
                <input type="text" id="eventSearch" class="form-control" placeholder="Search events..." onkeyup="filterEvents()">
            </div>
        `;
        cardBody.insertBefore(searchDiv, eventsTable.parentElement);
    }
});

document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});

function autoSaveForm() {
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        const inputs = form.querySelectorAll('input, textarea, select');
        inputs.forEach(input => {
            input.addEventListener('input', function() {
                const formData = new FormData(form);
                const data = Object.fromEntries(formData);
                localStorage.setItem('formAutoSave_' + form.id, JSON.stringify(data));
            });
        });
    });
}

function restoreFormData() {
    const forms = document.querySelectorAll('form[id]');
    forms.forEach(form => {
        const savedData = localStorage.getItem('formAutoSave_' + form.id);
        if (savedData) {
            const data = JSON.parse(savedData);
            Object.keys(data).forEach(key => {
                const input = form.querySelector(`[name="${key}"]`);
                if (input && input.type !== 'submit') {
                    input.value = data[key];
                }
            });
        }
    });
}

function clearAutoSaveData(formId) {
    localStorage.removeItem('formAutoSave_' + formId);
}

document.addEventListener('DOMContentLoaded', function() {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});

function printEventsList() {
    window.print();
}

function exportToCSV() {
    const table = document.querySelector('.table');
    if (!table) return;
    
    let csv = [];
    const rows = table.querySelectorAll('tr');
    
    for (let i = 0; i < rows.length; i++) {
        const row = [], cols = rows[i].querySelectorAll('td, th');
        
        for (let j = 0; j < cols.length - 1; j++) { // Exclude actions column
            let cellText = cols[j].innerText.replace(/"/g, '""');
            row.push('"' + cellText + '"');
        }
        
        csv.push(row.join(','));
    }
    
    const csvFile = new Blob([csv.join('\n')], { type: 'text/csv' });
    const downloadLink = document.createElement('a');
    downloadLink.download = 'events_' + new Date().toISOString().split('T')[0] + '.csv';
    downloadLink.href = window.URL.createObjectURL(csvFile);
    downloadLink.style.display = 'none';
    document.body.appendChild(downloadLink);
    downloadLink.click();
    document.body.removeChild(downloadLink);
}

document.addEventListener('keydown', function(e) {
    if (e.ctrlKey && e.key === 'n' && window.location.pathname.includes('events.php')) {
        e.preventDefault();
        document.querySelector('input[name="event_name"]')?.focus();
    }
    
    if (e.ctrlKey && e.key === 's' && window.location.pathname.includes('edit_event.php')) {
        e.preventDefault();
        document.querySelector('button[name="update_event"]')?.click();
    }
    
    if (e.key === 'Escape' && window.location.pathname.includes('edit_event.php')) {
        if (confirm('Are you sure you want to cancel editing? Unsaved changes will be lost.')) {
            window.location.href = 'events.php';
        }
    }
});

document.addEventListener('DOMContentLoaded', function() {
    if (window.location.pathname.includes('edit_event.php')) {
        restoreFormData();
        autoSaveForm();
    }

    const navbar = document.querySelector('.gala-navbar');

    // Normal scroll-based shrink
    window.addEventListener('scroll', function () {
        if (!navbar) return;

        if (window.scrollY > 10) {
            navbar.classList.add('shrink');
        } else {
            navbar.classList.remove('shrink');
        }
    });

    console.log('Events Monitoring System initialized successfully!');
});

