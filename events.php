<?php
session_start();
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Events Management</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="bi bi-calendar-event"></i> Events Monitor
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="index.php">
                    <i class="bi bi-house"></i> Home
                </a>
                <a class="nav-link active" href="events.php">
                    <i class="bi bi-list"></i> All Events
                </a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h1 class="mb-4">
            <i class="bi bi-calendar-event"></i> Events Management
        </h1>

        <!-- Add Event Form -->
        <div class="card mb-4">
            <div class="card-header bg-success text-white">
                <h5 class="card-title mb-0">
                    <i class="bi bi-plus-circle"></i> Add New Event
                </h5>
            </div>
            <div class="card-body">
                <?php
                // Handle form submission
                if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_event'])) {
                    $event_name = mysqli_real_escape_string($conn, $_POST['event_name']);
                    $event_location = mysqli_real_escape_string($conn, $_POST['event_location']);
                    $event_date = mysqli_real_escape_string($conn, $_POST['event_date']);
                    $event_remarks = mysqli_real_escape_string($conn, $_POST['event_remarks']);
                    $pricing = (float)$_POST['pricing'];
                    
                    $stmt = $conn->prepare("INSERT INTO events (event_name, event_location, event_date, event_remarks, pricing) VALUES (?, ?, ?, ?, ?)");
                    $stmt->bind_param("ssssd", $event_name, $event_location, $event_date, $event_remarks, $pricing);
                    
                    if ($stmt->execute()) {
                        echo "<div class='alert alert-success'>Event added successfully!</div>";
                    } else {
                        echo "<div class='alert alert-danger'>Error adding event: " . $conn->error . "</div>";
                    }
                    $stmt->close();
                }
                
                // Handle delete
                if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
                    $event_id = (int)$_GET['delete'];
                    $stmt = $conn->prepare("DELETE FROM events WHERE id = ?");
                    $stmt->bind_param("i", $event_id);
                    
                    if ($stmt->execute()) {
                        echo "<div class='alert alert-success'>Event deleted successfully!</div>";
                    } else {
                        echo "<div class='alert alert-danger'>Error deleting event</div>";
                    }
                    $stmt->close();
                }
                ?>
                
                <form method="POST" action="">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="event_name" class="form-label">Event Name *</label>
                            <input type="text" class="form-control" id="event_name" name="event_name" required maxlength="255">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="event_location" class="form-label">Event Location *</label>
                            <input type="text" class="form-control" id="event_location" name="event_location" required maxlength="255">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="event_date" class="form-label">Event Date *</label>
                            <input type="date" class="form-control" id="event_date" name="event_date" required min="<?php echo date('Y-m-d'); ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="pricing" class="form-label">Pricing (₱) *</label>
                            <input type="number" class="form-control" id="pricing" name="pricing" step="0.01" min="0" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="event_remarks" class="form-label">Event Remarks</label>
                        <textarea class="form-control" id="event_remarks" name="event_remarks" rows="3" placeholder="Optional remarks about the event"></textarea>
                    </div>
                    
                    <button type="submit" name="add_event" class="btn btn-success">
                        <i class="bi bi-plus-circle"></i> Add Event
                    </button>
                </form>
            </div>
        </div>

        <!-- Events List -->
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0">
                    <i class="bi bi-list"></i> All Events
                </h5>
            </div>
            <div class="card-body">
                <?php
                $query = "SELECT * FROM events ORDER BY event_date DESC";
                $result = $conn->query($query);
                
                if ($result->num_rows > 0):
                ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>Event Name</th>
                                <th>Location</th>
                                <th>Date</th>
                                <th>Pricing</th>
                                <th>Date Added</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($event = $result->fetch_assoc()): ?>
                                <tr>
                                    <td>
                                        <strong><?php echo htmlspecialchars($event['event_name']); ?></strong>
                                        <?php if (!empty($event['event_remarks'])): ?>
                                            <br><small class="text-muted"><?php echo htmlspecialchars(substr($event['event_remarks'], 0, 50)); ?><?php echo strlen($event['event_remarks']) > 50 ? '...' : ''; ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <i class="bi bi-geo-alt text-primary"></i>
                                        <?php echo htmlspecialchars($event['event_location']); ?>
                                    </td>
                                    <td>
                                        <i class="bi bi-calendar text-info"></i>
                                        <?php echo date('M d, Y', strtotime($event['event_date'])); ?>
                                        <?php if (strtotime($event['event_date']) < strtotime('today')): ?>
                                            <br><small class="badge bg-secondary">Past</small>
                                        <?php elseif (strtotime($event['event_date']) == strtotime('today')): ?>
                                            <br><small class="badge bg-warning">Today</small>
                                        <?php else: ?>
                                            <br><small class="badge bg-success">Upcoming</small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <strong class="text-success">₱<?php echo number_format($event['pricing'], 2); ?></strong>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            <?php echo date('M d, Y', strtotime($event['date_added'])); ?>
                                        </small>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="edit_event.php?id=<?php echo $event['id']; ?>" class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-pencil"></i> Edit
                                            </a>
                                            <a href="?delete=<?php echo $event['id']; ?>" 
                                               class="btn btn-sm btn-outline-danger" 
                                               onclick="return confirm('Are you sure you want to delete this event?')">
                                                <i class="bi bi-trash"></i> Delete
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="bi bi-calendar-x display-1 text-muted"></i>
                        <h4 class="text-muted">No Events Found</h4>
                        <p class="text-muted">Start by adding your first event using the form above.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <footer class="bg-light mt-5 py-4">
        <div class="container text-center">
            <p class="text-muted mb-0">&copy; 2025 Events Monitoring System. All rights reserved.</p>
        </div>
    </footer>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="js/script.js"></script>
</body>
</html>
