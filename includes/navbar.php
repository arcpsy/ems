<nav class="navbar navbar-expand-lg navbar-dark gala-navbar">
  <div class="container-sm">
    <!-- Logo / Brand -->
    <a class="navbar-brand" href="index.php">
      <i class="bi bi-clipboard2-check-fill me-2"></i> GalaGo Events
    </a>

    <!-- Mobile toggler button -->
    <button class="navbar-toggler custom-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <i class="bi bi-list"></i>
    </button>

    <!-- Collapsible nav links -->
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <!-- Home link with custom icon size -->
        <li class="nav-item">
          <a
            class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'index.php' ? 'active' : ''; ?>"
            href="index.php"
            style="--icon-size: 1.125rem;">
            <i class="bi bi-house-door-fill nav-icon"></i> Home
          </a>
        </li>

        <!-- All Events link with slightly smaller icon -->
        <li class="nav-item">
          <a
            class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'events.php' ? 'active' : ''; ?>"
            href="events.php"
            style="--icon-size: 1rem;">
            <i class="bi bi-calendar3 nav-icon"></i> All Events
          </a>
        </li>
      </ul>
    </div>
  </div>
</nav>
