<?php
$pageTitle = 'Historique';

ob_start();
?>

<div class="page-header">
    <div>
        <h2>📅 Historique du suivi</h2>
        <p><?= $totalLogs ?> entrée(s) enregistrée(s)</p>
    </div>
    <a href="<?= BASE_URL ?>/log" class="btn btn-primary">📝 Journal du jour</a>
</div>

<?php if (!empty($flash)): ?>
    <div class="alert alert-<?= e($flash['type']) ?>">
        <?= $flash['message'] ?>
    </div>
<?php endif; ?>

<?php if (empty($logs)): ?>
    <div class="card">
        <div class="empty-state">
            <div class="empty-icon">📋</div>
            <h3>Aucune entrée</h3>
            <p>Commencez par remplir votre journal quotidien.</p>
            <a href="<?= BASE_URL ?>/log" class="btn btn-primary" style="margin-top: 1rem;">Commencer →</a>
        </div>
    </div>
<?php else: ?>
    <div class="card">
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Quantité</th>
                        <th>Envie</th>
                        <th>Humeur</th>
                        <th>Notes</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($logs as $log): ?>
                        <tr>
                            <td>
                                <strong><?= formatDate($log['log_date']) ?></strong>
                            </td>
                            <td>
                                <?php if ((float)$log['quantity'] == 0): ?>
                                    <span style="color: var(--success); font-weight: 600;">✓ 0</span>
                                <?php else: ?>
                                    <?= e($log['quantity']) ?>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span style="color: <?= (int)$log['craving_level'] > 5 ? 'var(--danger)' : 'var(--success)' ?>">
                                    <?= e($log['craving_level']) ?>/10
                                </span>
                            </td>
                            <td><?= e($appConfig['moods'][$log['mood']] ?? $log['mood']) ?></td>
                            <td>
                                <?php if (!empty($log['notes'])): ?>
                                    <span title="<?= e($log['notes']) ?>">
                                        <?= e(mb_strimwidth($log['notes'], 0, 40, '...')) ?>
                                    </span>
                                <?php else: ?>
                                    <span style="color: var(--text-muted);">—</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="actions">
                                    <a href="<?= BASE_URL ?>/log/edit/<?= e($log['id']) ?>" class="btn btn-outline btn-sm">✏️</a>
                                    <form method="POST" action="<?= BASE_URL ?>/log/delete/<?= e($log['id']) ?>" style="display:inline;">
                                        <?= csrfField() ?>
                                        <button type="submit" class="btn btn-danger btn-sm" data-confirm="Supprimer cette entrée ?">🗑️</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <?php if ($totalPages > 1): ?>
        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="<?= BASE_URL ?>/log/history?page=<?= $page - 1 ?>">← Précédent</a>
            <?php endif; ?>

            <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                <?php if ($i === $page): ?>
                    <span class="active"><?= $i ?></span>
                <?php else: ?>
                    <a href="<?= BASE_URL ?>/log/history?page=<?= $i ?>"><?= $i ?></a>
                <?php endif; ?>
            <?php endfor; ?>

            <?php if ($page < $totalPages): ?>
                <a href="<?= BASE_URL ?>/log/history?page=<?= $page + 1 ?>">Suivant →</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
<?php endif; ?>

<?php
$content = ob_get_clean();
require VIEWS_PATH . '/layouts/app.php';
?>
