<?php
/**
 * BreakFree - AuthController
 */

require_once MODELS_PATH . '/User.php';
require_once MODELS_PATH . '/LoginAttempt.php';

class AuthController
{
    private User $user;
    private LoginAttempt $loginAttempt;

    public function __construct()
    {
        $this->user = new User();
        $this->loginAttempt = new LoginAttempt();
    }

    /**
     * Afficher le formulaire de connexion
     */
    public function loginForm(): void
    {
        if (isLoggedIn()) {
            redirect('/dashboard');
        }
        $flash = getFlash();
        require VIEWS_PATH . '/auth/login.php';
    }

    /**
     * Traiter la connexion
     */
    public function login(): void
    {
        if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
            setFlash('error', 'Token de sécurité invalide.');
            redirect('/login');
        }

        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $ip       = getClientIp();

        // Rate limiting
        if ($this->loginAttempt->isBlocked($ip)) {
            setFlash('error', 'Trop de tentatives. Réessayez dans 15 minutes.');
            redirect('/login');
        }

        // Validation
        if (empty($email) || empty($password)) {
            setFlash('error', 'Email et mot de passe requis.');
            setOldInput(['email' => $email]);
            redirect('/login');
        }

        if (!isValidEmail($email)) {
            setFlash('error', 'Format email invalide.');
            setOldInput(['email' => $email]);
            redirect('/login');
        }

        // Authentification
        $user = $this->user->findByEmail($email);

        if (!$user || !$this->user->verifyPassword($password, $user['password'])) {
            $this->loginAttempt->record($ip, $email);
            setFlash('error', 'Email ou mot de passe incorrect.');
            setOldInput(['email' => $email]);
            redirect('/login');
        }

        // Succès - effacer les tentatives de login pour cette IP
        $this->loginAttempt->clearForIp($ip);
        clearOldInput();
        session_regenerate_id(true);
        $_SESSION['user_id']   = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['user_email'] = $user['email'];

        redirect('/dashboard');
    }

    /**
     * Afficher le formulaire d'inscription
     */
    public function registerForm(): void
    {
        if (isLoggedIn()) {
            redirect('/dashboard');
        }
        $flash = getFlash();
        require VIEWS_PATH . '/auth/register.php';
    }

    /**
     * Traiter l'inscription
     */
    public function register(): void
    {
        if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
            setFlash('error', 'Token de sécurité invalide.');
            redirect('/register');
        }

        $name     = trim($_POST['name'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm  = $_POST['password_confirm'] ?? '';

        $errors = [];

        if (empty($name) || strlen($name) < 2) {
            $errors[] = 'Le nom doit contenir au moins 2 caractères.';
        }
        if (!isValidEmail($email)) {
            $errors[] = 'Format email invalide.';
        }
        if (strlen($password) < 8) {
            $errors[] = 'Le mot de passe doit contenir au moins 8 caractères.';
        }
        if (!preg_match('/[A-Z]/', $password) || !preg_match('/[0-9]/', $password)) {
            $errors[] = 'Le mot de passe doit contenir au moins une majuscule et un chiffre.';
        }
        if ($password !== $confirm) {
            $errors[] = 'Les mots de passe ne correspondent pas.';
        }
        if ($this->user->emailExists($email)) {
            $errors[] = 'Cet email est déjà utilisé.';
        }

        if (!empty($errors)) {
            setFlash('error', implode('<br>', $errors));
            setOldInput(['name' => $name, 'email' => $email]);
            redirect('/register');
        }

        try {
            $newUser = $this->user->create($name, $email, $password);
            if ($newUser) {
                clearOldInput();
                session_regenerate_id(true);
                $_SESSION['user_id']   = $newUser['id'];
                $_SESSION['user_name'] = $newUser['name'];
                $_SESSION['user_role'] = $newUser['role'];
                $_SESSION['user_email'] = $newUser['email'];

                setFlash('success', 'Bienvenue sur BreakFree ! Configurez votre profil pour commencer.');
                redirect('/profile');
            }
        } catch (Exception $e) {
            error_log("Registration error: " . $e->getMessage());
            setFlash('error', 'Une erreur est survenue lors de l\'inscription.');
            setOldInput(['name' => $name, 'email' => $email]);
            redirect('/register');
        }
    }

    /**
     * Déconnexion
     */
    public function logout(): void
    {
        session_unset();
        session_destroy();
        session_start();
        setFlash('success', 'Vous avez été déconnecté.');
        redirect('/login');
    }

    /**
     * Formulaire de mot de passe oublié
     */
    public function forgotPasswordForm(): void
    {
        $flash = getFlash();
        require VIEWS_PATH . '/auth/forgot-password.php';
    }

    /**
     * Traiter la demande de reset
     */
    public function forgotPassword(): void
    {
        if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
            setFlash('error', 'Token de sécurité invalide.');
            redirect('/forgot-password');
        }

        $email = trim($_POST['email'] ?? '');

        // Message générique pour éviter l'énumération d'emails
        setFlash('success', 'Si un compte existe avec cet email, un lien de réinitialisation a été envoyé.');

        if (isValidEmail($email) && $this->user->emailExists($email)) {
            $token = bin2hex(random_bytes(32));
            $this->user->setResetToken($email, $token);

            // En production, envoyer par email
            // Pour le développement, on log le lien
            $resetLink = BASE_URL . '/reset-password?token=' . $token;
            error_log("Password reset link for $email: $resetLink");
        }

        redirect('/forgot-password');
    }

    /**
     * Formulaire de réinitialisation
     */
    public function resetPasswordForm(): void
    {
        $token = $_GET['token'] ?? '';
        if (empty($token)) {
            setFlash('error', 'Token invalide.');
            redirect('/login');
        }

        $user = $this->user->findByResetToken($token);
        if (!$user) {
            setFlash('error', 'Token expiré ou invalide.');
            redirect('/forgot-password');
        }

        $flash = getFlash();
        require VIEWS_PATH . '/auth/reset-password.php';
    }

    /**
     * Traiter la réinitialisation
     */
    public function resetPassword(): void
    {
        if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
            setFlash('error', 'Token de sécurité invalide.');
            redirect('/login');
        }

        $token    = $_POST['token'] ?? '';
        $password = $_POST['password'] ?? '';
        $confirm  = $_POST['password_confirm'] ?? '';

        $user = $this->user->findByResetToken($token);
        if (!$user) {
            setFlash('error', 'Token expiré ou invalide.');
            redirect('/forgot-password');
        }

        if (strlen($password) < 8 || $password !== $confirm) {
            setFlash('error', 'Mot de passe invalide ou non correspondant (min 8 caractères).');
            redirect('/reset-password?token=' . $token);
        }

        $this->user->updatePassword($user['id'], $password);
        $this->user->clearResetToken($user['id']);

        setFlash('success', 'Mot de passe réinitialisé avec succès. Connectez-vous.');
        redirect('/login');
    }
}
