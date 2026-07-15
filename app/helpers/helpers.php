<?php
/**
 * =====================================================
 * HELPER FUNCTIONS
 * =====================================================
 * Fonctions utilitaires globales pour l'application
 */

/**
 * Chemin basique vers le répertoire racine
 */
function basePath($path = '') {
    $base = dirname(dirname(dirname(__FILE__)));
    return $base . ($path ? DIRECTORY_SEPARATOR . $path : '');
}

/**
 * URL de base de l'application
 */
function baseUrl($path = '') {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $base = $protocol . '://' . $host . '/LearnServer/public';
    return $base . ($path ? '/' . ltrim($path, '/') : '');
}

/**
 * Chemin vers les vues
 */
function viewPath($view) {
    return basePath('app/views') . DIRECTORY_SEPARATOR . str_replace('.', DIRECTORY_SEPARATOR, $view) . '.php';
}

/**
 * Chemin vers les assets
 */
function assetUrl($path) {
    // Pointer vers le dossier assets parent jusqu'à ce que les fichiers soient copiés
    $basePath = '/LearnServer/assets';
    return $basePath . '/' . ltrim($path, '/');
    // Les assets doivent être dans le dossier public pour être accessibles.
    return baseUrl('assets/' . ltrim($path, '/'));
}

/**
 * Redirection
 */
function redirect($path) {
    header("Location: " . baseUrl($path));
    exit;
}

/**
 * Rendre une vue avec variables
 */
function view($name, $data = []) {
    extract($data);
    $viewFile = viewPath($name);
    
    if (!file_exists($viewFile)) {
        die("Vue non trouvée: {$name}");
    }
    
    include $viewFile;
}

/**
 * Vérifier si l'utilisateur est connecté
 */
function isAuthenticated() {
    return isset($_SESSION['user_id']) && isset($_SESSION['user_role']);
}

/**
 * Vérifier le rôle de l'utilisateur
 */
function hasRole($role) {
    return isAuthenticated() && $_SESSION['user_role'] === $role;
}

/**
 * Obtenir l'utilisateur courant
 */
function currentUser() {
    if (!isAuthenticated()) {
        return null;
    }
    
    return [
        'id'        => $_SESSION['user_id'] ?? null,
        'role'      => $_SESSION['user_role'] ?? null,
        'nom'       => $_SESSION['user_nom'] ?? null,
        'prenom'    => $_SESSION['user_prenom'] ?? null,
        'email'     => $_SESSION['user_email'] ?? null,
        'matricule' => $_SESSION['user_matricule'] ?? null,
    ];
}

/**
 * Nettoyer les entrées
 */
function sanitize($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

/**
 * Validation email
 */
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Générer un matricule
 */
function generateMatricule($role) {
    return strtoupper(substr($role, 0, 3)) . rand(10000, 99999);
}

/**
 * Hacher un mot de passe
 */
function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

/**
 * Mot de passe par défaut pour les comptes créés par l'admin
 */
function defaultUserPassword() {
    return 'EduManage@2026';
}

/**
 * Vérifier un mot de passe
 */
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

/**
 * Retourner un message JSON
 */
function jsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

/**
 * CSRF Token - Protection contre les attaques CSRF
 */
function generateToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Vérifier le CSRF Token
 */
function verifyToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Afficher un token CSRF
 */
function csrfField() {
    echo '<input type="hidden" name="csrf_token" value="' . generateToken() . '">';
}