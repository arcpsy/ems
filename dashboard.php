<?php
session_start();
require_once 'config.php';
require_once 'auth_functions.php';

// Require login to access the system
requireLogin();

$page_title = "GalaGo - Events Monitoring System - Home";

// Fetch data for the dashboard
$total_query = "SELECT COUNT(*) as total FROM events";
$total_result = $conn->query($total_query);
$total_events = $total_result->fetch_assoc()['total'];

$upcoming_query = "SELECT * FROM events WHERE event_date >= CURDATE() ORDER BY event_date ASC LIMIT 5";
$upcoming_result = $conn->query($upcoming_query);

$past_query = "SELECT COUNT(*) as past FROM events WHERE event_date < CURDATE()";
$past_result = $conn->query($past_query);
$past_events = $past_result->fetch_assoc()['past'];

$today_query = "SELECT COUNT(*) as today FROM events WHERE event_date = CURDATE()";
$today_result = $conn->query($today_query);
$today_events = $today_result->fetch_assoc()['today'];

$revenue_query = "SELECT SUM(pricing) as total_revenue FROM events";
$revenue_result = $conn->query($revenue_query);
$total_revenue = $revenue_result->fetch_assoc()['total_revenue'] ?? 0;
?>

<!-- Students: Dagatan, Tristan Kyle; Dobli, Ferdinand John; Laynes, Carlo Allan; Manuel, Meynard Roi; Niñora, Michael Andrei; Sintos, Tristan James -->
<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href=".\style2.css"> <!-- do not edit or delete css -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css" rel="stylesheet">
<link href="css/style.css" rel="stylesheet">
<title><?php echo $page_title; ?></title>
<style>
:root {
    --purple-primary: #8B5CF6;
    --purple-light: #A78BFA;
    --purple-dark: #7C3AED;
    --purple-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    --purple-gradient-2: linear-gradient(135deg, #8B5CF6 0%, #3B82F6 100%);
    --purple-gradient-3: linear-gradient(135deg, #EC4899 0%, #8B5CF6 100%);
    --purple-gradient-4: linear-gradient(135deg, #10B981 0%, #059669 100%);
    --success: #10B981;
    --warning: #F59E0B;
    --danger: #EF4444;
    --info: #06B6D4;
    --white: #FFFFFF;
    --black: #1F2937;
    --gray-100: #F3F4F6;
    --gray-200: #E5E7EB;
    --gray-300: #D1D5DB;
    --gray-700: #374151;
    --shadow-purple: 0 20px 25px -5px rgba(139, 92, 246, 0.1), 0 10px 10px -5px rgba(139, 92, 246, 0.04);
    --shadow-glow: 0 0 50px rgba(139, 92, 246, 0.3);
    --glass-bg: rgba(255, 255, 255, 0.1);
    --glass-border: rgba(255, 255, 255, 0.2);
}

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    background: var(--purple-gradient);
    min-height: 100vh;
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    overflow-x: hidden;
    position: relative;
}

/* Animated Background */
body::before {
    content: '';
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: 
        radial-gradient(circle at 20% 50%, rgba(139, 92, 246, 0.3) 0%, transparent 50%),
        radial-gradient(circle at 80% 20%, rgba(59, 130, 246, 0.3) 0%, transparent 50%),
        radial-gradient(circle at 40% 80%, rgba(236, 72, 153, 0.3) 0%, transparent 50%);
    z-index: -1;
    animation: backgroundPulse 15s ease-in-out infinite;
}

@keyframes backgroundPulse {
    0%, 100% { opacity: 0.6; transform: scale(1); }
    50% { opacity: 1; transform: scale(1.1); }
}

/* Navigation */
.navbar {
    background: rgba(255, 255, 255, 0.15) !important;
    backdrop-filter: blur(20px);
    border-bottom: 1px solid rgba(255, 255, 255, 0.2);
    box-shadow: 0 8px 32px rgba(139, 92, 246, 0.1);
    position: sticky;
    top: 0;
    z-index: 1000;
    transition: all 0.3s ease;
}

.navbar:hover {
    background: rgba(255, 255, 255, 0.2) !important;
}

.navbar-brand {
    font-weight: 800;
    color: var(--white) !important;
    font-size: 1.5rem;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
}

.navbar-brand:hover {
    transform: scale(1.05);
    text-shadow: 0 0 20px rgba(255, 255, 255, 0.5);
}

.nav-link {
    color: rgba(255, 255, 255, 0.9) !important;
    font-weight: 600;
    border-radius: 25px;
    padding: 0.6rem 1.2rem !important;
    margin: 0 0.25rem;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
}

.nav-link::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    transition: all 0.5s;
    z-index: -1;
}

.nav-link:hover::before,
.nav-link.active::before {
    left: 100%;
}

.nav-link:hover,
.nav-link.active {
    background: rgba(255, 255, 255, 0.2);
    color: var(--white) !important;
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(255, 255, 255, 0.2);
}

/* Dropdown Menu Styling */
.dropdown-menu {
    background: rgba(255, 255, 255, 0.95) !important;
    backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.3);
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    margin-top: 0.5rem;
    min-width: 200px;
    padding: 0.5rem 0;
}

.dropdown-item {
    color: #333 !important;
    font-weight: 500;
    padding: 0.75rem 1.5rem;
    transition: all 0.3s ease;
    border-radius: 0;
    display: flex;
    align-items: center;
}

.dropdown-item:hover,
.dropdown-item:focus {
    background: rgba(139, 92, 246, 0.1) !important;
    color: var(--purple-primary) !important;
    transform: translateX(5px);
}

.dropdown-item i {
    color: var(--purple-primary);
    font-size: 1.1rem;
}

.dropdown-divider {
    border-color: rgba(139, 92, 246, 0.2);
    margin: 0.5rem 0;
}

/* User dropdown button styling */
.dropdown-toggle::after {
    color: rgba(255, 255, 255, 0.8);
}

/* Rest of your existing styles... */
/* (Keep all the other styles from your original dashboard.php) */

/* Hero Section */
.hero-section {
    padding: 4rem 0;
    position: relative;
}

.hero-content {
    position: relative;
    z-index: 2;
}

