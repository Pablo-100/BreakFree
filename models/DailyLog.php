<?php
/**
 * BreakFree - Modèle DailyLog
 */

require_once CONFIG_PATH . '/database.php';

class DailyLog
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    /**
     * Créer ou mettre à jour un log quotidien (upsert)
     */
    public function upsert(string $userId, array $data): bool
    {
        $stmt = $this->db->prepare("
            INSERT INTO daily_logs (user_id, log_date, quantity, craving_level, mood, notes)
            VALUES (:user_id, :log_date, :quantity, :craving_level, :mood, :notes)
            ON CONFLICT (user_id, log_date)
            DO UPDATE SET
                quantity      = EXCLUDED.quantity,
                craving_level = EXCLUDED.craving_level,
                mood          = EXCLUDED.mood,
                notes         = EXCLUDED.notes
        ");

        return $stmt->execute([
            ':user_id'       => $userId,
            ':log_date'      => $data['log_date'],
            ':quantity'      => $data['quantity'],
            ':craving_level' => $data['craving_level'],
            ':mood'          => $data['mood'],
            ':notes'         => $data['notes'] ?? '',
        ]);
    }

    /**
     * Trouver un log par ID
     */
    public function findById(string $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM daily_logs WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch() ?: null;
    }

    /**
     * Mettre à jour un log existant
     */
    public function update(string $id, string $userId, array $data): bool
    {
        $stmt = $this->db->prepare("
            UPDATE daily_logs SET
                log_date      = :log_date,
                quantity      = :quantity,
                craving_level = :craving_level,
                mood          = :mood,
                notes         = :notes
            WHERE id = :id AND user_id = :user_id
        ");

        return $stmt->execute([
            ':id'            => $id,
            ':user_id'       => $userId,
            ':log_date'      => $data['log_date'],
            ':quantity'      => $data['quantity'],
            ':craving_level' => $data['craving_level'],
            ':mood'          => $data['mood'],
            ':notes'         => $data['notes'] ?? '',
        ]);
    }

    /**
     * Supprimer un log
     */
    public function delete(string $id, string $userId): bool
    {
        $stmt = $this->db->prepare("DELETE FROM daily_logs WHERE id = :id AND user_id = :user_id");
        return $stmt->execute([':id' => $id, ':user_id' => $userId]);
    }

    /**
     * Récupérer le log du jour
     */
    public function getTodayLog(string $userId): ?array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM daily_logs
            WHERE user_id = :user_id AND log_date = CURRENT_DATE
            LIMIT 1
        ");
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetch() ?: null;
    }

    /**
     * Historique des logs (paginé)
     */
    public function getHistory(string $userId, int $limit = 30, int $offset = 0): array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM daily_logs
            WHERE user_id = :user_id
            ORDER BY log_date DESC
            LIMIT :limit OFFSET :offset
        ");
        $stmt->bindValue(':user_id', $userId);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Compter le nombre de logs d'un utilisateur
     */
    public function countByUser(string $userId): int
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM daily_logs WHERE user_id = :user_id");
        $stmt->execute([':user_id' => $userId]);
        return (int)($stmt->fetch()['count'] ?? 0);
    }

    /**
     * Données pour graphique consommation (30 derniers jours)
     */
    public function getConsumptionChart(string $userId, int $days = 30): array
    {
        $stmt = $this->db->prepare("
            SELECT log_date, quantity
            FROM daily_logs
            WHERE user_id = :user_id AND log_date >= CURRENT_DATE - :days
            ORDER BY log_date ASC
        ");
        $stmt->execute([':user_id' => $userId, ':days' => $days]);
        return $stmt->fetchAll();
    }

    /**
     * Données pour graphique cravings (30 derniers jours)
     */
    public function getCravingsChart(string $userId, int $days = 30): array
    {
        $stmt = $this->db->prepare("
            SELECT log_date, craving_level
            FROM daily_logs
            WHERE user_id = :user_id AND log_date >= CURRENT_DATE - :days
            ORDER BY log_date ASC
        ");
        $stmt->execute([':user_id' => $userId, ':days' => $days]);
        return $stmt->fetchAll();
    }

    /**
     * Streak: nombre de jours consécutifs avec quantité = 0
     */
    public function getStreak(string $userId): int
    {
        // On récupère les logs ordonnés par date DESC
        $stmt = $this->db->prepare("
            SELECT log_date, quantity
            FROM daily_logs
            WHERE user_id = :user_id
            ORDER BY log_date DESC
        ");
        $stmt->execute([':user_id' => $userId]);
        $logs = $stmt->fetchAll();

        $streak = 0;
        $expectedDate = new DateTime('today');

        foreach ($logs as $log) {
            $logDate = new DateTime($log['log_date']);
            if ($logDate->format('Y-m-d') === $expectedDate->format('Y-m-d')) {
                if ((float)$log['quantity'] == 0) {
                    $streak++;
                    $expectedDate->modify('-1 day');
                } else {
                    break;
                }
            } else {
                break;
            }
        }

        return $streak;
    }

    /**
     * Total consommé sur une période
     */
    public function getTotalConsumed(string $userId, int $days = 0): float
    {
        $sql = "SELECT COALESCE(SUM(quantity), 0) as total FROM daily_logs WHERE user_id = :user_id";
        $params = [':user_id' => $userId];

        if ($days > 0) {
            $cutoff = date('Y-m-d', strtotime("-{$days} days"));
            $sql .= " AND log_date >= :cutoff";
            $params[':cutoff'] = $cutoff;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (float)($stmt->fetch()['total'] ?? 0);
    }

    /**
     * Moyenne hebdomadaire
     */
    public function getWeeklyAverage(string $userId): float
    {
        $stmt = $this->db->prepare("
            SELECT COALESCE(AVG(quantity), 0) as avg_qty
            FROM daily_logs
            WHERE user_id = :user_id AND log_date >= CURRENT_DATE - 7
        ");
        $stmt->execute([':user_id' => $userId]);
        return round((float)($stmt->fetch()['avg_qty'] ?? 0), 2);
    }

    /**
     * Tendance: comparer les 7 derniers jours vs les 7 jours précédents
     * Retourne: 'improving', 'stable', 'worsening'
     */
    public function getTrend(string $userId): string
    {
        $stmt = $this->db->prepare("
            SELECT
                COALESCE(AVG(CASE WHEN log_date >= CURRENT_DATE - 7 THEN quantity END), 0) as recent,
                COALESCE(AVG(CASE WHEN log_date >= CURRENT_DATE - 14 AND log_date < CURRENT_DATE - 7 THEN quantity END), 0) as previous
            FROM daily_logs
            WHERE user_id = :user_id AND log_date >= CURRENT_DATE - 14
        ");
        $stmt->execute([':user_id' => $userId]);
        $row = $stmt->fetch();

        $recent   = (float)$row['recent'];
        $previous = (float)$row['previous'];

        if ($previous == 0 && $recent == 0) return 'stable';
        if ($previous == 0) return 'worsening';

        $change = (($recent - $previous) / $previous) * 100;

        if ($change < -10) return 'improving';
        if ($change > 10) return 'worsening';
        return 'stable';
    }

    /**
     * Nombre de jours sans consommation (quantity = 0)
     */
    public function getCleanDays(string $userId): int
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as count FROM daily_logs
            WHERE user_id = :user_id AND quantity = 0
        ");
        $stmt->execute([':user_id' => $userId]);
        return (int)($stmt->fetch()['count'] ?? 0);
    }

    // ─── Méthodes Admin (statistiques globales) ───

    /**
     * Moyenne globale craving level
     */
    public function globalAvgCraving(): float
    {
        $stmt = $this->db->query("SELECT COALESCE(AVG(craving_level), 0) as avg FROM daily_logs");
        return round((float)($stmt->fetch()['avg'] ?? 0), 1);
    }

    /**
     * Nombre total de logs
     */
    public function globalTotalLogs(): int
    {
        $stmt = $this->db->query("SELECT COUNT(*) as count FROM daily_logs");
        return (int)($stmt->fetch()['count'] ?? 0);
    }

    /**
     * Graphe global consommation (anonymisé)
     */
    public function globalConsumptionChart(int $days = 30): array
    {
        $stmt = $this->db->prepare("
            SELECT log_date, AVG(quantity) as avg_quantity, AVG(craving_level) as avg_craving
            FROM daily_logs
            WHERE log_date >= CURRENT_DATE - :days
            GROUP BY log_date
            ORDER BY log_date ASC
        ");
        $stmt->execute([':days' => $days]);
        return $stmt->fetchAll();
    }

    /**
     * Répartition par type d'addiction (via join users)
     */
    public function globalByAddictionType(): array
    {
        $stmt = $this->db->query("
            SELECT u.addiction_type, COUNT(DISTINCT u.id) as user_count
            FROM users u
            WHERE u.addiction_type IS NOT NULL AND u.role = 'user'
            GROUP BY u.addiction_type
            ORDER BY user_count DESC
        ");
        return $stmt->fetchAll();
    }
}
