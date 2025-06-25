<?php
session_start();
require_once __DIR__ . '/../config/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Events Page Test</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-danger">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="bi bi-calendar-event"></i> Events Monitor
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="index.php">Home</a>
                <a class="nav-link active" href="events.php">All Events</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="alert alert-success">
            <h1><i class="bi bi-check-circle"></i> SUCCESS! This is the EVENTS page, not the homepage!</h1>
            <p>URL: <?php echo $_SERVER['REQUEST_URI']; ?></p>
            <p>File: <?php echo __FILE__; ?></p>
        </div>

        <div class="card mb-4">
            <div class="card-header bg-success text-white">
                <h5><i class="bi bi-plus-circle"></i> Add New Event</h5>
            </div>
            <div class="card-body">
                <?php
                if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_event'])) {
                    $event_name = $_POST['event_name'];
                    $event_location = $_POST['event_location'];
                    $event_date = $_POST['event_date'];
                    $pricing = $_POST['pricing'];
                    $remarks = $_POST['event_remarks'] ?? '';
                    
                    $stmt = $conn->prepare("INSERT INTO events (event_name, event_location, event_date, event_remarks, pricing) VALUES (?, ?, ?, ?, ?)");
                    $stmt->bind_param("ssssd", $event_name, $event_location, $event_date, $remarks, $pricing);
                    
                    if ($stmt->execute()) {
                        echo "<div class='alert alert-success'>Event added successfully!</div>";
                    } else {
                        echo "<div class='alert alert-danger'>Error: " . $conn->error . "</div>";
                    }
                    $stmt->close();
                }
                ?>
                
                <form method="POST" action="">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Event Name *</label>
                            <input type="text" name="event_name" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Event Location *</label>
                            <input type="text" name="event_location" class="form-control" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Event Date *</label>
                            <input type="date" name="event_date" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Pricing (₱) *</label>
                            <input type="number" name="pricing" class="form-control" step="0.01" min="0" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Event Remarks</label>
                        <textarea name="event_remarks" class="form-control" rows="3"></textarea>
                    </div>
                    <button type="submit" name="add_event" class="btn btn-success">
                        <i class="bi bi-plus-circle"></i> Add Event
                    </button>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5><i class="bi bi-list"></i> All Events</h5>
            </div>
            <div class="card-body">
                <?php
                $result = $conn->query("SELECT * FROM events ORDER BY event_date DESC");
                if ($result->num_rows > 0):
                ?>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Event Name</th>
                                <th>Location</th>
                                <th>Date</th>
                                <th>Pricing</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($event = $result->fetch_assoc()): ?>
                            <tr>
                                <td>
                                    <strong><?php echo htmlspecialchars($event['event_name']); ?></strong>
                                    <?php if (!empty($event['event_remarks'])): ?>
                                        <br><small class="text-muted"><?php echo htmlspecialchars($event['event_remarks']); ?></small>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($event['event_location']); ?></td>
                                <td><?php echo date('M d, Y', strtotime($event['event_date'])); ?></td>
                                <td>₱<?php echo number_format($event['pricing'], 2); ?></td>
                                <td>
                                    <a href="edit_event.php?id=<?php echo $event['id']; ?>" class="btn btn-sm btn-primary">
                                        <i class="bi bi-pencil"></i> Edit
                                    </a>
                                    <a href="?delete=<?php echo $event['id']; ?>" 
                                       class="btn btn-sm btn-danger"
                                       onclick="return confirm('Are you sure you want to delete this event?')">
                                        <i class="bi bi-trash"></i> Delete
                                    </a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                    <div class="text-center py-4">
                        <i class="bi bi-calendar-x display-4 text-muted"></i>
                        <h4 class="text-muted">No Events Found</h4>
                        <p class="text-muted">Add your first event using the form above.</p>
                    </div>
                <?php endif; ?>
                
                <?php
                if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
                    $event_id = (int)$_GET['delete'];
                    $stmt = $conn->prepare("DELETE FROM events WHERE id = ?");
                    $stmt->bind_param("i", $event_id);
                    
                    if ($stmt->execute()) {
                        echo "<script>alert('Event deleted successfully!'); window.location.href='events.php';</script>";
                    } else {
                        echo "<script>alert('Error deleting event!');</script>";
                    }
                    $stmt->close();
                }
                ?>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>