<?php

// Handle form submission for adding new event
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_event'])) {
    $event_name = mysqli_real_escape_string($conn, $_POST['event_name']);
    $event_location = mysqli_real_escape_string($conn, $_POST['event_location']);
    $event_date = mysqli_real_escape_string($conn, $_POST['event_date']);
    $event_remarks = mysqli_real_escape_string($conn, $_POST['event_remarks']);
    $pricing = (float)$_POST['pricing'];
    
    if (!empty($event_name) && !empty($event_location) && !empty($event_date) && $pricing >= 0) {
        $stmt = $conn->prepare("INSERT INTO events (event_name, event_location, event_date, event_remarks, pricing) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssd", $event_name, $event_location, $event_date, $event_remarks, $pricing);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = "Event added successfully!";
        } else {
            $_SESSION['error'] = "Error adding event";
        }
        $stmt->close();
        header("Location: events.php");
        exit();
    }
}

// Handle delete request
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