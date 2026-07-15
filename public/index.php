<?php
/**
 * =====================================================
 * APPLICATION FRONT CONTROLLER
 * =====================================================
 * Point d'entrée unique de l'application
 * Gère le routage et l'initialisation
 */

// Démarrer les sessions
session_start();

// Déterminer le chemin racine
define('ROOT_DIR', dirname(dirname(__FILE__)));
define('APP_DIR', ROOT_DIR . '/app');
define('PUBLIC_DIR', ROOT_DIR . '/public');

// Inclure les fichiers d'initialisation
require_once ROOT_DIR . '/app/config/database.php';
require_once ROOT_DIR . '/app/helpers/helpers.php';
require_once ROOT_DIR . '/app/middleware/AuthMiddleware.php';

// Charger les modèles
require_once APP_DIR . '/models/User.php';
require_once APP_DIR . '/models/Student.php';
require_once APP_DIR . '/models/Teacher.php';
require_once APP_DIR . '/models/Admin.php';

// Charger les contrôleurs
require_once APP_DIR . '/controllers/AuthController.php';
require_once APP_DIR . '/controllers/AdminController.php';
require_once APP_DIR . '/controllers/StudentController.php';
require_once APP_DIR . '/controllers/TeacherController.php';

/**
 * =====================================================
 * ROUTAGE
 * =====================================================
 */

// Récupérer l'URI demandée
$requestUri = $_SERVER['REQUEST_URI'];
$basePath = '/LearnServer/public';
$path = str_replace($basePath, '', $requestUri);
$path = trim($path, '/');

// Si vide, afficher la page de connexion
if (empty($path)) {
    $path = 'index';
}

/**
 * Rôle et redirection si non authentifié
 */
$isAuthenticated = isAuthenticated();
$userRole = $_SESSION['user_role'] ?? null;

/**
 * Routes publiques (pas d'authentification requise)
 */
if ($path === 'index' || $path === '' || preg_match('|^index\.php|', $path)) {
    if ($isAuthenticated) {
        $redirects = [
            'admin'   => baseUrl('admin/dashboard'),
            'student' => baseUrl('student/dashboard'),
            'teacher' => baseUrl('teacher/dashboard')
        ];
        header("Location: " . ($redirects[$userRole] ?? baseUrl('index.php')));
        exit;
    }
    $auth = new AuthController($pdo);
    $auth->showLogin();
    exit;
}

if ($path === 'register') {
    if ($isAuthenticated) {
        redirect('index.php');
    }
    $auth = new AuthController($pdo);
    $auth->showRegister();
    exit;
}

if ($path === 'login' || preg_match('|^login\.php|', $path)) {
    $_SERVER['REQUEST_METHOD'] = 'POST';
    $auth = new AuthController($pdo);
    $auth->login();
    exit;
}

if ($path === 'do-register') {
    $_SERVER['REQUEST_METHOD'] = 'POST';
    $auth = new AuthController($pdo);
    $auth->register();
    exit;
}

if ($path === 'logout') {
    $auth = new AuthController($pdo);
    $auth->logout();
    exit;
}

/**
 * Routes admin
 */
if (preg_match('|^admin/(.*)$|', $path, $matches)) {
    AuthMiddleware::requireAdmin();
    $admin = new AdminController($pdo);
    $action = $matches[1] ?? 'dashboard';

    switch ($action) {
        case 'dashboard':
        case '':
            $admin->dashboard();
            break;
        case 'students':
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_student'])) {
                $admin->addStudent();
            } elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_student'])) {
                $admin->updateStudent();
            } elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_student'])) {
                $admin->deleteStudent();
            } else {
                $admin->students();
            }
            break;
        case 'teachers':
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_teacher'])) {
                $admin->addTeacher();
            } elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_teacher'])) {
                $admin->updateTeacher();
            } elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_teacher'])) {
                $admin->deleteTeacher();
            } else {
                $admin->teachers();
            }
            break;
        case 'subjects':
            header('Location: /LearnServer/admin/subjects.php');
            exit;
            break;
        case 'schedule':
            header('Location: /LearnServer/admin/schedule.php');
            exit;
            break;
        case 'notes':
            header('Location: /LearnServer/admin/notes.php');
            exit;
            break;
        case 'settings':
        case 'profile':
            $admin->settings();
            break;
        default:
            http_response_code(404);
            die('Page non trouvée');
    }
    exit;
}

/**
 * Routes étudiant
 */
if (preg_match('|^student/(.*)$|', $path, $matches)) {
    AuthMiddleware::requireStudent();
    $student = new StudentController($pdo);
    $action = $matches[1] ?? 'dashboard';

    switch ($action) {
        case 'dashboard':
        case '':
            $student->dashboard();
            break;
        case 'notes':
            $student->notes();
            break;
        case 'schedule':
            $student->schedule();
            break;
        case 'profile':
            $student->profile();
            break;
        default:
            http_response_code(404);
            die('Page non trouvée');
    }
    exit;
}

/**
 * Routes enseignant
 */
if (preg_match('|^teacher/(.*)$|', $path, $matches)) {
    AuthMiddleware::requireTeacher();
    $teacher = new TeacherController($pdo);
    $action = $matches[1] ?? 'dashboard';

    switch ($action) {
        case 'dashboard':
        case '':
            $teacher->dashboard();
            break;
        case 'schedule':
            $teacher->schedule();
            break;
        case 'profile':
            $teacher->profile();
            break;
        default:
            http_response_code(404);
            die('Page non trouvée');
    }
    exit;
}

/**
 * Si rien ne correspond
 */
http_response_code(404);
die('Page non trouvée: ' . htmlspecialchars($path));
