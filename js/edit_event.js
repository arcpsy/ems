(function() {
    'use strict';
    window.addEventListener('load', function() {
        const form = document.getElementById('editEventForm');
        form.addEventListener('submit', function(event) {
            if (form.checkValidity() === false) {
                event.preventDefault();
                event.stopPropagation();
            } else {
                document.getElementById('loadingOverlay').classList.add('show');
            }
            form.classList.add('was-validated');
        }, false);
    }, false);
})();

document.addEventListener('DOMContentLoaded', function() {
    const remarksTextarea = document.getElementById('event_remarks');
    const charCounter = document.getElementById('charCounter');
    const maxLength = 1000;

    function updateCharCounter() {
        const currentLength = remarksTextarea.value.length;
        const remaining = Math.max(0, maxLength - currentLength);
        charCounter.textContent = `${remaining} characters remaining`;
        
        if (remaining <= 100) {
            charCounter.classList.add('warning');
        } else {
            charCounter.classList.remove('warning');
        }
    }

    remarksTextarea.addEventListener('input', updateCharCounter);
    updateCharCounter();
});

function updatePreview() {
    const eventName = document.getElementById('event_name').value;
    const eventLocation = document.getElementById('event_location').value;
    const eventDate = document.getElementById('event_date').value;
    const pricing = document.getElementById('pricing').value;
    const remarks = document.getElementById('event_remarks').value;

    document.getElementById('preview-name').textContent = eventName || 'Event Name';
    document.getElementById('preview-location').textContent = eventLocation || 'Event Location';
    
    if (eventDate) {
        const date = new Date(eventDate);
        document.getElementById('preview-date').textContent = date.toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
    }
    
    if (pricing) {
        document.getElementById('preview-pricing').textContent = `₱${parseFloat(pricing).toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        })}`;
    }

    const remarksElement = document.getElementById('preview-remarks');
    if (remarks) {
        remarksElement.textContent = remarks;
        remarksElement.parentElement.parentElement.style.display = 'flex';
    } else {
        remarksElement.parentElement.parentElement.style.display = 'none';
    }

    document.getElementById('last-updated').textContent = new Date().toLocaleTimeString();
}

let autoSaveTimeout;
function autoSave() {
    clearTimeout(autoSaveTimeout);
    autoSaveTimeout = setTimeout(() => {
        const saveIndicator = document.getElementById('saveIndicator');
        saveIndicator.classList.add('show');
        setTimeout(() => {
            saveIndicator.classList.remove('show');
        }, 2000);
    }, 1000);
}

document.addEventListener('DOMContentLoaded', function() {
    const inputs = document.querySelectorAll('#editEventForm input, #editEventForm textarea');
    inputs.forEach(input => {
        input.addEventListener('input', function() {
            updatePreview();
            autoSave();
        });
    });

    const elements = document.querySelectorAll('.animate-fade-in-up, .animate-slide-in');
    elements.forEach((el, index) => {
        el.style.animationDelay = `${index * 0.2}s`;
    });

    inputs.forEach(input => {
        input.addEventListener('focus', function() {
            this.parentElement.style.transform = 'scale(1.02)';
            this.parentElement.style.boxShadow = '0 8px 25px rgba(102, 126, 234, 0.3)';
        });

        input.addEventListener('blur', function() {
            this.parentElement.style.transform = 'scale(1)';
            this.parentElement.style.boxShadow = 'none';
        });
    });

    const buttons = document.querySelectorAll('.btn-enhanced');
    buttons.forEach(button => {
        button.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-3px) scale(1.02)';
        });

        button.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });
});

function previewChanges() {
    const modal = document.createElement('div');
    modal.className = 'modal fade';
    modal.innerHTML = `
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title">
                        <i class="bi bi-eye"></i> Event Preview
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="preview-card">
                        <h4 class="text-white mb-3">${document.getElementById('event_name').value}</h4>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="preview-item">
                                    <div class="preview-icon"><i class="bi bi-geo-alt"></i></div>
                                    <div class="preview-content">
                                        <div class="preview-label">Location</div>
                                        <div class="preview-value">${document.getElementById('event_location').value}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="preview-item">
                                    <div class="preview-icon"><i class="bi bi-calendar"></i></div>
                                    <div class="preview-content">
                                        <div class="preview-label">Date</div>
                                        <div class="preview-value">${new Date(document.getElementById('event_date').value).toLocaleDateString('en-US', {year: 'numeric', month: 'long', day: 'numeric'})}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="preview-item">
                            <div class="preview-icon"><i class="bi bi-cash"></i></div>
                            <div class="preview-content">
                                <div class="preview-label">Pricing</div>
                                <div class="preview-value">₱${parseFloat(document.getElementById('pricing').value).toLocaleString('en-US', {minimumFractionDigits: 2})}</div>
                            </div>
                        </div>
                        ${document.getElementById('event_remarks').value ? `
                        <div class="preview-item">
                            <div class="preview-icon"><i class="bi bi-chat-text"></i></div>
                            <div class="preview-content">
                                <div class="preview-label">Remarks</div>
                                <div class="preview-value">${document.getElementById('event_remarks').value}</div>
                            </div>
                        </div>
                        ` : ''}
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="document.getElementById('editEventForm').submit()">
                        <i class="bi bi-check-circle"></i> Save Changes
                    </button>
                </div>
            </div>
        </div>
    `;
    document.body.appendChild(modal);
    const bsModal = new bootstrap.Modal(modal);
    bsModal.show();
    modal.addEventListener('hidden.bs.modal', () => {
        document.body.removeChild(modal);
    });
}

document.addEventListener('keydown', function(e) {
    if (e.ctrlKey && e.key === 's') {
        e.preventDefault();
        const form = document.getElementById('editEventForm');
        if (form) {
            form.submit();
        }
    }
});

window.addEventListener('load', function() {
    window.onbeforeunload = null;
    const originalAddEventListener = window.addEventListener;
    window.addEventListener = function(type, listener, options) {
        if (type === 'beforeunload') {
            console.log('Blocked beforeunload event listener');
            return;
        }
        return originalAddEventListener.call(this, type, listener, options);
    };
});