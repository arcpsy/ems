<?php
session_start();
require_once 'config.php';

$page_title = "GalaGo - Events Monitoring System - Home";

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
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GalaGo Events - Ultimate Event Management System</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <style>
        .hero-section {
            min-height: 70vh;
            display: flex;
            align-items: center;
            position: relative;
            overflow: hidden;
        }

        .hero-content {
            position: relative;
            z-index: 2;
        }

        .hero-title {
            font-size: 4rem;
            font-weight: 900;
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 1.5rem;
            text-shadow: 0 4px 8px rgba(79, 172, 254, 0.3);
        }

        .hero-subtitle {
            font-size: 1.5rem;
            color: rgba(255, 255, 255, 0.9);
            margin-bottom: 2rem;
            font-weight: 400;
        }

        .hero-cta {
            display: flex;
            gap: 1rem;
            margin-bottom: 3rem;
        }

        .hero-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            gap: 2rem;
            margin-top: 3rem;
        }

        .hero-stat {
            text-align: center;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 16px;
            padding: 1.5rem;
            transition: all 0.3s ease;
        }

        .hero-stat:hover {
            transform: translateY(-5px);
            background: rgba(255, 255, 255, 0.15);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: 800;
            background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            display: block;
        }

        .stat-label {
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.9rem;
            font-weight: 600;
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin: 3rem 0;
        }

        .dashboard-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 24px;
            padding: 2rem;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            height: 100%;
        }

        .dashboard-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #4facfe, #00f2fe, #43e97b);
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }

        .dashboard-card:hover::before {
            transform: scaleX(1);
        }

        .dashboard-card:hover {
            transform: translateY(-10px) rotateX(5deg);
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.3);
        }

        .card-icon {
            width: 60px;
            height: 60px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
            color: white;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
        }

        .card-icon.success { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
        .card-icon.info { background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); }
        .card-icon.warning { background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); }
        .card-icon.primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }

        .card-title {
            color: white;
            font-size: 1.3rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .card-description {
            color: rgba(255, 255, 255, 0.8);
            line-height: 1.6;
            margin-bottom: 1.5rem;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin: 3rem 0;
        }

        .feature-card {
            text-align: center;
            padding: 2rem;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .feature-card::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            background: radial-gradient(circle, rgba(79, 172, 254, 0.1) 0%, transparent 70%);
            transform: translate(-50%, -50%);
            transition: all 0.4s ease;
        }

        .feature-card:hover::before {
            width: 200%;
            height: 200%;
        }

        .feature-card:hover {
            transform: translateY(-10px) scale(1.05);
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(79, 172, 254, 0.3);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
        }

        .feature-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            transition: all 0.3s ease;
        }

        .feature-card:hover .feature-icon {
            transform: scale(1.2) rotateY(360deg);
        }

        .feature-title {
            color: white;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .feature-description {
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.9rem;
            line-height: 1.5;
        }

        .upcoming-events {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 20px;
            padding: 2rem;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .event-item {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 1rem;
            margin-bottom: 1rem;
            border-left: 4px solid transparent;
            transition: all 0.3s ease;
        }

        .event-item:hover {
            background: rgba(255, 255, 255, 0.15);
            border-left-color: #4facfe;
            transform: translateX(10px);
        }

        .event-name {
            color: white;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .event-details {
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.9rem;
        }

        .team-credits {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 20px;
            padding: 2rem;
            text-align: center;
            border: 1px solid rgba(255, 255, 255, 0.1);
            margin: 3rem 0;
        }

        .team-title {
            color: white;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .team-members {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 1rem;
        }

        .team-member {
            background: rgba(255, 255, 255, 0.1);
            color: rgba(255, 255, 255, 0.9);
            padding: 0.5rem 1rem;
            border-radius: 25px;
            font-size: 0.9rem;
            font-weight: 500;
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
        }

        .team-member:hover {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            transform: scale(1.05);
            box-shadow: 0 4px 15px rgba(79, 172, 254, 0.4);
        }

        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.5rem;
            }
            
            .hero-subtitle {
                font-size: 1.2rem;
            }
            
            .hero-cta {
                flex-direction: column;
                align-items: center;
            }
            
            .hero-stats {
                grid-template-columns: repeat(2, 1fr);
                gap: 1rem;
            }
            
            .dashboard-grid,
            .features-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }
            
            .team-members {
                flex-direction: column;
                align-items: center;
            }
        }

        .loading-animation {
            opacity: 0;
            transform: translateY(30px);
            animation: fadeInUp 0.8s ease-out forwards;
        }

        @keyframes fadeInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .floating-shape {
            position: absolute;
            border-radius: 50%;
            background: linear-gradient(135deg, rgba(79, 172, 254, 0.1) 0%, rgba(0, 242, 254, 0.1) 100%);
            animation: float 6s ease-in-out infinite;
        }

        .floating-shape:nth-child(1) {
            top: 10%;
            left: 10%;
            width: 80px;
            height: 80px;
            animation-delay: 0s;
        }

        .floating-shape:nth-child(2) {
            top: 20%;
            right: 10%;
            width: 60px;
            height: 60px;
            animation-delay: 2s;
        }

        .floating-shape:nth-child(3) {
            bottom: 20%;
            left: 20%;
            width: 100px;
            height: 100px;
            animation-delay: 4s;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
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
                        <a class="nav-link active" href="index.php">
                            <i class="bi bi-house"></i> Home
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="events.php">
                            <i class="bi bi-list"></i> All Events
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="floating-shape"></div>
    <div class="floating-shape"></div>
    <div class="floating-shape"></div>

    <div class="container">
        <section class="hero-section">
            <div class="hero-content loading-animation">
                <div class="row align-items-center">
                    <div class="col-lg-8">
                        <h1 class="hero-title">Welcome to GalaGo</h1>
                        <p class="hero-subtitle">
                            The ultimate event management platform that transforms how you create, manage, and track events
                        </p>
                        <div class="hero-cta">
                            <a href="events.php" class="btn btn-success btn-lg">
                                <i class="bi bi-eye"></i> Explore Events
                            </a>
                            <a href="events.php" class="btn btn-light btn-lg">
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

        <section class="loading-animation" style="animation-delay: 0.2s;">
            <div class="dashboard-grid">
                <div class="dashboard-card">
                    <div class="card-icon success">
                        <i class="bi bi-bar-chart"></i>
                    </div>
                    <h3 class="card-title">System Overview</h3>
                    <p class="card-description">
                        Get comprehensive insights into your event portfolio with real-time statistics and analytics.
                    </p>
                    <div class="text-center mb-3">
                        <div class="stat-number" style="font-size: 3rem;"><?php echo $total_events; ?></div>
                        <div class="stat-label">Events Managed</div>
                    </div>
                    <a href="events.php" class="btn btn-success w-100">
                        <i class="bi bi-plus-circle"></i> Manage Events
                    </a>
                </div>

                <div class="dashboard-card">
                    <div class="card-icon info">
                        <i class="bi bi-megaphone-fill"></i>
                    </div>
                    <h3 class="card-title">Upcoming Events</h3>
                    <div class="upcoming-events">
                        <?php if ($upcoming_result->num_rows > 0): ?>
                            <?php while($event = $upcoming_result->fetch_assoc()): ?>
                                <div class="event-item">
                                    <div class="event-name"><?php echo htmlspecialchars($event['event_name']); ?></div>
                                    <div class="event-details">
                                        <i class="bi bi-calendar"></i> <?php echo date('M d, Y', strtotime($event['event_date'])); ?>
                                        <br>
                                        <i class="bi bi-geo-alt"></i> <?php echo htmlspecialchars($event['event_location']); ?>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <div class="text-center text-white opacity-75">
                                <i class="bi bi-calendar-x display-4 mb-3"></i>
                                <p>No upcoming events scheduled</p>
                                <a href="events.php" class="btn btn-info btn-sm">
                                    <i class="bi bi-plus"></i> Create First Event
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="dashboard-card">
                    <div class="card-icon warning">
                        <i class="bi bi-graph-up"></i>
                    </div>
                    <h3 class="card-title">Performance Metrics</h3>
                    <p class="card-description">
                        Track your event success rates and revenue generation across all your managed events.
                    </p>
                    <div class="row text-center mb-3">
                        <div class="col-6">
                            <div class="stat-number" style="font-size: 2rem;"><?php echo $past_events; ?></div>
                            <div class="stat-label">Completed</div>
                        </div>
                        <div class="col-6">
                            <div class="stat-number" style="font-size: 2rem;">₱<?php echo number_format($total_revenue, 0); ?></div>
                            <div class="stat-label">Revenue</div>
                        </div>
                    </div>
                    <a href="events.php" class="btn btn-warning w-100">
                        <i class="bi bi-graph-up"></i> View Analytics
                    </a>
                </div>
            </div>
        </section>

        <section class="loading-animation" style="animation-delay: 0.4s;">
            <div class="text-center mb-5">
                <h2 class="text-white mb-3">Powerful Features</h2>
                <p class="text-white opacity-75">Everything you need to manage events like a pro</p>
            </div>
            
            <div class="features-grid">
                <div class="feature-card">
                    <i class="bi bi-plus-circle feature-icon"></i>
                    <h6 class="feature-title">Create Events</h6>
                    <p class="feature-description">
                        Design and create stunning events with comprehensive details, pricing, and scheduling options.
                    </p>
                </div>
                
                <div class="feature-card">
                    <i class="bi bi-pencil-square feature-icon"></i>
                    <h6 class="feature-title">Edit & Update</h6>
                    <p class="feature-description">
                        Seamlessly update event information, modify schedules, and adjust pricing in real-time.
                    </p>
                </div>
                
                <div class="feature-card">
                    <i class="bi bi-trash feature-icon"></i>
                    <h6 class="feature-title">Smart Management</h6>
                    <p class="feature-description">
                        Efficiently organize and remove events with intelligent confirmation and backup systems.
                    </p>
                </div>
                
                <div class="feature-card">
                    <i class="bi bi-eye feature-icon"></i>
                    <h6 class="feature-title">Comprehensive View</h6>
                    <p class="feature-description">
                        Access detailed event insights with multiple viewing modes and advanced filtering options.
                    </p>
                </div>
                
                <div class="feature-card">
                    <i class="bi bi-search feature-icon"></i>
                    <h6 class="feature-title">Advanced Search</h6>
                    <p class="feature-description">
                        Find events instantly with powerful search capabilities and intelligent filtering systems.
                    </p>
                </div>
                
                <div class="feature-card">
                    <i class="bi bi-phone feature-icon"></i>
                    <h6 class="feature-title">Mobile Ready</h6>
                    <p class="feature-description">
                        Access your events anywhere with our fully responsive design and mobile optimization.
                    </p>
                </div>
            </div>
        </section>

        <section class="team-credits loading-animation" style="animation-delay: 0.6s;">
            <h4 class="team-title">Meet Our Amazing Development Team</h4>
            <p class="text-white opacity-75 mb-4">
                Crafted with passion and dedication by talented developers
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
                    <a href="events.php" class="btn btn-outline-light btn-lg">
                        <i class="bi bi-info-circle"></i> Learn More
                    </a>
                </div>
            </div>
        </section>
    </div>

    <footer class="mt-5 py-4">
        <div class="container text-center">
            <div class="mb-3">
                <a href="index.php" class="navbar-brand">
                    <i class="bi bi-collection"></i> GalaGo Events
                </a>
            </div>
            <p class="text-white opacity-75 mb-2">
                &copy; <?php echo date('Y'); ?> GalaGo Events Monitoring System. All rights reserved.
            </p>
            <small class="text-white opacity-50">
                Empowering event organizers with cutting-edge technology
            </small>
        </div>
    </footer>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const loadingElements = document.querySelectorAll('.loading-animation');
            loadingElements.forEach((element, index) => {
                element.style.animationDelay = `${index * 0.2}s`;
            });

            const counters = document.querySelectorAll('.stat-number');
            counters.forEach(counter => {
                const target = parseInt(counter.textContent.replace(/[₱,]/g, ''));
                let current = 0;
                const increment = target / 100;
                const timer = setInterval(() => {
                    current += increment;
                    if (current >= target) {
                        counter.textContent = counter.textContent.includes('₱') 
                            ? `₱${target.toLocaleString()}` 
                            : target.toLocaleString();
                        clearInterval(timer);
                    } else {
                        const displayValue = Math.floor(current);
                        counter.textContent = counter.textContent.includes('₱') 
                            ? `₱${displayValue.toLocaleString()}` 
                            : displayValue.toLocaleString();
                    }
                }, 20);
            });

            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
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

            const cards = document.querySelectorAll('.dashboard-card, .feature-card');
            cards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-10px) rotateX(5deg)';
                });

                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0) rotateX(0)';
                });
            });

            window.addEventListener('scroll', function() {
                const scrolled = window.pageYOffset;
                const shapes = document.querySelectorAll('.floating-shape');
                shapes.forEach((shape, index) => {
                    const speed = 0.5 + (index * 0.1);
                    shape.style.transform = `translateY(${scrolled * speed}px)`;
                });
            });

            document.addEventListener('mousemove', function(e) {
                const mouseX = e.clientX / window.innerWidth;
                const mouseY = e.clientY / window.innerHeight;
                
                const shapes = document.querySelectorAll('.floating-shape');
                shapes.forEach((shape, index) => {
                    const speed = (index + 1) * 10;
                    shape.style.transform += ` translate(${mouseX * speed}px, ${mouseY * speed}px)`;
                });
            });

            const buttons = document.querySelectorAll('.btn');
            buttons.forEach(button => {
                button.addEventListener('click', function() {
                    this.style.transform = 'scale(0.95)';
                    setTimeout(() => {
                        this.style.transform = 'scale(1)';
                    }, 150);
                });
            });

            const titles = document.querySelectorAll('.hero-title, .card-title, .feature-title');
            titles.forEach(title => {
                title.addEventListener('mouseenter', function() {
                    this.style.textShadow = '0 0 20px rgba(79, 172, 254, 0.6)';
                });

                title.addEventListener('mouseleave', function() {
                    this.style.textShadow = '0 4px 8px rgba(79, 172, 254, 0.3)';
                });
            });

            setTimeout(() => {
                document.body.classList.add('loaded');
            }, 1000);
        });

        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);

        document.querySelectorAll('.dashboard-card, .feature-card, section').forEach(el => {
            observer.observe(el);
        });
    </script>
</body>
</html>
