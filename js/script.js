const GalaGo = {
    config: {
        animationDuration: 300,
        debounceDelay: 300,
        autoSaveDelay: 2000,
        notificationDuration: 5000
    },
    
    utils: {
        debounce: function(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        },

        throttle: function(func, limit) {
            let inThrottle;
            return function() {
                const args = arguments;
                const context = this;
                if (!inThrottle) {
                    func.apply(context, args);
                    inThrottle = true;
                    setTimeout(() => inThrottle = false, limit);
                }
            };
        },

        generateId: function() {
            return Math.random().toString(36).substr(2, 9);
        },

        formatCurrency: function(amount) {
            return new Intl.NumberFormat('en-PH', {
                style: 'currency',
                currency: 'PHP'
            }).format(amount);
        },

        formatDate: function(date, options = {}) {
            const defaultOptions = {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            };
            return new Date(date).toLocaleDateString('en-US', { ...defaultOptions, ...options });
        }
    },

    animations: {
        fadeIn: function(element, duration = 300) {
            element.style.opacity = '0';
            element.style.display = 'block';
            
            let start = null;
            function animate(timestamp) {
                if (!start) start = timestamp;
                const progress = timestamp - start;
                const opacity = Math.min(progress / duration, 1);
                
                element.style.opacity = opacity;
                
                if (progress < duration) {
                    requestAnimationFrame(animate);
                }
            }
            requestAnimationFrame(animate);
        },

        slideUp: function(element, duration = 300) {
            element.style.transform = 'translateY(30px)';
            element.style.opacity = '0';
            element.style.transition = `all ${duration}ms cubic-bezier(0.4, 0, 0.2, 1)`;
            
            setTimeout(() => {
                element.style.transform = 'translateY(0)';
                element.style.opacity = '1';
            }, 10);
        },

        scale: function(element, scale = 1.05, duration = 150) {
            element.style.transition = `transform ${duration}ms ease`;
            element.style.transform = `scale(${scale})`;
            
            setTimeout(() => {
                element.style.transform = 'scale(1)';
            }, duration);
        },

        pulse: function(element) {
            element.style.animation = 'pulse 0.6s ease-in-out';
            setTimeout(() => {
                element.style.animation = '';
            }, 600);
        },

        shake: function(element) {
            element.style.animation = 'shake 0.5s ease-in-out';
            setTimeout(() => {
                element.style.animation = '';
            }, 500);
        }
    },

    ui: {
        showNotification: function(message, type = 'success', duration = 5000) {
            const notification = document.createElement('div');
            notification.className = `notification notification-${type}`;
            notification.innerHTML = `
                <div class="notification-content">
                    <i class="bi bi-${type === 'success' ? 'check-circle' : 'exclamation-triangle'}"></i>
                    <span>${message}</span>
                    <button class="notification-close" onclick="this.parentElement.parentElement.remove()">
                        <i class="bi bi-x"></i>
                    </button>
                </div>
            `;

            Object.assign(notification.style, {
                position: 'fixed',
                top: '2rem',
                right: '2rem',
                background: type === 'success' 
                    ? 'linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)'
                    : 'linear-gradient(135deg, #fa709a 0%, #fee140 100%)',
                color: 'white',
                padding: '1rem 1.5rem',
                borderRadius: '12px',
                boxShadow: '0 8px 25px rgba(0, 0, 0, 0.3)',
                zIndex: '1100',
                transform: 'translateX(400px)',
                transition: 'all 0.3s cubic-bezier(0.4, 0, 0.2, 1)',
                backdropFilter: 'blur(10px)',
                border: '1px solid rgba(255, 255, 255, 0.2)'
            });

            document.body.appendChild(notification);

            setTimeout(() => {
                notification.style.transform = 'translateX(0)';
            }, 10);

            setTimeout(() => {
                notification.style.transform = 'translateX(400px)';
                setTimeout(() => {
                    if (notification.parentElement) {
                        notification.remove();
                    }
                }, 300);
            }, duration);

            return notification;
        },

        showLoading: function(message = 'Loading...') {
            const overlay = document.createElement('div');
            overlay.className = 'loading-overlay';
            overlay.innerHTML = `
                <div class="loading-content">
                    <div class="loading-spinner"></div>
                    <p class="loading-text">${message}</p>
                </div>
            `;

            Object.assign(overlay.style, {
                position: 'fixed',
                top: '0',
                left: '0',
                width: '100%',
                height: '100%',
                background: 'rgba(0, 0, 0, 0.8)',
                backdropFilter: 'blur(10px)',
                display: 'flex',
                alignItems: 'center',
                justifyContent: 'center',
                zIndex: '9999',
                opacity: '0',
                transition: 'opacity 0.3s ease'
            });

            document.body.appendChild(overlay);
            
            setTimeout(() => {
                overlay.style.opacity = '1';
            }, 10);

            return overlay;
        },

        hideLoading: function(overlay) {
            if (overlay) {
                overlay.style.opacity = '0';
                setTimeout(() => {
                    if (overlay.parentElement) {
                        overlay.remove();
                    }
                }, 300);
            }
        },

        createModal: function(options) {
            const modal = document.createElement('div');
            modal.className = 'modal fade enhanced-modal';
            modal.innerHTML = `
                <div class="modal-dialog ${options.size || 'modal-lg'}">
                    <div class="modal-content">
                        <div class="modal-header ${options.headerClass || 'bg-primary'} text-white">
                            <h5 class="modal-title">
                                ${options.icon ? `<i class="bi bi-${options.icon}"></i>` : ''} 
                                ${options.title}
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            ${options.body}
                        </div>
                        ${options.footer ? `<div class="modal-footer">${options.footer}</div>` : ''}
                    </div>
                </div>
            `;

            document.body.appendChild(modal);
            const bsModal = new bootstrap.Modal(modal);
            
            modal.addEventListener('hidden.bs.modal', () => {
                document.body.removeChild(modal);
            });

            return bsModal;
        }
    },

    forms: {
        validateForm: function(form) {
            const inputs = form.querySelectorAll('input, textarea, select');
            let isValid = true;

            inputs.forEach(input => {
                if (input.hasAttribute('required') && !input.value.trim()) {
                    this.showFieldError(input, 'This field is required');
                    isValid = false;
                } else if (input.type === 'email' && input.value && !this.isValidEmail(input.value)) {
                    this.showFieldError(input, 'Please enter a valid email address');
                    isValid = false;
                } else if (input.type === 'number' && input.value && isNaN(input.value)) {
                    this.showFieldError(input, 'Please enter a valid number');
                    isValid = false;
                } else {
                    this.clearFieldError(input);
                }
            });

            return isValid;
        },

        showFieldError: function(field, message) {
            field.classList.add('is-invalid');
            GalaGo.animations.shake(field);
            
            let errorDiv = field.parentElement.querySelector('.invalid-feedback');
            if (errorDiv) {
                errorDiv.textContent = message;
            }
        },

        clearFieldError: function(field) {
            field.classList.remove('is-invalid');
        },

        isValidEmail: function(email) {
            const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return re.test(email);
        },

        enableAutoSave: function(form, callback) {
            const inputs = form.querySelectorAll('input, textarea, select');
            let saveTimeout;
            let hasChanges = false;

            inputs.forEach(input => {
                input.addEventListener('input', () => {
                    hasChanges = true;
                    clearTimeout(saveTimeout);
                    
                    saveTimeout = setTimeout(() => {
                        if (hasChanges && callback) {
                            callback(new FormData(form));
                            hasChanges = false;
                            GalaGo.ui.showNotification('Changes saved automatically', 'success', 2000);
                        }
                    }, GalaGo.config.autoSaveDelay);
                });
            });
        }
    },

    search: {
        initializeSearch: function(searchInput, targetElements, options = {}) {
            if (!searchInput) {
                console.error('Search input element is required');
                return;
            }
            
            if (!targetElements || targetElements.length === 0) {
                console.warn('No target elements provided for search');
                return;
            }
            
            const searchFunction = GalaGo.utils.debounce((query) => {
                this.performSearch(query, targetElements, options);
            }, GalaGo.config.debounceDelay);

            searchInput.addEventListener('input', (e) => {
                searchFunction(e.target.value);
            });

            searchInput.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    searchInput.value = '';
                    this.performSearch('', targetElements, options);
                }
            });
        },

        performSearch: function(query, targetElements, options = {}) {
            if (!targetElements || targetElements.length === 0) {
                console.warn('No target elements to search');
                return;
            }
            
            const searchTerm = query.toLowerCase().trim();
            let visibleCount = 0;

            targetElements.forEach((element, index) => {
                if (!element) return;
                
                const searchText = element.textContent.toLowerCase();
                const matches = searchTerm === '' || searchText.includes(searchTerm);

                if (matches) {
                    visibleCount++;
                    element.style.display = '';
                    
                    element.style.opacity = '0';
                    element.style.transform = 'translateY(20px)';
                    element.style.transition = 'all 0.3s ease';
                    
                    setTimeout(() => {
                        element.style.opacity = '1';
                        element.style.transform = 'translateY(0)';
                    }, index * 50);
                } else {
                    element.style.opacity = '0';
                    element.style.transform = 'translateY(-20px)';
                    element.style.transition = 'all 0.3s ease';
                    
                    setTimeout(() => {
                        if (element.style.opacity === '0') {
                            element.style.display = 'none';
                        }
                    }, 300);
                }
            });

            if (options.counterElement) {
                options.counterElement.textContent = `${visibleCount} result${visibleCount !== 1 ? 's' : ''}`;
            }

            if (options.noResultsElement) {
                if (visibleCount === 0 && searchTerm !== '') {
                    options.noResultsElement.style.display = 'block';
                    if (typeof GalaGo !== 'undefined' && GalaGo.animations) {
                        GalaGo.animations.fadeIn(options.noResultsElement);
                    }
                } else {
                    options.noResultsElement.style.display = 'none';
                }
            }
        }
    },

    effects: {
        createParticles: function(element) {
            if (!element) {
                console.warn('Element is required for particle effect');
                return;
            }
            
            const colors = ['#4facfe', '#00f2fe', '#43e97b', '#38f9d7', '#fa709a', '#fee140'];
            
            for (let i = 0; i < 20; i++) {
                const particle = document.createElement('div');
                particle.className = 'particle';
                
                Object.assign(particle.style, {
                    position: 'absolute',
                    width: '6px',
                    height: '6px',
                    background: colors[Math.floor(Math.random() * colors.length)],
                    borderRadius: '50%',
                    pointerEvents: 'none',
                    left: '50%',
                    top: '50%',
                    transform: 'translate(-50%, -50%)',
                    zIndex: '1000'
                });

                element.appendChild(particle);

                const angle = (Math.PI * 2 * i) / 20;
                const velocity = 100 + Math.random() * 50;
                const duration = 1000 + Math.random() * 500;

                try {
                    particle.animate([
                        { 
                            transform: 'translate(-50%, -50%) scale(1)',
                            opacity: 1
                        },
                        { 
                            transform: `translate(${Math.cos(angle) * velocity - 50}%, ${Math.sin(angle) * velocity - 50}%) scale(0)`,
                            opacity: 0
                        }
                    ], {
                        duration: duration,
                        easing: 'cubic-bezier(0.4, 0, 0.2, 1)'
                    }).onfinish = () => {
                        if (particle.parentElement) {
                            particle.remove();
                        }
                    };
                } catch (error) {
                    console.warn('Animation API not supported, using fallback');
                    setTimeout(() => {
                        if (particle.parentElement) {
                            particle.remove();
                        }
                    }, duration);
                }
            }
        },

        createRipple: function(element, event) {
            if (!element || !event) {
                console.warn('Element and event are required for ripple effect');
                return;
            }
            
            try {
                const ripple = document.createElement('span');
                const rect = element.getBoundingClientRect();
                const size = Math.max(rect.width, rect.height);
                const x = event.clientX - rect.left - size / 2;
                const y = event.clientY - rect.top - size / 2;

                Object.assign(ripple.style, {
                    position: 'absolute',
                    width: size + 'px',
                    height: size + 'px',
                    left: x + 'px',
                    top: y + 'px',
                    background: 'rgba(255, 255, 255, 0.3)',
                    borderRadius: '50%',
                    transform: 'scale(0)',
                    animation: 'ripple 0.6s ease-out',
                    pointerEvents: 'none'
                });

                element.style.position = 'relative';
                element.style.overflow = 'hidden';
                element.appendChild(ripple);

                setTimeout(() => {
                    if (ripple.parentElement) {
                        ripple.remove();
                    }
                }, 600);
            } catch (error) {
                console.warn('Ripple effect failed:', error);
            }
        },

        initializeFloatingElements: function() {
            const floatingElements = document.querySelectorAll('.floating-shape, .floating-element');
            
            if (floatingElements.length === 0) {
                console.log('No floating elements found to initialize');
                return;
            }
            
            floatingElements.forEach((element, index) => {
                if (!element) return;
                
                const baseDelay = index * 1000;
                const animationDuration = 6000 + Math.random() * 3000;
                
                element.style.animation = `float ${animationDuration}ms ease-in-out infinite`;
                element.style.animationDelay = baseDelay + 'ms';
            });
        },

        initializeParallax: function() {
            const parallaxElements = document.querySelectorAll('[data-parallax]');
            
            if (parallaxElements.length === 0) {
                console.log('No parallax elements found to initialize');
                return;
            }
            
            const updateParallax = GalaGo.utils.throttle(() => {
                const scrollTop = window.pageYOffset;
                
                parallaxElements.forEach(element => {
                    if (!element) return;
                    
                    const speed = element.dataset.parallax || 0.5;
                    const yPos = -(scrollTop * speed);
                    element.style.transform = `translateY(${yPos}px)`;
                });
            }, 16);

            window.addEventListener('scroll', updateParallax);
        }
    },

    interactions: {
        initializeButtons: function() {
            const buttons = document.querySelectorAll('.btn, button');
            
            if (buttons.length === 0) {
                console.log('No buttons found to initialize');
                return;
            }
            
            buttons.forEach(button => {
                if (!button) return;
                
                button.addEventListener('click', (e) => {
                    if (typeof GalaGo !== 'undefined' && GalaGo.effects) {
                        GalaGo.effects.createRipple(button, e);
                    }
                });

                button.addEventListener('mouseenter', () => {
                    if (!button.disabled) {
                        button.style.transform = 'translateY(-2px)';
                        button.style.boxShadow = '0 8px 25px rgba(0, 0, 0, 0.2)';
                        button.style.transition = 'all 0.3s ease';
                    }
                });

                button.addEventListener('mouseleave', () => {
                    button.style.transform = 'translateY(0)';
                    button.style.boxShadow = '';
                    button.style.transition = 'all 0.3s ease';
                });

                if (button.type === 'submit') {
                    button.addEventListener('click', () => {
                        if (!button.disabled) {
                            setTimeout(() => {
                                button.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';
                                button.disabled = true;
                            }, 100);
                        }
                    });
                }
            });
        },

        initializeCards: function() {
            const cards = document.querySelectorAll('.card, .event-card, .dashboard-card');
            
            if (cards.length === 0) {
                console.log('No cards found to initialize');
                return;
            }
            
            cards.forEach(card => {
                if (!card) return;
                
                card.addEventListener('mouseenter', () => {
                    card.style.transform = 'translateY(-8px) rotateX(2deg)';
                    card.style.boxShadow = '0 20px 40px rgba(0, 0, 0, 0.2)';
                    card.style.transition = 'all 0.3s cubic-bezier(0.4, 0, 0.2, 1)';
                });

                card.addEventListener('mouseleave', () => {
                    card.style.transform = 'translateY(0) rotateX(0)';
                    card.style.boxShadow = '';
                    card.style.transition = 'all 0.3s cubic-bezier(0.4, 0, 0.2, 1)';
                });
            });
        },

        initializeForms: function() {
            const forms = document.querySelectorAll('form');
            
            if (forms.length === 0) {
                console.log('No forms found to initialize');
                return;
            }
            
            forms.forEach(form => {
                if (!form) return;
                
                const floatingInputs = form.querySelectorAll('.form-floating input, .form-floating textarea');
                floatingInputs.forEach(input => {
                    if (!input) return;
                    
                    input.addEventListener('focus', () => {
                        if (input.parentElement) {
                            input.parentElement.classList.add('focused');
                        }
                    });

                    input.addEventListener('blur', () => {
                        if (!input.value && input.parentElement) {
                            input.parentElement.classList.remove('focused');
                        }
                    });
                });

                form.addEventListener('submit', (e) => {
                    if (form.classList.contains('needs-validation')) {
                        if (!form.checkValidity()) {
                            e.preventDefault();
                            e.stopPropagation();
                        }
                        form.classList.add('was-validated');
                    }
                });
            });
        }
    },

    init: function() {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => this.initializeComponents());
        } else {
            this.initializeComponents();
        }
        
        window.addEventListener('load', function() {
            window.onbeforeunload = null;
        });
    },

    initializeComponents: function() {
        try {
            this.interactions.initializeButtons();
            this.interactions.initializeCards();
            this.interactions.initializeForms();
            this.effects.initializeFloatingElements();
            this.effects.initializeParallax();

            const searchInput = document.getElementById('eventSearch');
            if (searchInput) {
                const searchTargets = document.querySelectorAll('.event-card, .event-list-item');
                if (searchTargets.length > 0) {
                    this.search.initializeSearch(searchInput, searchTargets);
                }
            }

            document.addEventListener('keydown', (e) => {
                if (e.ctrlKey && e.key === 'k' && searchInput) {
                    e.preventDefault();
                    searchInput.focus();
                }

                if (e.key === 'Escape' && document.activeElement === searchInput) {
                    searchInput.value = '';
                    searchInput.blur();
                }
            });

            const anchorLinks = document.querySelectorAll('a[href^="#"]');
            anchorLinks.forEach(anchor => {
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

            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('animate-in');
                        this.animations.slideUp(entry.target);
                    }
                });
            }, observerOptions);

            const animatableElements = document.querySelectorAll('.card, .dashboard-card, .feature-card, section');
            animatableElements.forEach(el => {
                observer.observe(el);
            });

            const successAlerts = document.querySelectorAll('.alert-success');
            successAlerts.forEach(alert => {
                this.effects.createParticles(alert);
                
                setTimeout(() => {
                    alert.style.opacity = '0';
                    alert.style.transform = 'translateX(100%)';
                    setTimeout(() => {
                        if (alert.parentElement) {
                            alert.remove();
                        }
                    }, 300);
                }, this.config.notificationDuration);
            });

            if (typeof bootstrap !== 'undefined') {
                const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                tooltipTriggerList.map(function (tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl);
                });
            }

            console.log('🎉 GalaGo Events System initialized successfully!');
        } catch (error) {
            console.error('Error initializing GalaGo components:', error);
            console.log('⚠️ GalaGo is running in fallback mode');
        }
    }
};

