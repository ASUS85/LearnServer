<?php
/**
 * =====================================================
 * DATABASE CONFIGURATION
 * =====================================================
 * Configuration centralisée de la base de données
 * Utilise PDO pour une connexion sécurisée
 */

$dbConfig = [
    'host'     => 'localhost',
    'dbname'   => 'gestion_cours_db',
    'username' => 'root',
    'password' => '',
    'charset'  => 'utf8mb4'
];

try {
    $dsn = "mysql:host={$dbConfig['host']};dbname={$dbConfig['dbname']};charset={$dbConfig['charset']}";
    
    $pdo = new PDO(
        $dsn,
        $dbConfig['username'],
        $dbConfig['password']
    );

    // Configuration des erreurs
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}
