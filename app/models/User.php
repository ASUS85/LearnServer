<?php
/**
 * =====================================================
 * USER MODEL
 * =====================================================
 * Modèle de base pour les utilisateurs
 */

class User {
    
    protected $pdo;
    protected $table;
    protected $role;
    protected $tableMap = [
        'admin'   => 'admins',
        'student' => 'etudiants',
        'teacher' => 'enseignants'
    ];

    public function __construct($pdo, $role) {
        $this->pdo = $pdo;
        $this->role = $role;
        $this->table = $this->tableMap[$role];
    }

    /**
     * Récupérer un utilisateur par email
     */
    public function findByEmail($email) {
        $stmt = $this->pdo->prepare("SELECT * FROM {$this->table} WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    /**
     * Récupérer un utilisateur par ID
     */
    public function findById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM {$this->table} WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * Récupérer tous les utilisateurs
     */
    public function getAll() {
        return $this->pdo->query("SELECT * FROM {$this->table}")->fetchAll();
    }

    /**
     * Créer un utilisateur
     */
    public function create($data) {
        // À implémenter dans les classes enfants
        return false;
    }

    /**
     * Mettre à jour un utilisateur
     */
    public function update($id, $data) {
        // À implémenter dans les classes enfants
        return false;
    }

    /**
     * Supprimer un utilisateur
     */
    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM {$this->table} WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Compter les utilisateurs
     */
    public function count() {
        return $this->pdo->query("SELECT COUNT(*) FROM {$this->table}")->fetchColumn();
    }
}
