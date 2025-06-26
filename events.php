<?php
session_start();
require_once 'config/config.php';
require_once 'inc/event_list/event-handler.php';

$page_title = "GalaGo - Events Monitoring System - Events";

$page_css = "css/events.css";
$page_js = "js/events.js";
$body_class = "d-flex flex-column min-vh-100";

ob_start();
?>

<div>

    <div class="container-sm mt-4 flex-grow-1">
       <h1 class="mb-4">
            <i class="bi bi-clipboard-data" style="color: #818589;"></i> Events Management
        </h1>

        <!-- Add Event Button -->
        <section id="add-event-section" class="mb-4">
            <div class="d-flex justify-content-end">
                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addEventModal">
                    <i class="bi bi-plus-circle"></i> Add Event
                </button>
            </div>
        </section>

        <!-- Events Section -->
        <section>
            <!-- Events Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="mb-0">
                    <i class="bi bi-calendar-check text-dark"></i> All Events (<?php echo $result->num_rows; ?>)
                </h3>
                <div class="view-toggle btn-group" role="group">
                    <button type="button" class="btn active" id="gridBtn" onclick="toggleView('grid')">
                        <i class="bi bi-grid-3x3-gap"></i> Grid
                    </button>
                    <button type="button" class="btn" id="listBtn" onclick="toggleView('list')">
                        <i class="bi bi-list-ul"></i> List
                    </button>
                </div>
            </div>

            <?php if ($result->num_rows > 0): ?>
                <!-- Grid View (Cards) -->
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

                <!-- List View -->
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
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="bi bi-calendar-x display-1 text-muted"></i>
                    <h4 class="text-muted">No Events Found</h4>
                    <p class="text-muted">Start by adding your first event using the button above.</p>
                </div>
            <?php endif; ?>
        </section>
    </div>

    <div class="modal fade" id="addEventModal" tabindex="-1" aria-labelledby="addEventModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="addEventModalLabel">
                    <i class="bi bi-plus-circle"></i> Add New Event
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="card mb-0">
                    <!-- Removed duplicate card-header here -->
                    <div class="card-body">
                        <form method="POST" action="">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="event_name" class="form-label">Event Name *</label>
                                    <input type="text" class="form-control" id="event_name" name="event_name" required maxlength="255">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="event_location" class="form-label">Event Location *</label>
                                    <input type="text" class="form-control" id="event_location" name="event_location" required maxlength="255">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="event_date" class="form-label">Event Date *</label>
                                    <input type="date" class="form-control" id="event_date" name="event_date" required min="<?php echo date('Y-m-d'); ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="pricing" class="form-label">Pricing (₱) *</label>
                                    <input type="number" class="form-control" id="pricing" name="pricing" step="0.01" min="0" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="event_remarks" class="form-label">Event Remarks</label>
                                <textarea class="form-control" id="event_remarks" name="event_remarks" rows="3" placeholder="Optional remarks about the event" maxlength="1000"></textarea>
                                <div class="form-text">
                                    <span id="charCounter" class="text-muted">1000 characters remaining</span>
                                </div>
                            </div>
                            <button type="submit" name="add_event" class="btn btn-success">
                                <i class="bi bi-plus-circle"></i> Add Event
                            </button>
                        </form>
                    </div>
                </div>
            </div> <!-- end modal-body -->
        </div> <!-- end modal-content -->
    </div> <!-- end modal-dialog -->
</div> <!-- end modal -->
    <!-- Modals -->
 <!-- end modal | Laynes -->



    <?php 
    $result->data_seek(0); // Reset result pointer
    while($event = $result->fetch_assoc()): 
    ?>
    
    <!-- View Modal -->
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

    <!-- Delete Modal -->
    <div class="modal fade" id="deleteModal<?php echo $event['id']; ?>" tabindex="-1" aria-labelledby="deleteModalLabel<?php echo $event['id']; ?>" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="deleteModalLabel<?php echo $event['id']; ?>">
                        <i class="bi bi-exclamation-triangle"></i> Confirm Delete
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <i class="bi bi-trash display-1 text-danger mb-3"></i>
                    <h5>Are you sure you want to delete this event?</h5>
                    <p class="text-muted">
                        <strong>"<?php echo htmlspecialchars($event['event_name']); ?>"</strong><br>
                        This action cannot be undone.
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i> Cancel
                    </button>
                    <form method="POST" action="" style="display: inline;">
                        <input type="hidden" name="event_id" value="<?php echo $event['id']; ?>">
                        <button type="submit" name="delete_event" class="btn btn-danger">
                            <i class="bi bi-trash"></i> Delete Event
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php endwhile; ?>
    
<?php
$content = ob_get_clean();
include 'template/base.php';
?>
</div>
