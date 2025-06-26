<div class="row">
    <div class="col-lg-8">
        <div class="edit-form-container animate-fade-in-up" style="animation-delay: 0.2s;">
            <form method="POST" action="" class="needs-validation" novalidate id="editEventForm">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="text" 
                                    class="form-control" 
                                    id="event_name" 
                                    name="event_name" 
                                    value="<?php echo htmlspecialchars($event['event_name']); ?>" 
                                    required 
                                    maxlength="255"
                                    placeholder="Event Name">
                            <label for="event_name">Event Name *</label>
                            <div class="invalid-feedback">Please provide a valid event name.</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="text" 
                                    class="form-control" 
                                    id="event_location" 
                                    name="event_location" 
                                    value="<?php echo htmlspecialchars($event['event_location']); ?>" 
                                    required 
                                    maxlength="255"
                                    placeholder="Event Location">
                            <label for="event_location">Event Location *</label>
                            <div class="invalid-feedback">Please provide a valid event location.</div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="date" 
                                    class="form-control" 
                                    id="event_date" 
                                    name="event_date" 
                                    value="<?php echo $event['event_date']; ?>" 
                                    required
                                    placeholder="Event Date">
                            <label for="event_date">Event Date *</label>
                            <div class="invalid-feedback">Please provide a valid event date.</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="number" 
                                    class="form-control" 
                                    id="pricing" 
                                    name="pricing" 
                                    value="<?php echo $event['pricing']; ?>" 
                                    step="0.01" 
                                    min="0" 
                                    required
                                    placeholder="Pricing">
                            <label for="pricing">Pricing (₱) *</label>
                            <div class="invalid-feedback">Please provide a valid pricing amount.</div>
                        </div>
                    </div>
                </div>
                
                <div class="form-floating position-relative">
                    <textarea class="form-control floating-textarea" 
                                id="event_remarks" 
                                name="event_remarks" 
                                maxlength="1000"
                                placeholder="Event Remarks"><?php echo htmlspecialchars($event['event_remarks']); ?></textarea>
                    <label for="event_remarks">Event Remarks</label>
                    <div class="character-counter" id="charCounter">
                        1000 characters remaining
                    </div>
                </div>
                
                <div class="mb-3">
                    <small class="text-white opacity-75">
                        <i class="bi bi-info-circle"></i> 
                        Event was originally added on: <?php echo date('F d, Y \a\t g:i A', strtotime($event['date_added'])); ?>
                    </small>
                </div>
                
                <div class="btn-group-enhanced">
                    <button type="submit" name="update_event" class="btn btn-primary btn-enhanced">
                        <i class="bi bi-check-circle"></i> Update Event
                    </button>
                    <a href="events.php" class="btn btn-secondary btn-enhanced">
                        <i class="bi bi-arrow-left"></i> Cancel
                    </a>
                    <button type="button" class="btn btn-info btn-enhanced" onclick="previewChanges()">
                        <i class="bi bi-eye"></i> Preview
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="event-preview animate-fade-in-up" style="animation-delay: 0.4s;">
            <div class="preview-card">
                <h5 class="text-white mb-4">
                    <i class="bi bi-eye"></i> Live Preview
                </h5>
                
                <div class="preview-item">
                    <div class="preview-icon">
                        <i class="bi bi-calendar-event"></i>
                    </div>
                    <div class="preview-content">
                        <div class="preview-label">Event Name</div>
                        <div class="preview-value" id="preview-name">
                            <?php echo htmlspecialchars($event['event_name']); ?>
                        </div>
                    </div>
                </div>

                <div class="preview-item">
                    <div class="preview-icon">
                        <i class="bi bi-geo-alt"></i>
                    </div>
                    <div class="preview-content">
                        <div class="preview-label">Location</div>
                        <div class="preview-value" id="preview-location">
                            <?php echo htmlspecialchars($event['event_location']); ?>
                        </div>
                    </div>
                </div>

                <div class="preview-item">
                    <div class="preview-icon">
                        <i class="bi bi-calendar"></i>
                    </div>
                    <div class="preview-content">
                        <div class="preview-label">Event Date</div>
                        <div class="preview-value" id="preview-date">
                            <?php echo date('F d, Y', strtotime($event['event_date'])); ?>
                        </div>
                    </div>
                </div>

                <div class="preview-item">
                    <div class="preview-icon">
                        <i class="bi bi-cash"></i>
                    </div>
                    <div class="preview-content">
                        <div class="preview-label">Pricing</div>
                        <div class="preview-value" id="preview-pricing">
                            ₱<?php echo number_format($event['pricing'], 2); ?>
                        </div>
                    </div>
                </div>

                <?php if (!empty($event['event_remarks'])): ?>
                <div class="preview-item">
                    <div class="preview-icon">
                        <i class="bi bi-chat-text"></i>
                    </div>
                    <div class="preview-content">
                        <div class="preview-label">Remarks</div>
                        <div class="preview-value" id="preview-remarks">
                            <?php echo htmlspecialchars($event['event_remarks']); ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <div class="text-center mt-4">
                    <small class="text-white opacity-50">
                        <i class="bi bi-clock"></i> Last updated: <span id="last-updated">Now</span>
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>