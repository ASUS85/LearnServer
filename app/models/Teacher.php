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
     * Statistiques de l'enseignant
     */
    public function getStatistics($teacherId) {
        $totalCoursesStmt = $this->pdo->prepare("SELECT COUNT(*) FROM emplois_temps WHERE enseignant_id = ?");
        $totalCoursesStmt->execute([$teacherId]);
        $totalCourses = (int) $totalCoursesStmt->fetchColumn();

        $totalStudentsStmt = $this->pdo->prepare("
            SELECT COUNT(DISTINCT etudiants.id)
            FROM etudiants
            INNER JOIN notes ON notes.etudiant_id = etudiants.id
            INNER JOIN emplois_temps ON emplois_temps.matiere_id = notes.matiere_id
            WHERE emplois_temps.enseignant_id = ?
        ");
        $totalStudentsStmt->execute([$teacherId]);

        $totalNotesStmt = $this->pdo->prepare("
            SELECT COUNT(*)
            FROM notes
            INNER JOIN emplois_temps ON emplois_temps.matiere_id = notes.matiere_id
            WHERE emplois_temps.enseignant_id = ?
        ");
        $totalNotesStmt->execute([$teacherId]);

        return [
            'total_courses' => $totalCourses,
            'total_students' => (int) $totalStudentsStmt->fetchColumn(),
            'total_notes' => (int) $totalNotesStmt->fetchColumn(),
        ];
    }

    /**
     * Emploi du temps du jour
     */
    public function getTodaySchedule($teacherId, $day) {
        $stmt = $this->pdo->prepare("
            SELECT
                emplois_temps.*,
                matieres.nom_matiere,
                filieres.nom_filiere,
                niveaux.nom_niveau
            FROM emplois_temps
            INNER JOIN matieres ON matieres.id = emplois_temps.matiere_id
            INNER JOIN filieres ON filieres.id = emplois_temps.filiere_id
            INNER JOIN niveaux ON niveaux.id = emplois_temps.niveau_id
            WHERE emplois_temps.enseignant_id = ?
              AND emplois_temps.jour = ?
            ORDER BY heure_debut ASC
        ");
        $stmt->execute([$teacherId, $day]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Emploi du temps complet pour le calendrier
     */
    public function getWeeklySchedule($teacherId) {
        $stmt = $this->pdo->prepare("
            SELECT
                emplois_temps.*,
                matieres.nom_matiere,
                filieres.nom_filiere,
                niveaux.nom_niveau
            FROM emplois_temps
            INNER JOIN matieres ON matieres.id = emplois_temps.matiere_id
            INNER JOIN filieres ON filieres.id = emplois_temps.filiere_id
            INNER JOIN niveaux ON niveaux.id = emplois_temps.niveau_id
            WHERE emplois_temps.enseignant_id = ?
            ORDER BY FIELD(emplois_temps.jour, 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'), heure_debut ASC
        ");
        $stmt->execute([$teacherId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Dernières notes publiées
     */
    public function getRecentNotes($teacherId, $limit = 5) {
        $limit = (int) $limit;
        $stmt = $this->pdo->prepare("
            SELECT
                notes.note,
                notes.session,
                notes.date_note,
                etudiants.nom,
                etudiants.prenom,
                matieres.nom_matiere
            FROM notes
            INNER JOIN etudiants ON etudiants.id = notes.etudiant_id
            INNER JOIN matieres ON matieres.id = notes.matiere_id
            INNER JOIN emplois_temps ON emplois_temps.matiere_id = notes.matiere_id
            WHERE emplois_temps.enseignant_id = ?
            ORDER BY notes.date_note DESC
            LIMIT {$limit}
        ");
        $stmt->execute([$teacherId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Créer un enseignant
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
                telephone
            )
            VALUES(?, ?, ?, ?, ?, ?)
        ");

        return $stmt->execute([
            $data['matricule'] ?? generateMatricule('teacher'),
            $data['nom'],
            $data['prenom'],
            $data['email'],
            hashPassword($rawPassword),
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
        $stmt = $this->pdo->prepare("
            SELECT DISTINCT m.*, c.nom_classe
            FROM enseignant_matiere em
            JOIN matieres m ON em.matiere_id = m.id
            JOIN classes c ON m.classe_id = c.id
            WHERE em.enseignant_id = ?
        ");
        $stmt->execute([$teacherId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
