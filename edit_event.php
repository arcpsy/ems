<?php
session_start();
require_once 'config/config.php';

$page_css = "css/edit_event.css";
$page_js = "js/edit_event.js";

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
$page_title = "Edit Event - " . htmlspecialchars($event['event_name']);
$stmt->close();


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

ob_start();
?>

<div>
    <div class="loading-overlay" id="loadingOverlay">
        <div class="text-center">
            <div class="loading-spinner mb-3"></div>
            <p class="text-white">Updating Event...</p>
        </div>
    </div>

    <div class="save-indicator" id="saveIndicator">
        <i class="bi bi-check-circle me-2"></i>Changes Saved
    </div>

    <div class="container mt-4">
        <?php include 'inc/event_edit/breadcrumb.php'; ?>
        <?php include 'inc/event_edit/edit-header.php'; ?>
        <?php include 'inc/event_edit/form-fields.php'; ?>
    </div>

</div>

<?php
$content = ob_get_clean();
include 'template/base.php';
?>