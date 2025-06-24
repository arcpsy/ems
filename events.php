<?php

session_start();
require_once 'config.php';

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
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Events Management</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <style>
        /* Card View Styles */
        .event-card {
            transition: all 0.3s ease;
            border: none;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .event-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        .event-status-past { border-left: 4px solid #6c757d; }
        .event-status-today { border-left: 4px solid #ffc107; }
        .event-status-upcoming { border-left: 4px solid #198754; }
        .pricing-badge {
            font-size: 1.2rem;
            font-weight: 600;
        }
        .event-date-large {
            font-size: 2rem;
            font-weight: bold;
            line-height: 1;
        }
        .event-month {
            font-size: 0.8rem;
            text-transform: uppercase;
            font-weight: 600;
        }

        /* List View Styles */
        .list-view {
            display: none;
        }
        .list-view.active {
            display: block;
        }
        .grid-view.active {
            display: block;
        }
        .grid-view {
            display: none;
        }

        .event-list-item {
<<<<<<< HEAD
            background: linear-gradient(135deg, #fff 0%, #black 100%);
=======
            background: linear-gradient(135deg, #fff 0%, #f8f9fa 100%);
>>>>>>> cb7c2b243afb3c0a9a74457ddd7bad723dcffa10
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid rgba(0,0,0,0.05);
            position: relative;
            overflow: hidden;
        }

        .event-list-item:hover {
            transform: translateX(10px);
            box-shadow: 0 8px 30px rgba(0,0,0,0.12);
        }

        .event-list-item::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 6px;
            transition: all 0.3s ease;
        }

        .event-list-item.status-upcoming::before {
            background: linear-gradient(180deg, #28a745 0%, #20c997 100%);
        }
        .event-list-item.status-today::before {
            background: linear-gradient(180deg, #ffc107 0%, #fd7e14 100%);
        }
        .event-list-item.status-past::before {
            background: linear-gradient(180deg, #6c757d 0%, #495057 100%);
        }

        .event-date-circle {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: white;
            position: relative;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }

        .event-date-circle.upcoming {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        }
        .event-date-circle.today {
            background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);
        }
        .event-date-circle.past {
            background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
        }

        .event-date-day {
            font-size: 1.8rem;
            line-height: 1;
        }
        .event-date-month {
            font-size: 0.7rem;
            text-transform: uppercase;
            opacity: 0.9;
        }

        .event-content {
            flex: 1;
            min-width: 0;
        }

        .event-title {
            font-size: 1.4rem;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 0.5rem;
            line-height: 1.2;
        }

        .event-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            margin-bottom: 0.75rem;
        }

        .event-meta-item {
            display: flex;
            align-items: center;
            gap: 0.4rem;
            color: #6c757d;
            font-size: 0.9rem;
        }

        .event-meta-item i {
            color: #007bff;
            width: 16px;
        }

        .event-description {
            color: #6c757d;
            font-size: 0.9rem;
            line-height: 1.4;
            margin-bottom: 1rem;
        }

        .event-actions {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .price-tag {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 25px;
            font-weight: 600;
            font-size: 1.1rem;
            box-shadow: 0 2px 10px rgba(40, 167, 69, 0.3);
            white-space: nowrap;
        }

        .status-badge {
            position: absolute;
            top: 1rem;
            right: 1rem;
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-badge.upcoming {
            background: rgba(40, 167, 69, 0.1);
            color: #28a745;
            border: 1px solid rgba(40, 167, 69, 0.2);
        }
        .status-badge.today {
            background: rgba(255, 193, 7, 0.1);
            color: #ffc107;
            border: 1px solid rgba(255, 193, 7, 0.2);
        }
        .status-badge.past {
            background: rgba(108, 117, 125, 0.1);
            color: #6c757d;
            border: 1px solid rgba(108, 117, 125, 0.2);
        }

        .view-toggle {
            background: white;
            border: 2px solid #007bff;
            border-radius: 10px;
            overflow: hidden;
        }

        .view-toggle .btn {
            border: none;
            border-radius: 0;
            background: transparent;
            color: #007bff;
            transition: all 0.3s ease;
        }

        .view-toggle .btn.active {
            background: #007bff;
            color: white;
        }

        .view-toggle .btn:hover {
            background: rgba(0, 123, 255, 0.1);
        }

        .view-toggle .btn.active:hover {
            background: #0056b3;
        }

        @media (max-width: 768px) {
            .event-list-item {
                flex-direction: column;
                text-align: center;
            }
            
            .event-date-circle {
                width: 60px;
                height: 60px;
                margin-bottom: 1rem;
            }
            
            .event-date-day {
                font-size: 1.4rem;
            }
            
            .status-badge {
                position: static;
                margin-bottom: 1rem;
            }
        }

        /* Character Counter Styles */
        #charCounter {
            font-size: 0.875rem;
            font-weight: 500;
            transition: color 0.3s ease;
        }
        
        #charCounter.text-warning {
            color: #fd7e14 !important;
            font-weight: 600;
        }
        
        #charCounter.text-muted {
            color: #6c757d !important;
        }
    </style>
</head>
<body class="d-flex flex-column min-vh-100">
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="bi bi-collection"></i> GalaGo Events
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">
                            <i class="bi bi-house"></i> Home
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="events.php">
                            <i class="bi bi-list"></i> All Events
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

<<<<<<< HEAD
    <div class="container mt-4 flex-grow-1">
=======
    <div class="container mt-4">
>>>>>>> cb7c2b243afb3c0a9a74457ddd7bad723dcffa10
        <!-- Success/Error Messages -->
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle"></i> <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle"></i> <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

<<<<<<< HEAD
       <h1 class="mb-4">
            <i class="bi bi-clipboard-data" style="color: #818589;"></i> Events Management
=======
        <h1 class="mb-4">
            <i class="bi bi-calendar-event"></i> Events Management
>>>>>>> cb7c2b243afb3c0a9a74457ddd7bad723dcffa10
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
                <div class="grid-view active" id="gridView">
                    <div class="row">
                        <?php while($event = $result->fetch_assoc()): 
                            // Determine event status
                            $event_timestamp = strtotime($event['event_date']);
                            $today_timestamp = strtotime('today');
                            
                            if ($event_timestamp < $today_timestamp) {
                                $status = 'past';
                                $status_class = 'event-status-past';
                                $status_badge = 'bg-secondary';
                                $status_text = 'Past';
                            } elseif ($event_timestamp == $today_timestamp) {
                                $status = 'today';
                                $status_class = 'event-status-today';
                                $status_badge = 'bg-warning';
                                $status_text = 'Today';
                            } else {
                                $status = 'upcoming';
                                $status_class = 'event-status-upcoming';
                                $status_badge = 'bg-success';
                                $status_text = 'Upcoming';
                            }
                        ?>
                        <div class="col-lg-6 col-xl-4 mb-4">
                            <div class="card event-card <?php echo $status_class; ?>">
                                <div class="card-body">
                                    <!-- Event Date Display -->
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <div class="text-center">
                                            <div class="event-date-large text-primary">
                                                <?php echo date('d', strtotime($event['event_date'])); ?>
                                            </div>
                                            <div class="event-month text-muted">
                                                <?php echo date('M Y', strtotime($event['event_date'])); ?>
                                            </div>
                                        </div>
                                        <span class="badge <?php echo $status_badge; ?> fs-6">
                                            <?php echo $status_text; ?>
                                        </span>
                                    </div>

                                    <!-- Event Details -->
                                    <h5 class="card-title text-dark mb-2">
                                        <?php echo htmlspecialchars($event['event_name']); ?>
                                    </h5>
                                    
                                    <div class="mb-2">
                                        <i class="bi bi-geo-alt text-primary"></i>
                                        <span class="text-muted"><?php echo htmlspecialchars($event['event_location']); ?></span>
                                    </div>

                                    <div class="mb-3">
                                        <i class="bi bi-calendar text-info"></i>
                                        <span class="text-muted"><?php echo date('l, F d, Y', strtotime($event['event_date'])); ?></span>
                                    </div>

                                    <?php if (!empty($event['event_remarks'])): ?>
                                    <div class="mb-3">
                                        <small class="text-muted">
                                            <i class="bi bi-chat-text"></i>
                                            <?php echo htmlspecialchars(substr($event['event_remarks'], 0, 80)); ?>
                                            <?php echo strlen($event['event_remarks']) > 80 ? '...' : ''; ?>
                                        </small>
                                    </div>
                                    <?php endif; ?>

                                    <!-- Pricing -->
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <span class="pricing-badge text-success">
                                            ₱<?php echo number_format($event['pricing'], 2); ?>
                                        </span>
                                        <small class="text-muted">
                                            Added: <?php echo date('M d', strtotime($event['date_added'])); ?>
                                        </small>
                                    </div>

                                    <!-- Action Buttons -->
                                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                        <button type="button" class="btn btn-outline-info btn-sm" 
                                                data-bs-toggle="modal" data-bs-target="#viewModal<?php echo $event['id']; ?>">
                                            <i class="bi bi-eye"></i> View
                                        </button>
                                        <a href="edit_event.php?id=<?php echo $event['id']; ?>" class="btn btn-outline-primary btn-sm">
                                            <i class="bi bi-pencil"></i> Edit
                                        </a>
                                        <button type="button" class="btn btn-outline-danger btn-sm" 
                                                data-bs-toggle="modal" data-bs-target="#deleteModal<?php echo $event['id']; ?>">
                                            <i class="bi bi-trash"></i> Delete
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endwhile; ?>
                    </div>
                </div>

                <!-- List View -->
                <div class="list-view" id="listView">
                    <div class="row">
                        <div class="col-12">
                            <?php 
                            $result->data_seek(0); // Reset result pointer
                            while($event = $result->fetch_assoc()): 
                                // Determine event status
                                $event_timestamp = strtotime($event['event_date']);
                                $today_timestamp = strtotime('today');
                                
                                if ($event_timestamp < $today_timestamp) {
                                    $status = 'past';
                                    $circle_class = 'past';
                                    $badge_class = 'past';
                                    $status_text = 'Past Event';
                                } elseif ($event_timestamp == $today_timestamp) {
                                    $status = 'today';
                                    $circle_class = 'today';
                                    $badge_class = 'today';
                                    $status_text = 'Today';
                                } else {
                                    $status = 'upcoming';
                                    $circle_class = 'upcoming';
                                    $badge_class = 'upcoming';
                                    $status_text = 'Upcoming';
                                }
                            ?>
                            <div class="event-list-item status-<?php echo $status; ?> p-4 mb-4 d-flex align-items-center position-relative">
                                <!-- Status Badge -->
                                <div class="status-badge <?php echo $badge_class; ?>">
                                    <?php echo $status_text; ?>
                                </div>

                                <!-- Date Circle -->
                                <div class="event-date-circle <?php echo $circle_class; ?> me-4 flex-shrink-0">
                                    <div class="event-date-day"><?php echo date('d', strtotime($event['event_date'])); ?></div>
                                    <div class="event-date-month"><?php echo date('M', strtotime($event['event_date'])); ?></div>
                                </div>

                                <!-- Event Content -->
                                <div class="event-content me-4">
                                    <h4 class="event-title"><?php echo htmlspecialchars($event['event_name']); ?></h4>
                                    
                                    <div class="event-meta">
                                        <div class="event-meta-item">
                                            <i class="bi bi-geo-alt"></i>
                                            <span><?php echo htmlspecialchars($event['event_location']); ?></span>
                                        </div>
                                        <div class="event-meta-item">
                                            <i class="bi bi-calendar"></i>
                                            <span><?php echo date('l, F d, Y', strtotime($event['event_date'])); ?></span>
                                        </div>
                                        <div class="event-meta-item">
                                            <i class="bi bi-clock"></i>
                                            <span>Added <?php echo date('M d, Y', strtotime($event['date_added'])); ?></span>
                                        </div>
                                    </div>

                                    <?php if (!empty($event['event_remarks'])): ?>
                                    <div class="event-description">
                                        <i class="bi bi-chat-text me-2"></i>
                                        <?php echo htmlspecialchars(substr($event['event_remarks'], 0, 120)); ?>
                                        <?php echo strlen($event['event_remarks']) > 120 ? '...' : ''; ?>
                                    </div>
                                    <?php endif; ?>

                                    <div class="event-actions">
                                        <button type="button" class="btn btn-outline-info btn-sm" 
                                                data-bs-toggle="modal" data-bs-target="#viewModal<?php echo $event['id']; ?>">
                                            <i class="bi bi-eye"></i> View Details
                                        </button>
                                        <a href="edit_event.php?id=<?php echo $event['id']; ?>" class="btn btn-outline-primary btn-sm">
                                            <i class="bi bi-pencil"></i> Edit
                                        </a>
                                        <button type="button" class="btn btn-outline-danger btn-sm" 
                                                data-bs-toggle="modal" data-bs-target="#deleteModal<?php echo $event['id']; ?>">
                                            <i class="bi bi-trash"></i> Delete
                                        </button>
                                    </div>
                                </div>

                                <!-- Price Tag -->
                                <div class="price-tag flex-shrink-0">
                                    ₱<?php echo number_format($event['pricing'], 2); ?>
                                </div>
                            </div>
                            <?php endwhile; ?>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="bi bi-calendar-x display-1 text-muted"></i>
                    <h4 class="text-muted">No Events Found</h4>
                    <p class="text-muted">Start by adding your first event using the button above.</p>
                </div>
            <?php endif; ?>
        </section>
    </div>


 <!-- Add Event Modal | Laynes -->
 <!-- Add Event Modal | Laynes -->
 <!-- Add Event Modal | Laynes -->



    <div class="modal fade" id="addEventModal" tabindex="-1" aria-labelledby="addEventModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="addEventModalLabel">
                    <i class="bi bi-plus-circle"></i> Add New Event
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
<<<<<<< HEAD
            <div class="modal-body">
                <div class="card mb-0">
                    <!-- Removed duplicate card-header here -->
                    <div class="card-body">
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
                                <textarea class="form-control" id="event_remarks" name="event_remarks" rows="3" placeholder="Optional remarks about the event" maxlength="1000"></textarea>
                                <div class="form-text">
                                    <span id="charCounter" class="text-muted">1000 characters remaining</span>
                                </div>
                            </div>
                            <button type="submit" name="add_event" class="btn btn-success">
                                <i class="bi bi-plus-circle"></i> Add Event
                            </button>
                        </form>
                    </div>
                </div>
            </div> <!-- end modal-body -->
        </div> <!-- end modal-content -->
    </div> <!-- end modal-dialog -->
</div> <!-- end modal -->
    <!-- Modals -->
 <!-- end modal | Laynes -->



    <?php 
    $result->data_seek(0); // Reset result pointer
    while($event = $result->fetch_assoc()): 
    ?>
    
    <!-- View Modal -->
    <div class="modal fade" id="viewModal<?php echo $event['id']; ?>" tabindex="-1" aria-labelledby="viewModalLabel<?php echo $event['id']; ?>" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title" id="viewModalLabel<?php echo $event['id']; ?>">
                        <i class="bi bi-eye"></i> Event Details
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6><i class="bi bi-calendar-event text-primary"></i> Event Name:</h6>
                            <p class="fs-5 fw-bold"><?php echo htmlspecialchars($event['event_name']); ?></p>
                            
                            <h6><i class="bi bi-geo-alt text-primary"></i> Location:</h6>
                            <p><?php echo htmlspecialchars($event['event_location']); ?></p>
                            
                            <h6><i class="bi bi-calendar text-primary"></i> Event Date:</h6>
                            <p><?php echo date('F d, Y (l)', strtotime($event['event_date'])); ?></p>
                        </div>
                        <div class="col-md-6">
                            <h6><i class="bi bi-cash text-success"></i> Pricing:</h6>
                            <p class="h4 text-success">₱<?php echo number_format($event['pricing'], 2); ?></p>
                            
                            <h6><i class="bi bi-clock text-muted"></i> Date Added:</h6>
                            <p><?php echo date('F d, Y \a\t g:i A', strtotime($event['date_added'])); ?></p>
                            
                            <h6><i class="bi bi-info-circle text-warning"></i> Status:</h6>
                            <p>
                                <?php if (strtotime($event['event_date']) < strtotime('today')): ?>
                                    <span class="badge bg-secondary fs-6">Past Event</span>
                                <?php elseif (strtotime($event['event_date']) == strtotime('today')): ?>
                                    <span class="badge bg-warning fs-6">Today</span>
                                <?php else: ?>
                                    <span class="badge bg-success fs-6">Upcoming</span>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
=======
            <div class="card-body">
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
                        <textarea class="form-control" id="event_remarks" name="event_remarks" rows="3" placeholder="Optional remarks about the event" maxlength="1000"></textarea>
                        <div class="form-text">
                            <span id="charCounter" class="text-muted">1000 characters remaining</span>
                        </div>
                    </div>
                    
                    <button type="submit" name="add_event" class="btn btn-success">
                        <i class="bi bi-plus-circle"></i> Add Event
                    </button>
                </form>
            </div>
        </div>

        <!-- Events Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="mb-0">
                <i class="bi bi-calendar-check"></i> All Events (<?php echo $result->num_rows; ?>)
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
            <div class="grid-view active" id="gridView">
                <div class="row">
                    <?php while($event = $result->fetch_assoc()): 
                        // Determine event status
                        $event_timestamp = strtotime($event['event_date']);
                        $today_timestamp = strtotime('today');
                        
                        if ($event_timestamp < $today_timestamp) {
                            $status = 'past';
                            $status_class = 'event-status-past';
                            $status_badge = 'bg-secondary';
                            $status_text = 'Past';
                        } elseif ($event_timestamp == $today_timestamp) {
                            $status = 'today';
                            $status_class = 'event-status-today';
                            $status_badge = 'bg-warning';
                            $status_text = 'Today';
                        } else {
                            $status = 'upcoming';
                            $status_class = 'event-status-upcoming';
                            $status_badge = 'bg-success';
                            $status_text = 'Upcoming';
                        }
                    ?>
                    <div class="col-lg-6 col-xl-4 mb-4">
                        <div class="card event-card <?php echo $status_class; ?>">
                            <div class="card-body">
                                <!-- Event Date Display -->
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div class="text-center">
                                        <div class="event-date-large text-primary">
                                            <?php echo date('d', strtotime($event['event_date'])); ?>
                                        </div>
                                        <div class="event-month text-muted">
                                            <?php echo date('M Y', strtotime($event['event_date'])); ?>
                                        </div>
                                    </div>
                                    <span class="badge <?php echo $status_badge; ?> fs-6">
                                        <?php echo $status_text; ?>
                                    </span>
                                </div>

                                <!-- Event Details -->
                                <h5 class="card-title text-dark mb-2">
                                    <?php echo htmlspecialchars($event['event_name']); ?>
                                </h5>
                                
                                <div class="mb-2">
                                    <i class="bi bi-geo-alt text-primary"></i>
                                    <span class="text-muted"><?php echo htmlspecialchars($event['event_location']); ?></span>
                                </div>

                                <div class="mb-3">
                                    <i class="bi bi-calendar text-info"></i>
                                    <span class="text-muted"><?php echo date('l, F d, Y', strtotime($event['event_date'])); ?></span>
                                </div>

                                <?php if (!empty($event['event_remarks'])): ?>
                                <div class="mb-3">
                                    <small class="text-muted">
                                        <i class="bi bi-chat-text"></i>
                                        <?php echo htmlspecialchars(substr($event['event_remarks'], 0, 80)); ?>
                                        <?php echo strlen($event['event_remarks']) > 80 ? '...' : ''; ?>
                                    </small>
                                </div>
                                <?php endif; ?>

                                <!-- Pricing -->
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span class="pricing-badge text-success">
                                        ₱<?php echo number_format($event['pricing'], 2); ?>
                                    </span>
                                    <small class="text-muted">
                                        Added: <?php echo date('M d', strtotime($event['date_added'])); ?>
                                    </small>
                                </div>

                                <!-- Action Buttons -->
                                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                    <button type="button" class="btn btn-outline-info btn-sm" 
                                            data-bs-toggle="modal" data-bs-target="#viewModal<?php echo $event['id']; ?>">
                                        <i class="bi bi-eye"></i> View
                                    </button>
                                    <a href="edit_event.php?id=<?php echo $event['id']; ?>" class="btn btn-outline-primary btn-sm">
                                        <i class="bi bi-pencil"></i> Edit
                                    </a>
                                    <button type="button" class="btn btn-outline-danger btn-sm" 
                                            data-bs-toggle="modal" data-bs-target="#deleteModal<?php echo $event['id']; ?>">
                                        <i class="bi bi-trash"></i> Delete
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
            </div>

            <!-- List View -->
            <div class="list-view" id="listView">
                <div class="row">
                    <div class="col-12">
                        <?php 
                        $result->data_seek(0); // Reset result pointer
                        while($event = $result->fetch_assoc()): 
                            // Determine event status
                            $event_timestamp = strtotime($event['event_date']);
                            $today_timestamp = strtotime('today');
                            
                            if ($event_timestamp < $today_timestamp) {
                                $status = 'past';
                                $circle_class = 'past';
                                $badge_class = 'past';
                                $status_text = 'Past Event';
                            } elseif ($event_timestamp == $today_timestamp) {
                                $status = 'today';
                                $circle_class = 'today';
                                $badge_class = 'today';
                                $status_text = 'Today';
                            } else {
                                $status = 'upcoming';
                                $circle_class = 'upcoming';
                                $badge_class = 'upcoming';
                                $status_text = 'Upcoming';
                            }
                        ?>
                        <div class="event-list-item status-<?php echo $status; ?> p-4 mb-4 d-flex align-items-center position-relative">
                            <!-- Status Badge -->
                            <div class="status-badge <?php echo $badge_class; ?>">
                                <?php echo $status_text; ?>
                            </div>

                            <!-- Date Circle -->
                            <div class="event-date-circle <?php echo $circle_class; ?> me-4 flex-shrink-0">
                                <div class="event-date-day"><?php echo date('d', strtotime($event['event_date'])); ?></div>
                                <div class="event-date-month"><?php echo date('M', strtotime($event['event_date'])); ?></div>
                            </div>

                            <!-- Event Content -->
                            <div class="event-content me-4">
                                <h4 class="event-title"><?php echo htmlspecialchars($event['event_name']); ?></h4>
                                
                                <div class="event-meta">
                                    <div class="event-meta-item">
                                        <i class="bi bi-geo-alt"></i>
                                        <span><?php echo htmlspecialchars($event['event_location']); ?></span>
                                    </div>
                                    <div class="event-meta-item">
                                        <i class="bi bi-calendar"></i>
                                        <span><?php echo date('l, F d, Y', strtotime($event['event_date'])); ?></span>
                                    </div>
                                    <div class="event-meta-item">
                                        <i class="bi bi-clock"></i>
                                        <span>Added <?php echo date('M d, Y', strtotime($event['date_added'])); ?></span>
                                    </div>
                                </div>

                                <?php if (!empty($event['event_remarks'])): ?>
                                <div class="event-description">
                                    <i class="bi bi-chat-text me-2"></i>
                                    <?php echo htmlspecialchars(substr($event['event_remarks'], 0, 120)); ?>
                                    <?php echo strlen($event['event_remarks']) > 120 ? '...' : ''; ?>
                                </div>
                                <?php endif; ?>

                                <div class="event-actions">
                                    <button type="button" class="btn btn-outline-info btn-sm" 
                                            data-bs-toggle="modal" data-bs-target="#viewModal<?php echo $event['id']; ?>">
                                        <i class="bi bi-eye"></i> View Details
                                    </button>
                                    <a href="edit_event.php?id=<?php echo $event['id']; ?>" class="btn btn-outline-primary btn-sm">
                                        <i class="bi bi-pencil"></i> Edit
                                    </a>
                                    <button type="button" class="btn btn-outline-danger btn-sm" 
                                            data-bs-toggle="modal" data-bs-target="#deleteModal<?php echo $event['id']; ?>">
                                        <i class="bi bi-trash"></i> Delete
                                    </button>
                                </div>
                            </div>

                            <!-- Price Tag -->
                            <div class="price-tag flex-shrink-0">
                                ₱<?php echo number_format($event['pricing'], 2); ?>
                            </div>
                        </div>
                        <?php endwhile; ?>
                    </div>
                </div>
            </div>

        <?php else: ?>
            <div class="text-center py-5">
                <i class="bi bi-calendar-x display-1 text-muted"></i>
                <h4 class="text-muted">No Events Found</h4>
                <p class="text-muted">Start by adding your first event using the form above.</p>
            </div>
        <?php endif; ?>
    </div>

    <!-- Modals -->
    <?php 
    $result->data_seek(0); // Reset result pointer
    while($event = $result->fetch_assoc()): 
    ?>
    
    <!-- View Modal -->
    <div class="modal fade" id="viewModal<?php echo $event['id']; ?>" tabindex="-1" aria-labelledby="viewModalLabel<?php echo $event['id']; ?>" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title" id="viewModalLabel<?php echo $event['id']; ?>">
                        <i class="bi bi-eye"></i> Event Details
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6><i class="bi bi-calendar-event text-primary"></i> Event Name:</h6>
                            <p class="fs-5 fw-bold"><?php echo htmlspecialchars($event['event_name']); ?></p>
                            
                            <h6><i class="bi bi-geo-alt text-primary"></i> Location:</h6>
                            <p><?php echo htmlspecialchars($event['event_location']); ?></p>
                            
                            <h6><i class="bi bi-calendar text-primary"></i> Event Date:</h6>
                            <p><?php echo date('F d, Y (l)', strtotime($event['event_date'])); ?></p>
                        </div>
                        <div class="col-md-6">
                            <h6><i class="bi bi-cash text-success"></i> Pricing:</h6>
                            <p class="h4 text-success">₱<?php echo number_format($event['pricing'], 2); ?></p>
                            
                            <h6><i class="bi bi-clock text-muted"></i> Date Added:</h6>
                            <p><?php echo date('F d, Y \a\t g:i A', strtotime($event['date_added'])); ?></p>
                            
                            <h6><i class="bi bi-info-circle text-warning"></i> Status:</h6>
                            <p>
                                <?php if (strtotime($event['event_date']) < strtotime('today')): ?>
                                    <span class="badge bg-secondary fs-6">Past Event</span>
                                <?php elseif (strtotime($event['event_date']) == strtotime('today')): ?>
                                    <span class="badge bg-warning fs-6">Today</span>
                                <?php else: ?>
                                    <span class="badge bg-success fs-6">Upcoming</span>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
>>>>>>> cb7c2b243afb3c0a9a74457ddd7bad723dcffa10
                    
                    <?php if (!empty($event['event_remarks'])): ?>
                    <div class="row">
                        <div class="col-12">
                            <h6><i class="bi bi-chat-text text-info"></i> Event Remarks:</h6>
                            <div class="bg-light p-3 rounded">
                                <?php echo nl2br(htmlspecialchars($event['event_remarks'])); ?>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i> Close
                    </button>
                    <a href="edit_event.php?id=<?php echo $event['id']; ?>" class="btn btn-primary">
                        <i class="bi bi-pencil"></i> Edit Event
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Modal -->
    <div class="modal fade" id="deleteModal<?php echo $event['id']; ?>" tabindex="-1" aria-labelledby="deleteModalLabel<?php echo $event['id']; ?>" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="deleteModalLabel<?php echo $event['id']; ?>">
                        <i class="bi bi-exclamation-triangle"></i> Confirm Delete
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <i class="bi bi-trash display-1 text-danger mb-3"></i>
                    <h5>Are you sure you want to delete this event?</h5>
                    <p class="text-muted">
                        <strong>"<?php echo htmlspecialchars($event['event_name']); ?>"</strong><br>
                        This action cannot be undone.
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i> Cancel
                    </button>
                    <form method="POST" action="" style="display: inline;">
                        <input type="hidden" name="event_id" value="<?php echo $event['id']; ?>">
                        <button type="submit" name="delete_event" class="btn btn-danger">
                            <i class="bi bi-trash"></i> Delete Event
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php endwhile; ?>

<<<<<<< HEAD
    <footer class="bg-light mt-auto py-4">
=======
    <footer class="bg-light mt-5 py-4">
>>>>>>> cb7c2b243afb3c0a9a74457ddd7bad723dcffa10
        <div class="container text-center">
            <p class="text-muted mb-0">&copy; GalaGo Events Monitoring System. All rights reserved.</p>
        </div>
    </footer>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="js/script.js"></script>
<<<<<<< HEAD
=======
    
>>>>>>> cb7c2b243afb3c0a9a74457ddd7bad723dcffa10
    <script>
        function toggleView(viewType) {
            const gridView = document.getElementById('gridView');
            const listView = document.getElementById('listView');
            const gridBtn = document.getElementById('gridBtn');
            const listBtn = document.getElementById('listBtn');
            
            if (viewType === 'list') {
                gridView.classList.remove('active');
                listView.classList.add('active');
                gridBtn.classList.remove('active');
                listBtn.classList.add('active');
            } else {
                listView.classList.remove('active');
                gridView.classList.add('active');
                listBtn.classList.remove('active');
                gridBtn.classList.add('active');
            }
        }

        // Character counter for event remarks
        document.addEventListener('DOMContentLoaded', function() {
            const remarksTextarea = document.getElementById('event_remarks');
            const charCounter = document.getElementById('charCounter');
            const maxLength = 1000;

<<<<<<< HEAD
            function updateCharCounter() {
                const currentLength = remarksTextarea.value.length;
                const remaining = Math.max(0, maxLength - currentLength);
                charCounter.textContent = `${remaining} characters remaining`;
=======
            // Remove any old character counters from script.js
            const oldCounters = remarksTextarea.parentNode.querySelectorAll('.char-counter');
            oldCounters.forEach(counter => counter.remove());

            function updateCharCounter() {
                const currentLength = remarksTextarea.value.length;
                const remaining = Math.max(0, maxLength - currentLength); // Never go below 0
                
                charCounter.textContent = `${remaining} characters remaining`;
                
                // Change color based on remaining characters
>>>>>>> cb7c2b243afb3c0a9a74457ddd7bad723dcffa10
                if (remaining <= 50) {
                    charCounter.className = 'text-warning';
                } else {
                    charCounter.className = 'text-muted';
                }
            }

<<<<<<< HEAD
            remarksTextarea.addEventListener('input', updateCharCounter);
=======
            // Update counter on input
            remarksTextarea.addEventListener('input', updateCharCounter);
            
            // Initial update
>>>>>>> cb7c2b243afb3c0a9a74457ddd7bad723dcffa10
            updateCharCounter();
        });
    </script>
</body>
</html>