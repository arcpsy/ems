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