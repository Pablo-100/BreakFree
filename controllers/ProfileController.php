<?php
/**
 * BreakFree - ProfileController
 */

require_once MODELS_PATH . '/User.php';

class ProfileController
{
    private User $user;

    public function __construct()
    {
        $this->user = new User();
    }

    /**
     * Afficher le profil
     */
    public function index(): void
    {
        if (!isLoggedIn()) {
            redirect('/login');
        }

        $userData  = $this->user->findById(currentUserId());
        $appConfig = require CONFIG_PATH . '/app.php';
        $flash     = getFlash();

        require VIEWS_PATH . '/profile/index.php';
    }

    /**
     * Mettre à jour le profil
     */
    public function update(): void
    {
        if (!isLoggedIn()) {
            redirect('/login');
        }

        if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
            setFlash('error', 'Token de sécurité invalide.');
            redirect('/profile');
        }

        $name          = trim($_POST['name'] ?? '');
        $addictionType = trim($_POST['addiction_type'] ?? '');
        $goalType      = trim($_POST['goal_type'] ?? 'arret_total');
        $startDate     = trim($_POST['start_date'] ?? '');
        $dailyCost     = floatval($_POST['daily_cost'] ?? 0);

        $errors = [];

        if (empty($name) || strlen($name) < 2) {
            $errors[] = 'Le nom doit contenir au moins 2 caractères.';
        }

        $appConfig = require CONFIG_PATH . '/app.php';
        $validTypes = array_keys($appConfig['addiction_types']);
        if (!in_array($addictionType, $validTypes)) {
            $errors[] = 'Type d\'addiction invalide.';
        }

        if (!in_array($goalType, ['arret_total', 'reduction'])) {
            $errors[] = 'Type d\'objectif invalide.';
        }

        if (empty($startDate) || !strtotime($startDate)) {
            $errors[] = 'Date de début invalide.';
        }

        if ($dailyCost < 0) {
            $errors[] = 'Le coût quotidien doit être positif.';
        }

        // Changement de mot de passe optionnel
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if (!empty($newPassword)) {
            if (strlen($newPassword) < 8) {
                $errors[] = 'Le nouveau mot de passe doit contenir au moins 8 caractères.';
            }
            if ($newPassword !== $confirmPassword) {
                $errors[] = 'Les mots de passe ne correspondent pas.';
            }
        }

        if (!empty($errors)) {
            setFlash('error', implode('<br>', $errors));
            redirect('/profile');
        }

        try {
            $this->user->updateProfile(currentUserId(), [
                'name'           => $name,
                'addiction_type'  => $addictionType,
                'goal_type'       => $goalType,
                'start_date'      => $startDate,
                'daily_cost'      => $dailyCost,
            ]);

            // Mettre à jour le nom en session
            $_SESSION['user_name'] = $name;

            if (!empty($newPassword)) {
                $this->user->updatePassword(currentUserId(), $newPassword);
            }

            setFlash('success', 'Profil mis à jour avec succès !');
        } catch (Exception $e) {
            error_log("Profile update error: " . $e->getMessage());
            setFlash('error', 'Erreur lors de la mise à jour du profil.');
        }

        redirect('/profile');
    }
}
