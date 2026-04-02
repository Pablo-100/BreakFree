<?php
$pageTitle = 'Gestion des utilisateurs';

ob_start();
?>

<div class="page-header">
    <div>
        <h2>👥 Gestion des utilisateurs</h2>
        <p><?= $totalUsers ?> utilisateur<?= $totalUsers > 1 ? 's' : '' ?> inscrits</p>
    </div>
    <a href="<?= BASE_URL ?>/admin" class="btn btn-outline">← Retour admin</a>
</div>

<?php if (!empty($flash)): ?>
    <div class="alert alert-<?= e($flash['type']) ?>">
        <?= $flash['message'] ?>
    </div>
<?php endif; ?>

<div class="card">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Addiction</th>
                    <th>Inscrit le</th>
                    <th>Rôle</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($users)): ?>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td>
                                <div class="user-cell">
                                    <div class="user-avatar-sm">
                                        <?= strtoupper(substr($user['name'], 0, 1)) ?>
                                    </div>
                                    <span><?= e($user['name']) ?></span>
                                </div>
                            </td>
                            <td><?= e($user['email']) ?></td>
                            <td>
                                <span class="badge-pill">
                                    <?= e($appConfig['addiction_types'][$user['addiction_type']] ?? ucfirst($user['addiction_type'])) ?>
                                </span>
                            </td>
                            <td><?= formatDate($user['created_at']) ?></td>
                            <td>
                                <?php if ($user['role'] === 'admin'): ?>
                                    <span class="role-badge role-admin">Admin</span>
                                <?php else: ?>
                                    <span class="role-badge role-user">Utilisateur</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($user['role'] !== 'admin'): ?>
                                    <form method="POST" action="<?= BASE_URL ?>/admin/users/delete/<?= e($user['id']) ?>"
                                          class="inline-form"
                                          data-confirm="Supprimer l'utilisateur <?= e($user['name']) ?> ? Cette action est irréversible.">
                                        <?= csrfField() ?>
                                        <button type="submit" class="btn btn-danger btn-sm">🗑️ Supprimer</button>
                                    </form>
                                <?php else: ?>
                                    <span class="text-muted">—</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center">Aucun utilisateur trouvé</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <?php if ($totalPages > 1): ?>
        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="<?= BASE_URL ?>/admin/users?page=<?= $page - 1 ?>" class="pagination-link">← Précédent</a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="<?= BASE_URL ?>/admin/users?page=<?= $i ?>"
                   class="pagination-link <?= $i === $page ? 'active' : '' ?>">
                    <?= $i ?>
                </a>
            <?php endfor; ?>

            <?php if ($page < $totalPages): ?>
                <a href="<?= BASE_URL ?>/admin/users?page=<?= $page + 1 ?>" class="pagination-link">Suivant →</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
require VIEWS_PATH . '/layouts/app.php';
?>