const customStyles = `
    <style>
        @keyframes ripple {
            to {
                transform: scale(4);
                opacity: 0;
            }
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
            20%, 40%, 60%, 80% { transform: translateX(5px); }
        }

        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(79, 172, 254, 0.7); }
            70% { box-shadow: 0 0 0 10px rgba(79, 172, 254, 0); }
            100% { box-shadow: 0 0 0 0 rgba(79, 172, 254, 0); }
        }

        .animate-in {
            opacity: 1 !important;
            transform: translateY(0) !important;
        }

        .form-floating.focused label {
            color: #4facfe !important;
        }

        .notification {
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .notification-close {
            background: none;
            border: none;
            color: white;
            cursor: pointer;
            padding: 0.25rem;
            border-radius: 4px;
            transition: background 0.2s ease;
        }

        .notification-close:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        .loading-spinner {
            width: 40px;
            height: 40px;
            border: 4px solid rgba(255, 255, 255, 0.3);
            border-top: 4px solid #4facfe;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-bottom: 1rem;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .loading-text {
            color: white;
            font-weight: 500;
            margin: 0;
        }

        /* Smooth transitions for all interactive elements */
        .btn, .card, .form-control, .modal-content {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Enhanced focus states */
        .btn:focus,
        .form-control:focus {
            outline: 2px solid rgba(79, 172, 254, 0.5);
            outline-offset: 2px;
        }

        /* Particle animation container */
        .particles-container {
            position: relative;
            overflow: hidden;
        }
    </style>
`;

