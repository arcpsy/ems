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
            </div>
        </div>
    </div>
</div>