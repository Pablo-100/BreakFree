-- ============================================
-- BreakFree - Script SQL PostgreSQL (Neon)
-- ============================================
-- Compatible Neon Serverless PostgreSQL
-- Exécuter ce script pour initialiser la BDD
-- ============================================

-- Extension UUID
CREATE EXTENSION IF NOT EXISTS "pgcrypto";

-- ============================================
-- Table: users
-- ============================================
CREATE TABLE IF NOT EXISTS users (
    id             UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    name           VARCHAR(100) NOT NULL,
    email          VARCHAR(255) NOT NULL UNIQUE,
    password       VARCHAR(255) NOT NULL,
    addiction_type  VARCHAR(50) DEFAULT NULL,
    goal_type      VARCHAR(20) DEFAULT 'arret_total' CHECK (goal_type IN ('arret_total', 'reduction')),
    start_date     DATE DEFAULT NULL,
    daily_cost     NUMERIC(10, 2) DEFAULT 0.00,
    role           VARCHAR(10) NOT NULL DEFAULT 'user' CHECK (role IN ('user', 'admin')),
    reset_token    VARCHAR(255) DEFAULT NULL,
    reset_expires  TIMESTAMP DEFAULT NULL,
    created_at     TIMESTAMP NOT NULL DEFAULT NOW(),
    updated_at     TIMESTAMP NOT NULL DEFAULT NOW()
);

-- Index pour recherche rapide par email
CREATE INDEX IF NOT EXISTS idx_users_email ON users(email);
CREATE INDEX IF NOT EXISTS idx_users_role ON users(role);

-- ============================================
-- Table: daily_logs
-- ============================================
CREATE TABLE IF NOT EXISTS daily_logs (
    id             UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    user_id        UUID NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    log_date       DATE NOT NULL,
    quantity       NUMERIC(10, 2) DEFAULT 0,
    craving_level  SMALLINT DEFAULT 0 CHECK (craving_level >= 0 AND craving_level <= 10),
    mood           VARCHAR(20) DEFAULT 'neutre',
    notes          TEXT DEFAULT '',
    created_at     TIMESTAMP NOT NULL DEFAULT NOW(),

    -- Un seul log par jour par utilisateur
    UNIQUE(user_id, log_date)
);

CREATE INDEX IF NOT EXISTS idx_daily_logs_user_id ON daily_logs(user_id);
CREATE INDEX IF NOT EXISTS idx_daily_logs_date ON daily_logs(log_date);
CREATE INDEX IF NOT EXISTS idx_daily_logs_user_date ON daily_logs(user_id, log_date DESC);

-- ============================================
-- Table: login_attempts (rate limiting)
-- ============================================
CREATE TABLE IF NOT EXISTS login_attempts (
    id             SERIAL PRIMARY KEY,
    ip_address     VARCHAR(45) NOT NULL,
    email          VARCHAR(255) NOT NULL,
    attempted_at   TIMESTAMP NOT NULL DEFAULT NOW()
);

CREATE INDEX IF NOT EXISTS idx_login_attempts_ip ON login_attempts(ip_address, attempted_at);

-- ============================================
-- Table: user_badges
-- ============================================
CREATE TABLE IF NOT EXISTS user_badges (
    id             UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    user_id        UUID NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    badge_id       VARCHAR(50) NOT NULL,
    earned_at      TIMESTAMP NOT NULL DEFAULT NOW(),

    UNIQUE(user_id, badge_id)
);

CREATE INDEX IF NOT EXISTS idx_user_badges_user ON user_badges(user_id);

-- ============================================
-- Fonction: mise à jour updated_at automatique
-- ============================================
CREATE OR REPLACE FUNCTION update_updated_at_column()
RETURNS TRIGGER AS $$
BEGIN
    NEW.updated_at = NOW();
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

DROP TRIGGER IF EXISTS trigger_users_updated_at ON users;

CREATE TRIGGER trigger_users_updated_at
    BEFORE UPDATE ON users
    FOR EACH ROW
    EXECUTE FUNCTION update_updated_at_column();

-- ============================================
-- Admin par défaut (mot de passe: Admin123!)
-- ============================================
INSERT INTO users (name, email, password, role)
VALUES (
    'Administrateur',
    'admin@breakfree.app',
    '$2y$12$LJ3m4yPnMDGCHOIPlMVNOOuBfpCOJgJjmV7a3CaSbER/p.NyhCO/a',
    'admin'
)
ON CONFLICT (email) DO NOTHING;

-- ============================================
-- Nettoyage périodique (via cron ou EVENT SCHEDULER)
-- ============================================
-- DELETE FROM login_attempts WHERE attempted_at < NOW() - INTERVAL '1 hour';