if (document.head) {
    document.head.insertAdjacentHTML('beforeend', customStyles);
} else {
    document.addEventListener('DOMContentLoaded', () => {
        document.head.insertAdjacentHTML('beforeend', customStyles);
    });
}

GalaGo.init();

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
                if (typeof bootstrap !== 'undefined') {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }
            }, 5000);
        }
    });
});

function confirmDelete(eventName) {
    if (typeof GalaGo !== 'undefined' && GalaGo.ui && GalaGo.ui.createModal) {
        return GalaGo.ui.createModal({
            title: 'Confirm Delete',
            icon: 'exclamation-triangle',
            headerClass: 'bg-danger',
            body: `
                <div class="text-center">
                    <i class="bi bi-trash display-1 text-danger mb-3"></i>
                    <h5>Are you sure you want to delete this event?</h5>
                    <p class="text-muted">
                        <strong>"${eventName}"</strong><br>
                        This action cannot be undone.
                    </p>
                </div>
            `,
            footer: `
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" onclick="proceedWithDelete()">Delete Event</button>
            `
        });
    } else {
        return confirm(`Are you sure you want to delete the event "${eventName}"? This action cannot be undone.`);
    }
}

function formatCurrency(input) {
    if (!input || !input.value) return;
    let value = input.value.replace(/[^\d.]/g, '');
    if (value) {
        input.value = parseFloat(value).toFixed(2);
    }
}

