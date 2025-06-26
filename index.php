<?php
session_start();
require_once 'config/config.php';

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
    <?php include 'includes/system-overview-card.php'; ?>
    <?php include 'includes/upcoming-events-card.php'; ?>
</div>


<?php include 'includes/features-grid.php'; ?>
<?php include 'includes/dev-team-section.php'; ?>

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