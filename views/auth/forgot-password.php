<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mot de passe oublié — BreakFree</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css">
</head>
<body>
    <div class="auth-wrapper">
        <div class="auth-card animate-fade">
            <div class="auth-logo">
                <h1>🔓 BreakFree</h1>
                <p>Réinitialiser votre mot de passe</p>
            </div>

            <?php if (!empty($flash)): ?>
                <div class="alert alert-<?= e($flash['type']) ?>">
                    <?= $flash['message'] ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="<?= BASE_URL ?>/forgot-password">
                <?= csrfField() ?>

                <div class="form-group">
                    <label for="email">Adresse email</label>
                    <input type="email" id="email" name="email" class="form-control"
                           placeholder="votre@email.com" required autofocus>
                </div>

                <button type="submit" class="btn btn-primary btn-block">
                    Envoyer le lien de réinitialisation
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