function filterEvents() {
    const searchInput = document.getElementById('eventSearch');
    if (!searchInput) {
        console.log('Search input not found');
        return;
    }
    
    const filter = searchInput.value.toLowerCase();
    const cards = document.querySelectorAll('.event-card, .event-list-item, .table tbody tr');
    
    if (cards.length === 0) {
        console.log('No elements found to filter');
        return;
    }
    
    cards.forEach(element => {
        if (!element) return;
        
        const text = element.textContent.toLowerCase();
        const matches = text.indexOf(filter) > -1;
        
        if (matches) {
            element.style.display = '';
            if (typeof GalaGo !== 'undefined' && GalaGo.animations) {
                GalaGo.animations.fadeIn(element);
            } else {
                element.style.opacity = '1';
            }
        } else {
            element.style.opacity = '0';
            element.style.transform = 'translateY(-20px)';
            element.style.transition = 'all 0.3s ease';
            setTimeout(() => {
                if (element.style.opacity === '0') {
                    element.style.display = 'none';
                }
            }, 300);
        }
    });
}

function toggleView(viewType) {
    const gridView = document.getElementById('gridView');
    const listView = document.getElementById('listView');
    const gridBtn = document.getElementById('gridBtn');
    const listBtn = document.getElementById('listBtn');
    
    if (!gridView || !listView) {
        console.error('View elements not found');
        if (typeof window.simpleToggleView === 'function') {
            window.simpleToggleView(viewType);
        }
        return;
    }
    
    if (viewType === 'list') {
        gridView.classList.remove('active');
        listView.classList.add('active');
        if (gridBtn) gridBtn.classList.remove('active');
        if (listBtn) listBtn.classList.add('active');
        
        gridView.style.display = 'none';
        
        listView.style.display = 'block';
        listView.style.opacity = '0';
        listView.style.transition = 'all 0.3s ease';
        setTimeout(() => {
            listView.style.opacity = '1';
            listView.style.transform = 'translateY(0)';
        }, 50);
        
        if (typeof GalaGo !== 'undefined' && GalaGo.animations) {
            const listItems = listView.querySelectorAll('.event-list-item');
            listItems.forEach((item, index) => {
                setTimeout(() => {
                    GalaGo.animations.slideUp(item);
                }, index * 100);
            });
        }
    } else {
        listView.classList.remove('active');
        gridView.classList.add('active');
        if (listBtn) listBtn.classList.remove('active');
        if (gridBtn) gridBtn.classList.add('active');
        
        listView.style.display = 'none';
        
        gridView.style.display = 'block';
        gridView.style.opacity = '0';
        gridView.style.transition = 'all 0.3s ease';
        setTimeout(() => {
            gridView.style.opacity = '1';
            gridView.style.transform = 'translateY(0)';
        }, 50);
        
        if (typeof GalaGo !== 'undefined' && GalaGo.animations) {
            const gridItems = gridView.querySelectorAll('.event-card');
            gridItems.forEach((item, index) => {
                setTimeout(() => {
                    GalaGo.animations.slideUp(item);
                }, index * 50);
            });
        }
    }
}

window.GalaGo = GalaGo;
