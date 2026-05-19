<?php
/**
 * =====================================================
 * AUTH CONTROLLER
 * =====================================================
 * Contrôle l'authentification (login, register, logout)
 */

class AuthController {

    private $pdo;
    private $student;
    private $teacher;
    private $admin;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->student = new Student($pdo);
        $this->teacher = new Teacher($pdo);
        $this->admin = new Admin($pdo);
    }

    /**
     * Afficher la page de login
     */
    public function showLogin() {
        view('auth.login');
    }

    /**
     * Afficher la page d'inscription
     */
    public function showRegister() {
        view('auth.register');
    }

    /**
     * Traiter le login
     */
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->showLogin();
            return;
        }

        $email = sanitize($_POST['email'] ?? '');
        $password = sanitize($_POST['password'] ?? '');
        $role = sanitize($_POST['role'] ?? '');
        
        $error = '';

        // Validation
        if (empty($email) || empty($password) || empty($role)) {
            $error = "Tous les champs sont obligatoires.";
        }

        // Vérifier le rôle
        if (empty($error) && !in_array($role, ['admin', 'student', 'teacher'])) {
            $error = "Type de compte invalide.";
        }

        // Authentification
        if (empty($error)) {
            $user = null;

            if ($role === 'admin') {
                $user = $this->admin->findByEmail($email);
            } elseif ($role === 'student') {
                $user = $this->student->findByEmail($email);
            } else {
                $user = $this->teacher->findByEmail($email);
            }

            if (!$user) {
                $error = "Compte introuvable.";
            } elseif (!verifyPassword($password, $user['mot_de_passe'])) {
                $error = "Mot de passe incorrect.";
            }
        }

        // Si erreur
        if (!empty($error)) {
            view('auth.login', ['error' => $error]);
            return;
        }

        // Créer la session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_role'] = $role;
        $_SESSION['user_nom'] = $user['nom'];
        $_SESSION['user_prenom'] = $user['prenom'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_matricule'] = $user['matricule'] ?? null;

        // Redirection
        $redirects = [
            'admin'   => 'admin/dashboard',
            'student' => 'student/dashboard',
            'teacher' => 'teacher/dashboard'
        ];

        redirect($redirects[$role]);
    }

    /**
     * Traiter l'inscription
     */
    public function register() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->showRegister();
            return;
        }

        $nom = sanitize($_POST['nom'] ?? '');
        $prenom = sanitize($_POST['prenom'] ?? '');
        $email = sanitize($_POST['email'] ?? '');
        $telephone = sanitize($_POST['telephone'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        $role = sanitize($_POST['role'] ?? '');

        $message = '';
        $error = '';

        // Validation
        if (empty($nom) || empty($prenom) || empty($email) || empty($password) || empty($role)) {
            $error = "Veuillez remplir tous les champs.";
        } elseif ($password !== $confirmPassword) {
            $error = "Les mots de passe ne correspondent pas.";
        } elseif (!isValidEmail($email)) {
            $error = "Email invalide.";
        } elseif (strlen($password) < 6) {
            $error = "Le mot de passe doit contenir au moins 6 caractères.";
        }

        // Vérifier l'email
        if (empty($error)) {
            $user = null;

            if ($role === 'admin') {
                $user = $this->admin->findByEmail($email);
            } elseif ($role === 'student') {
                $user = $this->student->findByEmail($email);
            } else {
                $user = $this->teacher->findByEmail($email);
            }

            if ($user) {
                $error = "Cet email existe déjà.";
            }
        }

        // Créer l'utilisateur
        if (empty($error)) {
            $data = [
                'nom'       => $nom,
                'prenom'    => $prenom,
                'email'     => $email,
                'mot_de_passe' => $password,
                'telephone' => $telephone
            ];

            try {
                if ($role === 'admin') {
                    $this->admin->create($data);
                } elseif ($role === 'student') {
                    $this->student->create($data);
                } else {
                    $this->teacher->create($data);
                }

                $message = "Compte créé avec succès. Veuillez vous connecter.";
                view('auth.login', ['message' => $message]);
                return;

            } catch (Exception $e) {
                $error = "Erreur lors de la création du compte.";
            }
        }

        view('auth.register', ['error' => $error]);
    }

    /**
     * Logout
     */
    public function logout() {
        session_destroy();
        redirect('index.php');
    }
}
