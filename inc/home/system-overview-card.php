<div class="col-md-6 d-flex mb-4 mb-md-0">
    <div class="card flex-fill d-flex flex-column h-100">
        <div class="card-header bg-success text-white">
            <h5 class="card-title mb-0">
                <i class="bi bi-bar-chart"></i> System Overview
            </h5>
        </div>
        <div class="card-body d-flex flex-column">
            <div class="flex-grow-1 d-flex flex-column justify-content-center align-items-center w-100">
                <div class="bg-light rounded w-100 py-5 d-flex flex-column justify-content-center align-items-center">
                    <h2 class="text-success mb-0"><?php echo $total_events; ?></h2>
                    <p class="text-muted mb-0">Total Events</p>
                </div>
            </div>
            <div class="mt-auto d-grid">
                <a href="events.php" class="btn btn-success">
                    <i class="bi bi-plus-circle"></i> Manage Events
                </a>
            </div>
        </div>
    </div>
</div>