<!-- Upcoming Events Section -->
<section class="upcoming-section container-fluid px-4 mt-5 ">
  <div class="glass-card upcoming-events-wrapper p-4">
    <!-- Title Header -->
    <div class="upcoming-header d-flex align-items-center justify-content-center gap-2 mb-4">
      <i class="bi bi-calendar-event-fill upcoming-icon"></i>
      <h2 class="upcoming-heading mb-0">Upcoming Events</h2>
    </div>


    <div class="card-body pt-2">
      <?php if ($upcoming_result->num_rows > 0): ?>
        <div class="row g-4">
          <?php while($event = $upcoming_result->fetch_assoc()): ?>
            <?php
              $eventDate = strtotime($event['event_date']);
              $day = date('d', $eventDate);
              $month = date('M', $eventDate);
            ?>
            <div class="col-md-6 col-lg-4">
              <div class="calendar-event-card d-flex align-items-center p-3">
                <!-- Date -->
                <div class="calendar-date-box text-center me-3">
                  <div class="calendar-month"><?php echo $month; ?></div>
                  <div class="calendar-day"><?php echo $day; ?></div>
                </div>
                <!-- Info -->
                <div class="calendar-event-info">
                  <h6 class="event-name mb-1"><?php echo htmlspecialchars($event['event_name']); ?></h6>
                  <small class="event-meta">
                    <i class="bi bi-geo-alt-fill"></i> <?php echo htmlspecialchars($event['event_location']); ?>
                  </small>
                </div>
              </div>
            </div>
          <?php endwhile; ?>
        </div>
      <?php else: ?>
        <div class="text-center text-muted py-5">
          <i class="bi bi-calendar-x display-4 mb-3"></i>
          <p class="mb-0">No upcoming events scheduled</p>
        </div>
      <?php endif; ?>
    </div>
  </div>
</section>
