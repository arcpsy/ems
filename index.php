<?php
session_start();
require_once 'config.php';

$page_title = "GalaGo - Events Monitoring System - Home";

$total_query = "SELECT COUNT(*) as total FROM events";
$total_result = $conn->query($total_query);
$total_events = $total_result->fetch_assoc()['total'];

$upcoming_query = "SELECT * FROM events WHERE event_date >= CURDATE() ORDER BY event_date ASC LIMIT 5";
$upcoming_result = $conn->query($upcoming_query);

// Get today's events count
$today_query = "SELECT COUNT(*) as today FROM events WHERE DATE(event_date) = CURDATE()";
$today_result = $conn->query($today_query);
$today_events = $today_result->fetch_assoc()['today'] ?? 0;

// Get total revenue yung pricing (assuming you have a 'pricing' column in your events table)
$revenue_query = "SELECT SUM(pricing) as revenue FROM events";
$revenue_result = $conn->query($revenue_query);
$total_revenue = $revenue_result->fetch_assoc()['revenue'] ?? 0;

ob_start();
?>


<div class="container">
    <section class="hero-section">
        <div class="hero-content loading-animation">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h1 class="hero-title">Welcome to GalaGo</h1>
                    <p class="hero-subtitle">
                        Manage and track all your company events efficiently.
                    </p>
                    <p>
                        Keep track of event details, locations, dates, pricing and more with GalaGo's comprehensive monitoring system.
                    </p>
                    <div class="hero-cta mb-4">
                        <a href="events.php" class="btn btn-success btn-lg">
                            <i class="bi bi-eye"></i> Explore Events
                        </a>
                        <a href="events.php" class="btn btn-light btn-lg btn-create-event">
                            <i class="bi bi-plus-circle"></i> Create Event
                        </a>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="hero-stats">
                        <div class="hero-stat">
                            <span class="stat-number"><?php echo $total_events; ?></span>
                            <span class="stat-label">Total Events</span>
                        </div>
                        <div class="hero-stat">
                            <span class="stat-number"><?php echo $today_events; ?></span>
                            <span class="stat-label">Today</span>
                        </div>
                        <div class="hero-stat">
                            <span class="stat-number"><?php echo $upcoming_result->num_rows; ?></span>
                            <span class="stat-label">Upcoming</span>
                        </div>
                        <div class="hero-stat">
                            <span class="stat-number">₱<?php echo number_format($total_revenue, 0); ?></span>
                            <span class="stat-label">Revenue</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
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

 <div class="white-container loading-animation" style="animation-delay: 0.6s;">
    <section class="team-credits m-0 p-0" style="background: none; border: none; box-shadow: none;">
        <h4 class="team-title">Meet Our Amazing Development Team</h4>
        <p class="text-black opacity-75 mb-4">
            Created with passion and dedication by talented developers
        </p>
        <div class="team-members">
            <span class="team-member">Ferdinand John Dobli</span>
            <span class="team-member">Tristan Kyle Dagatan</span>
            <span class="team-member">Carlo Allan Laynes</span>
            <span class="team-member">Meynard Roi Manuel</span>
            <span class="team-member">Michael Andrei Niñora</span>
            <span class="team-member">Tristan James Sintos</span>
        </div>
    </section>
</div>

        <section class="text-center py-5 loading-animation" style="animation-delay: 0.8s;">
            <div class="dashboard-card" style="max-width: 600px; margin: 0 auto;">
                <div class="card-icon primary mx-auto">
                    <i class="bi bi-rocket-takeoff"></i>
                </div>
                <h3 class="card-title">Ready to Get Started?</h3>
                <p class="card-description">
                    Join thousands of event organizers who trust GalaGo to manage their events efficiently and professionally.
                </p>
                <div class="d-flex gap-3 justify-content-center">
                    <a href="events.php" class="btn btn-success btn-lg">
                        <i class="bi bi-play-fill"></i> Start Now
                    </a>
                </div>
            </div>
        </section>
    </div>

<?php
$content = ob_get_clean();
include 'templates/base.html';
?>