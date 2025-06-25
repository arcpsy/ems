<?php
session_start();
require_once 'config.php';

if (!$conn) {
    die("Database connection failed!");
}

$tableCheck = $conn->query("SHOW TABLES LIKE 'events'");
if ($tableCheck->num_rows == 0) {
    die("Events table does not exist! Please run the database.sql file first.");
}

if (!function_exists('sanitize_input')) {
    function sanitize_input($data) {
        global $conn;
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return mysqli_real_escape_string($conn, $data);
    }
}

if (!function_exists('validate_date')) {
    function validate_date($date, $format = 'Y-m-d') {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_event'])) {
    error_log("POST request received for add_event");
    error_log("POST data: " . print_r($_POST, true));
    
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
            error_log("Event added successfully: " . $event_name);
        } else {
            $_SESSION['error'] = "Error adding event: " . $conn->error;
            error_log("Error adding event: " . $conn->error);
        }
        $stmt->close();
    } else {
        $_SESSION['error'] = implode(", ", $errors);
        error_log("Validation errors: " . implode(", ", $errors));
    }
    
    header("Location: events.php");
    exit();
}

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

$query = "SELECT * FROM events ORDER BY event_date DESC";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Events Management - GalaGo</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <style>
        .debug-info {
            background: rgba(255, 0, 0, 0.1);
            border: 1px solid rgba(255, 0, 0, 0.3);
            border-radius: 8px;
            padding: 1rem;
            margin: 1rem 0;
            color: white;
            font-family: monospace;
            font-size: 0.9rem;
        }
        
        .page-header {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 24px;
            padding: 2rem;
            margin-bottom: 2rem;
            position: relative;
            overflow: hidden;
        }

        .page-header::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(79, 172, 254, 0.1) 0%, transparent 70%);
            animation: rotate 15s linear infinite;
        }

        .stats-counter {
            font-size: 3rem;
            font-weight: 800;
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-shadow: 0 4px 8px rgba(79, 172, 254, 0.3);
        }

        .floating-stats {
            position: fixed;
            top: 50%;
            left: 2rem;
            transform: translateY(-50%);
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 16px;
            padding: 1rem;
            z-index: 1000;
            transition: all 0.3s ease;
        }

        .floating-stats:hover {
            transform: translateY(-50%) scale(1.05);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
        }

        .event-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 2rem;
            padding: 1rem 0;
        }

        .event-card {
            height: auto;
            min-height: 400px;
        }

        .event-card .card-body {
            padding: 2rem;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .event-card .d-flex.gap-2 {
            margin-top: auto;
        }

        .pricing-badge {
            background: var(--success-gradient);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: var(--radius-full);
            font-weight: 700;
            font-size: 1.1rem;
            box-shadow: 0 4px 15px rgba(79, 172, 254, 0.4);
            border: 1px solid rgba(255, 255, 255, 0.2);
            display: inline-block;
        }

        .no-events-state {
            text-align: center;
            padding: 4rem 2rem;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 24px;
            border: 2px dashed rgba(255, 255, 255, 0.3);
            margin: 2rem 0;
            backdrop-filter: blur(20px);
        }

        .no-events-state i {
            font-size: 4rem;
            background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 1rem;
            display: block;
        }

        .no-events-state h4 {
            color: white;
            margin-bottom: 1rem;
        }

        .no-events-state p {
            color: rgba(255, 255, 255, 0.75);
            margin-bottom: 2rem;
        }

        .search-box {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 50px;
            padding: 0.75rem 1.5rem;
            width: 100%;
            max-width: 400px;
            margin: 0 auto 2rem;
            color: white;
            transition: all 0.3s ease;
        }

        .search-box:focus {
            background: rgba(255, 255, 255, 0.15);
            border-color: rgba(79, 172, 254, 0.8);
            box-shadow: 0 0 0 3px rgba(79, 172, 254, 0.2);
            transform: scale(1.02);
        }

        .search-box::placeholder {
            color: rgba(255, 255, 255, 0.6);
        }

        .add-event-fab {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            border: none;
            color: white;
            font-size: 1.5rem;
            box-shadow: 0 8px 25px rgba(79, 172, 254, 0.4);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 1000;
            cursor: pointer;
        }

        .add-event-fab:hover {
            transform: scale(1.15) translateY(-5px);
            box-shadow: 0 15px 35px rgba(79, 172, 254, 0.6);
            animation: pulse 1s ease-in-out infinite;
        }

        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(79, 172, 254, 0.7); }
            70% { box-shadow: 0 0 0 20px rgba(79, 172, 254, 0); }
            100% { box-shadow: 0 0 0 0 rgba(79, 172, 254, 0); }
        }

        .loading-skeleton {
            background: linear-gradient(90deg, rgba(255,255,255,0.1) 25%, rgba(255,255,255,0.2) 50%, rgba(255,255,255,0.1) 75%);
            background-size: 200% 100%;
            animation: loading 1.5s infinite;
            border-radius: 8px;
            height: 1rem;
            margin: 0.5rem 0;
        }

        @keyframes loading {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }

        .event-category-tags {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
            margin-top: 1rem;
        }

        .category-tag {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 20px;
            padding: 0.25rem 0.75rem;
            font-size: 0.75rem;
            color: rgba(255, 255, 255, 0.8);
            transition: all 0.3s ease;
        }

        .category-tag:hover {
            background: rgba(79, 172, 254, 0.2);
            border-color: rgba(79, 172, 254, 0.4);
            transform: scale(1.05);
        }

        .success-notification {
            position: fixed;
            top: 2rem;
            right: 2rem;
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            color: white;
            padding: 1rem 1.5rem;
            border-radius: 12px;
            box-shadow: 0 8px 25px rgba(79, 172, 254, 0.4);
            z-index: 1100;
            transform: translateX(400px);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .success-notification.show {
            transform: translateX(0);
        }

        @media (max-width: 768px) {
            .floating-stats {
                display: none;
            }
            
            .event-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }
            
            .add-event-fab {
                bottom: 1rem;
                right: 1rem;
                width: 50px;
                height: 50px;
                font-size: 1.2rem;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand animate-fade-in" href="index.php">
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

    <div class="floating-stats d-none d-lg-block">
        <div class="text-center">
            <div class="stats-counter"><?php echo $result->num_rows; ?></div>
            <small class="text-white opacity-75">Total Events</small>
        </div>
    </div>

    <div class="container mt-4 animate-slide-up">
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

        <div class="debug-info">
            <strong>Debug Info:</strong><br>
            Database Connection: <?php echo $conn ? "✅ Connected" : "❌ Failed"; ?><br>
            Total Events in DB: <?php echo $result->num_rows; ?><br>
            POST Method: <?php echo $_SERVER['REQUEST_METHOD']; ?><br>
            POST Data: <?php echo !empty($_POST) ? "Present" : "None"; ?><br>
            <?php if (!empty($_POST)): ?>
                POST Keys: <?php echo implode(', ', array_keys($_POST)); ?><br>
            <?php endif; ?>
            Session Success: <?php echo isset($_SESSION['success']) ? $_SESSION['success'] : "None"; ?><br>
            Session Error: <?php echo isset($_SESSION['error']) ? $_SESSION['error'] : "None"; ?>
        </div>

        <div class="page-header animate-fade-in">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="mb-2 text-white">
                        <i class="bi bi-clipboard-data"></i> Events Management
                    </h1>
                    <p class="text-white opacity-75 mb-0">Manage and track all your company events efficiently</p>
                </div>
                <div class="col-md-4 text-md-end">
                    <div class="stats-counter"><?php echo $result->num_rows; ?></div>
                    <small class="text-white opacity-75">Events Tracked</small>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-6">
                <input type="text" class="search-box" placeholder="🔍 Search events..." id="eventSearch" onkeyup="filterEvents()">
            </div>
            <div class="col-md-6 d-flex justify-content-end align-items-center gap-3">
                <div class="view-toggle btn-group" role="group">
                    <button type="button" class="btn active" id="gridBtn" onclick="toggleView('grid')">
                        <i class="bi bi-grid-3x3-gap"></i> Grid
                    </button>
                    <button type="button" class="btn" id="listBtn" onclick="toggleView('list')">
                        <i class="bi bi-list-ul"></i> List
                    </button>
                </div>
                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addEventModal">
                    <i class="bi bi-plus-circle"></i> Add Event
                </button>
            </div>
        </div>

        <section>
            <?php if ($result->num_rows > 0): ?>
                <div class="grid-view active animate-fade-in" id="gridView">
                    <div class="event-grid">
                        <?php while($event = $result->fetch_assoc()): 
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
                        <div class="card event-card <?php echo $status_class; ?> animate-fade-in">
                            <div class="card-body position-relative">
                                <span class="badge <?php echo $status_badge; ?> position-absolute" style="top: 1rem; right: 1rem; z-index: 10;">
                                    <?php echo $status_text; ?>
                                </span>

                                <div class="d-flex justify-content-between align-items-start mb-3" style="padding-top: 0.5rem;">
                                    <div class="text-center">
                                        <div class="event-date-large text-white" style="font-size: 2.5rem; font-weight: 800;">
                                            <?php echo date('d', strtotime($event['event_date'])); ?>
                                        </div>
                                        <div class="event-month text-white opacity-75" style="font-size: 0.9rem; font-weight: 600;">
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
                                
                                <div class="event-meta-item mb-2">
                                    <i class="bi bi-geo-alt"></i>
                                    <span><?php echo htmlspecialchars($event['event_location']); ?></span>
                                </div>

                                <div class="event-meta-item mb-3">
                                    <i class="bi bi-calendar"></i>
                                    <span><?php echo date('l, F d, Y', strtotime($event['event_date'])); ?></span>
                                </div>

                                <?php if (!empty($event['event_remarks'])): ?>
                                <div class="mb-3">
                                    <small class="text-white opacity-75">
                                        <i class="bi bi-chat-text"></i>
                                        <?php echo htmlspecialchars(substr($event['event_remarks'], 0, 80)); ?>
                                        <?php echo strlen($event['event_remarks']) > 80 ? '...' : ''; ?>
                                    </small>
                                </div>
                                <?php endif; ?>

                                <div class="event-category-tags mb-3">
                                    <span class="category-tag"><?php echo ucfirst($status); ?></span>
                                    <span class="category-tag">
                                        <?php echo $event['pricing'] > 1000 ? 'Premium' : 'Standard'; ?>
                                    </span>
                                </div>

                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-outline-info btn-sm flex-fill" 
                                            data-bs-toggle="modal" data-bs-target="#viewModal<?php echo $event['id']; ?>">
                                        <i class="bi bi-eye"></i> View
                                    </button>
                                    <a href="edit_event.php?id=<?php echo $event['id']; ?>" class="btn btn-outline-primary btn-sm flex-fill">
                                        <i class="bi bi-pencil"></i> Edit
                                    </a>
                                    <button type="button" class="btn btn-outline-danger btn-sm flex-fill" 
                                            data-bs-toggle="modal" data-bs-target="#deleteModal<?php echo $event['id']; ?>">
                                        <i class="bi bi-trash"></i> Delete
                                    </button>
                                </div>
                            </div>
                        </div>
                        <?php endwhile; ?>
                    </div>
                </div>

                <div class="list-view" id="listView">
                    <div class="row">
                        <div class="col-12">
                            <?php 
                            $result->data_seek(0); 
                            while($event = $result->fetch_assoc()): 
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
                            <div class="event-list-item status-<?php echo $status; ?> p-4 mb-4 d-flex align-items-center position-relative animate-slide-up">
                                <div class="status-badge <?php echo $badge_class; ?>">
                                    <?php echo $status_text; ?>
                                </div>

                                <div class="event-date-circle <?php echo $circle_class; ?> me-4 flex-shrink-0">
                                    <div class="event-date-day"><?php echo date('d', strtotime($event['event_date'])); ?></div>
                                    <div class="event-date-month"><?php echo date('M', strtotime($event['event_date'])); ?></div>
                                </div>

                                <div class="event-content me-4 flex-grow-1">
                                    <h4 class="event-title"><?php echo htmlspecialchars($event['event_name']); ?></h4>
                                    
                                    <div class="d-flex flex-wrap gap-3 mb-2">
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
                                    <div class="text-white opacity-75 mb-3">
                                        <i class="bi bi-chat-text me-2"></i>
                                        <?php echo htmlspecialchars(substr($event['event_remarks'], 0, 120)); ?>
                                        <?php echo strlen($event['event_remarks']) > 120 ? '...' : ''; ?>
                                    </div>
                                    <?php endif; ?>

                                    <div class="d-flex gap-2">
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

                                <div class="price-tag flex-shrink-0">
                                    ₱<?php echo number_format($event['pricing'], 2); ?>
                                </div>
                            </div>
                            <?php endwhile; ?>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="no-events-state animate-fade-in">
                    <i class="bi bi-calendar-x"></i>
                    <h4 class="text-white mb-3">No Events Found</h4>
                    <p class="text-white opacity-75 mb-4">Start creating amazing events and watch your business grow!</p>
                    <button class="btn btn-success btn-lg" data-bs-toggle="modal" data-bs-target="#addEventModal">
                        <i class="bi bi-plus-circle"></i> Create Your First Event
                    </button>
                </div>
            <?php endif; ?>
        </section>
    </div>

    <button class="add-event-fab" data-bs-toggle="modal" data-bs-target="#addEventModal" title="Add New Event">
        <i class="bi bi-plus"></i>
    </button>

    <div class="modal fade" id="addEventModal" tabindex="-1" aria-labelledby="addEventModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="addEventModalLabel">
                        <i class="bi bi-plus-circle"></i> Create New Event
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="events.php" id="addEventForm">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="add_event_name" class="form-label">Event Name *</label>
                                <input type="text" class="form-control" id="add_event_name" name="event_name" required maxlength="255">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="add_event_location" class="form-label">Event Location *</label>
                                <input type="text" class="form-control" id="add_event_location" name="event_location" required maxlength="255">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="add_event_date" class="form-label">Event Date *</label>
                                <input type="date" class="form-control" id="add_event_date" name="event_date" required min="<?php echo date('Y-m-d'); ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="add_pricing" class="form-label">Pricing (₱) *</label>
                                <input type="number" class="form-control" id="add_pricing" name="pricing" step="0.01" min="0" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="add_event_remarks" class="form-label">Event Remarks</label>
                            <textarea class="form-control" id="add_event_remarks" name="event_remarks" rows="4" placeholder="Optional remarks about the event" maxlength="1000"></textarea>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="submit" name="add_event" value="1" class="btn btn-success flex-fill">
                                <i class="bi bi-plus-circle"></i> Create Event
                            </button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                <i class="bi bi-x-circle"></i> Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php 
    $result->data_seek(0);
    while($event = $result->fetch_assoc()): 
    ?>
    
    <div class="modal fade" id="viewModal<?php echo $event['id']; ?>" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title">
                        <i class="bi bi-eye"></i> Event Details
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <h6 class="text-white opacity-75"><i class="bi bi-calendar-event"></i> Event Name:</h6>
                                <p class="h5 text-white"><?php echo htmlspecialchars($event['event_name']); ?></p>
                            </div>
                            
                            <div class="mb-3">
                                <h6 class="text-white opacity-75"><i class="bi bi-geo-alt"></i> Location:</h6>
                                <p class="text-white"><?php echo htmlspecialchars($event['event_location']); ?></p>
                            </div>
                            
                            <div class="mb-3">
                                <h6 class="text-white opacity-75"><i class="bi bi-calendar"></i> Event Date:</h6>
                                <p class="text-white"><?php echo date('F d, Y (l)', strtotime($event['event_date'])); ?></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <h6 class="text-white opacity-75"><i class="bi bi-cash"></i> Pricing:</h6>
                                <div class="price-tag">₱<?php echo number_format($event['pricing'], 2); ?></div>
                            </div>
                            
                            <div class="mb-3">
                                <h6 class="text-white opacity-75"><i class="bi bi-clock"></i> Date Added:</h6>
                                <p class="text-white"><?php echo date('F d, Y \a\t g:i A', strtotime($event['date_added'])); ?></p>
                            </div>
                            
                            <div class="mb-3">
                                <h6 class="text-white opacity-75"><i class="bi bi-info-circle"></i> Status:</h6>
                                <?php if (strtotime($event['event_date']) < strtotime('today')): ?>
                                    <span class="status-badge past">Past Event</span>
                                <?php elseif (strtotime($event['event_date']) == strtotime('today')): ?>
                                    <span class="status-badge today">Today</span>
                                <?php else: ?>
                                    <span class="status-badge upcoming">Upcoming</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <?php if (!empty($event['event_remarks'])): ?>
                    <div class="row">
                        <div class="col-12">
                            <h6 class="text-white opacity-75"><i class="bi bi-chat-text"></i> Event Remarks:</h6>
                            <div class="p-3 rounded" style="background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.3);">
                                <p class="text-dark mb-0" style="color: #2d3748 !important; font-weight: 500; line-height: 1.6;"><?php echo nl2br(htmlspecialchars($event['event_remarks'])); ?></p>
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

    <div class="modal fade" id="deleteModal<?php echo $event['id']; ?>" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">
                        <i class="bi bi-exclamation-triangle"></i> Confirm Delete
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <i class="bi bi-trash" style="font-size: 4rem; color: #fa709a; margin-bottom: 1rem;"></i>
                    <h5 class="text-white">Are you sure you want to delete this event?</h5>
                    <p class="text-white opacity-75">
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

    <footer class="mt-5 py-4">
        <div class="container text-center">
            <p class="text-white opacity-75 mb-0">
                &copy; <?php echo date('Y'); ?> GalaGo Events Monitoring System. All rights reserved.
            </p>
            <small class="text-white opacity-50">
                Crafted with ❤️ for amazing event management
            </small>
        </div>
    </footer>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="js/script.js"></script>
    <script>
        function toggleView(viewType) {
            const gridView = document.getElementById('gridView');
            const listView = document.getElementById('listView');
            const gridBtn = document.getElementById('gridBtn');
            const listBtn = document.getElementById('listBtn');
            
            if (!gridView || !listView || !gridBtn || !listBtn) {
                console.error('View toggle elements not found');
                return;
            }
            
            if (viewType === 'list') {
                gridView.classList.remove('active');
                listView.classList.add('active');
                gridBtn.classList.remove('active');
                listBtn.classList.add('active');
                gridView.style.display = 'none';
                listView.style.display = 'block';
                listView.style.opacity = '0';
                listView.style.transition = 'all 0.3s ease';
                setTimeout(() => {
                    listView.style.opacity = '1';
                    listView.style.transform = 'translateY(0)';
                }, 50);
            } else {
                listView.classList.remove('active');
                gridView.classList.add('active');
                listBtn.classList.remove('active');
                gridBtn.classList.add('active');
                listView.style.display = 'none';
                gridView.style.display = 'block';
                gridView.style.opacity = '0';
                gridView.style.transition = 'all 0.3s ease';
                setTimeout(() => {
                    gridView.style.opacity = '1';
                    gridView.style.transform = 'translateY(0)';
                }, 50);
            }
        }

        function filterEvents() {
            const searchInput = document.getElementById('eventSearch');
            if (!searchInput) {
                console.error('Search input not found');
                return;
            }
            
            const filter = searchInput.value.toLowerCase();
            const cards = document.querySelectorAll('.event-card, .event-list-item');

            if (cards.length === 0) {
                console.warn('No event cards found to filter');
                return;
            }

            cards.forEach(card => {
                const text = card.textContent.toLowerCase();
                if (text.indexOf(filter) > -1) {
                    card.style.display = '';
                    card.style.opacity = '0';
                    card.style.transform = 'translateY(20px)';
                    card.style.transition = 'all 0.3s ease';
                    setTimeout(() => {
                        card.style.opacity = '1';
                        card.style.transform = 'translateY(0)';
                    }, 100);
                } else {
                    card.style.opacity = '0';
                    card.style.transform = 'translateY(-20px)';
                    card.style.transition = 'all 0.3s ease';
                    setTimeout(() => {
                        card.style.display = 'none';
                    }, 300);
                }
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            const remarksTextarea = document.getElementById('event_remarks');
            const charCounter = document.getElementById('charCounter');
            const maxLength = 1000;

            function updateCharCounter() {
                const currentLength = remarksTextarea.value.length;
                const remaining = Math.max(0, maxLength - currentLength);
                charCounter.textContent = `${remaining} characters remaining`;
                
                if (remaining <= 50) {
                    charCounter.className = 'text-warning';
                    charCounter.style.fontWeight = '700';
                    charCounter.style.textShadow = '0 0 10px rgba(255, 193, 7, 0.5)';
                } else {
                    charCounter.className = 'text-white opacity-75';
                    charCounter.style.fontWeight = '600';
                    charCounter.style.textShadow = 'none';
                }
            }

            if (remarksTextarea && charCounter) {
                remarksTextarea.addEventListener('input', updateCharCounter);
                updateCharCounter();
            }
        });

        (function() {
            'use strict';
            window.addEventListener('load', function() {
                var forms = document.getElementsByClassName('needs-validation');
                Array.prototype.filter.call(forms, function(form) {
                    form.addEventListener('submit', function(event) {
                        if (form.checkValidity() === false) {
                            event.preventDefault();
                            event.stopPropagation();
                        } else {
                            const submitBtn = form.querySelector('button[type="submit"]');
                            submitBtn.innerHTML = '<i class="bi bi-check-circle"></i> Creating...';
                            submitBtn.classList.add('success-animation');
                        }
                        form.classList.add('was-validated');
                    }, false);
                });
            }, false);
        })();

        document.addEventListener('DOMContentLoaded', function() {
            const gridView = document.getElementById('gridView');
            const listView = document.getElementById('listView');
            const gridBtn = document.getElementById('gridBtn');
            const listBtn = document.getElementById('listBtn');
            
            if (gridView && listView && gridBtn && listBtn) {
                gridView.classList.add('active');
                gridView.style.display = 'block';
                listView.classList.remove('active');
                listView.style.display = 'none';
                gridBtn.classList.add('active');
                listBtn.classList.remove('active');
            }
            
            const elements = document.querySelectorAll('.animate-fade-in, .animate-slide-up');
            if (elements.length > 0) {
                elements.forEach((el, index) => {
                    el.style.animationDelay = `${index * 0.1}s`;
                });
            }

            const anchorLinks = document.querySelectorAll('a[href^="#"]');
            if (anchorLinks.length > 0) {
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
            }

            const alerts = document.querySelectorAll('.alert-success');
            if (alerts.length > 0) {
                alerts.forEach(alert => {
                    setTimeout(() => {
                        if (alert && alert.classList.contains('show')) {
                            alert.classList.remove('show');
                            setTimeout(() => {
                                if (alert.parentElement) {
                                    alert.remove();
                                }
                            }, 150);
                        }
                    }, 5000);
                });
            }
        });

        document.querySelectorAll('.event-card, .event-list-item').forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-8px) scale(1.02)';
                this.style.boxShadow = '0 25px 50px rgba(0, 0, 0, 0.3)';
            });

            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0) scale(1)';
                this.style.boxShadow = '0 25px 50px rgba(0, 0, 0, 0.25)';
            });
        });
    </script>
</body>
</html>
