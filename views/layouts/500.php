<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Erreur serveur — BreakFree</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css">
</head>
<body>
    <div class="auth-wrapper">
        <div class="auth-card" style="text-align: center;">
            <div style="font-size: 5rem; margin-bottom: 1rem;">⚠️</div>
            <h1 style="font-size: 3rem; color: var(--danger); margin-bottom: 0.5rem;">500</h1>
            <h2 style="color: var(--text-secondary); margin-bottom: 1rem;">Erreur serveur</h2>
            <p style="color: var(--text-muted); margin-bottom: 2rem;">Une erreur est survenue. Veuillez réessayer plus tard.</p>
            <a href="<?= BASE_URL ?>/dashboard" class="btn btn-primary">← Retour au Dashboard</a>
        </div>
    </div>
</body>
</html>
