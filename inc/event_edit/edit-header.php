<div class="edit-header animate-fade-in-up">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h1 class="text-white mb-2">
                <i class="bi bi-pencil-square"></i> Edit Event
            </h1>
            <p class="text-white opacity-75 mb-0">
                Modify event details and update information
            </p>
        </div>
        <div class="col-md-4 text-md-end">
            <span class="badge bg-info fs-6">
                <i class="bi bi-calendar"></i>
                <?php echo date('M d, Y', strtotime($event['date_added'])); ?>
            </span>
        </div>
    </div>
</div>