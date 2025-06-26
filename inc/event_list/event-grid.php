<div class="grid-view active" id="gridView">
    <div class="row">
        <?php while($event = $result->fetch_assoc()): 
            // Determine event status
            $event_timestamp = strtotime($event['event_date']);
            $today_timestamp = strtotime('today');
            
            if ($event_timestamp < $today_timestamp) {
                $status = 'past';
                $status_class = 'event-status-past';
                $status_badge = 'bg-secondary';
                $status_text = 'Past';
            } elseif ($event_timestamp == $today_timestamp) {
                $status = 'today';
                $status_class = 'event-status-today';
                $status_badge = 'bg-warning';
                $status_text = 'Today';
            } else {
                $status = 'upcoming';
                $status_class = 'event-status-upcoming';
                $status_badge = 'bg-success';
                $status_text = 'Upcoming';
            }
        ?>
        <div class="col-lg-6 col-xl-4 mb-4">
            <div class="card event-card <?php echo $status_class; ?>">
                <div class="card-body">
                    <!-- Event Date Display -->
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="text-center">
                            <div class="event-date-large text-primary">
                                <?php echo date('d', strtotime($event['event_date'])); ?>
                            </div>
                            <div class="event-month text-muted">
                                <?php echo date('M Y', strtotime($event['event_date'])); ?>
                            </div>
                        </div>
                        <span class="badge <?php echo $status_badge; ?> fs-6">
                            <?php echo $status_text; ?>
                        </span>
                    </div>

                    <!-- Event Details -->
                    <h5 class="card-title text-dark mb-2">
                        <?php echo htmlspecialchars($event['event_name']); ?>
                    </h5>
                    
                    <div class="mb-2">
                        <i class="bi bi-geo-alt text-primary"></i>
                        <span class="text-muted"><?php echo htmlspecialchars($event['event_location']); ?></span>
                    </div>

                    <div class="mb-3">
                        <i class="bi bi-calendar text-info"></i>
                        <span class="text-muted"><?php echo date('l, F d, Y', strtotime($event['event_date'])); ?></span>
                    </div>

                    <?php if (!empty($event['event_remarks'])): ?>
                    <div class="mb-3">
                        <small class="text-muted">
                            <i class="bi bi-chat-text"></i>
                            <?php echo htmlspecialchars(substr($event['event_remarks'], 0, 80)); ?>
                            <?php echo strlen($event['event_remarks']) > 80 ? '...' : ''; ?>
                        </small>
                    </div>
                    <?php endif; ?>

                    <!-- Pricing -->
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="pricing-badge text-success">
                            ₱<?php echo number_format($event['pricing'], 2); ?>
                        </span>
                        <small class="text-muted">
                            Added: <?php echo date('M d', strtotime($event['date_added'])); ?>
                        </small>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <button type="button" class="btn btn-outline-info btn-sm" 
                                data-bs-toggle="modal" data-bs-target="#viewModal<?php echo $event['id']; ?>">
                            <i class="bi bi-eye"></i> View
                        </button>
                        <a href="edit_event.php?id=<?php echo $event['id']; ?>" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-pencil"></i> Edit
                        </a>
                        <button type="button" class="btn btn-outline-danger btn-sm" 
                                data-bs-toggle="modal" data-bs-target="#deleteModal<?php echo $event['id']; ?>">
                            <i class="bi bi-trash"></i> Delete
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
</div>
