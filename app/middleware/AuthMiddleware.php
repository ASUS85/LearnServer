<?php
/**
 * =====================================================
 * AUTHENTICATION MIDDLEWARE
 * =====================================================
 * Vérifier l'authentification et le rôle de l'utilisateur
 */

class AuthMiddleware {

    /**
     * Vérifier que l'utilisateur est connecté
     */
    public static function requireLogin() {
        if (!isAuthenticated()) {
            redirect('index.php');
        }
    }

    /**
     * Vérifier que l'utilisateur a un rôle spécifique
     */
    public static function requireRole($role) {
        if (!hasRole($role)) {
            http_response_code(403);
            die('Accès refusé. Vous n\'avez pas les droits nécessaires.');
        }
    }

    /**
     * Vérifier que l'utilisateur est admin
     */
    public static function requireAdmin() {
        self::requireLogin();
        self::requireRole('admin');
    }

    /**
     * Vérifier que l'utilisateur est étudiant
     */
    public static function requireStudent() {
        self::requireLogin();
        self::requireRole('student');
    }

    /**
     * Vérifier que l'utilisateur est enseignant
     */
    public static function requireTeacher() {
        self::requireLogin();
        self::requireRole('teacher');
    }

    /**
     * Rediriger si déjà connecté
     */
    public static function redirectIfAuthenticated() {
        if (isAuthenticated()) {
            $role = $_SESSION['user_role'];
            $redirects = [
                'admin'   => 'admin/dashboard',
                'student' => 'student/dashboard',
                'teacher' => 'teacher/dashboard'
            ];
            
            if (isset($redirects[$role])) {
                redirect($redirects[$role]);
            }
        }
    }
}
