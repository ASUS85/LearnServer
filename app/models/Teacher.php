<?php
/**
 * =====================================================
 * TEACHER MODEL
 * =====================================================
 * Modèle pour gérer les enseignants
 */

class Teacher extends User {

    public function __construct($pdo) {
        parent::__construct($pdo, 'teacher');
    }

    /**
     * Récupérer tous les enseignants
     */
    public function getAllWithDetails() {
        return $this->pdo->query("
            SELECT *
            FROM {$this->table}
            ORDER BY id DESC
        ")->fetchAll();
    }

    /**
     * Créer un enseignant
     */
    public function create($data) {
        $stmt = $this->pdo->prepare("
            INSERT INTO {$this->table}(
                matricule,
                nom,
                prenom,
                email,
                mot_de_passe,
                telephone
            )
            VALUES(?, ?, ?, ?, ?, ?)
        ");

        return $stmt->execute([
            $data['matricule'] ?? generateMatricule('teacher'),
            $data['nom'],
            $data['prenom'],
            $data['email'],
            isset($data['mot_de_passe']) ? hashPassword($data['mot_de_passe']) : null,
            $data['telephone'] ?? null
        ]);
    }

    /**
     * Mettre à jour un enseignant
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
     * Récupérer les classes enseignées
     */
    public function getClasses($teacherId) {
        return $this->pdo->prepare("
            SELECT DISTINCT m.*, c.nom_classe
            FROM enseignant_matiere em
            JOIN matieres m ON em.matiere_id = m.id
            JOIN classes c ON m.classe_id = c.id
            WHERE em.enseignant_id = ?
        ")->execute([$teacherId]) ? $this->pdo->prepare("
            SELECT DISTINCT m.*, c.nom_classe
            FROM enseignant_matiere em
            JOIN matieres m ON em.matiere_id = m.id
            JOIN classes c ON m.classe_id = c.id
            WHERE em.enseignant_id = ?
        ")->fetchAll() : [];
    }
}
