<div class="list-view" id="listView">
    <div class="row">
        <div class="col-12">
            <?php 
            $result->data_seek(0); // Reset result pointer
            while($event = $result->fetch_assoc()): 
                // Determine event status
                $event_timestamp = strtotime($event['event_date']);
                $today_timestamp = strtotime('today');
                
                if ($event_timestamp < $today_timestamp) {
                    $status = 'past';
                    $circle_class = 'past';
                    $badge_class = 'past';
                    $status_text = 'Past Event';
                } elseif ($event_timestamp == $today_timestamp) {
                    $status = 'today';
                    $circle_class = 'today';
                    $badge_class = 'today';
                    $status_text = 'Today';
                } else {
                    $status = 'upcoming';
                    $circle_class = 'upcoming';
                    $badge_class = 'upcoming';
                    $status_text = 'Upcoming';
                }
            ?>
            <div class="event-list-item status-<?php echo $status; ?> p-4 mb-4 d-flex align-items-center position-relative">
                <!-- Status Badge -->
                <div class="status-badge <?php echo $badge_class; ?>">
                    <?php echo $status_text; ?>
                </div>

                <!-- Date Circle -->
                <div class="event-date-circle <?php echo $circle_class; ?> me-4 flex-shrink-0">
                    <div class="event-date-day"><?php echo date('d', strtotime($event['event_date'])); ?></div>
                    <div class="event-date-month"><?php echo date('M', strtotime($event['event_date'])); ?></div>
                </div>

                <!-- Event Content -->
                <div class="event-content me-4">
                    <h4 class="event-title"><?php echo htmlspecialchars($event['event_name']); ?></h4>
                    
                    <div class="event-meta">
                        <div class="event-meta-item">
                            <i class="bi bi-geo-alt"></i>
                            <span><?php echo htmlspecialchars($event['event_location']); ?></span>
                        </div>
                        <div class="event-meta-item">
                            <i class="bi bi-calendar"></i>
                            <span><?php echo date('l, F d, Y', strtotime($event['event_date'])); ?></span>
                        </div>
                        <div class="event-meta-item">
                            <i class="bi bi-clock"></i>
                            <span>Added <?php echo date('M d, Y', strtotime($event['date_added'])); ?></span>
                        </div>
                    </div>

                    <?php if (!empty($event['event_remarks'])): ?>
                    <div class="event-description">
                        <i class="bi bi-chat-text me-2"></i>
                        <?php echo htmlspecialchars(substr($event['event_remarks'], 0, 120)); ?>
                        <?php echo strlen($event['event_remarks']) > 120 ? '...' : ''; ?>
                    </div>
                    <?php endif; ?>

                    <div class="event-actions">
                        <button type="button" class="btn btn-outline-info btn-sm" 
                                data-bs-toggle="modal" data-bs-target="#viewModal<?php echo $event['id']; ?>">
                            <i class="bi bi-eye"></i> View Details
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

                <!-- Price Tag -->
                <div class="price-tag flex-shrink-0">
                    ₱<?php echo number_format($event['pricing'], 2); ?>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
</div>