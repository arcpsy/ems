<section class="hero-section d-flex align-items-stretch">
  <div class="container-lg">
    <div class="row h-100 fadeIn">
      <!-- Left Column (8 cols wide) -->
      <div class="col-lg-8 d-flex flex-column justify-content-end h-100">
        <!-- All content stacked at the bottom -->
        <div>
          <!-- Hero Content -->
          <h1 class="hero-title">Welcome to GalaGo</h1>
          <p class="hero-subtitle">
            Manage and track all your company events efficiently.
          </p>
          <p class="hero-description">
            Keep track of event details, locations, dates, pricing and more with GalaGo's comprehensive monitoring system.
          </p>
            <div class="hero-cta mt-3">
            <a href="events.php" class="btn btn-gradient-create">
                <i class="bi bi-plus-circle"></i> Create Event
            </a>
            </div>


          <!-- Revenue -->
          <div class="mt-5">
            <div class="hero-stat text-center">
              <span class="stat-number">₱<?php echo number_format($total_revenue, 0); ?></span>
              <span class="stat-label">Revenue</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Right Column (4 cols wide) -->
      <div class="col-lg-4 d-flex flex-column justify-content-end h-100">
        <div>
          <div class="hero-stat text-center mb-4">
            <span class="stat-number"><?php echo $total_events; ?></span>
            <span class="stat-label">Total Events</span>
          </div>
          <div class="hero-stat text-center mb-4">
            <span class="stat-number"><?php echo $today_events; ?></span>
            <span class="stat-label">Today</span>
          </div>
          <div class="hero-stat text-center">
            <span class="stat-number"><?php echo $upcoming_result->num_rows; ?></span>
            <span class="stat-label">Upcoming</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
