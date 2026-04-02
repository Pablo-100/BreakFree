<?php
$pageTitle = 'Dashboard';

// Trend labels
$trendLabels = [
    'improving' => ['label' => '↗ En progrès', 'class' => 'improving'],
    'stable'    => ['label' => '→ Stable',     'class' => 'stable'],
    'worsening' => ['label' => '↘ Attention',  'class' => 'worsening'],
];
$trendInfo = $trendLabels[$trend] ?? $trendLabels['stable'];

// Level progress calculation
$appLevels = $appConfig['levels'];
$nextLevel = null;
foreach ($appLevels as $l) {
    if ($l['min_days'] > $streak) {
        $nextLevel = $l;
        break;
    }
}
$levelProgress = 100;
if ($nextLevel) {
    $range = $nextLevel['min_days'] - $level['min_days'];
    $current = $streak - $level['min_days'];
    $levelProgress = $range > 0 ? min(100, round(($current / $range) * 100)) : 100;
}

ob_start();
?>

<!-- Page Header -->
<div class="page-header">
    <div>
        <h2>👋 Bonjour, <?= e($userData['name']) ?></h2>
        <p>Votre parcours vers la liberté — <?= e($appConfig['addiction_types'][$userData['addiction_type']] ?? 'Non défini') ?></p>
    </div>
    <a href="<?= BASE_URL ?>/log" class="btn btn-primary">📝 Journal du jour</a>
</div>

<!-- Level Display -->
<div class="level-display">
    <div class="level-icon" style="color: <?= e($level['color']) ?>">
        <?php
        $levelIcons = ['🌱','💪','⚔️','🛡️','🏆','👑','🌟','🔱','✨'];
        echo $levelIcons[$level['level'] - 1] ?? '🌱';
        ?>
    </div>
    <div class="level-info">
        <div class="level-name" style="color: <?= e($level['color']) ?>">
            Niveau <?= $level['level'] ?> — <?= e($level['name']) ?>
        </div>
        <div class="level-label">
            <?php if ($nextLevel): ?>
                Prochain niveau : <?= e($nextLevel['name']) ?> (<?= $nextLevel['min_days'] ?> jours)
            <?php else: ?>
                Niveau maximum atteint ! 🎉
            <?php endif; ?>
        </div>
        <div class="level-bar">
            <div class="level-fill" style="width: <?= $levelProgress ?>%; background: <?= e($level['color']) ?>"></div>
        </div>
    </div>
</div>

<!-- Stats Grid -->
<div class="stats-grid">
    <div class="stat-card success">
        <div class="stat-icon">🔥</div>
        <div class="stat-value"><?= $streak ?></div>
        <div class="stat-label">Jours consécutifs</div>
    </div>

    <div class="stat-card info">
        <div class="stat-icon">💰</div>
        <div class="stat-value"><?= formatMoney($moneySaved) ?></div>
        <div class="stat-label">Argent économisé</div>
    </div>

    <div class="stat-card">
        <div class="stat-icon">📈</div>
        <div class="stat-value"><?= $progression ?>%</div>
        <div class="stat-label">Progression globale</div>
        <div class="progress-bar" style="margin-top: 0.5rem;">
            <div class="progress-fill" style="width: <?= $progression ?>%"></div>
        </div>
    </div>

    <div class="stat-card warning">
        <div class="stat-icon">📊</div>
        <div class="stat-value"><?= $weeklyAvg ?></div>
        <div class="stat-label">Moyenne / semaine</div>
        <span class="trend <?= e($trendInfo['class']) ?>" style="margin-top: 0.5rem;">
            <?= e($trendInfo['label']) ?>
        </span>
    </div>
</div>

<!-- Charts -->
<div class="charts-grid">
    <div class="chart-card">
        <div class="card-header">
            <h3>📉 Évolution de la consommation</h3>
        </div>
        <div style="height: 280px;">
            <canvas id="consumptionChart"></canvas>
        </div>
    </div>

    <div class="chart-card">
        <div class="card-header">
            <h3>🧠 Niveau d'envie (Cravings)</h3>
        </div>
        <div style="height: 280px;">
            <canvas id="cravingsChart"></canvas>
        </div>
    </div>
</div>

<!-- Badges -->
<div class="card" style="margin-bottom: 2rem;">
    <div class="card-header">
        <h3>🏅 Badges de progression</h3>
        <span style="font-size: 0.85rem; color: var(--text-muted);">
            <?= count($earnedBadges) ?> / <?= count($allBadges) ?>
        </span>
    </div>
    <div class="badges-grid">
        <?php foreach ($allBadges as $badge): ?>
            <?php $earned = isset($badgesMap[$badge['id']]); ?>
            <div class="badge-item <?= $earned ? 'earned' : 'locked' ?>">
                <span class="badge-icon"><?= $badge['icon'] ?></span>
                <div class="badge-name"><?= e($badge['name']) ?></div>
                <div class="badge-desc"><?= e($badge['desc']) ?></div>
                <?php if ($earned): ?>
                    <div style="font-size: 0.7rem; color: var(--success); margin-top: 0.25rem;">
                        ✓ <?= formatDate($badgesMap[$badge['id']]['earned_at']) ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Today's log status -->
<div class="card">
    <div class="card-header">
        <h3>📝 Journal d'aujourd'hui</h3>
        <?php if ($todayLog): ?>
            <span class="trend improving">✓ Complété</span>
        <?php else: ?>
            <a href="<?= BASE_URL ?>/log" class="btn btn-primary btn-sm">Remplir →</a>
        <?php endif; ?>
    </div>
    <?php if ($todayLog): ?>
        <div class="grid-2" style="gap: 1rem;">
            <div>
                <strong>Quantité :</strong> <?= e($todayLog['quantity']) ?><br>
                <strong>Envie :</strong> <?= e($todayLog['craving_level']) ?>/10
            </div>
            <div>
                <strong>Humeur :</strong> <?= e($appConfig['moods'][$todayLog['mood']] ?? $todayLog['mood']) ?><br>
                <?php if (!empty($todayLog['notes'])): ?>
                    <strong>Notes :</strong> <?= e($todayLog['notes']) ?>
                <?php endif; ?>
            </div>
        </div>
    <?php else: ?>
        <div class="empty-state" style="padding: 1.5rem;">
            <p>Vous n'avez pas encore rempli votre journal aujourd'hui.</p>
        </div>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();

$pageScripts = <<<JS
    createLineChart('consumptionChart', BASE_URL + '/api/chart/consumption', 'Consommation', ChartColors.accent, ChartColors.accentBg);
    createLineChart('cravingsChart', BASE_URL + '/api/chart/cravings', 'Niveau d\'envie', ChartColors.warning, ChartColors.warningBg);
JS;

// Inject BASE_URL for JS
$pageScripts = "const BASE_URL = '" . BASE_URL . "';\n" . $pageScripts;

require VIEWS_PATH . '/layouts/app.php';
?>
