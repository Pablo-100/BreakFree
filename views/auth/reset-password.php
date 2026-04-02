<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouveau mot de passe — BreakFree</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css">
</head>
<body>
    <div class="auth-wrapper">
        <div class="auth-card animate-fade">
            <div class="auth-logo">
                <h1>🔓 BreakFree</h1>
                <p>Définir un nouveau mot de passe</p>
            </div>

            <?php if (!empty($flash)): ?>
                <div class="alert alert-<?= e($flash['type']) ?>">
                    <?= $flash['message'] ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="<?= BASE_URL ?>/reset-password">
                <?= csrfField() ?>
                <input type="hidden" name="token" value="<?= e($_GET['token'] ?? '') ?>">

                <div class="form-group">
                    <label for="password">Nouveau mot de passe</label>
                    <input type="password" id="password" name="password" class="form-control"
                           placeholder="Min 8 caractères" required minlength="8">
                </div>

                <div class="form-group">
                    <label for="password_confirm">Confirmer</label>
                    <input type="password" id="password_confirm" name="password_confirm" class="form-control"
                           placeholder="Retapez le mot de passe" required>
                </div>

                <button type="submit" class="btn btn-primary btn-block">
                    Réinitialiser le mot de passe
                </button>
            </form>

            <div class="auth-footer">
                <a href="<?= BASE_URL ?>/login">← Retour à la connexion</a>
            </div>
        </div>
    </div>
    <script src="<?= BASE_URL ?>/js/app.js"></script>
</body>
</html>
