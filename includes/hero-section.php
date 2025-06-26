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