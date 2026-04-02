<?php
$pageTitle = 'Modifier entrée';

ob_start();
?>

<div class="page-header">
    <div>
        <h2>✏️ Modifier l'entrée</h2>
        <p>Date : <?= formatDate($log['log_date']) ?></p>
    </div>
    <a href="<?= BASE_URL ?>/log/history" class="btn btn-outline">← Retour</a>
</div>

<?php if (!empty($flash)): ?>
    <div class="alert alert-<?= e($flash['type']) ?>">
        <?= $flash['message'] ?>
    </div>
<?php endif; ?>

<form method="POST" action="<?= BASE_URL ?>/log/edit/<?= e($log['id']) ?>">
    <?= csrfField() ?>

    <div class="grid-2">
        <div class="card">
            <div class="card-header">
                <h3>📊 Données</h3>
            </div>

            <div class="form-group">
                <label for="log_date">Date</label>
                <input type="date" id="log_date" name="log_date" class="form-control"
                       value="<?= e($log['log_date']) ?>" max="<?= date('Y-m-d') ?>">
            </div>

            <div class="form-group">
                <label for="quantity">Quantité consommée</label>
                <input type="number" id="quantity" name="quantity" class="form-control"
                       value="<?= e($log['quantity']) ?>" min="0" step="0.5">
            </div>

            <div class="form-group">
                <label for="craving_level">Niveau d'envie</label>
                <div class="range-group">
                    <span style="font-size: 0.85rem; color: var(--text-muted);">Faible</span>
                    <input type="range" id="craving_level" name="craving_level"
                           min="0" max="10" step="1"
                           value="<?= e($log['craving_level']) ?>">
                    <span class="range-value" id="craving_level_value"><?= e($log['craving_level']) ?></span>
                    <span style="font-size: 0.85rem; color: var(--text-muted);">Fort</span>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3>💭 Ressenti</h3>
            </div>

            <div class="form-group">
                <label>Humeur</label>
                <div class="mood-selector">
                    <?php foreach ($appConfig['moods'] as $key => $label): ?>
                        <input type="radio" id="mood_<?= $key ?>" name="mood" value="<?= e($key) ?>"
                               class="mood-option"
                               <?= $log['mood'] === $key ? 'checked' : '' ?>>
                        <label for="mood_<?= $key ?>"><?= e($label) ?></label>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="form-group">
                <label for="notes">Notes</label>
                <textarea id="notes" name="notes" class="form-control" rows="5"><?= e($log['notes'] ?? '') ?></textarea>
            </div>
        </div>
    </div>

    <div style="margin-top: 1.5rem; display: flex; gap: 1rem;">
        <button type="submit" class="btn btn-primary">💾 Enregistrer</button>
        <a href="<?= BASE_URL ?>/log/history" class="btn btn-outline">Annuler</a>
    </div>
</form>

<?php
$content = ob_get_clean();
require VIEWS_PATH . '/layouts/app.php';
?>
