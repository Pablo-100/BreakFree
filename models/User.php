<?php
/**
 * BreakFree - Modèle User
 */

require_once CONFIG_PATH . '/database.php';

class User
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    /**
     * Créer un utilisateur
     */
    public function create(string $name, string $email, string $password): ?array
    {
        $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

        $stmt = $this->db->prepare("
            INSERT INTO users (name, email, password)
            VALUES (:name, :email, :password)
        ");

        $stmt->execute([
            ':name'     => $name,
            ':email'    => $email,
            ':password' => $hash,
        ]);

        $stmt = $this->db->prepare(" 
            SELECT * FROM users WHERE email = :email LIMIT 1
        ");
        $stmt->execute([':email' => $email]);
        return $stmt->fetch() ?: null;
    }

    /**
     * Trouver par email
     */
    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
        $stmt->execute([':email' => $email]);
        return $stmt->fetch() ?: null;
    }

    /**
     * Trouver par ID
     */
    public function findById(string $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch() ?: null;
    }

    /**
     * Mettre à jour le profil
     */
    public function updateProfile(string $id, array $data): bool
    {
        $stmt = $this->db->prepare("
            UPDATE users SET
                name           = :name,
                addiction_type = :addiction_type,
                goal_type      = :goal_type,
                start_date     = :start_date,
                daily_cost     = :daily_cost,
                updated_at     = NOW()
            WHERE id = :id
        ");

        return $stmt->execute([
            ':id'             => $id,
            ':name'           => $data['name'],
            ':addiction_type'  => $data['addiction_type'],
            ':goal_type'       => $data['goal_type'],
            ':start_date'      => $data['start_date'],
            ':daily_cost'      => $data['daily_cost'],
        ]);
    }

    /**
     * Changer le mot de passe
     */
    public function updatePassword(string $id, string $newPassword): bool
    {
        $hash = password_hash($newPassword, PASSWORD_BCRYPT, ['cost' => 12]);
        $stmt = $this->db->prepare("UPDATE users SET password = :password, updated_at = NOW() WHERE id = :id");
        return $stmt->execute([':password' => $hash, ':id' => $id]);
    }

    /**
     * Définir le token de reset
     */
    public function setResetToken(string $email, string $token): bool
    {
        $hash    = hash('sha256', $token);
        $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

        $stmt = $this->db->prepare("
            UPDATE users SET reset_token = :token, reset_expires = :expires, updated_at = NOW()
            WHERE email = :email
        ");

        return $stmt->execute([
            ':token'   => $hash,
            ':expires' => $expires,
            ':email'   => $email,
        ]);
    }

    /**
     * Trouver par token de reset
     */
    public function findByResetToken(string $token): ?array
    {
        $hash = hash('sha256', $token);
        $stmt = $this->db->prepare("
            SELECT * FROM users
            WHERE reset_token = :token AND reset_expires > NOW()
            LIMIT 1
        ");
        $stmt->execute([':token' => $hash]);
        return $stmt->fetch() ?: null;
    }

    /**
     * Effacer le token de reset
     */
    public function clearResetToken(string $id): bool
    {
        $stmt = $this->db->prepare("
            UPDATE users SET reset_token = NULL, reset_expires = NULL, updated_at = NOW() WHERE id = :id
        ");
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Vérifier le mot de passe
     */
    public function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    /**
     * Compter le nombre total d'utilisateurs
     */
    public function countAll(): int
    {
        $stmt = $this->db->query("SELECT COUNT(*) as count FROM users WHERE role = 'user'");
        return (int)($stmt->fetch()['count'] ?? 0);
    }

    /**
     * Lister tous les utilisateurs (pour admin)
     */
    public function listAll(int $limit = 50, int $offset = 0): array
    {
        $stmt = $this->db->prepare("
            SELECT id, name, email, addiction_type, start_date, role, created_at
            FROM users
            ORDER BY created_at DESC
            LIMIT :limit OFFSET :offset
        ");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Supprimer un utilisateur
     */
    public function delete(string $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM users WHERE id = :id AND role != 'admin'");
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Vérifier si email existe déjà
     */
    public function emailExists(string $email): bool
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM users WHERE email = :email");
        $stmt->execute([':email' => $email]);
        return ((int)$stmt->fetch()['count']) > 0;
    }
}
