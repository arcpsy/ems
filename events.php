<?php
session_start();
require_once 'config.php';

$page_title = "Events Management - GalaGo";

// Handle form submission for adding events
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_event'])) {
    $event_name = sanitize_input($_POST['event_name']);
    $event_location = sanitize_input($_POST['event_location']);
    $event_date = sanitize_input($_POST['event_date']);
    $event_remarks = isset($_POST['event_remarks']) ? sanitize_input($_POST['event_remarks']) : '';
    $pricing = (float)$_POST['pricing'];
    
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
        $stmt = $conn->prepare("INSERT INTO events (event_name, event_location, event_date, event_remarks, pricing) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssd", $event_name, $event_location, $event_date, $event_remarks, $pricing);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = "Event added successfully!";
        } else {
            $_SESSION['error'] = "Error adding event: " . $conn->error;
        }
        $stmt->close();
    } else {
        $_SESSION['error'] = implode(", ", $errors);
    }
    
    header("Location: events.php");
    exit();
}

// Handle delete functionality
if (isset($_POST['delete_event']) && is_numeric($_POST['event_id'])) {
    $event_id = (int)$_POST['event_id'];
    $stmt = $conn->prepare("DELETE FROM events WHERE id = ?");
    $stmt->bind_param("i", $event_id);
    
    if ($stmt->execute()) {
        $_SESSION['success'] = "Event deleted successfully!";
    } else {
        $_SESSION['error'] = "Error deleting event";
    }
    $stmt->close();
    header("Location: events.php");
    exit();
}

// Fetch all events
$query = "SELECT * FROM events ORDER BY event_date DESC";
$result = $conn->query($query);
?>

<!-- edit this, place your name and section here -->
<!-- Students: Dagatan, Tristan Kyle; Dobli, Ferdinand John; Laynes, Carlo Allan; Manuel, Meynard Roi; Niñora, Michael Andrei; Sintos, Tristan James -->
<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href=".\style2.css"> <!-- do not edit or delete css -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css" rel="stylesheet">
<link href="css/style.css" rel="stylesheet">
<title><?php echo $page_title; ?></title>
<style>
/* 🎨 STUNNING PURPLE-THEMED UI */
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap');

:root {
    --purple-primary: #8B5CF6;
    --purple-light: #A78BFA;
    --purple-dark: #7C3AED;
    --purple-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    --purple-gradient-2: linear-gradient(135deg, #8B5CF6 0%, #3B82F6 100%);
    --purple-gradient-3: linear-gradient(135deg, #EC4899 0%, #8B5CF6 100%);
    --success: #10B981;
    --warning: #F59E0B;
    --danger: #EF4444;
    --info: #06B6D4;
    --white: #FFFFFF;
    --black: #1F2937;
    --gray-100: #F3F4F6;
    --gray-200: #E5E7EB;
    --gray-300: #D1D5DB;
    --gray-700: #374151;
    --shadow-purple: 0 20px 25px -5px rgba(139, 92, 246, 0.1), 0 10px 10px -5px rgba(139, 92, 246, 0.04);
    --shadow-glow: 0 0 50px rgba(139, 92, 246, 0.3);
    --glass-bg: rgba(255, 255, 255, 0.1);
    --glass-border: rgba(255, 255, 255, 0.2);
}

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    background: var(--purple-gradient);
    min-height: 100vh;
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    overflow-x: hidden;
    position: relative;
}

/* Animated Background */
body::before {
    content: '';
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: 
        radial-gradient(circle at 20% 50%, rgba(139, 92, 246, 0.3) 0%, transparent 50%),
        radial-gradient(circle at 80% 20%, rgba(59, 130, 246, 0.3) 0%, transparent 50%),
        radial-gradient(circle at 40% 80%, rgba(236, 72, 153, 0.3) 0%, transparent 50%);
    z-index: -1;
    animation: backgroundPulse 15s ease-in-out infinite;
}

@keyframes backgroundPulse {
    0%, 100% { opacity: 0.6; transform: scale(1); }
    50% { opacity: 1; transform: scale(1.1); }
}

/* Navigation */
.navbar {
    background: rgba(255, 255, 255, 0.15) !important;
    backdrop-filter: blur(20px);
    border-bottom: 1px solid rgba(255, 255, 255, 0.2);
    box-shadow: 0 8px 32px rgba(139, 92, 246, 0.1);
    position: sticky;
    top: 0;
    z-index: 1000;
    transition: all 0.3s ease;
}

.navbar:hover {
    background: rgba(255, 255, 255, 0.2) !important;
}

.navbar-brand {
    font-weight: 800;
    color: var(--white) !important;
    font-size: 1.5rem;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
}

.navbar-brand:hover {
    transform: scale(1.05);
    text-shadow: 0 0 20px rgba(255, 255, 255, 0.5);
}

.nav-link {
    color: rgba(255, 255, 255, 0.9) !important;
    font-weight: 600;
    border-radius: 25px;
    padding: 0.6rem 1.2rem !important;
    margin: 0 0.25rem;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
}

.nav-link::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    transition: all 0.5s;
    z-index: -1;
}

