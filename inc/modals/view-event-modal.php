<div class="modal fade" id="viewModal<?php echo $event['id']; ?>" tabindex="-1" aria-labelledby="viewModalLabel<?php echo $event['id']; ?>" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="viewModalLabel<?php echo $event['id']; ?>">
                    <i class="bi bi-eye"></i> Event Details
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6><i class="bi bi-calendar-event text-primary"></i> Event Name:</h6>
                        <p class="fs-5 fw-bold"><?php echo htmlspecialchars($event['event_name']); ?></p>

                        <h6><i class="bi bi-geo-alt text-primary"></i> Location:</h6>
                        <p><?php echo htmlspecialchars($event['event_location']); ?></p>

                        <h6><i class="bi bi-calendar text-primary"></i> Event Date:</h6>
                        <p><?php echo date('F d, Y (l)', strtotime($event['event_date'])); ?></p>
                    </div>
                    <div class="col-md-6">
                        <h6><i class="bi bi-cash text-success"></i> Pricing:</h6>
                        <p class="h4 text-success">₱<?php echo number_format($event['pricing'], 2); ?></p>

                        <h6><i class="bi bi-clock text-muted"></i> Date Added:</h6>
                        <p><?php echo date('F d, Y \a\t g:i A', strtotime($event['date_added'])); ?></p>

                        <h6><i class="bi bi-info-circle text-warning"></i> Status:</h6>
                        <p>
                            <?php if (strtotime($event['event_date']) < strtotime('today')): ?>
                                <span class="badge bg-secondary fs-6">Past Event</span>
                            <?php elseif (strtotime($event['event_date']) == strtotime('today')): ?>
                                <span class="badge bg-warning fs-6">Today</span>
                            <?php else: ?>
                                <span class="badge bg-success fs-6">Upcoming</span>
                            <?php endif; ?>
                        </p>
                    </div>
                </div>

                <?php if (!empty($event['event_remarks'])): ?>
                    <div class="row">
                        <div class="col-12">
                            <h6><i class="bi bi-chat-text text-info"></i> Event Remarks:</h6>
                            <div class="bg-light p-3 rounded">
                                <?php echo nl2br(htmlspecialchars($event['event_remarks'])); ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle"></i> Close
                </button>
                <a href="edit_event.php?id=<?php echo $event['id']; ?>" class="btn btn-primary">
                    <i class="bi bi-pencil"></i> Edit Event
                </a>
            </div>
        </div>
    </div>
</div>