<?php
session_start();
require_once 'config.php';

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

ob_start();
?>

<div class="row">
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item"><a href="events.php">Events</a></li>
                <li class="breadcrumb-item active">Edit Event</li>
            </ol>
        </nav>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0">
                    <i class="bi bi-pencil-square"></i> Edit Event: <?php echo htmlspecialchars($event['event_name']); ?>
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="" class="needs-validation" novalidate>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="event_name" class="form-label">Event Name *</label>
                            <input type="text" class="form-control" id="event_name" name="event_name" 
                                   value="<?php echo htmlspecialchars($event['event_name']); ?>" 
                                   required maxlength="255">
                            <div class="invalid-feedback">
                                Please provide a valid event name.
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="event_location" class="form-label">Event Location *</label>
                            <input type="text" class="form-control" id="event_location" name="event_location" 
                                   value="<?php echo htmlspecialchars($event['event_location']); ?>" 
                                   required maxlength="255">
                            <div class="invalid-feedback">
                                Please provide a valid event location.
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="event_date" class="form-label">Event Date *</label>
                            <input type="date" class="form-control" id="event_date" name="event_date" 
                                   value="<?php echo $event['event_date']; ?>" required>
                            <div class="invalid-feedback">
                                Please provide a valid event date.
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="pricing" class="form-label">Pricing (₱) *</label>
                            <input type="number" class="form-control" id="pricing" name="pricing" 
                                   value="<?php echo $event['pricing']; ?>" 
                                   step="0.01" min="0" required>
                            <div class="invalid-feedback">
                                Please provide a valid pricing amount.
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="event_remarks" class="form-label">Event Remarks</label>
                        <textarea class="form-control" id="event_remarks" name="event_remarks" rows="4" 
                                  placeholder="Optional remarks about the event"><?php echo htmlspecialchars($event['event_remarks']); ?></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <small class="text-muted">
                            <i class="bi bi-info-circle"></i> 
                            Event was originally added on: <?php echo date('F d, Y \a\t g:i A', strtotime($event['date_added'])); ?>
                        </small>
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" name="update_event" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Update Event
                        </button>
                        <a href="events.php" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Event Details Preview -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-info text-white">
                <h6 class="card-title mb-0">
                    <i class="bi bi-eye"></i> Current Event Details
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Event Name:</strong> <?php echo htmlspecialchars($event['event_name']); ?></p>
                        <p><strong>Location:</strong> <?php echo htmlspecialchars($event['event_location']); ?></p>
                        <p><strong>Date:</strong> <?php echo date('F d, Y', strtotime($event['event_date'])); ?></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Pricing:</strong> ₱<?php echo number_format($event['pricing'], 2); ?></p>
                        <p><strong>Date Added:</strong> <?php echo date('F d, Y', strtotime($event['date_added'])); ?></p>
                        <?php if (!empty($event['event_remarks'])): ?>
                            <p><strong>Remarks:</strong> <?php echo htmlspecialchars($event['event_remarks']); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include 'templates/base.html';
?>