.nav-link:hover::before,
.nav-link.active::before {
    left: 100%;
}

.nav-link:hover,
.nav-link.active {
    background: rgba(255, 255, 255, 0.2);
    color: var(--white) !important;
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(255, 255, 255, 0.2);
}

/* Glass Morphism Cards */
.glass-card {
    background: var(--glass-bg);
    backdrop-filter: blur(20px);
    border: 1px solid var(--glass-border);
    border-radius: 20px;
    box-shadow: var(--shadow-purple);
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
}

.glass-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 1px;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.5), transparent);
}

.glass-card:hover {
    transform: translateY(-8px) rotateX(2deg);
    box-shadow: var(--shadow-glow);
    border-color: rgba(255, 255, 255, 0.3);
}

/* Page Header */
.page-header {
    background: var(--glass-bg);
    backdrop-filter: blur(20px);
    border: 1px solid var(--glass-border);
    border-radius: 25px;
    padding: 2rem;
    margin-bottom: 2rem;
    box-shadow: var(--shadow-purple);
    position: relative;
    overflow: hidden;
}

.page-header::after {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(255, 255, 255, 0.05) 0%, transparent 70%);
    animation: rotate 20s linear infinite;
}

@keyframes rotate {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

.page-title {
    color: var(--white);
    font-weight: 800;
    font-size: 2.5rem;
    margin-bottom: 0.5rem;
    position: relative;
    z-index: 1;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.page-subtitle {
    color: rgba(255, 255, 255, 0.8);
    margin: 0;
    position: relative;
    z-index: 1;
    font-weight: 500;
}

.stats-counter {
    font-size: 3rem;
    font-weight: 900;
    color: var(--white);
    text-shadow: 0 0 20px rgba(255, 255, 255, 0.5);
    position: relative;
    z-index: 1;
}

/* Forms - Enhanced visibility */
.form-control {
    background: rgba(255, 255, 255, 0.95) !important;
    border: 2px solid rgba(255, 255, 255, 0.4);
    border-radius: 15px;
    padding: 0.875rem 1.25rem;
    font-weight: 600 !important;
    color: #1a202c !important;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.form-control::placeholder {
    color: #718096 !important;
    font-weight: 500;
}

.form-control:focus {
    background: rgba(255, 255, 255, 1) !important;
    border-color: var(--purple-primary);
    box-shadow: 0 0 0 4px rgba(139, 92, 246, 0.1), 0 4px 6px rgba(0, 0, 0, 0.1);
    transform: translateY(-2px);
    outline: none;
    color: #1a202c !important;
}

/* Textarea specific styling */
textarea.form-control {
    background: rgba(255, 255, 255, 0.95) !important;
    color: #1a202c !important;
    font-weight: 500 !important;
    line-height: 1.5;
}

textarea.form-control:focus {
    background: rgba(255, 255, 255, 1) !important;
    color: #1a202c !important;
}

/* Input number and date specific styling */
input[type="number"].form-control,
input[type="date"].form-control {
    background: rgba(255, 255, 255, 0.95) !important;
    color: #1a202c !important;
    font-weight: 600 !important;
}

.form-label {
    font-weight: 700;
    color: var(--white);
    margin-bottom: 0.75rem;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
    font-size: 0.95rem;
}

/* Buttons */
.btn {
    border: none;
    border-radius: 15px;
    font-weight: 700;
    padding: 0.875rem 2rem;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
    text-transform: none;
    letter-spacing: 0.5px;
}

.btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    transition: all 0.5s;
}

.btn:hover::before {
    left: 100%;
}

.btn:hover {
    transform: translateY(-3px) scale(1.02);
}

.btn-primary {
    background: var(--purple-gradient-2);
    color: var(--white);
    box-shadow: 0 8px 25px rgba(139, 92, 246, 0.4);
}

.btn-primary:hover {
    box-shadow: 0 15px 35px rgba(139, 92, 246, 0.6);
    background: linear-gradient(135deg, #7C3AED 0%, #2563EB 100%);
}

.btn-success {
    background: linear-gradient(135deg, var(--success) 0%, #059669 100%);
    color: var(--white);
    box-shadow: 0 8px 25px rgba(16, 185, 129, 0.4);
}

.btn-outline-info,
.btn-outline-primary,
.btn-outline-danger,
.btn-outline-secondary {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    border: 2px solid rgba(255, 255, 255, 0.3);
    color: var(--white);
    font-weight: 600;
}

.btn-outline-info:hover {
    background: var(--info);
    border-color: var(--info);
    color: var(--white);
}

.btn-outline-primary:hover {
    background: var(--purple-primary);
    border-color: var(--purple-primary);
    color: var(--white);
}

.btn-outline-danger:hover {
    background: var(--danger);
    border-color: var(--danger);
    color: var(--white);
}

.btn-outline-secondary:hover {
    background: var(--gray-700);
    border-color: var(--gray-700);
    color: var(--white);
}

/* Search */
.search-container {
    position: relative;
    max-width: 500px;
}

.search-box {
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(20px);
    border: 2px solid rgba(255, 255, 255, 0.3);
    border-radius: 25px;
    padding: 1rem 1.25rem 1rem 3.5rem;
    width: 100%;
    font-weight: 500;
    color: var(--white);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.search-box::placeholder {
    color: rgba(255, 255, 255, 0.7);
}

.search-box:focus {
    background: rgba(255, 255, 255, 0.25);
    border-color: rgba(255, 255, 255, 0.5);
    box-shadow: 0 0 0 4px rgba(255, 255, 255, 0.1);
    outline: none;
    transform: scale(1.02);
}

.search-icon {
    position: absolute;
    left: 1.25rem;
    top: 50%;
    transform: translateY(-50%);
    color: rgba(255, 255, 255, 0.7);
    z-index: 10;
    font-size: 1.1rem;
}

/* View Toggle */
.view-toggle {
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(20px);
    border-radius: 20px;
    padding: 0.5rem;
    box-shadow: var(--shadow-purple);
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.view-toggle .btn {
    border-radius: 15px;
    padding: 0.75rem 1.5rem;
    margin: 0;
    background: transparent;
    color: rgba(255, 255, 255, 0.8);
    border: none;
    font-weight: 600;
}

.view-toggle .btn.active {
    background: var(--purple-gradient-2);
    color: var(--white);
    box-shadow: 0 4px 15px rgba(139, 92, 246, 0.4);
    transform: scale(1.05);
}

/* Event Cards */
.event-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(380px, 1fr));
    gap: 2rem;
    margin-top: 2rem;
}

.event-card {
    background: var(--glass-bg);
    backdrop-filter: blur(20px);
    border: 1px solid var(--glass-border);
    border-radius: 25px;
    position: relative;
    overflow: hidden;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: var(--shadow-purple);
    height: auto;
    min-height: 350px;
}

.event-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 4px;
    height: 100%;
    background: var(--purple-gradient-3);
    transform: scaleY(0);
    transition: transform 0.3s ease;
}

.event-card:hover::before {
    transform: scaleY(1);
}

.event-card:hover {
    transform: translateY(-10px) rotateX(5deg);
    box-shadow: var(--shadow-glow);
    border-color: rgba(255, 255, 255, 0.4);
}

.event-status-badge {
    position: absolute;
    top: 1.5rem;
    left: 1.5rem;
    padding: 0.6rem 1.2rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.75px;
    z-index: 10;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.3);
}

.bg-success {
    background: linear-gradient(135deg, var(--success) 0%, #059669 100%) !important;
    color: var(--white);
    box-shadow: 0 4px 15px rgba(16, 185, 129, 0.4);
}

.bg-warning {
    background: linear-gradient(135deg, var(--warning) 0%, #D97706 100%) !important;
    color: var(--white);
    box-shadow: 0 4px 15px rgba(245, 158, 11, 0.4);
}

.bg-secondary {
    background: linear-gradient(135deg, #6B7280 0%, #4B5563 100%) !important;
    color: var(--white);
    box-shadow: 0 4px 15px rgba(107, 114, 128, 0.4);
}

.card-body {
    padding: 2rem;
    height: 100%;
    display: flex;
    flex-direction: column;
}

.event-date-large {
    font-size: 3rem;
    font-weight: 900;
    color: var(--white);
    line-height: 1;
    text-shadow: 0 0 20px rgba(255, 255, 255, 0.3);
}

.event-month {
    color: rgba(255, 255, 255, 0.8);
    font-size: 0.9rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.pricing-badge {
    background: var(--purple-gradient-3);
    color: var(--white);
    padding: 0.875rem 1.75rem;
    border-radius: 20px;
    font-weight: 800;
    font-size: 1.1rem;
    box-shadow: 0 8px 25px rgba(236, 72, 153, 0.4);
    border: 1px solid rgba(255, 255, 255, 0.2);
    animation: pulse 2s ease-in-out infinite;
}

@keyframes pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.05); }
}

.event-title {
    color: var(--white);
    font-weight: 800;
    font-size: 1.4rem;
    margin-bottom: 1rem;
    line-height: 1.3;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.event-meta-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    color: rgba(255, 255, 255, 0.9);
    font-weight: 500;
    margin-bottom: 0.75rem;
}

.event-meta-item i {
    color: var(--purple-light);
    width: 20px;
    font-size: 1.1rem;
}

/* List View */
.list-view {
    display: none;
    margin-top: 2rem;
}

.list-view.active {
    display: block;
    animation: fadeInUp 0.6s ease-out;
}

.event-list-item {
    background: var(--glass-bg);
    backdrop-filter: blur(20px);
    border: 1px solid var(--glass-border);
    border-radius: 25px;
    margin-bottom: 2rem;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: var(--shadow-purple);
    border-left: 5px solid var(--purple-primary);
    overflow: hidden;
    position: relative;
}

.event-list-item::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 1px;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
}

.event-list-item:hover {
    transform: translateX(10px) scale(1.02);
    box-shadow: var(--shadow-glow);
    border-left-color: var(--purple-light);
}

.event-date-circle {
    width: 90px;
    height: 90px;
    border-radius: 20px;
    background: var(--purple-gradient-2);
    color: var(--white);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    font-weight: 800;
    box-shadow: 0 8px 25px rgba(139, 92, 246, 0.4);
    border: 2px solid rgba(255, 255, 255, 0.2);
}

/* Price Tag */
.price-tag {
    background: var(--purple-gradient-3);
    color: var(--white);
    padding: 1.25rem 2.5rem;
    border-radius: 25px;
    font-weight: 800;
    font-size: 1.5rem;
    display: inline-block;
    box-shadow: 0 8px 25px rgba(236, 72, 153, 0.4);
    border: 2px solid rgba(255, 255, 255, 0.2);
    position: relative;
    overflow: hidden;
}

.price-tag::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    transition: all 0.5s;
}

