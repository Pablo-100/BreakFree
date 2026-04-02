<?php
/**
 * BreakFree - AdminController
 */

require_once MODELS_PATH . '/User.php';
require_once MODELS_PATH . '/DailyLog.php';

class AdminController
{
    private User $user;
    private DailyLog $dailyLog;

    public function __construct()
    {
        $this->user = new User();
        $this->dailyLog = new DailyLog();
    }

    /**
     * Vérifier l'accès admin
     */
    private function checkAdmin(): void
    {
        if (!isLoggedIn() || !isAdmin()) {
            setFlash('error', 'Accès non autorisé.');
            redirect('/dashboard');
        }
    }

    /**
     * Dashboard admin
     */
    public function index(): void
    {
        $this->checkAdmin();

        $totalUsers     = $this->user->countAll();
        $totalLogs      = $this->dailyLog->globalTotalLogs();
        $avgCraving     = $this->dailyLog->globalAvgCraving();
        $byAddiction    = $this->dailyLog->globalByAddictionType();
        $flash          = getFlash();

        $appConfig = require CONFIG_PATH . '/app.php';

        require VIEWS_PATH . '/admin/index.php';
    }

    /**
     * Liste des utilisateurs
     */
    public function users(): void
    {
        $this->checkAdmin();

        $page     = max(1, (int)($_GET['page'] ?? 1));
        $limit    = 20;
        $offset   = ($page - 1) * $limit;

        $users      = $this->user->listAll($limit, $offset);
        $totalUsers = $this->user->countAll();
        $totalPages = ceil($totalUsers / $limit);
        $flash      = getFlash();

        require VIEWS_PATH . '/admin/users.php';
    }

    /**
     * Supprimer un utilisateur
     */
    public function deleteUser(string $id): void
    {
        $this->checkAdmin();

        if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
            setFlash('error', 'Token de sécurité invalide.');
            redirect('/admin/users');
        }

        if ($this->user->delete($id)) {
            setFlash('success', 'Utilisateur supprimé.');
        } else {
            setFlash('error', 'Impossible de supprimer cet utilisateur.');
        }

        redirect('/admin/users');
    }

    /**
     * API: Stats globales admin
     */
    public function apiStats(): void
    {
        if (!isLoggedIn() || !isAdmin()) {
            http_response_code(403);
            echo json_encode(['error' => 'Accès non autorisé']);
            return;
        }

        $days = (int)($_GET['days'] ?? 30);
        $globalChart = $this->dailyLog->globalConsumptionChart($days);

        header('Content-Type: application/json');
        echo json_encode([
            'labels'       => array_map(fn($r) => $r['log_date'], $globalChart),
            'consumption'  => array_map(fn($r) => round((float)$r['avg_quantity'], 2), $globalChart),
            'cravings'     => array_map(fn($r) => round((float)$r['avg_craving'], 1), $globalChart),
        ]);
    }
}
