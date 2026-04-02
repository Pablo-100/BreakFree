<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="BreakFree - Plateforme de suivi et d'accompagnement au sevrage des addictions">
    <title><?= e($pageTitle ?? 'BreakFree') ?> — BreakFree</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

    <!-- Styles -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css">
</head>
<body>
    <!-- Mobile menu -->
    <button class="menu-toggle" aria-label="Menu">☰</button>
    <div class="sidebar-overlay"></div>

    <div class="app-wrapper">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-brand">
                <h1>🔓 BreakFree</h1>
                <small>Libérez-vous, pas à pas</small>
            </div>

            <ul class="sidebar-nav">
                <li>
                    <a href="<?= BASE_URL ?>/dashboard" class="<?= ($_SERVER['REQUEST_URI'] ?? '') === '/dashboard' ? 'active' : '' ?>">
                        <span class="nav-icon">📊</span> Dashboard
                    </a>
                </li>
                <li>
                    <a href="<?= BASE_URL ?>/log" class="<?= str_starts_with($_SERVER['REQUEST_URI'] ?? '', '/log') && !str_contains($_SERVER['REQUEST_URI'] ?? '', 'history') ? 'active' : '' ?>">
                        <span class="nav-icon">📝</span> Journal du jour
                    </a>
                </li>
                <li>
                    <a href="<?= BASE_URL ?>/log/history" class="<?= str_contains($_SERVER['REQUEST_URI'] ?? '', '/log/history') ? 'active' : '' ?>">
                        <span class="nav-icon">📅</span> Historique
                    </a>
                </li>
                <li>
                    <a href="<?= BASE_URL ?>/profile" class="<?= str_contains($_SERVER['REQUEST_URI'] ?? '', '/profile') ? 'active' : '' ?>">
                        <span class="nav-icon">👤</span> Profil
                    </a>
                </li>
                <?php if (isAdmin()): ?>
                <li style="margin-top: 1rem; padding-top: 0.5rem; border-top: 1px solid var(--border);">
                    <a href="<?= BASE_URL ?>/admin" class="<?= ($_SERVER['REQUEST_URI'] ?? '') === '/admin' ? 'active' : '' ?>">
                        <span class="nav-icon">⚙️</span> Admin
                    </a>
                </li>
                <li>
                    <a href="<?= BASE_URL ?>/admin/users" class="<?= str_contains($_SERVER['REQUEST_URI'] ?? '', '/admin/users') ? 'active' : '' ?>">
                        <span class="nav-icon">👥</span> Utilisateurs
                    </a>
                </li>
                <?php endif; ?>
            </ul>

            <div class="sidebar-user">
                <div class="user-info">
                    <div class="user-avatar">
                        <?= strtoupper(substr($_SESSION['user_name'] ?? 'U', 0, 1)) ?>
                    </div>
                    <div>
                        <div class="user-name"><?= e($_SESSION['user_name'] ?? 'Utilisateur') ?></div>
                        <div class="user-role"><?= e(ucfirst($_SESSION['user_role'] ?? 'user')) ?></div>
                    </div>
                </div>
                <a href="<?= BASE_URL ?>/logout" class="btn btn-outline btn-sm btn-block" style="margin-top: 0.75rem;">
                    🚪 Déconnexion
                </a>
            </div>
        </aside>

        <!-- Main content -->
        <main class="main-content">
            <?php if (!empty($flash)): ?>
                <div class="alert alert-<?= e($flash['type']) ?>">
                    <?= $flash['message'] ?>
                </div>
            <?php endif; ?>

            <?= $content ?? '' ?>
        </main>
    </div>

    <script src="<?= BASE_URL ?>/js/app.js"></script>
    <?php if (!empty($pageScripts)): ?>
        <script><?= $pageScripts ?></script>
    <?php endif; ?>
</body>
</html>