.price-tag:hover::before {
    left: 100%;
}

/* Modals */
.modal {
    z-index: 1055;
}

.modal-backdrop {
    z-index: 1050;
    background-color: rgba(0, 0, 0, 0.6);
    backdrop-filter: blur(5px);
}

.modal-content {
    background: var(--glass-bg);
    backdrop-filter: blur(20px);
    border: 1px solid var(--glass-border);
    border-radius: 25px;
    box-shadow: var(--shadow-glow);
    overflow: hidden;
}

.modal-header {
    border-bottom: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 25px 25px 0 0;
    padding: 1.5rem;
    position: relative;
}

.modal-header.bg-info {
    background: var(--info) !important;
}

.modal-header.bg-danger {
    background: var(--danger) !important;
}

.modal-body {
    padding: 2rem;
    color: var(--white);
}

.modal-body h6 {
    color: rgba(255, 255, 255, 0.8);
}

.modal-body p {
    color: var(--white);
    font-weight: 500;
}

.modal-footer {
    border-top: 1px solid rgba(255, 255, 255, 0.2);
    padding: 1.5rem;
}

/* Alerts */
.alert {
    background: var(--glass-bg);
    backdrop-filter: blur(20px);
    border: 1px solid var(--glass-border);
    border-radius: 20px;
    font-weight: 500;
    padding: 1.25rem 1.75rem;
    margin-bottom: 1.5rem;
    box-shadow: var(--shadow-purple);
    color: var(--white);
}

