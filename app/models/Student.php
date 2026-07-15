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
        $rawPassword = trim((string)($data['mot_de_passe'] ?? ''));
        if ($rawPassword === '') {
            $rawPassword = defaultUserPassword();
        }

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
            hashPassword($rawPassword),
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
        $stmt = $this->pdo->prepare("
            SELECT n.*, m.nom_matiere
            FROM notes n
            LEFT JOIN matieres m ON n.matiere_id = m.id
            WHERE n.etudiant_id = ?
            ORDER BY n.date_note DESC, n.id DESC
        ");
        $stmt->execute([$studentId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Statistiques de l'étudiant
     */
    public function getStatistics($studentId) {
        $notesStmt = $this->pdo->prepare("SELECT COUNT(*) FROM notes WHERE etudiant_id = ?");
        $notesStmt->execute([$studentId]);

        $averageStmt = $this->pdo->prepare("SELECT ROUND(AVG(note), 2) FROM notes WHERE etudiant_id = ?");
        $averageStmt->execute([$studentId]);

        return [
            'total_notes' => (int) $notesStmt->fetchColumn(),
            'average_note' => $averageStmt->fetchColumn() ?: '0.00'
        ];
    }

    /**
     * Emploi du temps du jour de l'étudiant
     */
    public function getTodaySchedule($studentId, $day) {
        $stmt = $this->pdo->prepare("
            SELECT DISTINCT
                emplois_temps.*,
                matieres.nom_matiere,
                enseignants.nom AS enseignant_nom,
                enseignants.prenom AS enseignant_prenom,
                filieres.nom_filiere,
                niveaux.nom_niveau
            FROM emplois_temps
            INNER JOIN matieres ON matieres.id = emplois_temps.matiere_id
            LEFT JOIN enseignants ON enseignants.id = emplois_temps.enseignant_id
            INNER JOIN filieres ON filieres.id = emplois_temps.filiere_id
            INNER JOIN niveaux ON niveaux.id = emplois_temps.niveau_id
            WHERE emplois_temps.jour = ?
              AND emplois_temps.filiere_id = (SELECT filiere_id FROM {$this->table} WHERE id = ?)
              AND emplois_temps.niveau_id = (SELECT niveau_id FROM {$this->table} WHERE id = ?)
            ORDER BY heure_debut ASC
        ");
        $stmt->execute([$day, $studentId, $studentId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Emploi du temps complet pour le calendrier étudiant
     */
    public function getWeeklySchedule($studentId) {
        $stmt = $this->pdo->prepare("
            SELECT DISTINCT
                emplois_temps.*,
                matieres.nom_matiere,
                enseignants.nom AS enseignant_nom,
                enseignants.prenom AS enseignant_prenom,
                filieres.nom_filiere,
                niveaux.nom_niveau
            FROM emplois_temps
            INNER JOIN matieres ON matieres.id = emplois_temps.matiere_id
            LEFT JOIN enseignants ON enseignants.id = emplois_temps.enseignant_id
            INNER JOIN filieres ON filieres.id = emplois_temps.filiere_id
            INNER JOIN niveaux ON niveaux.id = emplois_temps.niveau_id
            WHERE emplois_temps.filiere_id = (SELECT filiere_id FROM {$this->table} WHERE id = ?)
              AND emplois_temps.niveau_id = (SELECT niveau_id FROM {$this->table} WHERE id = ?)
            ORDER BY FIELD(emplois_temps.jour, 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'), heure_debut ASC
        ");
        $stmt->execute([$studentId, $studentId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
