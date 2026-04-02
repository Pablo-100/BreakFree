<?php
$pageTitle = 'Profil';

ob_start();
?>

<div class="page-header">
    <div>
        <h2>👤 Mon Profil</h2>
        <p>Configurez votre suivi d'addiction</p>
    </div>
</div>

<?php if (!empty($flash)): ?>
    <div class="alert alert-<?= e($flash['type']) ?>">
        <?= $flash['message'] ?>
    </div>
<?php endif; ?>

<form method="POST" action="<?= BASE_URL ?>/profile">
    <?= csrfField() ?>

    <div class="grid-2">
        <!-- Informations personnelles -->
        <div class="card">
            <div class="card-header">
                <h3>📋 Informations personnelles</h3>
            </div>

            <div class="form-group">
                <label for="name">Nom complet</label>
                <input type="text" id="name" name="name" class="form-control"
                       value="<?= e($userData['name'] ?? '') ?>" required minlength="2">
            </div>

            <div class="form-group">
                <label>Email</label>
                <input type="email" class="form-control" value="<?= e($userData['email'] ?? '') ?>" disabled>
                <small style="color: var(--text-muted);">L'email ne peut pas être modifié</small>
            </div>
        </div>

        <!-- Paramètres addiction -->
        <div class="card">
            <div class="card-header">
                <h3>🎯 Paramètres de suivi</h3>
            </div>

            <div class="form-group">
                <label for="addiction_type">Type d'addiction</label>
                <select id="addiction_type" name="addiction_type" class="form-control" required>
                    <option value="">-- Choisir --</option>
                    <?php foreach ($appConfig['addiction_types'] as $key => $label): ?>
                        <option value="<?= e($key) ?>" <?= ($userData['addiction_type'] ?? '') === $key ? 'selected' : '' ?>>
                            <?= e($label) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="goal_type">Objectif</label>
                <select id="goal_type" name="goal_type" class="form-control" required>
                    <option value="arret_total" <?= ($userData['goal_type'] ?? '') === 'arret_total' ? 'selected' : '' ?>>
                        🎯 Arrêt total
                    </option>
                    <option value="reduction" <?= ($userData['goal_type'] ?? '') === 'reduction' ? 'selected' : '' ?>>
                        📉 Réduction progressive
                    </option>
                </select>
            </div>

            <div class="form-group">
                <label for="start_date">Date de début du sevrage</label>
                <input type="date" id="start_date" name="start_date" class="form-control"
                       value="<?= e($userData['start_date'] ?? '') ?>" required max="<?= date('Y-m-d') ?>">
            </div>

            <div class="form-group">
                <label for="daily_cost">Coût moyen quotidien (€)</label>
                <input type="number" id="daily_cost" name="daily_cost" class="form-control"
                       value="<?= e($userData['daily_cost'] ?? '0') ?>" step="0.01" min="0"
                       placeholder="ex: 10.00">
            </div>
        </div>
    </div>

    <!-- Changement de mot de passe -->
    <div class="card" style="margin-top: 1.5rem;">
        <div class="card-header">
            <h3>🔒 Changer le mot de passe</h3>
            <small style="color: var(--text-muted);">Optionnel</small>
        </div>

        <div class="grid-2">
            <div class="form-group">
                <label for="new_password">Nouveau mot de passe</label>
                <input type="password" id="new_password" name="new_password" class="form-control"
                       placeholder="Laisser vide pour ne pas changer" minlength="8">
            </div>

            <div class="form-group">
                <label for="confirm_password">Confirmer</label>
                <input type="password" id="confirm_password" name="confirm_password" class="form-control"
                       placeholder="Confirmer le nouveau mot de passe">
            </div>
        </div>
    </div>

    <div style="margin-top: 1.5rem; display: flex; gap: 1rem;">
        <button type="submit" class="btn btn-primary">
            💾 Enregistrer les modifications
        </button>
        <a href="<?= BASE_URL ?>/dashboard" class="btn btn-outline">Annuler</a>
    </div>
</form>

<?php
$content = ob_get_clean();
require VIEWS_PATH . '/layouts/app.php';
?>