.alert-success {
    border-left: 4px solid var(--success);
    background: rgba(16, 185, 129, 0.1);
}

.alert-danger {
    border-left: 4px solid var(--danger);
    background: rgba(239, 68, 68, 0.1);
}

.alert i {
    color: var(--white);
}

/* Character Counter */
.character-counter {
    font-size: 0.85rem;
    color: rgba(255, 255, 255, 0.7);
    margin-top: 0.5rem;
    font-weight: 500;
}

/* No Events State */
.no-events-state {
    text-align: center;
    padding: 4rem 2rem;
    background: var(--glass-bg);
    backdrop-filter: blur(20px);
    border-radius: 25px;
    border: 2px dashed rgba(255, 255, 255, 0.3);
    margin: 2rem 0;
}

.no-events-state i {
    font-size: 5rem;
    background: var(--purple-gradient-3);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin-bottom: 1.5rem;
}

.no-events-state h4 {
    color: var(--white);
    margin-bottom: 1rem;
    font-weight: 700;
}

.no-events-state p {
    color: rgba(255, 255, 255, 0.8);
    margin-bottom: 2rem;
}

/* Footer */
footer {
    background: rgba(255, 255, 255, 0.05);
    backdrop-filter: blur(20px);
    border-top: 1px solid rgba(255, 255, 255, 0.1);
    margin-top: 4rem;
    padding: 2rem 0;
}

footer p {
    color: rgba(255, 255, 255, 0.8);
    margin: 0;
    font-weight: 500;
}

