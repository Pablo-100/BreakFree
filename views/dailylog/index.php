<?php
$pageTitle = 'Journal du jour';

ob_start();
?>

<div class="page-header">
    <div>
        <h2>📝 Journal quotidien</h2>
        <p>Enregistrez votre ressenti du jour — <?= date('d/m/Y') ?></p>
    </div>
    <a href="<?= BASE_URL ?>/log/history" class="btn btn-outline">📅 Historique</a>
</div>

<?php if (!empty($flash)): ?>
    <div class="alert alert-<?= e($flash['type']) ?>">
        <?= $flash['message'] ?>
    </div>
<?php endif; ?>

<?php if ($todayLog): ?>
    <div class="alert alert-info">
        ✏️ Vous avez déjà un journal pour aujourd'hui. Le formulaire ci-dessous le mettra à jour.
    </div>
<?php endif; ?>

<form method="POST" action="<?= BASE_URL ?>/log">
    <?= csrfField() ?>

    <div class="grid-2">
        <!-- Données -->
        <div class="card">
            <div class="card-header">
                <h3>📊 Données du jour</h3>
            </div>

            <div class="form-group">
                <label for="log_date">Date</label>
                <input type="date" id="log_date" name="log_date" class="form-control"
                       value="<?= e($todayLog['log_date'] ?? date('Y-m-d')) ?>"
                       max="<?= date('Y-m-d') ?>">
            </div>

            <div class="form-group">
                <label for="quantity">Quantité consommée</label>
                <input type="number" id="quantity" name="quantity" class="form-control"
                       value="<?= e($todayLog['quantity'] ?? '0') ?>"
                       min="0" step="0.5" placeholder="0 = rien consommé">
                <small style="color: var(--text-muted);">Cigarettes, verres, doses... selon votre addiction</small>
            </div>

            <div class="form-group">
                <label for="craving_level">Niveau d'envie (craving)</label>
                <div class="range-group">
                    <span style="font-size: 0.85rem; color: var(--text-muted);">Faible</span>
                    <input type="range" id="craving_level" name="craving_level"
                           min="0" max="10" step="1"
                           value="<?= e($todayLog['craving_level'] ?? '5') ?>">
                    <span class="range-value" id="craving_level_value"><?= e($todayLog['craving_level'] ?? '5') ?></span>
                    <span style="font-size: 0.85rem; color: var(--text-muted);">Fort</span>
                </div>
            </div>
        </div>

        <!-- Ressenti -->
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
                               <?= ($todayLog['mood'] ?? 'neutre') === $key ? 'checked' : '' ?>>
                        <label for="mood_<?= $key ?>"><?= e($label) ?></label>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="form-group">
                <label for="notes">Notes libres</label>
                <textarea id="notes" name="notes" class="form-control"
                          placeholder="Comment vous sentez-vous ? Des déclencheurs ? Des victoires ?"
                          rows="5"><?= e($todayLog['notes'] ?? '') ?></textarea>
            </div>
        </div>
    </div>

    <div style="margin-top: 1.5rem; display: flex; gap: 1rem;">
        <button type="submit" class="btn btn-success btn-block">
            <?= $todayLog ? '✏️ Mettre à jour' : '✅ Enregistrer' ?>
        </button>
    </div>
</form>

<!-- Motivation -->
<div class="card" style="margin-top: 2rem; text-align: center; padding: 2rem;">
    <?php
    $quotes = [
        "Chaque jour sans consommation est une victoire. 💪",
        "Vous êtes plus fort(e) que votre addiction.",
        "Le premier pas est le plus difficile, mais vous l'avez déjà fait.",
        "La liberté se construit un jour à la fois.",
        "Votre futur vous remerciera pour chaque effort d'aujourd'hui.",
        "Le changement commence par une décision.",
        "Vous méritez une vie libre et épanouie.",
    ];
    $quote = $quotes[array_rand($quotes)];
    ?>
    <div style="font-size: 1.5rem; margin-bottom: 0.5rem;">💡</div>
    <p style="font-size: 1.1rem; font-style: italic; color: var(--text-secondary);">
        "<?= e($quote) ?>"
    </p>
</div>

<?php
$content = ob_get_clean();
require VIEWS_PATH . '/layouts/app.php';
?>
