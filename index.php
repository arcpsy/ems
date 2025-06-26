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
    <?php include 'includes/hero-section.php'; ?>
    <div class="row align-items-stretch">
        <?php include 'includes/system-overview-card.php'; ?>
        <?php include 'includes/upcoming-events-card.php'; ?>
    </div>
    <?php include 'includes/features-grid.php'; ?>
    <?php include 'includes/dev-team-section.php'; ?>
    <?php include 'includes/get-started-cta.php'; ?>
</div>

<?php
$content = ob_get_clean();
include 'templates/base.html';
?>