/* Animations */
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

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes slideInLeft {
    from {
        opacity: 0;
        transform: translateX(-30px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

.animate-fade-in {
    animation: fadeInUp 0.8s ease-out;
}

.animate-slide-in {
    animation: slideInLeft 0.6s ease-out;
}

/* Grid View Active */
.grid-view {
    display: none;
}

.grid-view.active {
    display: block;
    animation: fadeInUp 0.6s ease-out;
}

/* Responsive */
@media (max-width: 768px) {
    .event-grid {
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }
    
    .page-header {
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }
    
    .page-title {
        font-size: 2rem;
    }
    
    .card-body {
        padding: 1.5rem;
    }
    
    .stats-counter {
        font-size: 2.5rem;
    }
    
    .search-container {
        margin-bottom: 1rem;
    }
}

/* Hover Effects */
.btn:hover,
.event-card:hover,
.event-list-item:hover {
    animation: none;
}

/* Scrollbar */
::-webkit-scrollbar {
    width: 8px;
}

::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.1);
    border-radius: 10px;
}

::-webkit-scrollbar-thumb {
    background: var(--purple-gradient-2);
    border-radius: 10px;
}

::-webkit-scrollbar-thumb:hover {
    background: var(--purple-gradient-3);
}
</style>
</head>
<body> <!-- don't edit anything prior here except inside the script tags -->

<!-- Original professor form structure -->
<div id="inputForm" style="display: none;">
<form name="form1" action="" method="post"><!-- modify this-->
<br>
<!-- no edits within this space -->
 Email: <input type="text" name="email" value=""/><br/>
<input type="Submit" value="Verify"/>
<!-- until here -->
</form>
</div>

<!-- use these for the php outputs, remove the comments and this text. -->
<div id='display'>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="bi bi-gem me-2"></i>GalaGo Events
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">
                            <i class="bi bi-house-door me-1"></i>Home
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="events.php">
                            <i class="bi bi-calendar-star me-1"></i>All Events
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content Area -->
    <div class="container mt-4">
        <!-- Session Messages -->
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show animate-fade-in" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i><?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show animate-fade-in" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i><?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Page Header -->
        <div class="page-header animate-fade-in">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="page-title">
                        <i class="bi bi-calendar-star me-3"></i>Events Management
                    </h1>
                    <p class="page-subtitle">Manage and track all your company events efficiently</p>
                </div>
                <div class="col-md-4 text-md-end">
                    <div class="stats-counter"><?php echo $result->num_rows; ?></div>
                    <small class="text-white-50">Events Tracked</small>
                </div>
            </div>
        </div>

        <!-- ADD EVENT FORM ON TOP OF PAGE -->
        <div class="glass-card mb-4 animate-slide-in">
            <div class="card-header" style="background: var(--purple-gradient-2); color: white; border-radius: 20px 20px 0 0; padding: 1.5rem;">
                <h5 class="mb-0">
                    <i class="bi bi-plus-circle me-2"></i>Add New Event
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="events.php" class="needs-validation" novalidate>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="event_name" class="form-label">Event Name *</label>
                            <input type="text" class="form-control" id="event_name" name="event_name" required maxlength="255" placeholder="Enter event name">
                            <div class="invalid-feedback">Please provide a valid event name.</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="event_location" class="form-label">Event Location *</label>
                            <input type="text" class="form-control" id="event_location" name="event_location" required maxlength="255" placeholder="Enter event location">
                            <div class="invalid-feedback">Please provide a valid event location.</div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="event_date" class="form-label">Event Date *</label>
                            <input type="date" class="form-control" id="event_date" name="event_date" required min="<?php echo date('Y-m-d'); ?>">
                            <div class="invalid-feedback">Please provide a valid event date.</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="pricing" class="form-label">Pricing (₱) *</label>
                            <input type="number" class="form-control" id="pricing" name="pricing" step="0.01" min="0" required placeholder="0.00">
                            <div class="invalid-feedback">Please provide a valid pricing amount.</div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="event_remarks" class="form-label">Event Remarks</label>
                        <textarea class="form-control" id="event_remarks" name="event_remarks" rows="3" placeholder="Optional remarks about the event" maxlength="1000"></textarea>
                        <div class="character-counter" id="charCounter">1000 characters remaining</div>
                    </div>
                    <div class="d-flex gap-3">
                        <button type="submit" name="add_event" value="1" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-2"></i>Add Event
                        </button>
                        <button type="reset" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-clockwise me-2"></i>Reset Form
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Search and View Toggle -->
        <div class="row mb-4 align-items-center">
            <div class="col-md-6">
                <div class="search-container">
                    <i class="bi bi-search search-icon"></i>
                    <input type="text" class="search-box" placeholder="Search events..." id="eventSearch" onkeyup="filterEvents()">
                </div>
            </div>
            <div class="col-md-6 d-flex justify-content-md-end justify-content-start mt-3 mt-md-0">
                <div class="view-toggle btn-group" role="group">
                    <button type="button" class="btn active" id="gridBtn" onclick="toggleView('grid')">
                        <i class="bi bi-grid-3x2-gap me-2"></i>Grid
                    </button>
                    <button type="button" class="btn" id="listBtn" onclick="toggleView('list')">
                        <i class="bi bi-list-ul me-2"></i>List
                    </button>
                </div>
            </div>
        </div>

        <!-- Events Display -->
        <?php if ($result->num_rows > 0): ?>
            <!-- Grid View -->
            <div class="grid-view active animate-fade-in" id="gridView">
                <div class="event-grid">
                    <?php 
                    $result->data_seek(0);
                    while($event = $result->fetch_assoc()): 
                        $event_timestamp = strtotime($event['event_date']);
                        $today_timestamp = strtotime('today');
                        
                        if ($event_timestamp < $today_timestamp) {
                            $status_badge = 'bg-secondary';
                            $status_text = 'Past';
                        } elseif ($event_timestamp == $today_timestamp) {
                            $status_badge = 'bg-warning';
                            $status_text = 'Today';
                        } else {
                            $status_badge = 'bg-success';
                            $status_text = 'Upcoming';
                        }
                    ?>
                    <div class="event-card">
                        <div class="card-body position-relative">
                            <span class="badge <?php echo $status_badge; ?> event-status-badge">
                                <?php echo $status_text; ?>
                            </span>

                            <div class="d-flex justify-content-between align-items-start mb-4" style="margin-top: 4rem;">
                                <div class="text-center">
                                    <div class="event-date-large">
                                        <?php echo date('d', strtotime($event['event_date'])); ?>
                                    </div>
                                    <div class="event-month">
                                        <?php echo date('M Y', strtotime($event['event_date'])); ?>
                                    </div>
                                </div>
                                <div class="pricing-badge">
                                    ₱<?php echo number_format($event['pricing'], 2); ?>
                                </div>
                            </div>

                            <h5 class="event-title">
                                <?php echo htmlspecialchars($event['event_name']); ?>
                            </h5>
                            
                            <div class="event-meta-item">
                                <i class="bi bi-geo-alt-fill"></i>
                                <span><?php echo htmlspecialchars($event['event_location']); ?></span>
                            </div>

                            <div class="event-meta-item mb-3">
                                <i class="bi bi-calendar-fill"></i>
                                <span><?php echo date('l, F d, Y', strtotime($event['event_date'])); ?></span>
                            </div>

                            <?php if (!empty($event['event_remarks'])): ?>
                            <div class="mb-3">
                                <small style="color: rgba(255, 255, 255, 0.8);">
                                    <i class="bi bi-chat-text me-1"></i>
                                    <?php echo htmlspecialchars(substr($event['event_remarks'], 0, 80)); ?>
                                    <?php echo strlen($event['event_remarks']) > 80 ? '...' : ''; ?>
                                </small>
                            </div>
                            <?php endif; ?>

                            <div class="d-grid gap-2 d-md-flex justify-content-md-center mt-auto">
                                <button type="button" class="btn btn-outline-info btn-sm" data-bs-toggle="modal" data-bs-target="#viewModal<?php echo $event['id']; ?>">
                                    <i class="bi bi-eye me-1"></i>View
                                </button>
                                <a href="edit_event.php?id=<?php echo $event['id']; ?>" class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-pencil me-1"></i>Edit
                                </a>
                                <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal<?php echo $event['id']; ?>">
                                    <i class="bi bi-trash me-1"></i>Delete
                                </button>
                            </div>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
            </div>

            <!-- List View -->
            <div class="list-view" id="listView">
                <?php 
                $result->data_seek(0);
                while($event = $result->fetch_assoc()): 
                ?>
                <div class="event-list-item p-4 d-flex align-items-center">
                    <div class="event-date-circle me-4 flex-shrink-0">
                        <div class="h5 mb-0"><?php echo date('d', strtotime($event['event_date'])); ?></div>
                        <small><?php echo date('M', strtotime($event['event_date'])); ?></small>
                    </div>

                    <div class="flex-grow-1 me-4">
                        <h4 class="event-title mb-2"><?php echo htmlspecialchars($event['event_name']); ?></h4>
                        
                        <div class="d-flex flex-wrap gap-3 mb-2">
                            <div class="event-meta-item">
                                <i class="bi bi-geo-alt-fill"></i>
                                <span><?php echo htmlspecialchars($event['event_location']); ?></span>
                            </div>
                            <div class="event-meta-item">
                                <i class="bi bi-calendar-fill"></i>
                                <span><?php echo date('l, F d, Y', strtotime($event['event_date'])); ?></span>
                            </div>
                        </div>

                        <?php if (!empty($event['event_remarks'])): ?>
                        <div style="color: rgba(255, 255, 255, 0.8);" class="mb-3">
                            <i class="bi bi-chat-text me-2"></i>
                            <?php echo htmlspecialchars(substr($event['event_remarks'], 0, 120)); ?>
                            <?php echo strlen($event['event_remarks']) > 120 ? '...' : ''; ?>
                        </div>
                        <?php endif; ?>

                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-outline-info btn-sm" data-bs-toggle="modal" data-bs-target="#viewModal<?php echo $event['id']; ?>">
                                <i class="bi bi-eye me-1"></i>View Details
                            </button>
                            <a href="edit_event.php?id=<?php echo $event['id']; ?>" class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-pencil me-1"></i>Edit
                            </a>
                            <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal<?php echo $event['id']; ?>">
                                <i class="bi bi-trash me-1"></i>Delete
                            </button>
                        </div>
                    </div>

                    <div class="price-tag">
                        ₱<?php echo number_format($event['pricing'], 2); ?>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="no-events-state animate-fade-in">
                <i class="bi bi-calendar-x"></i>
                <h4>No Events Found</h4>
                <p>Start creating amazing events using the form above!</p>
            </div>
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <footer>
        <div class="container text-center">
            <p>&copy; <?php echo date('Y'); ?> GalaGo Events Monitoring System. All rights reserved.</p>
        </div>
    </footer>
</div>

<div id='inv_display'>
<!-- Additional display area as per professor's template -->
</div>

<!-- MODALS -->
<?php 
$result->data_seek(0);
while($event = $result->fetch_assoc()): 
?>
<!-- View Modal -->
<div class="modal fade" id="viewModal<?php echo $event['id']; ?>" tabindex="-1" aria-labelledby="viewModalLabel<?php echo $event['id']; ?>" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info">
                <h5 class="modal-title text-white" id="viewModalLabel<?php echo $event['id']; ?>">
                    <i class="bi bi-eye me-2"></i>Event Details
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <h6><i class="bi bi-calendar-event me-2"></i>Event Name:</h6>
                            <p class="h5"><?php echo htmlspecialchars($event['event_name']); ?></p>
                        </div>
                        
                        <div class="mb-3">
                            <h6><i class="bi bi-geo-alt me-2"></i>Location:</h6>
                            <p><?php echo htmlspecialchars($event['event_location']); ?></p>
                        </div>
                        
                        <div class="mb-3">
                            <h6><i class="bi bi-calendar me-2"></i>Event Date:</h6>
                            <p><?php echo date('F d, Y (l)', strtotime($event['event_date'])); ?></p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <h6><i class="bi bi-cash me-2"></i>Pricing:</h6>
                            <div class="price-tag">₱<?php echo number_format($event['pricing'], 2); ?></div>
                        </div>
                        
                        <div class="mb-3">
                            <h6><i class="bi bi-clock me-2"></i>Date Added:</h6>
                            <p><?php echo date('F d, Y \a\t g:i A', strtotime($event['date_added'])); ?></p>
                        </div>
                    </div>
                </div>
                
                <?php if (!empty($event['event_remarks'])): ?>
                <div class="row">
                    <div class="col-12">
                        <h6><i class="bi bi-chat-text me-2"></i>Event Remarks:</h6>
                        <div class="p-3 bg-light rounded">
                            <p class="mb-0 text-dark"><?php echo nl2br(htmlspecialchars($event['event_remarks'])); ?></p>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-2"></i>Close
                </button>
                <a href="edit_event.php?id=<?php echo $event['id']; ?>" class="btn btn-primary">
                    <i class="bi bi-pencil me-2"></i>Edit Event
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal<?php echo $event['id']; ?>" tabindex="-1" aria-labelledby="deleteModalLabel<?php echo $event['id']; ?>" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h5 class="modal-title text-white" id="deleteModalLabel<?php echo $event['id']; ?>">
                    <i class="bi bi-exclamation-triangle me-2"></i>Confirm Delete
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <i class="bi bi-trash" style="font-size: 4rem; background: var(--danger-gradient); -webkit-background-clip: text; -webkit-text-fill-color: transparent; margin-bottom: 1rem;"></i>
                <h5>Are you sure you want to delete this event?</h5>
                <p style="color: rgba(255, 255, 255, 0.8);">
                    <strong>"<?php echo htmlspecialchars($event['event_name']); ?>"</strong><br>
                    This action cannot be undone.
                </p>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-2"></i>Cancel
                </button>
                <form method="POST" action="" style="display: inline;">
                    <input type="hidden" name="event_id" value="<?php echo $event['id']; ?>">
                    <button type="submit" name="delete_event" class="btn btn-danger">
                        <i class="bi bi-trash me-2"></i>Delete Event
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php endwhile; ?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
<script src="js/script.js"></script>

<script>
// Enhanced JavaScript with animations
document.addEventListener('DOMContentLoaded', function() {
    // Character counter
    const remarksTextarea = document.getElementById('event_remarks');
    const charCounter = document.getElementById('charCounter');
    const maxLength = 1000;

    if (remarksTextarea && charCounter) {
        function updateCharCounter() {
            const currentLength = remarksTextarea.value.length;
            const remaining = Math.max(0, maxLength - currentLength);
            charCounter.textContent = `${remaining} characters remaining`;
            
            if (remaining <= 100) {
                charCounter.style.color = '#F59E0B';
                charCounter.style.fontWeight = '700';
            } else {
                charCounter.style.color = 'rgba(255, 255, 255, 0.7)';
                charCounter.style.fontWeight = '500';
            }
        }

        remarksTextarea.addEventListener('input', updateCharCounter);
        updateCharCounter();
    }

    // Staggered animation for event cards
    const eventCards = document.querySelectorAll('.event-card, .event-list-item');
    eventCards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(30px)';
        card.style.transition = 'all 0.6s cubic-bezier(0.4, 0, 0.2, 1)';
        
        setTimeout(() => {
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });

    // Enhanced hover effects
    eventCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.filter = 'brightness(1.1)';
        });

        card.addEventListener('mouseleave', function() {
            this.style.filter = 'brightness(1)';
        });
    });

    // Search box glow effect
    const searchBox = document.getElementById('eventSearch');
    if (searchBox) {
        searchBox.addEventListener('focus', function() {
            this.style.boxShadow = '0 0 0 4px rgba(255, 255, 255, 0.2), 0 0 20px rgba(139, 92, 246, 0.3)';
        });

        searchBox.addEventListener('blur', function() {
            this.style.boxShadow = '';
        });
    }

    // Button click animations
    document.querySelectorAll('.btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            const ripple = document.createElement('span');
            const rect = this.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const x = e.clientX - rect.left - size / 2;
            const y = e.clientY - rect.top - size / 2;
            
            ripple.style.cssText = `
                position: absolute;
                width: ${size}px;
                height: ${size}px;
                left: ${x}px;
                top: ${y}px;
                background: rgba(255, 255, 255, 0.3);
                border-radius: 50%;
                transform: scale(0);
                animation: ripple 0.6s ease-out;
                pointer-events: none;
            `;
            
            this.style.position = 'relative';
            this.style.overflow = 'hidden';
            this.appendChild(ripple);
            
            setTimeout(() => ripple.remove(), 600);
        });
    });
});