.hero-title {
    font-size: 5rem;
    font-weight: 900;
    color: var(--white);
    margin-bottom: 1.5rem;
    text-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    background: linear-gradient(135deg, #FFFFFF 0%, #E0E7FF 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    animation: titleGlow 3s ease-in-out infinite alternate;
}

@keyframes titleGlow {
    0% { text-shadow: 0 0 20px rgba(255, 255, 255, 0.5); }
    100% { text-shadow: 0 0 40px rgba(255, 255, 255, 0.8), 0 0 60px rgba(139, 92, 246, 0.4); }
}

.hero-subtitle {
    font-size: 1.5rem;
    color: rgba(255, 255, 255, 0.9);
    margin-bottom: 3rem;
    font-weight: 400;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.hero-cta .btn {
    border-radius: 25px;
    padding: 1rem 2.5rem;
    font-weight: 800;
    font-size: 1.2rem;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
}

.hero-cta .btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    transition: all 0.5s;
}

.hero-cta .btn:hover::before {
    left: 100%;
}

.hero-cta .btn:hover {
    transform: translateY(-5px) scale(1.05);
    box-shadow: var(--shadow-glow);
}

.btn-success {
    background: var(--purple-gradient-4);
    border: none;
    color: var(--white);
    box-shadow: 0 8px 25px rgba(16, 185, 129, 0.4);
}

/* Hero Stats - Fixed for better text display */
.hero-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 1.5rem;
    margin-top: 3rem;
}

.hero-stat {
    background: var(--glass-bg);
    backdrop-filter: blur(20px);
    border: 1px solid var(--glass-border);
    border-radius: 20px;
    padding: 2rem 1.5rem;
    text-align: center;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: var(--shadow-purple);
    position: relative;
    overflow: hidden;
    min-height: 140px;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.hero-stat::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 1px;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.5), transparent);
}

.hero-stat:hover {
    transform: translateY(-8px) rotateX(5deg);
    box-shadow: var(--shadow-glow);
    border-color: rgba(255, 255, 255, 0.3);
}

.stat-number {
    font-size: 2.2rem !important;
    font-weight: 900;
    color: var(--white);
    display: block;
    line-height: 1.1 !important;
    text-shadow: 0 0 20px rgba(255, 255, 255, 0.3);
    animation: countUp 2s ease-out;
    word-break: break-all;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    margin-bottom: 0.25rem;
}

.stat-label {
    color: rgba(255, 255, 255, 0.8);
    font-size: 0.8rem;
    font-weight: 600;
    margin-top: 0.25rem;
    text-transform: uppercase;
    letter-spacing: 1px;
    line-height: 1.2;
}

@keyframes countUp {
    from { transform: scale(0); opacity: 0; }
    to { transform: scale(1); opacity: 1; }
}

/* Dashboard Cards - Enhanced Glamorous Design */
.dashboard-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(380px, 1fr));
    gap: 2.5rem;
    margin: 4rem 0;
}

.dashboard-card {
    background: linear-gradient(145deg, rgba(255, 255, 255, 0.15) 0%, rgba(255, 255, 255, 0.08) 100%);
    backdrop-filter: blur(25px);
    border: 2px solid rgba(255, 255, 255, 0.2);
    border-radius: 30px;
    padding: 3rem;
    transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 
        0 25px 50px rgba(139, 92, 246, 0.15),
        0 8px 16px rgba(0, 0, 0, 0.1),
        inset 0 2px 4px rgba(255, 255, 255, 0.2);
    height: 100%;
    display: flex;
    flex-direction: column;
    position: relative;
    overflow: hidden;
    transform-style: preserve-3d;
}

.dashboard-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #10B981 0%, #059669 100%);
    transform: scaleX(0);
    transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
    border-radius: 30px 30px 0 0;
}

