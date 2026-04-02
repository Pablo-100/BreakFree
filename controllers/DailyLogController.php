<?php
/**
 * BreakFree - DailyLogController
 */

require_once MODELS_PATH . '/DailyLog.php';
require_once MODELS_PATH . '/UserBadge.php';

class DailyLogController
{
    private DailyLog $dailyLog;
    private UserBadge $userBadge;

    public function __construct()
    {
        $this->dailyLog = new DailyLog();
        $this->userBadge = new UserBadge();
    }

    /**
     * Formulaire du jour
     */
    public function index(): void
    {
        if (!isLoggedIn()) {
            redirect('/login');
        }

        $todayLog  = $this->dailyLog->getTodayLog(currentUserId());
        $appConfig = require CONFIG_PATH . '/app.php';
        $flash     = getFlash();

        require VIEWS_PATH . '/dailylog/index.php';
    }

    /**
     * Enregistrer le log du jour
     */
    public function store(): void
    {
        if (!isLoggedIn()) {
            redirect('/login');
        }

        if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
            setFlash('error', 'Token de sécurité invalide.');
            redirect('/log');
        }

        $quantity     = max(0, floatval($_POST['quantity'] ?? 0));
        $cravingLevel = max(0, min(10, (int)($_POST['craving_level'] ?? 0)));
        $mood         = trim($_POST['mood'] ?? 'neutre');
        $notes        = trim($_POST['notes'] ?? '');
        $logDate      = $_POST['log_date'] ?? date('Y-m-d');

        // Valider la date (pas dans le futur)
        if (strtotime($logDate) > strtotime('today')) {
            setFlash('error', 'La date ne peut pas être dans le futur.');
            redirect('/log');
        }

        $appConfig = require CONFIG_PATH . '/app.php';
        $validMoods = array_keys($appConfig['moods']);
        if (!in_array($mood, $validMoods)) {
            $mood = 'neutre';
        }

        try {
            $this->dailyLog->upsert(currentUserId(), [
                'log_date'      => $logDate,
                'quantity'      => $quantity,
                'craving_level' => $cravingLevel,
                'mood'          => $mood,
                'notes'         => $notes,
            ]);

            // Vérifier les badges
            $streak = $this->dailyLog->getStreak(currentUserId());
            $newBadges = $this->userBadge->checkAndAward(currentUserId(), $streak);

            if (!empty($newBadges)) {
                $badgeNames = array_map(fn($b) => $b['icon'] . ' ' . $b['name'], $newBadges);
                setFlash('success', 'Journal enregistré ! Nouveau(x) badge(s) : ' . implode(', ', $badgeNames));
            } else {
                setFlash('success', 'Journal du jour enregistré avec succès !');
            }
        } catch (Exception $e) {
            error_log("DailyLog store error: " . $e->getMessage());
            setFlash('error', 'Erreur lors de l\'enregistrement.');
        }

        redirect('/dashboard');
    }

    /**
     * Historique
     */
    public function history(): void
    {
        if (!isLoggedIn()) {
            redirect('/login');
        }

        $page   = max(1, (int)($_GET['page'] ?? 1));
        $limit  = 15;
        $offset = ($page - 1) * $limit;

        $logs      = $this->dailyLog->getHistory(currentUserId(), $limit, $offset);
        $totalLogs = $this->dailyLog->countByUser(currentUserId());
        $totalPages = ceil($totalLogs / $limit);
        $appConfig = require CONFIG_PATH . '/app.php';
        $flash     = getFlash();

        require VIEWS_PATH . '/dailylog/history.php';
    }

    /**
     * Formulaire d'édition
     */
    public function editForm(string $id): void
    {
        if (!isLoggedIn()) {
            redirect('/login');
        }

        $log = $this->dailyLog->findById($id);
        if (!$log || $log['user_id'] !== currentUserId()) {
            setFlash('error', 'Entrée introuvable.');
            redirect('/log/history');
        }

        $appConfig = require CONFIG_PATH . '/app.php';
        $flash     = getFlash();

        require VIEWS_PATH . '/dailylog/edit.php';
    }

    /**
     * Mettre à jour un log
     */
    public function update(string $id): void
    {
        if (!isLoggedIn()) {
            redirect('/login');
        }

        if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
            setFlash('error', 'Token de sécurité invalide.');
            redirect('/log/edit/' . $id);
        }

        $log = $this->dailyLog->findById($id);
        if (!$log || $log['user_id'] !== currentUserId()) {
            setFlash('error', 'Entrée introuvable.');
            redirect('/log/history');
        }

        try {
            $this->dailyLog->update($id, currentUserId(), [
                'log_date'      => $_POST['log_date'] ?? $log['log_date'],
                'quantity'      => max(0, floatval($_POST['quantity'] ?? 0)),
                'craving_level' => max(0, min(10, (int)($_POST['craving_level'] ?? 0))),
                'mood'          => trim($_POST['mood'] ?? 'neutre'),
                'notes'         => trim($_POST['notes'] ?? ''),
            ]);

            setFlash('success', 'Entrée mise à jour.');
        } catch (Exception $e) {
            error_log("DailyLog update error: " . $e->getMessage());
            setFlash('error', 'Erreur lors de la mise à jour.');
        }

        redirect('/log/history');
    }

    /**
     * Supprimer un log
     */
    public function delete(string $id): void
    {
        if (!isLoggedIn()) {
            redirect('/login');
        }

        if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
            setFlash('error', 'Token de sécurité invalide.');
            redirect('/log/history');
        }

        $this->dailyLog->delete($id, currentUserId());
        setFlash('success', 'Entrée supprimée.');
        redirect('/log/history');
    }
}
