<?php
session_start();
require_once 'config.php';
require_once 'auth_functions.php';

// Require login to access the system
requireLogin();

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error'] = "Invalid event ID";
    header("Location: events.php");
    exit();
}

$event_id = (int)$_GET['id'];

$stmt = $conn->prepare("SELECT * FROM events WHERE id = ?");
$stmt->bind_param("i", $event_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    $_SESSION['error'] = "Event not found";
    header("Location: events.php");
    exit();
}

$event = $result->fetch_assoc();
$stmt->close();

$page_title = "Edit Event - " . htmlspecialchars($event['event_name']);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_event'])) {
    $event_name = sanitize_input($_POST['event_name']);
    $event_location = sanitize_input($_POST['event_location']);
    $event_date = sanitize_input($_POST['event_date']);
    $event_remarks = sanitize_input($_POST['event_remarks']);
    $pricing = sanitize_input($_POST['pricing']);
    
    $errors = [];
    
    if (empty($event_name)) {
        $errors[] = "Event name is required";
    }
    
    if (empty($event_location)) {
        $errors[] = "Event location is required";
    }
    
    if (empty($event_date)) {
        $errors[] = "Event date is required";
    } elseif (!validate_date($event_date)) {
        $errors[] = "Invalid date format";
    }
    
    if (empty($pricing) || !is_numeric($pricing) || $pricing < 0) {
        $errors[] = "Valid pricing is required";
    }
    
    if (empty($errors)) {
        $stmt = $conn->prepare("UPDATE events SET event_name=?, event_location=?, event_date=?, event_remarks=?, pricing=? WHERE id=?");
        $stmt->bind_param("ssssdi", $event_name, $event_location, $event_date, $event_remarks, $pricing, $event_id);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = "Event updated successfully!";
            header("Location: events.php");
            exit();
        } else {
            $_SESSION['error'] = "Error updating event: " . $conn->error;
        }
        $stmt->close();
    } else {
        $_SESSION['error'] = implode(", ", $errors);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - GalaGo Events</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <style>
        .edit-header {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 24px;
            padding: 2rem;
            margin-bottom: 2rem;
            position: relative;
            overflow: hidden;
        }

        .edit-header::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(102, 126, 234, 0.1) 0%, transparent 70%);
            animation: rotate 20s linear infinite;
        }

        .edit-form-container {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 24px;
            padding: 3rem;
            margin-bottom: 2rem;
            position: relative;
            overflow: hidden;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.25);
        }

        .edit-form-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #667eea, #764ba2, #4facfe);
            animation: shimmer 3s ease-in-out infinite;
        }

        @keyframes shimmer {
            0%, 100% { opacity: 0.5; }
            50% { opacity: 1; }
        }

        .preview-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            padding: 2rem;
            position: relative;
            overflow: hidden;
        }

        .preview-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 2px;
            background: linear-gradient(90deg, #43e97b, #38f9d7);
        }

        .form-floating {
            position: relative;
            margin-bottom: 1.5rem;
        }

        .form-floating .form-control {
            height: calc(3.5rem + 2px);
            padding: 1rem 0.75rem;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 2px solid rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            color: white;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .form-floating .form-control:focus {
            background: rgba(255, 255, 255, 0.15);
            border-color: rgba(102, 126, 234, 0.8);
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.2);
            transform: translateY(-2px);
        }

        .form-floating .form-control::placeholder {
            color: transparent;
        }

        .form-floating label {
            position: absolute;
            top: 0;
            left: 0;
            height: 100%;
            padding: 1rem 0.75rem;
            pointer-events: none;
            border: 2px solid transparent;
            transform-origin: 0 0;
            transition: opacity 0.1s ease-in-out, transform 0.1s ease-in-out;
            color: rgba(255, 255, 255, 0.7);
            font-weight: 500;
        }

        .form-floating .form-control:focus ~ label,
        .form-floating .form-control:not(:placeholder-shown) ~ label {
            opacity: 1;
            transform: scale(0.85) translateY(-0.5rem) translateX(0.15rem);
            color: #667eea;
            font-weight: 600;
        }

        .floating-textarea {
            min-height: 120px;
            resize: vertical;
        }

        .character-counter {
            position: absolute;
            bottom: 0.5rem;
            right: 0.75rem;
            font-size: 0.75rem;
            color: rgba(255, 255, 255, 0.6);
            background: rgba(0, 0, 0, 0.3);
            padding: 0.25rem 0.5rem;
            border-radius: 12px;
            transition: all 0.3s ease;
        }

        .character-counter.warning {
            color: #ffc107;
            background: rgba(255, 193, 7, 0.2);
        }

        .btn-group-enhanced {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
            flex-wrap: wrap;
        }

        .btn-enhanced {
            flex: 1;
            min-width: 150px;
            padding: 1rem 2rem;
            font-weight: 600;
            border-radius: 50px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .btn-enhanced::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: all 0.5s;
        }

        .btn-enhanced:hover::before {
            left: 100%;
        }

        .btn-enhanced:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.3);
        }

        .event-preview {
            position: sticky;
            top: 2rem;
        }

        .preview-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 12px;
            margin-bottom: 1rem;
            transition: all 0.3s ease;
        }

        .preview-item:hover {
            background: rgba(255, 255, 255, 0.1);
            transform: translateX(5px);
        }

        .preview-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            color: white;
            font-weight: 600;
            flex-shrink: 0;
        }

        .preview-content {
            flex: 1;
            min-width: 0;
        }

        .preview-label {
            color: rgba(255, 255, 255, 0.6);
            font-size: 0.8rem;
            font-weight: 500;
            margin-bottom: 0.25rem;
        }

        .preview-value {
            color: white;
            font-weight: 600;
            word-break: break-word;
        }

        .breadcrumb-enhanced {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 50px;
            padding: 0.75rem 1.5rem;
            border: 1px solid rgba(255, 255, 255, 0.2);
            margin-bottom: 2rem;
        }

        .breadcrumb-enhanced .breadcrumb {
            margin: 0;
            background: transparent;
        }

        .breadcrumb-enhanced .breadcrumb-item a {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .breadcrumb-enhanced .breadcrumb-item a:hover {
            color: #4facfe;
            text-shadow: 0 0 10px rgba(79, 172, 254, 0.5);
        }

        .breadcrumb-enhanced .breadcrumb-item.active {
            color: white;
            font-weight: 600;
        }

        .save-indicator {
            position: fixed;
            top: 2rem;
            right: 2rem;
            background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 50px;
            font-weight: 600;
            box-shadow: 0 8px 25px rgba(67, 233, 123, 0.4);
            transform: translateX(400px);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 1100;
        }

        .save-indicator.show {
            transform: translateX(0);
        }

        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            backdrop-filter: blur(10px);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }

        .loading-overlay.show {
            opacity: 1;
            visibility: visible;
        }

        .loading-spinner {
            width: 60px;
            height: 60px;
            border: 4px solid rgba(255, 255, 255, 0.3);
            border-top: 4px solid #4facfe;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        @media (max-width: 768px) {
            .edit-form-container {
                padding: 2rem 1.5rem;
            }
            
            .btn-group-enhanced {
                flex-direction: column;
            }
            
            .btn-enhanced {
                min-width: auto;
            }
            
            .event-preview {
                position: static;
                margin-top: 2rem;
            }
        }

        .animate-slide-in {
            animation: slideIn 0.6s ease-out;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(-30px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .animate-fade-in-up {
            animation: fadeInUp 0.8s ease-out;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>
    <div class="loading-overlay" id="loadingOverlay">
        <div class="text-center">
            <div class="loading-spinner mb-3"></div>
            <p class="text-white">Updating Event...</p>
        </div>
    </div>

    <div class="save-indicator" id="saveIndicator">
        <i class="bi bi-check-circle me-2"></i>Changes Saved
    </div>

    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php">
                <i class="bi bi-collection"></i> GalaGo Events
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php">
                            <i class="bi bi-house"></i> Home
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="events.php">
                            <i class="bi bi-list"></i> All Events
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="breadcrumb-enhanced animate-slide-in">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                    <li class="breadcrumb-item"><a href="events.php">Events</a></li>
                    <li class="breadcrumb-item active">Edit Event</li>
                </ol>
            </nav>
        </div>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show animate-bounce" role="alert">
                <i class="bi bi-check-circle"></i> <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show animate-bounce" role="alert">
                <i class="bi bi-exclamation-triangle"></i> <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="edit-header animate-fade-in-up">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="text-white mb-2">
                        <i class="bi bi-pencil-square"></i> Edit Event
                    </h1>
                    <p class="text-white opacity-75 mb-0">
                        Modify event details and update information
                    </p>
                </div>
                <div class="col-md-4 text-md-end">
                    <span class="badge bg-info fs-6">
                        <i class="bi bi-calendar"></i>
                        <?php echo date('M d, Y', strtotime($event['date_added'])); ?>
                    </span>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="edit-form-container animate-fade-in-up" style="animation-delay: 0.2s;">
                    <form method="POST" action="" class="needs-validation" novalidate id="editEventForm">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" 
                                           class="form-control" 
                                           id="event_name" 
                                           name="event_name" 
                                           value="<?php echo htmlspecialchars($event['event_name']); ?>" 
                                           required 
                                           maxlength="255"
                                           placeholder="Event Name">
                                    <label for="event_name">Event Name *</label>
                                    <div class="invalid-feedback">Please provide a valid event name.</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" 
                                           class="form-control" 
                                           id="event_location" 
                                           name="event_location" 
                                           value="<?php echo htmlspecialchars($event['event_location']); ?>" 
                                           required 
                                           maxlength="255"
                                           placeholder="Event Location">
                                    <label for="event_location">Event Location *</label>
                                    <div class="invalid-feedback">Please provide a valid event location.</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="date" 
                                           class="form-control" 
                                           id="event_date" 
                                           name="event_date" 
                                           value="<?php echo $event['event_date']; ?>" 
                                           required
                                           placeholder="Event Date">
                                    <label for="event_date">Event Date *</label>
                                    <div class="invalid-feedback">Please provide a valid event date.</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="number" 
                                           class="form-control" 
                                           id="pricing" 
                                           name="pricing" 
                                           value="<?php echo $event['pricing']; ?>" 
                                           step="0.01" 
                                           min="0" 
                                           required
                                           placeholder="Pricing">
                                    <label for="pricing">Pricing (₱) *</label>
                                    <div class="invalid-feedback">Please provide a valid pricing amount.</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-floating position-relative">
                            <textarea class="form-control floating-textarea" 
                                      id="event_remarks" 
                                      name="event_remarks" 
                                      maxlength="1000"
                                      placeholder="Event Remarks"><?php echo htmlspecialchars($event['event_remarks']); ?></textarea>
                            <label for="event_remarks">Event Remarks</label>
                            <div class="character-counter" id="charCounter">
                                1000 characters remaining
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <small class="text-white opacity-75">
                                <i class="bi bi-info-circle"></i> 
                                Event was originally added on: <?php echo date('F d, Y \a\t g:i A', strtotime($event['date_added'])); ?>
                            </small>
                        </div>
                        
                        <div class="btn-group-enhanced">
                            <button type="submit" name="update_event" class="btn btn-primary btn-enhanced">
                                <i class="bi bi-check-circle"></i> Update Event
                            </button>
                            <a href="events.php" class="btn btn-secondary btn-enhanced">
                                <i class="bi bi-arrow-left"></i> Cancel
                            </a>
                            <button type="button" class="btn btn-info btn-enhanced" onclick="previewChanges()">
                                <i class="bi bi-eye"></i> Preview
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="event-preview animate-fade-in-up" style="animation-delay: 0.4s;">
                    <div class="preview-card">
                        <h5 class="text-white mb-4">
                            <i class="bi bi-eye"></i> Live Preview
                        </h5>
                        
                        <div class="preview-item">
                            <div class="preview-icon">
                                <i class="bi bi-calendar-event"></i>
                            </div>
                            <div class="preview-content">
                                <div class="preview-label">Event Name</div>
                                <div class="preview-value" id="preview-name">
                                    <?php echo htmlspecialchars($event['event_name']); ?>
                                </div>
                            </div>
                        </div>

                        <div class="preview-item">
                            <div class="preview-icon">
                                <i class="bi bi-geo-alt"></i>
                            </div>
                            <div class="preview-content">
                                <div class="preview-label">Location</div>
                                <div class="preview-value" id="preview-location">
                                    <?php echo htmlspecialchars($event['event_location']); ?>
                                </div>
                            </div>
                        </div>

                        <div class="preview-item">
                            <div class="preview-icon">
                                <i class="bi bi-calendar"></i>
                            </div>
                            <div class="preview-content">
                                <div class="preview-label">Event Date</div>
                                <div class="preview-value" id="preview-date">
                                    <?php echo date('F d, Y', strtotime($event['event_date'])); ?>
                                </div>
                            </div>
                        </div>

                        <div class="preview-item">
                            <div class="preview-icon">
                                <i class="bi bi-cash"></i>
                            </div>
                            <div class="preview-content">
                                <div class="preview-label">Pricing</div>
                                <div class="preview-value" id="preview-pricing">
                                    ₱<?php echo number_format($event['pricing'], 2); ?>
                                </div>
                            </div>
                        </div>

                        <?php if (!empty($event['event_remarks'])): ?>
                        <div class="preview-item">
                            <div class="preview-icon">
                                <i class="bi bi-chat-text"></i>
                            </div>
                            <div class="preview-content">
                                <div class="preview-label">Remarks</div>
                                <div class="preview-value" id="preview-remarks">
                                    <?php echo htmlspecialchars($event['event_remarks']); ?>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <div class="text-center mt-4">
                            <small class="text-white opacity-50">
                                <i class="bi bi-clock"></i> Last updated: <span id="last-updated">Now</span>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="mt-5 py-4">
        <div class="container text-center">
            <p class="text-white opacity-75 mb-0">
                &copy; <?php echo date('Y'); ?> GalaGo Events Monitoring System. All rights reserved.
            </p>
        </div>
    </footer>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
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
    </script>
</body>
</html>
