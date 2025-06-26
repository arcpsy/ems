<nav class="navbar navbar-expand-lg navbar-dark gala-navbar">
    <div class="container-sm">
        <a class="navbar-brand" href="index.php">
            <i class="bi bi-stars me-2"></i> GalaGo Events
        </a>
        <button class="navbar-toggler custom-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <i class="bi bi-list"></i>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'index.php' ? 'active' : ''; ?>" href="index.php">
                        <i class="bi bi-house-door-fill"></i> Home
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'events.php' ? 'active' : ''; ?>" href="events.php">
                        <i class="bi bi-calendar-range-fill" style="font-size: 1rem;"></i> All Events
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>