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
                <p class="fs-5">Are you sure you want to delete the event <strong><?php echo htmlspecialchars($event['event_name']); ?></strong>?</p>
            </div>
            <div class="modal-footer justify-content-center">
                <form method="POST" action="">
                    <input type="hidden" name="delete_event" value="1">
                    <input type="hidden" name="event_id" value="<?php echo $event['id']; ?>">
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash"></i> Delete
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Cancel
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>