<?php
/**
 * BreakFree - Modèle UserBadge
 */

require_once CONFIG_PATH . '/database.php';

class UserBadge
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    /**
     * Attribuer un badge
     */
    public function award(string $userId, string $badgeId): bool
    {
        $stmt = $this->db->prepare("
            INSERT INTO user_badges (user_id, badge_id)
            VALUES (:user_id, :badge_id)
            ON CONFLICT (user_id, badge_id) DO NOTHING
        ");
        return $stmt->execute([':user_id' => $userId, ':badge_id' => $badgeId]);
    }

    /**
     * Récupérer les badges d'un utilisateur
     */
    public function getUserBadges(string $userId): array
    {
        $stmt = $this->db->prepare("
            SELECT badge_id, earned_at FROM user_badges
            WHERE user_id = :user_id ORDER BY earned_at ASC
        ");
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetchAll();
    }

    /**
     * Vérifier si un badge est acquis
     */
    public function hasBadge(string $userId, string $badgeId): bool
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as count FROM user_badges
            WHERE user_id = :user_id AND badge_id = :badge_id
        ");
        $stmt->execute([':user_id' => $userId, ':badge_id' => $badgeId]);
        return ((int)$stmt->fetch()['count']) > 0;
    }

    /**
     * Vérifier et attribuer les badges automatiquement
     */
    public function checkAndAward(string $userId, int $streakDays): array
    {
        $appConfig = require CONFIG_PATH . '/app.php';
        $badges = $appConfig['badges'];
        $newBadges = [];

        foreach ($badges as $badge) {
            if ($streakDays >= $badge['days'] && !$this->hasBadge($userId, $badge['id'])) {
                $this->award($userId, $badge['id']);
                $newBadges[] = $badge;
            }
        }

        return $newBadges;
    }

    /**
     * Obtenir le niveau actuel
     */
    public function getLevel(int $streakDays): array
    {
        $appConfig = require CONFIG_PATH . '/app.php';
        $levels = $appConfig['levels'];
        $currentLevel = $levels[0];

        foreach ($levels as $level) {
            if ($streakDays >= $level['min_days']) {
                $currentLevel = $level;
            }
        }

        return $currentLevel;
    }
}
