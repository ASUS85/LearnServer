<?php
/**
 * =====================================================
 * STUDENT MODEL
 * =====================================================
 * Modèle pour gérer les étudiants
 */

class Student extends User {

    public function __construct($pdo) {
        parent::__construct($pdo, 'student');
    }

    /**
     * Récupérer tous les étudiants avec leurs filières et niveaux
     */
    public function getAllWithDetails() {
        return $this->pdo->query("
            SELECT 
                e.*,
                f.nom_filiere,
                n.nom_niveau
            FROM {$this->table} e
            LEFT JOIN filieres f ON e.filiere_id = f.id
            LEFT JOIN niveaux n ON e.niveau_id = n.id
            ORDER BY e.id DESC
        ")->fetchAll();
    }

    /**
     * Créer un étudiant
     */
    public function create($data) {
        $stmt = $this->pdo->prepare("
            INSERT INTO {$this->table}(
                matricule,
                nom,
                prenom,
                email,
                mot_de_passe,
                telephone,
                filiere_id,
                niveau_id
            )
            VALUES(?, ?, ?, ?, ?, ?, ?, ?)
        ");

        return $stmt->execute([
            $data['matricule'] ?? generateMatricule('student'),
            $data['nom'],
            $data['prenom'],
            $data['email'],
            isset($data['mot_de_passe']) ? hashPassword($data['mot_de_passe']) : null,
            $data['telephone'] ?? null,
            $data['filiere_id'] ?? null,
            $data['niveau_id'] ?? null
        ]);
    }

    /**
     * Mettre à jour un étudiant
     */
    public function update($id, $data) {
        $fields = [];
        $values = [];

        foreach ($data as $key => $value) {
            if ($key !== 'id') {
                if ($key === 'mot_de_passe' && !empty($value)) {
                    $value = hashPassword($value);
                }
                $fields[] = "{$key} = ?";
                $values[] = $value;
            }
        }
        $values[] = $id;

        if (empty($fields)) {
            return false;
        }

        $stmt = $this->pdo->prepare("
            UPDATE {$this->table}
            SET " . implode(', ', $fields) . "
            WHERE id = ?
        ");

        return $stmt->execute($values);
    }

    /**
     * Récupérer les notes d'un étudiant
     */
    public function getNotes($studentId) {
        return $this->pdo->prepare("
            SELECT n.*, m.nom_matiere
            FROM notes n
            LEFT JOIN matieres m ON n.matiere_id = m.id
            WHERE n.etudiant_id = ?
            ORDER BY n.date_creation DESC
        ")->execute([$studentId]) ? $this->pdo->prepare("
            SELECT n.*, m.nom_matiere
            FROM notes n
            LEFT JOIN matieres m ON n.matiere_id = m.id
            WHERE n.etudiant_id = ?
            ORDER BY n.date_creation DESC
        ")->fetchAll() : [];
    }
}
