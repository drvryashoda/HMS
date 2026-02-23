<?php
require_once __DIR__ . '/auth.php';

function renderHeader(string $title = 'Dashboard'): void
{
    $current = basename($_SERVER['PHP_SELF']);
    $u = user();
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?= e($title) ?> - <?= APP_NAME ?></title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="assets/css/style.css">
    </head>
    <body>
    <div class="app-shell">
        <aside class="sidebar">
            <div class="brand">
                <h2>🏥 HMS</h2>
                <p>Care with data</p>
            </div>
            <nav class="nav-links">
                <?php
                $links = [
                    'dashboard.php' => 'Dashboard',
                    'patients.php' => 'Patients',
                    'doctors.php' => 'Doctors',
                    'appointments.php' => 'Appointments',
                    'admissions.php' => 'Admissions',
                    'lab_tests.php' => 'Lab Tests',
                    'pharmacy.php' => 'Pharmacy',
                    'billing.php' => 'Billing',
                    'reports.php' => 'Reports',
                ];

                foreach ($links as $href => $label):
                    $active = $current === $href ? 'active' : '';
                    ?>
                    <a class="<?= $active ?>" href="<?= $href ?>"><?= e($label) ?></a>
                <?php endforeach; ?>
            </nav>
            <a class="logout-link" href="logout.php">Logout</a>
        </aside>
        <main class="content">
            <header class="topbar">
                <h1><?= e($title) ?></h1>
                <div class="user-chip">
                    <span><?= e($u['name'] ?? 'Unknown') ?></span>
                    <span class="role-badge <?= roleBadge($u['role'] ?? '') ?>"><?= e(strtoupper($u['role'] ?? '')) ?></span>
                </div>
            </header>
    <?php
}

function renderFooter(): void
{
    ?>
        </main>
    </div>
    <script src="assets/js/app.js"></script>
    </body>
    </html>
    <?php
}