// Toggle view functions with smooth transitions
function toggleView(viewType) {
    const gridView = document.getElementById('gridView');
    const listView = document.getElementById('listView');
    const gridBtn = document.getElementById('gridBtn');
    const listBtn = document.getElementById('listBtn');
    
    if (viewType === 'list') {
        // Fade out grid
        gridView.style.opacity = '0';
        gridView.style.transform = 'translateY(-20px)';
        
        setTimeout(() => {
            gridView.classList.remove('active');
            listView.classList.add('active');
            gridBtn.classList.remove('active');
            listBtn.classList.add('active');
            
            // Fade in list
            listView.style.opacity = '0';
            listView.style.transform = 'translateY(20px)';
            
            setTimeout(() => {
                listView.style.opacity = '1';
                listView.style.transform = 'translateY(0)';
            }, 50);
        }, 300);
    } else {
        // Fade out list
        listView.style.opacity = '0';
        listView.style.transform = 'translateY(-20px)';
        
        setTimeout(() => {
            listView.classList.remove('active');
            gridView.classList.add('active');
            listBtn.classList.remove('active');
            gridBtn.classList.add('active');
            
            // Fade in grid
            gridView.style.opacity = '0';
            gridView.style.transform = 'translateY(20px)';
            
            setTimeout(() => {
                gridView.style.opacity = '1';
                gridView.style.transform = 'translateY(0)';
            }, 50);
        }, 300);
    }
}

