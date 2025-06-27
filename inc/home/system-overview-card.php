
    
    <!-- System Overview Card -->
    <div class="col-md-6 d-flex">
      <div class="glass-card system-overview-card flex-fill d-flex flex-column">
        <div class="glass-header rounded-top">
          <h5 class="mb-0"><i class="bi bi-bar-chart"></i> System Overview</h5>
        </div>
        <div class="card-body d-flex flex-column flex-grow-1">
          <div class="flex-grow-1 d-flex flex-column justify-content-center align-items-center">
            <div class="overview-stats py-5 text-center w-100">
              <h2 class="overview-number mb-0"><?php echo $total_events; ?></h2>
              <p class="overview-label mb-0">Total Events</p>
            </div>
          </div>
          <div class="mt-auto d-grid">
            <a href="events.php" class="overview-btn">
              <i class="bi bi-plus-circle"></i> Manage Events
            </a>
          </div>
        </div>
      </div>
    </div>