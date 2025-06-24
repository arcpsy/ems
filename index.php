<?php
session_start();
require_once 'config.php';

$page_title = "GalaGo - Events Monitoring System - Home";

$total_query = "SELECT COUNT(*) as total FROM events";
$total_result = $conn->query($total_query);
$total_events = $total_result->fetch_assoc()['total'];

$upcoming_query = "SELECT * FROM events WHERE event_date >= CURDATE() ORDER BY event_date ASC LIMIT 5";
$upcoming_result = $conn->query($upcoming_query);

ob_start();
?>


<div class="row">
    <div class="col-12">
        <div class="jumbotron bg-primary text-white p-5 rounded mb-4">
           <h1 class="display-4 fw-semibold">
                 <i class="bi bi-calendar-event"></i> Welcome to GalaGo
            </h1>
            <p class="lead">Manage and track all your company events efficiently</p>
            <hr class="my-4 bg-white">
            <p>Keep track of event details, locations, dates, pricing and more with GalaGo's comprehensive monitoring system.</p>
            <a class="btn btn-light btn-lg" href="events.php" role="button">
                <i class="bi bi-eye"></i> View All Events
            </a>
            <div class="mt-4">
                <small class="text-black-50">
                    Developed by
                    <strong> Ferdinand Dobli</strong>,
                    <strong> Tristan Dagatan</strong>,
                    <strong> Carlo Laynes</strong>,
                    <strong> Meynard Manuel</strong>,
                    <strong> Andrei Niñora</strong> &
                    <strong> Tristan Sintos</strong>
                </small>
            </div>
        </div>
    </div>
</div>

<div class="row align-items-stretch">
    <div class="col-md-6 d-flex mb-4 mb-md-0">
        <div class="card flex-fill d-flex flex-column h-100">
            <div class="card-header bg-success text-white">
                <h5 class="card-title mb-0">
                    <i class="bi bi-bar-chart"></i> System Overview
                </h5>
            </div>
            <div class="card-body d-flex flex-column">
                <div class="flex-grow-1 d-flex flex-column justify-content-center align-items-center w-100">
                    <div class="bg-light rounded w-100 py-5 d-flex flex-column justify-content-center align-items-center">
                        <h2 class="text-success mb-0"><?php echo $total_events; ?></h2>
                        <p class="text-muted mb-0">Total Events</p>
                    </div>
                </div>
                <div class="mt-auto d-grid">
                    <a href="events.php" class="btn btn-success">
                        <i class="bi bi-plus-circle"></i> Manage Events
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 d-flex">
        <div class="card flex-fill d-flex flex-column">
            <div class="card-header bg-info text-white">
                <h5 class="card-title mb-0">
                    <i class="bi bi-megaphone-fill"></i> Upcoming Events
                </h5>
            </div>
            <div class="card-body d-flex flex-column">
                <?php if ($upcoming_result->num_rows > 0): ?>
                    <div class="list-group list-group-flush flex-grow-1 overflow-auto" style="max-height: 300px;">
                        <?php while($event = $upcoming_result->fetch_assoc()): ?>
                            <div class="list-group-item px-0 py-2">
                                <h6 class="mb-1"><?php echo htmlspecialchars($event['event_name']); ?></h6>
                                <small class="text-muted">
                                    <i class="bi bi-calendar"></i> <?php echo date('M d, Y', strtotime($event['event_date'])); ?>
                                    <br>
                                    <i class="bi bi-geo-alt"></i> <?php echo htmlspecialchars($event['event_location']); ?>
                                </small>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center text-muted flex-grow-1">
                        <i class="bi bi-calendar-x display-4"></i>
                        <p>No upcoming events scheduled</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header text-white" style="background-color: #6a2fe4;">
                <h5 class="card-title mb-0">
                    <i class="bi bi-info-circle"></i> System Features
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 text-center mb-3">
                        <i class="bi bi-plus-circle display-4 text-success"></i>
                        <h6>Add Events</h6>
                        <p class="text-muted small">Create new events with all details</p>
                    </div>
                    <div class="col-md-3 text-center mb-3">
                        <i class="bi bi-pencil-square display-4 text-primary"></i>
                        <h6>Edit Events</h6>
                        <p class="text-muted small">Update event information anytime</p>
                    </div>
                    <div class="col-md-3 text-center mb-3">
                        <i class="bi bi-trash display-4 text-danger"></i>
                        <h6>Delete Events</h6>
                        <p class="text-muted small">Remove events when no longer needed</p>
                    </div>
                    <div class="col-md-3 text-center mb-3">
                        <i class="bi bi-eye display-4 text-info"></i>
                        <h6>View All</h6>
                        <p class="text-muted small">List and manage all events</p>
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