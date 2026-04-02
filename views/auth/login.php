<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion — BreakFree</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css">
</head>
<body>
    <div class="auth-wrapper">
        <div class="auth-card animate-fade">
            <div class="auth-logo">
                <h1>🔓 BreakFree</h1>
                <p>Libérez-vous, pas à pas</p>
            </div>

            <?php if (!empty($flash)): ?>
                <div class="alert alert-<?= e($flash['type']) ?>">
                    <?= $flash['message'] ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="<?= BASE_URL ?>/login" autocomplete="on">
                <?= csrfField() ?>

                <div class="form-group">
                    <label for="email">Adresse email</label>
                    <input type="email" id="email" name="email" class="form-control"
                           value="<?= old('email') ?>"
                           placeholder="votre@email.com" required autofocus>
                </div>

                <div class="form-group">
                    <label for="password">Mot de passe</label>
                    <input type="password" id="password" name="password" class="form-control"
                           placeholder="••••••••" required>
                </div>

                <div style="text-align: right; margin-bottom: 1rem;">
                    <a href="<?= BASE_URL ?>/forgot-password" style="font-size: 0.85rem;">Mot de passe oublié ?</a>
                </div>

                <button type="submit" class="btn btn-primary btn-block">
                    Se connecter →
                </button>
            </form>

            <div class="auth-footer">
                Pas encore de compte ?
                <a href="<?= BASE_URL ?>/register">Créer un compte</a>
            </div>
        </div>
    </div>
    <script src="<?= BASE_URL ?>/js/app.js"></script>
</body>
</html>
