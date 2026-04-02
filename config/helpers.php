<?php
/**
 * BreakFree - Helpers / Fonctions utilitaires
 */

/**
 * Redirection HTTP
 */
function redirect(string $path): never
{
    header("Location: " . BASE_URL . $path);
    exit;
}

/**
 * Générer un token CSRF et le stocker en session
 */
function generateCsrfToken(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Valider le token CSRF
 */
function validateCsrfToken(?string $token): bool
{
    if (empty($token) || empty($_SESSION['csrf_token'])) {
        return false;
    }
    $valid = hash_equals($_SESSION['csrf_token'], $token);
    // Régénérer après validation
    unset($_SESSION['csrf_token']);
    return $valid;
}

/**
 * Champ hidden CSRF pour formulaires
 */
function csrfField(): string
{
    $token = generateCsrfToken();
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token) . '">';
}

/**
 * Échapper XSS
 */
function e(?string $string): string
{
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Vérifier si l'utilisateur est connecté
 */
function isLoggedIn(): bool
{
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Vérifier si admin
 */
function isAdmin(): bool
{
    return isLoggedIn() && ($_SESSION['user_role'] ?? '') === 'admin';
}

/**
 * Récupérer l'ID utilisateur connecté
 */
function currentUserId(): ?string
{
    return $_SESSION['user_id'] ?? null;
}

/**
 * Message flash
 */
function setFlash(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function getFlash(): ?array
{
    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $flash;
}

/**
 * Valider format email
 */
function isValidEmail(string $email): bool
{
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Obtenir l'adresse IP du client
 */
function getClientIp(): string
{
    return $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['HTTP_CLIENT_IP'] ?? $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
}

/**
 * Formater une date
 */
function formatDate(?string $date, string $format = 'd/m/Y'): string
{
    if (!$date) return '—';
    return date($format, strtotime($date));
}

/**
 * Calculer la différence en jours
 */
function daysBetween(string $from, string $to = 'now'): int
{
    $d1 = new DateTime($from);
    $d2 = new DateTime($to);
    return max(0, (int)$d1->diff($d2)->days);
}

/**
 * Formater monnaie
 */
function formatMoney(float $amount): string
{
    return number_format($amount, 2, ',', ' ') . ' €';
}

/**
 * Ancien champ de formulaire
 */
function old(string $key, string $default = ''): string
{
    return e($_SESSION['old_input'][$key] ?? $default);
}

function setOldInput(array $data): void
{
    $_SESSION['old_input'] = $data;
}

function clearOldInput(): void
{
    unset($_SESSION['old_input']);
}