// Enhanced filter with smooth animations
function filterEvents() {
    const searchInput = document.getElementById('eventSearch');
    const filter = searchInput.value.toLowerCase();
    const cards = document.querySelectorAll('.event-card, .event-list-item');

    cards.forEach((card, index) => {
        const text = card.textContent.toLowerCase();
        const matches = text.indexOf(filter) > -1;
        
        if (matches) {
            card.style.opacity = '0';
            card.style.transform = 'scale(0.8)';
            card.style.display = '';
            
            setTimeout(() => {
                card.style.opacity = '1';
                card.style.transform = 'scale(1)';
            }, index * 50);
        } else {
            card.style.opacity = '0';
            card.style.transform = 'scale(0.8)';
            
            setTimeout(() => {
                card.style.display = 'none';
            }, 300);
        }
    });
}

// Form validation with enhanced styling
(function() {
    'use strict';
    window.addEventListener('load', function() {
        const forms = document.getElementsByClassName('needs-validation');
        Array.prototype.filter.call(forms, function(form) {
            form.addEventListener('submit', function(event) {
                if (form.checkValidity() === false) {
                    event.preventDefault();
                    event.stopPropagation();
                    
                    // Shake animation for invalid form
                    form.style.animation = 'shake 0.5s ease-in-out';
                    setTimeout(() => {
                        form.style.animation = '';
                    }, 500);
                } else {
                    // Success animation
                    const submitBtn = form.querySelector('button[type="submit"]');
                    submitBtn.innerHTML = '<i class="bi bi-check-circle me-2"></i>Creating...';
                    submitBtn.style.background = 'linear-gradient(135deg, #10B981 0%, #059669 100%)';
                }
                form.classList.add('was-validated');
            }, false);
        });
    }, false);
})();

// Add CSS keyframes for animations
const style = document.createElement('style');
style.textContent = `
    @keyframes ripple {
        to {
            transform: scale(4);
            opacity: 0;
        }
    }
    
    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
        20%, 40%, 60%, 80% { transform: translateX(5px); }
    }
`;
document.head.appendChild(style);
</script>

</body>
</html>
