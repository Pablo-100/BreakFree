<?php
/**
 * BreakFree - Configuration base de données (Neon PostgreSQL)
 */

require_once __DIR__ . '/env.php';

class Database
{
    private static ?PDO $instance = null;

    public static function connect(): PDO
    {
        if (self::$instance === null) {
            $host    = env('DB_HOST', 'localhost');
            $port    = env('DB_PORT', '5432');
            $dbname  = env('DB_NAME', 'breakfree');
            $user    = env('DB_USER', 'postgres');
            $pass    = env('DB_PASS', '');
            $sslmode = env('DB_SSLMODE', 'require');

            $dsn = "pgsql:host={$host};port={$port};dbname={$dbname};sslmode={$sslmode}";

            try {
                self::$instance = new PDO($dsn, $user, $pass, [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ]);
            } catch (PDOException $e) {
                error_log("Database connection failed: " . $e->getMessage());
                throw new RuntimeException("Impossible de se connecter à la base de données.");
            }
        }
        return self::$instance;
    }

    /**
     * Fermer la connexion
     */
    public static function disconnect(): void
    {
        self::$instance = null;
    }
}
