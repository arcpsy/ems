<?php
session_start();
require_once 'config/config.php';

$page_title = "GalaGo - Events Monitoring System - Events";

$page_css = "css/events.css";
$page_js = "js/events.js";
$body_class = "d-flex flex-column min-vh-100";

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


ob_start();
?>

<div>
    <div class="container-sm mt-4 flex-grow-1">
        <h1 class="mb-4">
            <i class="bi bi-clipboard-data" style="color: #818589;"></i> Events Management
        </h1>

        <!-- Add Event Button -->
        <section id="add-event-section" class="mb-4">
            <div class="d-flex justify-content-end">
                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addEventModal">
                    <i class="bi bi-plus-circle"></i> Add Event
                </button>
            </div>
        </section>

        <!-- Events Section -->
        <section>
            <!-- Events Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="mb-0">
                    <i class="bi bi-calendar-check text-dark"></i> All Events (<?php echo $result->num_rows; ?>)
                </h3>
                <div class="view-toggle btn-group" role="group">
                    <button type="button" class="btn active" id="gridBtn" onclick="toggleView('grid')">
                        <i class="bi bi-grid-3x3-gap"></i> Grid
                    </button>
                    <button type="button" class="btn" id="listBtn" onclick="toggleView('list')">
                        <i class="bi bi-list-ul"></i> List
                    </button>
                </div>
            </div>

            <?php if ($result->num_rows > 0): ?>
                <!-- Grid View (Cards) -->
                <?php include 'inc/event_list/event-grid.php'; ?>

                <!-- List View -->
                <?php include 'inc/event_list/event-list.php'; ?>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="bi bi-calendar-x display-1 text-muted"></i>
                    <h4 class="text-muted">No Events Found</h4>
                    <p class="text-muted">Start by adding your first event using the button above.</p>
                </div>
            <?php endif; ?>
        </section>
    </div>

    <!-- Add Modal -->
    <?php include 'inc/modals/add-event-modal.php'; ?>

    <?php 
    $result->data_seek(0); // Reset result pointer
    while($event = $result->fetch_assoc()): 
    ?>
        <!-- View Modal -->
        <?php include 'inc/modals/view-event-modal.php'; ?>
        
        <!-- Delete Modal -->
        <?php include 'inc/modals/delete-event-modal.php'; ?>

    <?php endwhile; ?>
</div>
<?php
$content = ob_get_clean();
include 'template/base.php';
?>