.dashboard-card:nth-child(2)::before {
    background: linear-gradient(90deg, #EC4899 0%, #DB2777 100%);
}

.dashboard-card:nth-child(3)::before {
    background: linear-gradient(90deg, #8B5CF6 0%, #7C3AED 100%);
}

.dashboard-card::after {
    content: '';
    position: absolute;
    top: -2px;
    left: -2px;
    right: -2px;
    bottom: -2px;
    background: linear-gradient(45deg, transparent 0%, rgba(255, 255, 255, 0.1) 50%, transparent 100%);
    border-radius: 30px;
    z-index: -1;
    opacity: 0;
    transition: all 0.5s ease;
}

.dashboard-card:hover::before {
    transform: scaleX(1);
}

.dashboard-card:hover::after {
    opacity: 1;
    animation: shimmer 2s ease-in-out infinite;
}

@keyframes shimmer {
    0% { transform: translateX(-100%); }
    100% { transform: translateX(100%); }
}

.dashboard-card:hover {
    transform: translateY(-15px) rotateX(5deg) scale(1.02);
    box-shadow: 
        0 40px 80px rgba(139, 92, 246, 0.25),
        0 20px 40px rgba(236, 72, 153, 0.15),
        0 0 40px rgba(255, 255, 255, 0.1),
        inset 0 2px 4px rgba(255, 255, 255, 0.3);
    border-color: rgba(255, 255, 255, 0.4);
}

.card-icon {
    width: 90px;
    height: 90px;
    border-radius: 25px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2.2rem;
    margin-bottom: 2rem;
    color: var(--white);
    box-shadow: 
        0 15px 30px rgba(0, 0, 0, 0.15),
        inset 0 2px 4px rgba(255, 255, 255, 0.2);
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
}

.card-icon::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    border-radius: 25px;
    opacity: 0;
    transition: all 0.4s ease;
}

.card-icon.success { 
    background: linear-gradient(145deg, #10B981 0%, #059669 100%);
    box-shadow: 0 15px 30px rgba(16, 185, 129, 0.4);
}

.card-icon.success::before {
    background: linear-gradient(145deg, #34D399 0%, #10B981 100%);
}

.card-icon.info { 
    background: linear-gradient(145deg, #EC4899 0%, #DB2777 100%);
    box-shadow: 0 15px 30px rgba(236, 72, 153, 0.4);
}

.card-icon.info::before {
    background: linear-gradient(145deg, #F472B6 0%, #EC4899 100%);
}

.card-icon.warning { 
    background: linear-gradient(145deg, #8B5CF6 0%, #7C3AED 100%);
    box-shadow: 0 15px 30px rgba(139, 92, 246, 0.4);
}

.card-icon.warning::before {
    background: linear-gradient(145deg, #A78BFA 0%, #8B5CF6 100%);
}

.card-icon.primary { 
    background: linear-gradient(145deg, #667eea 0%, #764ba2 100%);
    box-shadow: 0 15px 30px rgba(102, 126, 234, 0.4);
}

.card-icon.primary::before {
    background: linear-gradient(145deg, #8B9BFF 0%, #667eea 100%);
}

.dashboard-card:hover .card-icon {
    transform: scale(1.15) rotateY(15deg) translateZ(20px);
    box-shadow: 
        0 25px 50px rgba(0, 0, 0, 0.2),
        0 0 30px currentColor,
        inset 0 2px 4px rgba(255, 255, 255, 0.3);
}

.dashboard-card:hover .card-icon::before {
    opacity: 1;
}

.card-icon i {
    position: relative;
    z-index: 2;
    filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.2));
    transition: all 0.3s ease;
}

.dashboard-card:hover .card-icon i {
    transform: scale(1.1);
    filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.3));
    text-shadow: 0 0 20px rgba(255, 255, 255, 0.5);
}

.card-title {
    color: var(--white);
    font-size: 1.6rem;
    font-weight: 800;
    margin-bottom: 1rem;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.card-description {
    color: rgba(255, 255, 255, 0.8);
    line-height: 1.6;
    margin-bottom: 1.5rem;
    flex-grow: 1;
    font-weight: 500;
}

/* Dashboard Card Stats - Fixed sizing */
.dashboard-card .row.text-center .stat-number {
    font-size: 2rem !important;
    line-height: 1.1 !important;
    margin-bottom: 0.25rem;
    word-break: break-all;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.dashboard-card .row.text-center .stat-label {
    font-size: 0.75rem;
    margin-top: 0.25rem;
}

.btn {
    border: none;
    border-radius: 15px;
    font-weight: 700;
    padding: 0.875rem 2rem;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
    text-transform: none;
    letter-spacing: 0.5px;
}

.btn:hover {
    transform: translateY(-3px) scale(1.02);
}

.btn-success {
    background: var(--purple-gradient-4);
    color: var(--white);
    box-shadow: 0 8px 25px rgba(16, 185, 129, 0.4);
}

.btn-info {
    background: var(--purple-gradient-3);
    color: var(--white);
    box-shadow: 0 8px 25px rgba(236, 72, 153, 0.4);
}

.btn-warning {
    background: var(--purple-gradient-2);
    color: var(--white);
    box-shadow: 0 8px 25px rgba(139, 92, 246, 0.4);
}

.btn-outline-light {
    border: 2px solid rgba(255, 255, 255, 0.5);
    color: var(--white);
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
}

.btn-outline-light:hover {
    background: rgba(255, 255, 255, 0.2);
    border-color: rgba(255, 255, 255, 0.8);
    color: var(--white);
}

/* Upcoming Events */
.upcoming-events {
    background: rgba(255, 255, 255, 0.05);
    border-radius: 20px;
    padding: 1.5rem;
    margin-top: 1rem;
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.event-item {
    background: rgba(255, 255, 255, 0.1);
    border-radius: 15px;
    padding: 1.25rem;
    margin-bottom: 1rem;
    border-left: 4px solid var(--purple-light);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    position: relative;
    overflow: hidden;
}

.event-item::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
    transition: all 0.5s;
}

.event-item:hover::before {
    left: 100%;
}

.event-item:hover {
    transform: translateX(10px);
    box-shadow: 0 8px 25px rgba(139, 92, 246, 0.2);
    border-left-color: var(--white);
}

.event-item:last-child {
    margin-bottom: 0;
}

.event-name {
    color: var(--white);
    font-weight: 700;
    margin-bottom: 0.5rem;
    font-size: 1.1rem;
}

.event-details {
    color: rgba(255, 255, 255, 0.8);
    font-size: 0.9rem;
    font-weight: 500;
}

.event-details i {
    color: var(--purple-light);
    margin-right: 0.5rem;
    width: 16px;
}

/* ... rest of your styles remain the same ... */

/* Features Section - Enhanced Glamorous Design */
.features-section {
    margin: 4rem 0;
    position: relative;
}

.features-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: radial-gradient(circle at 30% 20%, rgba(139, 92, 246, 0.15) 0%, transparent 50%),
                radial-gradient(circle at 70% 80%, rgba(236, 72, 153, 0.15) 0%, transparent 50%);
    pointer-events: none;
    animation: featuresBg 20s ease-in-out infinite;
}

@keyframes featuresBg {
    0%, 100% { transform: scale(1) rotate(0deg); opacity: 0.7; }
    50% { transform: scale(1.1) rotate(5deg); opacity: 1; }
}

.features-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
    gap: 2.5rem;
    margin-top: 3rem;
    position: relative;
    z-index: 2;
}

.feature-card {
    background: linear-gradient(145deg, rgba(255, 255, 255, 0.12) 0%, rgba(255, 255, 255, 0.08) 100%);
    backdrop-filter: blur(25px);
    border: 2px solid rgba(255, 255, 255, 0.15);
    border-radius: 30px;
    padding: 3rem 2.5rem;
    text-align: center;
    transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 
        0 25px 50px rgba(139, 92, 246, 0.1),
        0 8px 16px rgba(0, 0, 0, 0.1),
        inset 0 1px 1px rgba(255, 255, 255, 0.2);
    position: relative;
    overflow: hidden;
    transform-style: preserve-3d;
}

.feature-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, 
        rgba(139, 92, 246, 0.05) 0%, 
        rgba(59, 130, 246, 0.05) 25%,
        rgba(236, 72, 153, 0.05) 50%,
        rgba(16, 185, 129, 0.05) 75%,
        rgba(245, 158, 11, 0.05) 100%);
    opacity: 0;
    transition: all 0.5s ease;
    border-radius: 30px;
}

.feature-card:hover::before {
    opacity: 1;
    transform: scale(1.02);
}

.feature-card::after {
    content: '';
    position: absolute;
    top: -2px;
    left: -2px;
    right: -2px;
    bottom: -2px;
    background: linear-gradient(45deg, 
        #8B5CF6 0%, 
        #3B82F6 25%, 
        #EC4899 50%, 
        #10B981 75%, 
        #F59E0B 100%);
    border-radius: 30px;
    z-index: -1;
    opacity: 0;
    transition: all 0.5s ease;
    animation: borderGlow 3s ease-in-out infinite;
}

@keyframes borderGlow {
    0%, 100% { opacity: 0; transform: scale(1); }
    50% { opacity: 0.3; transform: scale(1.02); }
}

.feature-card:hover {
    transform: translateY(-15px) rotateX(5deg) rotateY(2deg) scale(1.02);
    box-shadow: 
        0 40px 80px rgba(139, 92, 246, 0.25),
        0 20px 40px rgba(236, 72, 153, 0.15),
        0 0 40px rgba(255, 255, 255, 0.1),
        inset 0 2px 4px rgba(255, 255, 255, 0.3);
    border-color: rgba(255, 255, 255, 0.4);
}

.feature-card:hover::after {
    opacity: 0.6;
    animation-duration: 1.5s;
}

.feature-icon {
    width: 90px;
    height: 90px;
    border-radius: 25px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 2rem;
    color: var(--white) !important;
    background: linear-gradient(145deg, rgba(255, 255, 255, 0.15) 0%, rgba(255, 255, 255, 0.05) 100%);
    backdrop-filter: blur(20px);
    border: 2px solid rgba(255, 255, 255, 0.2);
    box-shadow: 
        0 15px 30px rgba(0, 0, 0, 0.1),
        inset 0 2px 4px rgba(255, 255, 255, 0.2);
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
}

.feature-icon::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    border-radius: 25px;
    transition: all 0.4s ease;
    opacity: 0;
}

/* Specific icon colors and animations */
.feature-card:nth-child(1) .feature-icon {
    background: linear-gradient(145deg, #10B981 0%, #059669 100%);
    box-shadow: 0 15px 30px rgba(16, 185, 129, 0.4);
}

.feature-card:nth-child(1) .feature-icon::before {
    background: linear-gradient(145deg, #34D399 0%, #10B981 100%);
}

.feature-card:nth-child(2) .feature-icon {
    background: linear-gradient(145deg, #8B5CF6 0%, #7C3AED 100%);
    box-shadow: 0 15px 30px rgba(139, 92, 246, 0.4);
}

.feature-card:nth-child(2) .feature-icon::before {
    background: linear-gradient(145deg, #A78BFA 0%, #8B5CF6 100%);
}

.feature-card:nth-child(3) .feature-icon {
    background: linear-gradient(145deg, #EF4444 0%, #DC2626 100%);
    box-shadow: 0 15px 30px rgba(239, 68, 68, 0.4);
}

.feature-card:nth-child(3) .feature-icon::before {
    background: linear-gradient(145deg, #F87171 0%, #EF4444 100%);
}

.feature-card:nth-child(4) .feature-icon {
    background: linear-gradient(145deg, #06B6D4 0%, #0891B2 100%);
    box-shadow: 0 15px 30px rgba(6, 182, 212, 0.4);
}

.feature-card:nth-child(4) .feature-icon::before {
    background: linear-gradient(145deg, #22D3EE 0%, #06B6D4 100%);
}

.feature-card:nth-child(5) .feature-icon {
    background: linear-gradient(145deg, #EC4899 0%, #DB2777 100%);
    box-shadow: 0 15px 30px rgba(236, 72, 153, 0.4);
}

.feature-card:nth-child(5) .feature-icon::before {
    background: linear-gradient(145deg, #F472B6 0%, #EC4899 100%);
}

.feature-card:nth-child(6) .feature-icon {
    background: linear-gradient(145deg, #F59E0B 0%, #D97706 100%);
    box-shadow: 0 15px 30px rgba(245, 158, 11, 0.4);
}

.feature-card:nth-child(6) .feature-icon::before {
    background: linear-gradient(145deg, #FBBF24 0%, #F59E0B 100%);
}

.feature-card:hover .feature-icon {
    transform: scale(1.15) rotateY(15deg) translateZ(20px);
    box-shadow: 
        0 25px 50px rgba(0, 0, 0, 0.2),
        0 0 30px currentColor;
}

.feature-card:hover .feature-icon::before {
    opacity: 1;
}

.feature-icon i {
    position: relative;
    z-index: 2;
    filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.2));
    transition: all 0.3s ease;
    color: var(--white) !important;
    font-size: 2.5rem !important;
}

.feature-card:hover .feature-icon i {
    transform: scale(1.1);
    filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.3));
    text-shadow: 0 0 20px rgba(255, 255, 255, 0.5);
}

.feature-title {
    color: var(--white);
    font-weight: 800;
    margin-bottom: 1.5rem;
    font-size: 1.4rem;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    transition: all 0.3s ease;
    letter-spacing: 0.5px;
}

.feature-card:hover .feature-title {
    transform: translateY(-2px);
    text-shadow: 0 4px 8px rgba(0, 0, 0, 0.3), 0 0 20px rgba(255, 255, 255, 0.3);
}

.feature-description {
    color: rgba(255, 255, 255, 0.9);
    font-size: 1rem;
    line-height: 1.7;
    font-weight: 500;
    transition: all 0.3s ease;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
}

.feature-card:hover .feature-description {
    color: rgba(255, 255, 255, 1);
    transform: translateY(-1px);
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
}

/* Team Credits */
.team-credits {
    background: var(--glass-bg);
    backdrop-filter: blur(20px);
    border: 1px solid var(--glass-border);
    border-radius: 25px;
    padding: 3rem;
    text-align: center;
    margin: 4rem 0;
    box-shadow: var(--shadow-purple);
    position: relative;
    overflow: hidden;
}

.team-credits::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(236, 72, 153, 0.1) 0%, transparent 70%);
    animation: rotate 20s linear infinite;
}

@keyframes rotate {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

.team-title {
    color: var(--white);
    font-weight: 800;
    margin-bottom: 1rem;
    font-size: 1.8rem;
    position: relative;
    z-index: 1;
}

.team-members {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 1rem;
    margin-top: 1.5rem;
    position: relative;
    z-index: 1;
}

.team-member {
    background: var(--purple-gradient-2);
    color: var(--white);
    padding: 0.75rem 1.5rem;
    border-radius: 25px;
    font-size: 0.95rem;
    font-weight: 600;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 4px 15px rgba(139, 92, 246, 0.3);
    border: 1px solid rgba(255, 255, 255, 0.2);
    position: relative;
    overflow: hidden;
}

.team-member::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    transition: all 0.5s;
}

.team-member:hover::before {
    left: 100%;
}

.team-member:hover {
    transform: scale(1.05) translateY(-2px);
    box-shadow: 0 8px 25px rgba(139, 92, 246, 0.5);
}

/* Section Headers */
.section-header {
    text-align: center;
    margin-bottom: 3rem;
}

.section-title {
    color: var(--white);
    font-size: 3.5rem;
    font-weight: 900;
    margin-bottom: 1rem;
    text-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    background: linear-gradient(135deg, #FFFFFF 0%, #E0E7FF 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.section-subtitle {
    color: rgba(255, 255, 255, 0.9);
    font-size: 1.3rem;
    font-weight: 500;
}

/* Footer */
footer {
    background: rgba(255, 255, 255, 0.05);
    backdrop-filter: blur(20px);
    border-top: 1px solid rgba(255, 255, 255, 0.1);
    margin-top: 4rem;
    padding: 2rem 0;
}

footer p {
    color: rgba(255, 255, 255, 0.8);
    margin: 0;
    font-weight: 500;
}
/* Responsive - Updated */
@media (max-width: 768px) {
    .hero-title {
        font-size: 3rem;
    }
    
    .hero-subtitle {
        font-size: 1.2rem;
    }
    
    .hero-stats {
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
    }
    
    .hero-stat {
        padding: 1.5rem 1rem;
        min-height: 120px;
    }
    
    .stat-number {
        font-size: 1.8rem !important;
        line-height: 1.1 !important;
    }
    
    .stat-label {
        font-size: 0.75rem;
    }
    
    .dashboard-grid,
    .features-grid {
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }
    
    .section-title {
        font-size: 2.5rem;
    }
    
    .team-members {
        flex-direction: column;
        align-items: center;
    }
    
    .feature-card,
    .dashboard-card {
        padding: 2rem;
    }
    
    .dashboard-card .row.text-center .stat-number {
        font-size: 1.7rem !important;
    }
}

@media (max-width: 480px) {
    .stat-number {
        font-size: 1.6rem !important;
    }
    
    .hero-stats {
        grid-template-columns: 1fr 1fr;
    }
    
    .dashboard-card .row.text-center .stat-number {
        font-size: 1.5rem !important;
    }
}

/* Alerts */
.alert {
    background: var(--glass-bg);
    backdrop-filter: blur(20px);
    border: 1px solid var(--glass-border);
    border-radius: 20px;
    font-weight: 500;
    padding: 1.25rem 1.75rem;
    margin-bottom: 1.5rem;
    box-shadow: var(--shadow-purple);
    color: var(--white);
}

.alert-success {
    border-left: 4px solid var(--success);
    background: rgba(16, 185, 129, 0.1);
}

.alert-danger {
    border-left: 4px solid var(--danger);
    background: rgba(239, 68, 68, 0.1);
}

.alert i {
    color: var(--white);
}
</style>
</head>
<body> <!-- don't edit anything prior here except inside the script tags -->

<!-- Original professor form structure -->
<div id="inputForm" style="display: none;">
<form name="form1" action="" method="post"><!-- modify this-->
<br>
<!-- no edits within this space -->
 Email: <input type="text" name="email" value=""/><br/>
<input type="Submit" value="Verify"/>
<!-- until here -->
</form>
</div>

<!-- use these for the php outputs, remove the comments and this text. -->
<div id='display'>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php">
                <i class="bi bi-gem me-2"></i>GalaGo Events
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="dashboard.php">
                            <i class="bi bi-house-door me-1"></i>Home
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="events.php">
                            <i class="bi bi-calendar-star me-1"></i>All Events
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle me-1"></i><?php echo htmlspecialchars(explode(' ', $_SESSION['user_name'])[0]); ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="logout.php"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container">
        <!-- Session Messages -->
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show animate-fade-in" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i><?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show animate-fade-in" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i><?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Hero Section -->
        <section class="hero-section animate-fade-in">
            <div class="hero-content">
                <div class="row align-items-center">
                    <div class="col-lg-8">
                        <h1 class="hero-title">Welcome to GalaGo</h1>
                        <p class="hero-subtitle">
                            The ultimate event management platform that transforms how you create, manage, and track events with stunning visual excellence
                        </p>
                        <div class="hero-cta">
                            <a href="events.php" class="btn btn-success btn-lg">
                                <i class="bi bi-rocket-takeoff me-2"></i>Explore Events
                            </a>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="hero-stats">
                            <div class="hero-stat" style="animation-delay: 0.1s;">
                                <span class="stat-number" data-target="<?php echo $total_events; ?>"><?php echo $total_events; ?></span>
                                <span class="stat-label">Total Events</span>
                            </div>
                            <div class="hero-stat" style="animation-delay: 0.2s;">
                                <span class="stat-number" data-target="<?php echo $today_events; ?>"><?php echo $today_events; ?></span>
                                <span class="stat-label">Today</span>
                            </div>
                            <div class="hero-stat" style="animation-delay: 0.3s;">
                                <span class="stat-number" data-target="<?php echo $upcoming_result->num_rows; ?>"><?php echo $upcoming_result->num_rows; ?></span>
                                <span class="stat-label">Upcoming</span>
                            </div>
                            <div class="hero-stat" style="animation-delay: 0.4s;">
                                <span class="stat-number" data-target="<?php echo $total_revenue; ?>">₱<?php echo number_format($total_revenue, 0); ?></span>
                                <span class="stat-label">Revenue</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Dashboard Section -->
        <section class="animate-slide-up" style="animation-delay: 0.2s;">
            <div class="dashboard-grid">
                <div class="dashboard-card" style="animation-delay: 0.1s;">
                    <div class="card-icon success">
                        <i class="bi bi-bar-chart-line-fill"></i>
                    </div>
                    <h3 class="card-title">System Overview</h3>
                    <p class="card-description">
                        Get comprehensive insights into your event portfolio with real-time statistics and stunning analytics visualization.
                    </p>
                    <div class="text-center mb-3">
                        <div class="stat-number" style="font-size: 3.5rem;"><?php echo $total_events; ?></div>
                        <div class="stat-label">Events Managed</div>
                    </div>
                    <a href="events.php" class="btn btn-success w-100">
                        <i class="bi bi-plus-circle me-2"></i>Manage Events
                    </a>
                </div>

                <div class="dashboard-card" style="animation-delay: 0.2s;">
                    <div class="card-icon info">
                        <i class="bi bi-calendar-heart-fill"></i>
                    </div>
                    <h3 class="card-title">Upcoming Events</h3>
                    <div class="upcoming-events">
                        <?php if ($upcoming_result->num_rows > 0): ?>
                            <?php while($event = $upcoming_result->fetch_assoc()): ?>
                                <div class="event-item">
                                    <div class="event-name"><?php echo htmlspecialchars($event['event_name']); ?></div>
                                    <div class="event-details">
                                        <i class="bi bi-calendar-fill"></i><?php echo date('M d, Y', strtotime($event['event_date'])); ?>
                                        <br>
                                        <i class="bi bi-geo-alt-fill"></i><?php echo htmlspecialchars($event['event_location']); ?>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <div class="text-center text-white-50">
                                <i class="bi bi-calendar-x display-4 mb-3" style="color: rgba(255, 255, 255, 0.3);"></i>
                                <p>No upcoming events scheduled</p>
                                <a href="events.php" class="btn btn-info btn-sm">
                                    <i class="bi bi-plus me-1"></i>Create First Event
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="dashboard-card" style="animation-delay: 0.3s;">
                    <div class="card-icon warning">
                        <i class="bi bi-graph-up-arrow"></i>
                    </div>
                    <h3 class="card-title">Performance Metrics</h3>
                    <p class="card-description">
                        Track your event success rates and revenue generation with beautiful visual insights across all managed events.
                    </p>
                    <div class="row text-center mb-3">
                        <div class="col-6">
                            <div class="stat-number"><?php echo $past_events; ?></div>
                            <div class="stat-label">Completed</div>
                        </div>
                        <div class="col-6">
                            <div class="stat-number">₱<?php echo number_format($total_revenue, 0); ?></div>
                            <div class="stat-label">Revenue</div>
                        </div>
                    </div>
                    <a href="events.php" class="btn btn-warning w-100">
                        <i class="bi bi-graph-up me-2"></i>View Analytics
                    </a>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section class="features-section animate-slide-up" style="animation-delay: 0.4s;">
            <div class="section-header">
                <h2 class="section-title">Powerful Features</h2>
                <p class="section-subtitle">Everything you need to manage events like a pro with stunning visual appeal</p>
            </div>
            
            <div class="features-grid">
                <div class="feature-card" style="animation-delay: 0.1s;">
                    <div class="feature-icon">
                        <i class="bi bi-plus-circle-fill"></i>
                    </div>
                    <h6 class="feature-title">Create Events</h6>
                    <p class="feature-description">
                        Design and create stunning events with comprehensive details, pricing, and scheduling options in a beautiful interface.
                    </p>
                </div>
                
                <div class="feature-card" style="animation-delay: 0.2s;">
                    <div class="feature-icon">
                        <i class="bi bi-pencil-square"></i>
                    </div>
                    <h6 class="feature-title">Edit & Update</h6>
                    <p class="feature-description">
                        Seamlessly update event information, modify schedules, and adjust pricing in real-time with smooth animations.
                    </p>
                </div>
                
                <div class="feature-card" style="animation-delay: 0.3s;">
                    <div class="feature-icon">
                        <i class="bi bi-trash-fill"></i>
                    </div>
                    <h6 class="feature-title">Smart Management</h6>
                    <p class="feature-description">
                        Efficiently organize and remove events with intelligent confirmation and backup systems in a visually pleasing way.
                    </p>
                </div>
                
                <div class="feature-card" style="animation-delay: 0.4s;">
                    <div class="feature-icon">
                        <i class="bi bi-eye-fill"></i>
                    </div>
                    <h6 class="feature-title">Comprehensive View</h6>
                    <p class="feature-description">
                        Access detailed event insights with multiple viewing modes and advanced filtering options in stunning layouts.
                    </p>
                </div>
                
                <div class="feature-card" style="animation-delay: 0.5s;">
                    <div class="feature-icon">
                        <i class="bi bi-search-heart"></i>
                    </div>
                    <h6 class="feature-title">Advanced Search</h6>
                    <p class="feature-description">
                        Find events instantly with powerful search capabilities and intelligent filtering systems with beautiful animations.
                    </p>
                </div>
                
                <div class="feature-card" style="animation-delay: 0.6s;">
                    <div class="feature-icon">
                        <i class="bi bi-phone-fill"></i>
                    </div>
                    <h6 class="feature-title">Mobile Ready</h6>
                    <p class="feature-description">
                        Access your events anywhere with our fully responsive design and mobile optimization that looks stunning everywhere.
                    </p>
                </div>
            </div>
        </section>

        <!-- Team Credits -->
        <section class="team-credits animate-slide-up" style="animation-delay: 0.6s;">
            <h4 class="team-title">Meet Our Amazing Development Team</h4>
            <p style="color: rgba(255, 255, 255, 0.8); position: relative; z-index: 1; margin-bottom: 0;">
                Crafted with passion and dedication by talented developers who believe in stunning user experiences
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

        <!-- Call to Action -->
        <section class="text-center py-5 animate-slide-up" style="animation-delay: 0.8s;">
            <div class="dashboard-card" style="max-width: 600px; margin: 0 auto;">
                <div class="card-icon primary mx-auto">
                    <i class="bi bi-rocket-takeoff-fill"></i>
                </div>
                <h3 class="card-title">Ready to Get Started?</h3>
                <p class="card-description">
                    Join thousands of event organizers who trust GalaGo to manage their events efficiently and professionally with stunning visual appeal.
                </p>
                <div class="d-flex gap-3 justify-content-center flex-wrap">
                    <a href="events.php" class="btn btn-success btn-lg">
                        <i class="bi bi-play-fill me-2"></i>Start Now
                    </a>
                    <a href="events.php" class="btn btn-outline-light btn-lg">
                        <i class="bi bi-info-circle me-2"></i>Learn More
                    </a>
                </div>
            </div>
        </section>
    </div>

    <!-- Footer -->
    <footer>
        <div class="container text-center">
            <p>&copy; <?php echo date('Y'); ?> GalaGo Events Monitoring System. All rights reserved.</p>
        </div>
    </footer>
</div>

<div id='inv_display'>
<!-- Additional display area as per professor's template -->
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
<script src="js/script.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize all glamorous effects
    createGlamorousParticles();
    createPulsingBackground();
    createAdvancedScrollAnimations();
    createMagneticEffect();
    createTextShimmer();
    
    // Staggered animation for all elements
    const animateElements = document.querySelectorAll('.animate-fade-in, .animate-slide-up, .dashboard-card, .feature-card, .hero-stat');
    animateElements.forEach((element, index) => {
        element.style.opacity = '0';
        element.style.transform = 'translateY(50px)';
        element.style.transition = 'all 0.8s cubic-bezier(0.4, 0, 0.2, 1)';
        
        setTimeout(() => {
            element.style.opacity = '1';
            element.style.transform = 'translateY(0)';
        }, index * 150);
    });

    // Animated counter for stats
    const counters = document.querySelectorAll('.stat-number');
    const observerOptions = {
        threshold: 0.7
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                animateCounter(entry.target);
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    counters.forEach(counter => {
        observer.observe(counter);
    });

    function animateCounter(element) {
        const target = parseInt(element.dataset.target || element.textContent.replace(/[₱,]/g, ''));
        if (target && target > 0 && target < 10000) {
            let current = 0;
            const increment = target / 60;
            const timer = setInterval(() => {
                current += increment;
                if (current >= target) {
                    element.textContent = element.textContent.includes('₱') 
                        ? `₱${target.toLocaleString()}` 
                        : target.toLocaleString();
                    clearInterval(timer);
                } else {
                    const displayValue = Math.floor(current);
                    element.textContent = element.textContent.includes('₱') 
                        ? `₱${displayValue.toLocaleString()}` 
                        : displayValue.toLocaleString();
                }
            }, 16);
        }
    }

    // Enhanced hover effects with advanced particles
    document.querySelectorAll('.dashboard-card, .feature-card, .hero-stat').forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.filter = 'brightness(1.15) saturate(1.2)';
            createAdvancedParticleEffect(this);
        });

        card.addEventListener('mouseleave', function() {
            this.style.filter = 'brightness(1) saturate(1)';
        });
    });

    // Button ripple effects
    document.querySelectorAll('.btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            createRippleEffect(this, e);
        });
    });

    // Auto-dismiss alerts with fade effect
    document.querySelectorAll('.alert').forEach(alert => {
        setTimeout(() => {
            if (alert && alert.classList.contains('show')) {
                alert.style.opacity = '0';
                alert.style.transform = 'translateX(100%)';
                setTimeout(() => {
                    if (alert.parentElement) {
                        alert.remove();
                    }
                }, 300);
            }
        }, 5000);
    });

    // Navbar scroll effect
    let lastScrollTop = 0;
    const navbar = document.querySelector('.navbar');
    
    window.addEventListener('scroll', function() {
        let scrollTop = window.pageYOffset || document.documentElement.scrollTop;
        
        if (scrollTop > lastScrollTop) {
            navbar.style.transform = 'translateY(-100%)';
        } else {
            navbar.style.transform = 'translateY(0)';
        }
        
        if (scrollTop > 100) {
            navbar.style.background = 'rgba(255, 255, 255, 0.25)';
            navbar.style.boxShadow = '0 8px 32px rgba(139, 92, 246, 0.2)';
        }
        
        lastScrollTop = scrollTop;
    });
});

// Enhanced particle effects and glamorous animations
function createGlamorousParticles() {
    const colors = ['#8B5CF6', '#EC4899', '#10B981', '#F59E0B', '#3B82F6', '#EF4444'];
    
    for (let i = 0; i < 20; i++) {
        setTimeout(() => {
            const particle = document.createElement('div');
            particle.style.cssText = `
                position: fixed;
                width: ${Math.random() * 8 + 4}px;
                height: ${Math.random() * 8 + 4}px;
                background: ${colors[Math.floor(Math.random() * colors.length)]};
                border-radius: 50%;
                pointer-events: none;
                z-index: 1;
                box-shadow: 0 0 20px currentColor;
                left: ${Math.random() * 100}vw;
                top: 100vh;
            `;
            
            document.body.appendChild(particle);
            
            // Animate particle
            particle.animate([
                { 
                    transform: 'translateY(0) rotate(0deg) scale(1)',
                    opacity: 0 
                },
                { 
                    transform: 'translateY(-20vh) rotate(180deg) scale(1.5)',
                    opacity: 1,
                    offset: 0.1
                },
                { 
                    transform: 'translateY(-80vh) rotate(360deg) scale(1)',
                    opacity: 1,
                    offset: 0.9
                },
                { 
                    transform: 'translateY(-100vh) rotate(540deg) scale(0)',
                    opacity: 0 
                }
            ], {
                duration: Math.random() * 8000 + 12000,
                easing: 'cubic-bezier(0.4, 0, 0.2, 1)'
            }).onfinish = () => {
                if (particle.parentElement) {
                    particle.remove();
                }
            };
        }, i * 300);
    }
    
    // Restart particle generation
    setTimeout(createGlamorousParticles, 15000);
}

// Advanced hover effects with multiple particle bursts
function createAdvancedParticleEffect(element) {
    const colors = ['#8B5CF6', '#EC4899', '#10B981', '#F59E0B', '#3B82F6', '#EF4444'];
    const rect = element.getBoundingClientRect();
    
    // Create multiple particle bursts
    for (let burst = 0; burst < 3; burst++) {
        setTimeout(() => {
            for (let i = 0; i < 12; i++) {
                const particle = document.createElement('div');
                particle.style.cssText = `
                    position: fixed;
                    width: ${Math.random() * 6 + 3}px;
                    height: ${Math.random() * 6 + 3}px;
                    background: ${colors[Math.floor(Math.random() * colors.length)]};
                    border-radius: 50%;
                    pointer-events: none;
                    left: ${rect.left + rect.width / 2}px;
                    top: ${rect.top + rect.height / 2}px;
                    z-index: 1000;
                    box-shadow: 0 0 15px currentColor;
                `;
                
                document.body.appendChild(particle);
                
                const angle = (Math.PI * 2 * i) / 12;
                const velocity = 60 + Math.random() * 40;
                const duration = 1000 + Math.random() * 500;
                
                particle.animate([
                    { 
                        transform: 'translate(-50%, -50%) scale(0) rotate(0deg)',
                        opacity: 1
                    },
                    { 
                        transform: 'translate(-50%, -50%) scale(1.5) rotate(180deg)',
                        opacity: 1,
                        offset: 0.3
                    },
                    { 
                        transform: `translate(${Math.cos(angle) * velocity - 50}%, ${Math.sin(angle) * velocity - 50}%) scale(0) rotate(360deg)`,
                        opacity: 0
                    }
                ], {
                    duration: duration,
                    easing: 'cubic-bezier(0.4, 0, 0.2, 1)'
                }).onfinish = () => {
                    if (particle.parentElement) {
                        particle.remove();
                    }
                };
            }
        }, burst * 150);
    }
}

// Create pulsing background effects
function createPulsingBackground() {
    const heroSection = document.querySelector('.hero-section');
    if (heroSection) {
        const pulse = document.createElement('div');
        pulse.style.cssText = `
            position: absolute;
            top: 20%;
            left: 10%;
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(139, 92, 246, 0.1) 0%, transparent 70%);
            border-radius: 50%;
            pointer-events: none;
            z-index: 0;
        `;
        
        heroSection.appendChild(pulse);
        
        pulse.animate([
            { transform: 'scale(1)', opacity: 0.3 },
            { transform: 'scale(1.5)', opacity: 0.1 },
            { transform: 'scale(1)', opacity: 0.3 }
        ], {
            duration: 4000,
            iterations: Infinity,
            easing: 'ease-in-out'
        });
    }
}

// Enhanced scroll animations with intersection observer
function createAdvancedScrollAnimations() {
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry, index) => {
            if (entry.isIntersecting) {
                entry.target.style.transform = 'translateY(0) rotateX(0)';
                entry.target.style.opacity = '1';
                
                // Add staggered particle effects for feature cards
                if (entry.target.classList.contains('feature-card')) {
                    setTimeout(() => {
                        createAdvancedParticleEffect(entry.target);
                    }, index * 200);
                }
            }
        });
    }, observerOptions);
    
    // Observe all cards and sections
    document.querySelectorAll('.feature-card, .dashboard-card, .hero-stat').forEach((element, index) => {
        element.style.opacity = '0';
        element.style.transform = 'translateY(50px) rotateX(10deg)';
        element.style.transition = `all 0.8s cubic-bezier(0.4, 0, 0.2, 1) ${index * 0.1}s`;
        observer.observe(element);
    });
}

