<?php
/**
 * BreakFree - Modèle LoginAttempt (Rate Limiting)
 */

require_once CONFIG_PATH . '/database.php';

class LoginAttempt
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    /**
     * Enregistrer une tentative de connexion
     */
    public function record(string $ip, string $email): void
    {
        $stmt = $this->db->prepare("
            INSERT INTO login_attempts (ip_address, email) VALUES (:ip, :email)
        ");
        $stmt->execute([':ip' => $ip, ':email' => $email]);
    }

    /**
     * Compter les tentatives récentes
     */
    public function countRecent(string $ip, int $minutes = 15): int
    {
        $cutoff = date('Y-m-d H:i:s', strtotime("-{$minutes} minutes"));
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as count FROM login_attempts
            WHERE ip_address = :ip AND attempted_at > :cutoff
        ");
        $stmt->execute([':ip' => $ip, ':cutoff' => $cutoff]);
        return (int)($stmt->fetch()['count'] ?? 0);
    }

    /**
     * Vérifier si l'IP est bloquée
     */
    public function isBlocked(string $ip, int $maxAttempts = 50, int $lockoutMinutes = 15): bool
    {
        return $this->countRecent($ip, $lockoutMinutes) >= $maxAttempts;
    }

    /**
     * Supprimer les tentatives pour une IP (après login réussi)
     */
    public function clearForIp(string $ip): void
    {
        $stmt = $this->db->prepare("DELETE FROM login_attempts WHERE ip_address = :ip");
        $stmt->execute([':ip' => $ip]);
    }

    /**
     * Nettoyer les anciennes tentatives
     */
    public function cleanup(int $hours = 1): void
    {
        $this->db->exec("DELETE FROM login_attempts WHERE attempted_at < NOW() - INTERVAL '1 hour'");
    }
}
