<?php
$pageTitle = 'Administration';

ob_start();
?>

<div class="page-header">
    <div>
        <h2>🛡️ Tableau de bord Admin</h2>
        <p>Vue d'ensemble de la plateforme</p>
    </div>
</div>

<!-- Stats globales -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon">👥</div>
        <div class="stat-info">
            <span class="stat-number"><?= $totalUsers ?></span>
            <span class="stat-label">Utilisateurs</span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">📝</div>
        <div class="stat-info">
            <span class="stat-number"><?= $totalLogs ?></span>
            <span class="stat-label">Entrées total</span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">📊</div>
        <div class="stat-info">
            <span class="stat-number"><?= number_format($avgCraving, 1) ?>/10</span>
            <span class="stat-label">Envie moyenne</span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">📈</div>
        <div class="stat-info">
            <span class="stat-number"><?= $totalLogs > 0 ? number_format($totalLogs / max($totalUsers, 1), 1) : '0' ?></span>
            <span class="stat-label">Entrées / utilisateur</span>
        </div>
    </div>
</div>

<!-- Graphiques Admin -->
<div class="grid-2">
    <div class="card">
        <div class="card-header">
            <h3>📉 Consommation globale (30j)</h3>
        </div>
        <canvas id="adminConsumptionChart" height="250"></canvas>
    </div>

    <div class="card">
        <div class="card-header">
            <h3>🎯 Répartition par addiction</h3>
        </div>
        <?php if (!empty($byAddiction)): ?>
            <div class="addiction-stats">
                <?php foreach ($byAddiction as $item): ?>
                    <div class="addiction-stat-row">
                        <div class="addiction-stat-label">
                            <span class="addiction-badge"><?= e($appConfig['addiction_types'][$item['addiction_type']] ?? ucfirst($item['addiction_type'])) ?></span>
                        </div>
                        <div class="addiction-stat-bar-wrap">
                            <div class="addiction-stat-bar"
                                 style="width: <?= $totalUsers > 0 ? round(($item['count'] / $totalUsers) * 100) : 0 ?>%">
                            </div>
                        </div>
                        <span class="addiction-stat-count"><?= $item['count'] ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p style="text-align:center; color:var(--text-muted); padding:2rem;">Aucune donnée disponible</p>
        <?php endif; ?>
    </div>
</div>

<!-- Actions rapides -->
<div class="card" style="margin-top: 1.5rem;">
    <div class="card-header">
        <h3>⚡ Actions rapides</h3>
    </div>
    <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
        <a href="<?= BASE_URL ?>/admin/users" class="btn btn-primary">👥 Gérer les utilisateurs</a>
        <a href="<?= BASE_URL ?>/dashboard" class="btn btn-outline">📊 Mon tableau de bord</a>
    </div>
</div>

<?php
$pageScripts = <<<JS
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof createAdminChart === 'function') {
        createAdminChart('adminConsumptionChart', BASE_URL + '/api/admin/stats');
    }
});
</script>
JS;

$content = ob_get_clean();
require VIEWS_PATH . '/layouts/app.php';
?>
