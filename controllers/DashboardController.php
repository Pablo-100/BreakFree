<?php
/**
 * BreakFree - DashboardController
 */

require_once MODELS_PATH . '/User.php';
require_once MODELS_PATH . '/DailyLog.php';
require_once MODELS_PATH . '/UserBadge.php';

class DashboardController
{
    private User $user;
    private DailyLog $dailyLog;
    private UserBadge $userBadge;

    public function __construct()
    {
        $this->user = new User();
        $this->dailyLog = new DailyLog();
        $this->userBadge = new UserBadge();
    }

    /**
     * Dashboard principal
     */
    public function index(): void
    {
        if (!isLoggedIn()) {
            redirect('/login');
        }

        $userId = currentUserId();
        $userData = $this->user->findById($userId);

        if (!$userData || !$userData['addiction_type'] || !$userData['start_date']) {
            setFlash('info', 'Veuillez compléter votre profil pour commencer le suivi.');
            redirect('/profile');
        }

        $appConfig = require CONFIG_PATH . '/app.php';

        // Calculs
        $streak       = $this->dailyLog->getStreak($userId);
        $cleanDays    = $this->dailyLog->getCleanDays($userId);
        $weeklyAvg    = $this->dailyLog->getWeeklyAverage($userId);
        $trend        = $this->dailyLog->getTrend($userId);
        $todayLog     = $this->dailyLog->getTodayLog($userId);
        $totalLogs    = $this->dailyLog->countByUser($userId);

        // Argent économisé: jours sans consommation × coût quotidien
        $dailyCost    = (float)$userData['daily_cost'];
        $moneySaved   = $cleanDays * $dailyCost;

        // Jours depuis le début
        $daysSinceStart = daysBetween($userData['start_date']);

        // Progression %
        $progression = $daysSinceStart > 0 ? min(100, round(($cleanDays / $daysSinceStart) * 100, 1)) : 0;

        // Badges
        $this->userBadge->checkAndAward($userId, $streak);
        $earnedBadges = $this->userBadge->getUserBadges($userId);
        $allBadges    = $appConfig['badges'];

        // Niveau
        $level = $this->userBadge->getLevel($streak);

        // Mapped badges
        $badgesMap = [];
        foreach ($earnedBadges as $eb) {
            $badgesMap[$eb['badge_id']] = $eb;
        }

        $flash = getFlash();

        require VIEWS_PATH . '/dashboard/index.php';
    }

    /**
     * API: Données graphique consommation
     */
    public function apiConsumption(): void
    {
        if (!isLoggedIn()) {
            http_response_code(401);
            echo json_encode(['error' => 'Non autorisé']);
            return;
        }

        $days = (int)($_GET['days'] ?? 30);
        $data = $this->dailyLog->getConsumptionChart(currentUserId(), $days);

        header('Content-Type: application/json');
        echo json_encode([
            'labels' => array_map(fn($r) => $r['log_date'], $data),
            'values' => array_map(fn($r) => (float)$r['quantity'], $data),
        ]);
    }

    /**
     * API: Données graphique cravings
     */
    public function apiCravings(): void
    {
        if (!isLoggedIn()) {
            http_response_code(401);
            echo json_encode(['error' => 'Non autorisé']);
            return;
        }

        $days = (int)($_GET['days'] ?? 30);
        $data = $this->dailyLog->getCravingsChart(currentUserId(), $days);

        header('Content-Type: application/json');
        echo json_encode([
            'labels' => array_map(fn($r) => $r['log_date'], $data),
            'values' => array_map(fn($r) => (int)$r['craving_level'], $data),
        ]);
    }

    /**
     * API: Stats rapides
     */
    public function apiStats(): void
    {
        if (!isLoggedIn()) {
            http_response_code(401);
            echo json_encode(['error' => 'Non autorisé']);
            return;
        }

        $userId   = currentUserId();
        $userData = $this->user->findById($userId);

        $streak    = $this->dailyLog->getStreak($userId);
        $cleanDays = $this->dailyLog->getCleanDays($userId);
        $weeklyAvg = $this->dailyLog->getWeeklyAverage($userId);
        $trend     = $this->dailyLog->getTrend($userId);
        $moneySaved = $cleanDays * (float)($userData['daily_cost'] ?? 0);

        header('Content-Type: application/json');
        echo json_encode([
            'streak'      => $streak,
            'clean_days'  => $cleanDays,
            'weekly_avg'  => $weeklyAvg,
            'trend'       => $trend,
            'money_saved' => $moneySaved,
        ]);
    }
}