// Magnetic mouse effect for cards
function createMagneticEffect() {
    document.querySelectorAll('.feature-card, .dashboard-card').forEach(card => {
        card.addEventListener('mousemove', function(e) {
            const rect = this.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            const centerX = rect.width / 2;
            const centerY = rect.height / 2;
            const rotateX = (y - centerY) / 10;
            const rotateY = (centerX - x) / 10;
            
            this.style.transform = `
                translateY(-15px) 
                rotateX(${rotateX}deg) 
                rotateY(${rotateY}deg) 
                scale(1.02)
            `;
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) rotateX(0) rotateY(0) scale(1)';
        });
    });
}

// Text shimmer effect
function createTextShimmer() {
    const titles = document.querySelectorAll('.hero-title, .section-title, .feature-title, .card-title');
    titles.forEach(title => {
        title.style.background = 'linear-gradient(45deg, #ffffff 25%, #e0e7ff 50%, #ffffff 75%)';
        title.style.backgroundSize = '200% 100%';
        title.style.webkitBackgroundClip = 'text';
        title.style.webkitTextFillColor = 'transparent';
        title.style.backgroundClip = 'text';
        title.style.animation = 'shimmerText 3s ease-in-out infinite';
    });
}

// Create ripple effect
function createRippleEffect(element, event) {
    const ripple = document.createElement('span');
    const rect = element.getBoundingClientRect();
    const size = Math.max(rect.width, rect.height);
    const x = event.clientX - rect.left - size / 2;
    const y = event.clientY - rect.top - size / 2;

    ripple.style.cssText = `
        position: absolute;
        width: ${size}px;
        height: ${size}px;
        left: ${x}px;
        top: ${y}px;
        background: rgba(255, 255, 255, 0.3);
        border-radius: 50%;
        transform: scale(0);
        animation: ripple 0.6s ease-out;
        pointer-events: none;
        z-index: 1000;
    `;

    element.style.position = 'relative';
    element.style.overflow = 'hidden';
    element.appendChild(ripple);

    setTimeout(() => {
        if (ripple.parentElement) {
            ripple.remove();
        }
    }, 600);
}

// Add shimmer and ripple animations
const shimmerStyle = document.createElement('style');
shimmerStyle.textContent = `
    @keyframes shimmerText {
        0% { background-position: -200% 0; }
        100% { background-position: 200% 0; }
    }
    
    @keyframes floatingElements {
        0%, 100% { transform: translateY(0px) rotate(0deg); }
        50% { transform: translateY(-20px) rotate(5deg); }
    }
    
    @keyframes ripple {
        to {
            transform: scale(4);
            opacity: 0;
        }
    }
    
    .feature-card:nth-child(odd) {
        animation: floatingElements 6s ease-in-out infinite;
        animation-delay: 0s;
    }
    
    .feature-card:nth-child(even) {
        animation: floatingElements 6s ease-in-out infinite;
        animation-delay: -3s;
    }
`;
document.head.appendChild(shimmerStyle);
</script>

</body>
</html>