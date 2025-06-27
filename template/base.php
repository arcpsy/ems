<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <title><?php echo isset($page_title) ? htmlspecialchars($page_title) : 'Events Monitoring System'; ?></title>

    <link rel="stylesheet" href="css/bootstrap.min.css" />
    <link rel="stylesheet" href="css/bootstrap-icons.min.css" />
    <link rel="stylesheet" href="css/style.css" />
    <?php if (!empty($page_css)): ?>
    <link rel="stylesheet" href="<?= $page_css ?>">
    <?php endif; ?>
    <script defer src="js/bootstrap.bundle.min.js"></script>
</head>
<body<?= isset($body_class) ? ' class="' . $body_class . '"' : '' ?>>
    <!-- Navbar -->
    <?php include 'inc/navbar.php'; ?>

    <!-- Main content -->
    <main class="container">
        <?php include 'inc/flash-messages.php'; ?>

        <!-- Page-specific content -->
        <?php echo $content ?? ''; ?>
    </main>

    <!-- Footer -->
    <footer class="footer-glass mt-5 py-4">
    <div class="container text-center">
        <p class="footer-text mb-0">
        &copy; <?php echo date('Y'); ?> <strong>GalaGo</strong> Events Monitoring System. All rights reserved.
        </p>
    </div>
    </footer>


    <script src="js/script.js"></script>
    <?php if (!empty($page_js)): ?>
    <script src="<?= $page_js ?>"></script>
    <?php endif; ?>
</body>
</html>
