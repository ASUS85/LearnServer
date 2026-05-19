<?php
/**
 * =====================================================
 * ADMIN MODEL
 * =====================================================
 * Modèle pour les administrateurs
 */

class Admin extends User {

    public function __construct($pdo) {
        parent::__construct($pdo, 'admin');
    }

    /**
     * Créer un admin
     */
    public function create($data) {
        $stmt = $this->pdo->prepare("
            INSERT INTO {$this->table}(
                nom,
                prenom,
                email,
                mot_de_passe
            )
            VALUES(?, ?, ?, ?)
        ");

        return $stmt->execute([
            $data['nom'],
            $data['prenom'],
            $data['email'],
            isset($data['mot_de_passe']) ? hashPassword($data['mot_de_passe']) : null
        ]);
    }

    /**
     * Mettre à jour un admin
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
     * Récupérer les statistiques
     */
    public function getStatistics() {
        return [
            'total_students' => $this->pdo->query("SELECT COUNT(*) FROM etudiants")->fetchColumn(),
            'total_teachers' => $this->pdo->query("SELECT COUNT(*) FROM enseignants")->fetchColumn(),
            'total_subjects'  => $this->pdo->query("SELECT COUNT(*) FROM matieres")->fetchColumn(),
            'total_schedules' => $this->pdo->query("SELECT COUNT(*) FROM emplois_temps")->fetchColumn(),
        ];
    }
}
