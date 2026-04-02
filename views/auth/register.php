<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription — BreakFree</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css">
</head>
<body>
    <div class="auth-wrapper">
        <div class="auth-card animate-fade">
            <div class="auth-logo">
                <h1>🔓 BreakFree</h1>
                <p>Commencez votre parcours de liberté</p>
            </div>

            <?php if (!empty($flash)): ?>
                <div class="alert alert-<?= e($flash['type']) ?>">
                    <?= $flash['message'] ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="<?= BASE_URL ?>/register" autocomplete="on">
                <?= csrfField() ?>

                <div class="form-group">
                    <label for="name">Nom complet</label>
                    <input type="text" id="name" name="name" class="form-control"
                           value="<?= old('name') ?>"
                           placeholder="Votre nom" required autofocus minlength="2">
                </div>

                <div class="form-group">
                    <label for="email">Adresse email</label>
                    <input type="email" id="email" name="email" class="form-control"
                           value="<?= old('email') ?>"
                           placeholder="votre@email.com" required>
                </div>

                <div class="form-group">
                    <label for="password">Mot de passe</label>
                    <input type="password" id="password" name="password" class="form-control"
                           placeholder="Min 8 caractères, 1 majuscule, 1 chiffre" required minlength="8">
                </div>

                <div class="form-group">
                    <label for="password_confirm">Confirmer le mot de passe</label>
                    <input type="password" id="password_confirm" name="password_confirm" class="form-control"
                           placeholder="Retapez le mot de passe" required>
                </div>

                <button type="submit" class="btn btn-primary btn-block">
                    Créer mon compte →
                </button>
            </form>

            <div class="auth-footer">
                Déjà un compte ?
                <a href="<?= BASE_URL ?>/login">Se connecter</a>
            </div>
        </div>
    </div>
    <script src="<?= BASE_URL ?>/js/app.js"></script>
</body>
</html>
